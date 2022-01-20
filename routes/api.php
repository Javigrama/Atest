<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------
----------
| API Routes
|----------------------------------------------------------------
----------
|
| Here is where you can register API routes for your application.
These
| routes are loaded by the RouteServiceProvider within a group
which
| is assigned the "api" middleware group. Enjoy building your
API!
|
*/
Route::post('login', [AuthController::class, 'authenticate']);
Route::post('register', [AuthController::class, 'register']);
Route::get('product', [ProductController::class, 'index']);
Route::get('product/{id}', [ProductController::class, 'show']);
Route::group(['middleware' => ['jwt.verify']], function() {
//Todo lo que este dentro de este grupo requiere verificación de usuario.
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('get-user', [AuthController::class, 'getUser']);
    Route::post('product', [ProductController::class,
    'store']);
    Route::put('product/{id}', [ProductController::class,
    'update']);
    Route::delete('product/{id}', [ProductController::class,
    'destroy']);
});