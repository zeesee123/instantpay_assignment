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


//always remember whenever there is a way off error in api stuff it always will return the index page as it's output

Route::middleware('throttle:10,1')->group(function(){

    Route::post('/login',[AuthController::class,'login']);

    Route::post('/register',[AuthController::class,'register']);

});




Route::middleware(['auth:sanctum','throttle:60,1'])->group(function(){

    Route::get('/users',[AuthController::class,'users']);
    
    Route::post('/logout',[AuthController::class,'logout']); //this will only work if the middleware is applied else it won't


    Route::prefix('boards')->group(function(){
            
        Route::get('/',[BoardController::class,'index']);
    
        Route::post('/',[BoardController::class,'store']);

        Route::put('/{id}',[BoardController::class,'update']);

        Route::get('/{id}',[BoardController::class,'show']);

        Route::delete('/{id}',[BoardController::class,'destroy']);


    });


    
    //task routes


    Route::prefix('/tasks')->group(function(){

        Route::get('/board/{id}',[TaskController::class,'index']);

        Route::post('/board/{id}',[TaskController::class,'store']);

        Route::get('/{id}',[TaskController::class,'show']);

        Route::delete('/{id}',[TaskController::class,'destroy']);

        Route::put('/{id}',[TaskController::class,'update']);
    });

    
    

    

    // Route::post('/create_board',[BoardController::class,''])
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
