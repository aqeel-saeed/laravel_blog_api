<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index (Request $request) {

        $rules = [
            'title' => ['string','max:255', 'nullable'],
            'content' => ['string','max:255', 'nullable'],
            'category_id' => 'integer|exists:categories,id|nullable',
            'user_id' => 'integer|exists:users,id|nullable',
        ];

        $validator = Validator::make($request->query(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $title = $request->query('title');
        $content = $request->query('content');
        $category_id = $request->query('category_id');
        $user_id = $request->query('user_id');

        $postsQuery = Post::query();

        if ($title){
            $postsQuery = $postsQuery->where('content','LIKE', '%'.$title.'%');
        }
        if ($content) {
            $postsQuery = $postsQuery->where('content','LIKE', '%'.$content.'%');
        }
        if ($category_id) {
            $postsQuery = $postsQuery->where('category_id', $category_id);
        }
        if ($user_id) {
            $postsQuery = $postsQuery->where('user_id', $user_id);
        }

        $postsQuery = $postsQuery->get();

        return response()->json([
            'message' => 'Indexed successfuly!',
            'date'=> $postsQuery,
        ], 200);
    }

    public function store (Request $request)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'numeric', 'min:1'],
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $user = $request->user();

        $title = $request->input('title');
        $content = $request->input('content');
        $category_id = $request->input('category_id');

        if ($request->hasFile('image')) {
            $destination_path = 'public/images/posts';
            $image = $request->file('image');

            $image_name = implode('.', [
                md5_file($image->getPathname()),
                $image->getClientOriginalExtension()
            ]);

            $path = $request->file('image')->storeAs($destination_path, $image_name);
        }

        $post = $user->Post()->create([
            'title' => $title,
            'content' => $content,
            'category_id' => $category_id,
            'image' => $image_name,
        ]);

        return response()->json([
            'message' => 'Created successfuly!',
            'data' => $post,
        ], 200);
    }

    public function show (Request $request, Post $post) {

        if (!$post) {
            return response()->json([
                'message' => 'invalid id',
                'data'=> $post,
            ], 404);
        }

        $post = $post->with('user', 'comments');

        return response()->json([
            'message' => 'Showed successfuly!',
            'data' => $post,
        ], 200);
    }

    public function update (Request $request, Post $post) {

        $rules = [
            'title' => ['string', 'max:255', 'nullable'],
            'content' => ['string', 'nullable'],
            'category_id' => 'integer|exists:categories,id|nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $user = $request->user();
        if ($user['id'] != $post['user_id']) {
            return response()->json([
                'message' => 'unauthorized',
            ], 403);
        }

        $title = $request->input('title');
        $content = $request->input('content');
        $category_id = $request->input('category_id');

        if ($title)
            $post->update([
                'title' => $title,
            ]);
        if ($content)
            $post->update([
                'content' => $content,
            ]);
        if ($category_id)
            $post->update([
                'category_id' => $category_id,
            ]);

        if ($request->hasFile('image')) {
            $destination_path = 'public/images/posts';
            $image = $request->file('image');

            $image_name = implode('.', [
                md5_file($image->getPathname()),
                $image->getClientOriginalExtension()
            ]);

            $path = $request->file('image')->storeAs($destination_path, $image_name);
        }

        if ($path)
            $post->update([
                'image' => $path,
            ]);

        return response()->json([
            'message' => 'Updated successfuly!',
            'data' => $post,
        ], 200);
    }

    public function destroy (Request $request, Post $post) {

        $user = $request->user();

        if ($user['id'] != $post['user_id']) {
            return response()->json([
                'message' => 'unauthorized',
            ], 403);
        };

        if ($post) {
            $post->delete();

            return response()->json([
                'message' => 'Destroyed successfuly!',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid ID!',
            ], 404);
        }
    }

    public function myProducts (Request $request) {

        $user = $request->user();
        $posts = $user->posts()->get();

        return response()->json([
            'data' => $posts,
        ], 200);
    }
}
