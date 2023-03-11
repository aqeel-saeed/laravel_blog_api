<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index (Post $post) {

        if (!$post) {
            return response()->json([
                'message' => 'Invalid ID',
            ], 404);
        }

        $comments = $post->comments()->get();

        if($comments){
            return response()->json([
                'message' => 'Indexed successfully',
                'data' => $comments
            ], 200);
        } else {
            return response()->json([
                'message' => 'No comments',
                'data' => $comments
            ], 200);
        }
    }

    public function store (Request $request, Post $post) {

        $rules = [
            'comment' => ['required', 'string', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $user = auth()->user();
        $data = $request->input('comment');

        $comment = $post->commnts()->create([
            'comment' => $data,
            'user_id' => $user['id'],
        ]);

        return response()->json([
            'message' => 'Commented successfully',
            'data' => $comment
        ], 200);
    }

    public function show (Comment $comment) {

        return response()->json([
            'message' => 'Showed successfully',
            'data' => $comment
        ], 200);
    }

    public function update (Request $request, Comment $comment) {

        $user = $request->user();

        if ($user['id'] != $comment['user_id']) {
            return response()->json([
                'message' => 'unauthorized',
            ], 403);
        };

        $rules = [
            'comment' => ['required', 'string', 'max:255'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $data = $request->input('comment');

        $comment->update([
            'comment' => $data,
        ]);

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $comment
        ], 200);
    }

    public function destroy (Request $request, Comment $comment) {

        $user = $request->user();
        $post = Post::query()->find($comment['post_id']);

        if ($user['id'] != $comment['user_id'] &&
            $user['id'] != $post['user_id']
        ) {
            return response()->json([
                'message' => 'unauthorized',
            ], 403);
        };

        if ($comment) {
            $comment->delete();

            return response()->json([
                'message' => 'Destroyed successfuly!',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid ID!',
            ], 404);
        }
    }
}
