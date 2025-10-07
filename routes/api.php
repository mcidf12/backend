<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('usuarios',UserController::class);

Route::post('auth/login', LoginController::class);
Route::post('auth/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');

//Route::get('/ruta-con-log', function () {return 'Esta ruta registrará sus encabezados';})->middleware('logear.encabezados');
