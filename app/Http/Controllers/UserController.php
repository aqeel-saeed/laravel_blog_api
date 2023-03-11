<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index (Request $request) {

        $users = User::query();

        $name = $request->query('name');
        $email = $request->query('email');

        if ($name) {
            $users = $users->where('name','LIKE', '%'.$name.'%');
        }
        if ($email) {
            $users = $users->where('email', $email);
        }

        $users = $users->get();

        return response()->json([
            'message' => 'Indexed successfuly!',
            'date'=> $users,
        ], 200);
    }

    public function show (User $user) {

        if(!$user){
            return response()->json([
                'message' => 'invalid id',
                'date'=> $user,
            ], 404);
        }

        return response()->json([
            'message' => 'Showed successfuly!',
            'date'=> $user,
        ], 200);
    }

    public function update (Request $request, User $user) {

        $rules = [
            'name' => ['string', 'max:255',],
            'email' => ['string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['string', 'min:8', 'confirmed', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            return response()->json([
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        if ($name)
            $user->update([
                'name' => $name,
            ]);
        if ($email)
            $user->update([
                'email' => $email,
            ]);
        if ($password)
            $user->update([
                'password' => $password,
            ]);

        return response()->json([
            'message' => 'Updated successfuly!',
            'data' => $user,
        ], 200);

    }

    public function destroy (User $user) {

        $user->delete();

        return response()->json([
            'message' => 'Destroyed successfuly!',
        ], 200);
    }

    public function myProfile (Request $request) {

        $user = $request->user();
        
        return response()->json([
            'data' => $user,
        ], 200);
    }
}
