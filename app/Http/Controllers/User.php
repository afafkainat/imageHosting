<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Mail\verifyemail;
use Illuminate\Http\Request;
use App\Models\Image;
use App\Notifications\VerifyUser;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class User extends Controller
{

    public function signup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2|max:100',
                'image' => 'image|mimes:png,jpg',
                'email' => 'required|email|unique:users',
                'age' => 'required|min:2',
                'password' => 'required|min:6|max:100',
                'confirm_password' => 'required|same:password'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation fails',
                    'errors' => $validator->errors()
                ], 422);
            }


            $name = $request->name;
            $picture = $request->file('image')->store('images');
            $age = $request->age;
            $email = $request->email;
            $password = Hash::make($request->password);
            $token = Str::random(60);

            DB::table('User')->insert([
                'name' => $name,
                'age' => $age,
                'image' => $picture,
                'email' => $email,
                'password' => $password,
                'token' => $token
            ]);

            $user = DB::table('User')->where('email', $request->email)->first();
            Mail::to($email)->send(new verifyemail($user));

            return response()->json([
                'message' => 'User Created successfully',
                'data' => ['token' => $token],

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'User not  Created ',
                'errors' => $e
            ], 201);
        }
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'email' => 'required|email',
            'password' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation fails',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = DB::table('User')->where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                $token = Str::random(40);
                DB::table('User')->where('token', $user->token)->update(['token' => $token]);
                return response()->json([
                    'message' => 'Login Successfull',
                    'token' => $token,
                    'data' => $user
                ], 200);
            } else {
                return response()->json([
                    'message' => 'incorrect credentials'
                ], 400);
            }
        }
    }


    public function updateProfile(Request $request, $email)
    {
        $user = DB::table('User')->where('email', $email)->first();

        if ($user) {
            if ($request->has('name')) {
                $name = $request->name;
                DB::table('User')->where('email', $email)->update(['name' => $name]);
            }
            if ($request->has('age')) {
                $age = $request->age;
                DB::table('User')->where('email', $email)->update(['age' => $age]);
            }
            if ($request->has('password')) {
                $password = $request->password;
                DB::table('User')->where('email', $email)->update(['password' => $password]);
            }
            if ($request->has('image')) {
                $picture = $request->image;
                DB::table('User')->where('email', $email)->update(['picture' => $picture]);
            }
            if ($request->has('email')) {
                $email = $request->email;
                DB::table('User')->where('email', $email)->update(['email' => $email]);
            }
            $value = 200;
            $status = 'Success';
            $message = 'Information Updated';
        } else {
            $status = 'error';
            $message = 'Account not exist';
        }
        return response()->json(['status' => $status, 'message' => $message], 200);
    }


    public function forgetPassword(Request $request)
    {
        try {


                $user = DB::table('User')->where('email', $request->email)->first();


                $datetime = Carbon::now()->format('Y-m-d H:i:s');

                if(Mail::to($user->email)->send(new ResetPassword($user)))

                {

                    return response()->json([
                        'succcess' => true,
                        'msg' => 'please check your email to reset password'
                    ]);
                }

                else

                {

                    return response()->json([
                        'succcess' => true,
                        'msg' => 'please check your email to reset password'

                    ]);
                }


            }

        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                 'msg' => $e
                ]);
        }
    }

    public function showImage(Request $request)
    {
        $data = $request->header('Authorization');
        if($data==""){
           $image=Image::where('status','Public')->get();
           foreach ($image as $user) {
            echo $user->path;
           }
        }
        else{
            $image=Image::where('status','Public')->get();
            $users = DB::table('Image')
                        ->where('status','Public')
                        ->where('status','Private')
                        ->where('status','Hidden')->get();
          return $users.$image;
        }
    }

}
