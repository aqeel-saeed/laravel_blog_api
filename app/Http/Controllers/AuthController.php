<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function logIn (Request $request) {

        $rules = [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors()->all(),
                Response::HTTP_UNPROCESSABLE_ENTITY // = 422
            );
        }

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            throw new AuthenticationException();
        }

        $user = $request->user();

        // add token to the user
        $tokenResult = $user->createToken('authToken');

        $data['user'] = $user;
        $data['token_type'] = 'Bearer';
        $data['access_token'] = $tokenResult->accessToken;

        return response()->json(
            $data,
            Response::HTTP_OK // = 200
        );
    }

    public function SignUp (Request $request) {

        $rules = [
            'name' => ['required', 'string', 'max:255',],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors()->all(),
                Response::HTTP_UNPROCESSABLE_ENTITY // = 422
            );
        }

        $request['password'] = Hash::make($request['password']);

        $user = User::query()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // add token to the user
        $tokenResult = $user->createToken('Personal Access Token');

        $data['user'] = $user;
        $data['token_type'] = 'Bearer';
        $data['access_token'] = $tokenResult->accessToken;

        return response()->json(
            $data,
            Response::HTTP_OK // = 200
        );
    }

    public function logOut (Request $request) {

        $request->user()->token()->revoke();

        return response()->json([
            "message" => "logged out successfully"
        ],200);
    }
}
