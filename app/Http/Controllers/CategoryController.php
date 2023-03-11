<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index (Request $request) {

        $name = $request->query('name');
        $categoryQuery = Category::query();

        if ($name) {
            $categoryQuery = $categoryQuery->where('name', 'LIKE', "%{$name}%");
        }

        $categoryQuery = $categoryQuery->get();

        return response()->json([
            'message' => 'Indexed successfuly!',
            'data' => $categoryQuery,
        ], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->all()
            ], 422);
        }

        $category = Category::query()
        ->create([
            'name' => $request['name'],
        ]);

        return response()->json([
            'message' => 'Created successfuly!',
            'data' => $category
        ], 200);
    }

    public function show(Category $category)
    {
        if (!$category) {
            return response()->json([
                'message' => 'Invalid id',
            ], 404);
        }

        return response()->json([
            'message' => 'Showed successfuly!',
            'date'=> $category,
        ], 200);
    }

    public function update(Request $request, Category $category)
    {
        $rules = [
            'name' => ['string', 'max:255', 'unique:categories'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->all()
            ], 422);
        }

        $category->update([
            'name' => $request['name']
        ]);

        return response()->json([
            'message' => 'Updated successfuly!',
            'data' => $category
        ], 200);
    }

    public function destroy(Category $category)
    {
        if (!$category) {
            return response()->json([
                'message' => 'invalid id',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Destroyed successfuly!',
        ], 200);
    }
}
