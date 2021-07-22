<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostsApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $input = $request->all();
        $post = new Post();
        $post->content = $input['content'];
        $user = Auth::guard('api')->user();
        //here no need to check if user is null or not coz in the middleware i'm making sure you can not be here unless you sent valid token
        $post->user_id = $user->id;

        $post->save();
        $success['message'] = 'post has been created successfully';

        return response()->json(['success' => $success], 201);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'post_id'=>'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $input = $request->all();
        $post = Post::where('id','=',$input['post_id'])->first();
        if($post){
            $user = Auth::guard('api')->user();
            if($post->user_id == $user->id){
                $post->content = $input['content'];
                $post->save();
            }
            else{
                return response()->json(['error' => 'You can not edit this post , it doest not belongs to you'], 400);

            }
            $success['message'] = 'post has been edited successfully';
            $success['post'] = $post;
            return response()->json(['success' => $success], 200);

        }
        else{
            return response()->json(['error' => 'post was not found'], 404);

        }


    }

    public function getAll(){

        //here I Used DB instead of eloquent to customize the output and remove ids instead of building json step by step because this is a public request and does not need authentication
        $posts = DB::table('posts')
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->select('posts.id', 'posts.content', 'users.name AS Post_Owner_Name'  )
            ->get();


        return response()->json(['posts' => $posts], 200);
    }
    public function getUserPosts(){
        $user = Auth::guard('api')->user();

        $posts = Post::with('user','comments')->where('user_id','=',$user->id)->get();

        return response()->json(['posts' => $posts], 200);
    }

    public function delete(Request $request){
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer|min:1',

        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $post = Post::where('id','=',$request->input('post_id'))->first();
        if($post){

            $user = Auth::guard('api')->user();
            if($post->user_id == $user->id){
                // here no need to delete comments by foreach coz in migration I specified on delete cascade comments
                $post->delete();
                $success['message'] = 'post has been deleted successfully';
                return response()->json(['success' => $success], 200);

            }
            else{
                return response()->json(['error' => 'You can not delete this post , it doest not belongs to you'], 400);

            }


        }
        else{
            return response()->json(['error' => 'post was not found'], 404);

        }


    }
}
