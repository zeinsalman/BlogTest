<?php

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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::post('register','Api\UsersApiController@register');


Route::post('login','Api\UsersApiController@login');
Route::group(['middleware' => ['jwt.verify']], function() {
    Route::post('createPost','Api\PostsApiController@store');
    Route::get('getMyPosts','Api\PostsApiController@getUserPosts');
    Route::put('updatePost','Api\PostsApiController@update');
    Route::delete('deletePost','Api\PostsApiController@delete');
    Route::post('createComment','Api\CommentsApiController@store');
    Route::delete('deleteComment','Api\CommentsApiController@delete');
});
Route::get('getAllPosts','Api\PostsApiController@getAll');

