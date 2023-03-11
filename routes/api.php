<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/signUp', [AuthController::class, 'signUp']);
Route::post('/logIn', [AuthController::class, 'logIn']);
Route::get('/logOut', [AuthController::class, 'logOut'])->middleware('auth:api');

// Route::get('/category/{category}', [CategoryController::class, 'index']);
// Route::post('/category/{category}', [CategoryController::class, 'store']);
// Route::get('/category/{category}', [CategoryController::class, 'show']);
// Route::post('/category/{category}', [CategoryController::class, 'show']);
// Route::delete('/category/{category}', [CategoryController::class, 'show']);

Route::group(['prefix' => 'categories', 'middleware' => ['auth:api']], function(){
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{category}', [CategoryController::class, 'show']);
    Route::post('/{category}', [CategoryController::class, 'update']);
    Route::delete('/{category}', [CategoryController::class, 'destroy']);
});

Route::group(['prefix' => 'posts', 'middleware' => ['auth:api']], function(){
    Route::get('/', [PostController::class, 'index']);
    Route::post('/', [PostController::class, 'store']);
    Route::get('/{post}', [PostController::class, 'show']);
    Route::post('/{post}', [PostController::class, 'update']);
    Route::delete('/{post}', [PostController::class, 'destroy']);
});

Route::group(['prefix' => 'Comments', 'middleware' => ['auth:api']], function(){
    Route::get('/', [CommentController::class, 'index']);
    Route::post('/', [CommentController::class, 'store']);
    Route::get('/{comment}', [CommentController::class, 'show']);
    Route::post('/{comment}', [CommentController::class, 'update']);
    Route::delete('/{comment}', [CommentController::class, 'destroy']);
});

Route::group(['prefix' => 'Users', 'middleware' => ['auth:api']], function(){
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{user}', [UserController::class, 'show']);
    Route::post('/{user}', [UserController::class, 'update']);
    Route::delete('/{user}', [UserController::class, 'destroy']);
});

Route::group(['prefix' => 'Tags', 'middleware' => ['auth:api']], function(){
    Route::get('/', [TagController::class, 'index']);
    Route::post('/', [TagController::class, 'store']);
    Route::get('/{tag}', [TagController::class, 'show']);
    Route::post('/{tag}', [TagController::class, 'update']);
    Route::delete('/{tag}', [TagController::class, 'destroy']);
});
