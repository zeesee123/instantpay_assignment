<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BoardController;

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



// Public routes with throttling for login and registration
Route::middleware('throttle:10,1')->group(function(){

    // Route for user login
    Route::post('/login',[AuthController::class,'login']);

    // Route for user registration
    Route::post('/register',[AuthController::class,'register']);

});



// Authenticated routes with throttling
Route::middleware(['auth:sanctum','throttle:60,1'])->group(function(){

    // Route to get a list of all users (accessible to any authenticated user)
    Route::get('/users',[AuthController::class,'users']);
    
    // Route for user logout
    Route::post('/logout',[AuthController::class,'logout']); //this will only work if the middleware is applied else it won't


    //*********/ Routes for managing boards*********
    Route::prefix('boards')->group(function(){
            
        // Get all boards for the authenticated user
        Route::get('/',[BoardController::class,'index']);
    
        // Create a new board
        Route::post('/',[BoardController::class,'store']);

        // Update a specific board, Only the owner of the board can update it.
        Route::put('/{id}',[BoardController::class,'update']);

        // Get a specific board, Only the owner of the board can view it
        Route::get('/{id}',[BoardController::class,'show']);

         // Delete a specific board, Only the owner of the board can delete it
        Route::delete('/{id}',[BoardController::class,'destroy']);


    });


    

    //*************/ Routes for managing tasks***********
    Route::prefix('/tasks')->group(function(){

        // Get all tasks for a specific board , Only the owner of the board can view these tasks.
        Route::get('/board/{id}',[TaskController::class,'index']);

        // Create a new task in a specific board, Only the owner of the board can add these tasks.
        Route::post('/board/{id}',[TaskController::class,'store']);

        // Get a specific task, Only the owner of the board can retrieve the tasks.
        Route::get('/{id}',[TaskController::class,'show']);

        // Update a specific task, Only the owner of the board to which this task belongs can update the task.
        Route::put('/{id}',[TaskController::class,'update']);
        
        
        // Delete a specific task, Only the owner of the board to which this task belongs can delete the task. 
        Route::delete('/{id}',[TaskController::class,'destroy']);

        
    });

    
    

    


});



