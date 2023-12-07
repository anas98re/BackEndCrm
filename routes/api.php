<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\PrivgLevelUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TaskController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('test',[RegisterController::class,'test']);

    //Tasks
    Route::post('addTask',[TaskController::class,'addTask']);
    Route::post('addGroup',[GroupController::class,'addGroup']);
    Route::post('assignTaskToEmployee/{id}',[TaskController::class,'assignTaskToEmployee']);
    Route::post('changeStatuseTask/{id}',[TaskController::class,'changeStatuseTask']);
    Route::get('viewTasksByIdAssigned/{id}',[TaskController::class,'viewTasksByIdAssigned']);
    Route::get('viewTaskByIdTask/{id}',[TaskController::class,'viewTaskByIdTask']);
    Route::get('viewAllTasksByStatus/{statusName}',[TaskController::class,'viewAllTasksByStatus']);
    Route::get('viewAllTasks',[TaskController::class,'viewAllTasks']);
    Route::post('viewAllTasksByDateTimeCrated',[TaskController::class,'viewAllTasksByDateTimeCrated']);
    Route::post('filterTaskesByAll',[TaskController::class,'filterTaskesByAll']);
    Route::post('changeTaskGroup/{id}',[TaskController::class,'changeTaskGroup']);

});

Route::post('addEmailFromAdmin',[RegisterController::class,'addEmailFromAdmin']);
Route::post('checkEmail',[RegisterController::class,'checkEmail']);
Route::post('login',[RegisterController::class,'login']);
Route::post('updatePermissions',[PrivgLevelUserController::class,'updatePermissions']);


Route::get('getinvoiceTask',[TaskController::class,'getinvoiceTask']);

