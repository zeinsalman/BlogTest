<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Post;
use App\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentsApiController extends Controller
{
    public function store(Request $request)
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
            $comment = new PostComment();
            $comment->content = $input['content'];
            $user = Auth::guard('api')->user();
            //here no need to check if user is null or not coz in the middleware i'm making sure you can not be here unless you sent valid token
            $comment->user_id = $user->id;
            $comment->post_id = $post->id;
            $comment->save();
            $success['message'] = 'comment has been created successfully';

            return response()->json(['success' => $success], 201);
        }
        else{
            return response()->json(['error' => 'Post was not found'], 404);
        }

    }

    public function delete(Request $request){
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = Auth::guard('api')->user();
        $comment = PostComment::where('id','=',$request->input('comment_id'))->first();
        if($comment){
            if($comment->user_id == $user->id){
                $comment->delete();
                $success['message'] = 'comment has been deleted successfully';
                return response()->json(['success' => $success], 200);

            }
            else{
                return response()->json(['error' => 'You can not delete this comment , it doest not belongs to you'], 400);

            }
        }
        else{
            return response()->json(['error' => 'comment was not found'], 404);

        }


    }
}
