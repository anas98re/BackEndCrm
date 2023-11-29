<?php

use App\Http\Controllers\PrivgLevelUserController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::post('register',[RegisterController::class,'register']);

Route::middleware('auth:sanctum','verified')->group(function () {

});

Route::post('addEmailFromAdmin',[RegisterController::class,'addEmailFromAdmin']);
Route::post('checkEmail',[RegisterController::class,'checkEmail']);
Route::post('login',[RegisterController::class,'login']);
Route::post('updatePermissions',[PrivgLevelUserController::class,'updatePermissions']);
