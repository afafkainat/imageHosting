<?php

use App\Http\Controllers\image;
use App\Http\Controllers\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/insert',[User::class,'signup']);
Route::post('/login',[User::class,'login']);
Route::post('/update/{email}',[User::class,'updateProfile']);
Route::get('account/verify/{token}',[User::class, 'verifyAccount']);
Route::post('/forgetPassword',[User::class,'forgetPassword']);
Route::get('showImage',[User::class,'showImage']);

//image
Route::post('/upload',[image::class,'upload']);
Route::get('/verify',[image::class,'verify']);
Route::get('/search',[image::class,'search']);
Route::get('/Share', [image::class, 'imagelink']);
Route::delete('/delete', [image::class, 'remove']);
