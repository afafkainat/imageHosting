<?php

namespace App\Http\Controllers;

use App\Models\Image as ModelsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class image extends Controller
{
    public function uploae(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:value(50)',
            'profile_photo' =>'required|image|mimes:jpeg,png,jpg|max:1024'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation fails',
                'errors' => $validator->errors()
            ], 422);
        }

        $data=$request->validated();
        $token = $request->header('Authorization');
        $user= DB::table('Image')->where('remember_me',$token)->first();
        if (!empty($request->has('profile_photo'))) {
            $file =$request->file('profile_photo');
            $extension = $file->getClientOriginalExtension();
            $path = $request->File("profile_photo")->store('Images');
            if($request->has('status')==""){
                   $status="hidden";
            }else{
                $status=$request->status;
            }

            $image=ModelsImage::Create([
                'name' => $data['name'],
                'date' => Carbon::now(),
                'time' => time(),
                'extension' => $extension,
                'path' => $path,
                'status' => $status
            ]);
             $user->image()->attach($image->id);
             return response()->json($image);
        }
    }

    public function verify(Request $request)
    {
        $email=$request->query('email');
        $token = $request->header('Authorization');
        $user=DB::table('Image')->where('remember_me',$token)->first()->where('email',$email)->get();
        if($user){
         $id=$request->query('id');
         $user->image()->attach($id);
         return response()->json([
            'message' => 'User is authenticated ',
            'data'=>$user

        ], 201);
        }else{
            return response()->json([
                'message' => 'User is not authenticated ',

            ], 201);

        }
    }

    public function search(Request $request)
    {
        $token = $request->header('Authorization');
        $user=DB::table('Image')->where('remember_me',$token)->first();
        $image = $user->image();
        if ($request->input('name'))
        {
            $image= $image->where('name',$request->name)->get();
        }
        $images=$this->$image;
        if($images)
        {
            foreach($image as $user){
                echo $user ;
            }
            return response()->json([
                "message"=>"successfull"
            ]);
           }
        else{
         return response()->json([
            "message"=>" not authenticated user "
        ]);
         }
    }

    public function imagelink(Request $request)
    {
        $id=$request->id;
         $link="http://localhost:8000/api/shareableLink/.$id";
         return response()->json([
            'message'=>'your link is ready to share',
            'data'=>$link
        ]);
    }

    public function remove(Request $request)
    {
        $image=DB::table('Image')->where('id',$request->id)->first();
        $image->delete();
    }

}
