<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{

    public function index (Post $post) {

        if (!$post) {
            return response()->json([
                'message' => 'Invalid ID',
            ], 404);
        }

        $tags = $post->tags()->get();
        $users = [];

        foreach($tags as $tag) {
            $user = User::select('id', 'name')->where('id', $tag['user_id'])->first();

            if($user) {
                $users[] = $user;
            }
        }

        return response()->json(['users' => $users]);


        if ($tags) {
            return response()->json([
                'message' => 'Indexed successfully',
                'data' => $users
            ], 200);
        } else {
            return response()->json([
                'message' => 'No tags',
                'data' => $users
            ], 200);
        }
    }

    public function store (Request $request, Post $post) {

        $user = $request->user();

        if ($user['id'] != $post['user_id']) {
            return response()->json([
                'message' => 'unauthorized',
            ], 403);
        };

        $rules = [
            'tags.*' => 'integer|exists:users,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $tags = $request->input('tags');

        foreach($tags as $tag) {
            $post->tags()->create([
                'user_id' => $tag,
            ]);
        }

        return response()->json([
            'message' => 'Taged successfully',
        ], 200);
    }

    public function destroy (Request $request, Tag $tag) {

        $user = $request->user();
        $post = Post::query()->find($tag['post_id']);

        if ($user['id'] != $tag['user_id'] &&
            $user['id'] != $post['user_id']
        ) {
            return response()->json([
                'message' => 'unauthorized',
            ], 403);
        }

        if ($tag) {
            $tag->delete();

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
