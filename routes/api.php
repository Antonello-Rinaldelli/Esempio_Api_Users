<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsersController;
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

Route::prefix('v1')->group(function (){

    //Rotta registrazione
    Route::post('register', [UsersController::class, 'register']);

    //Rotta login

    Route::post('login', [AuthController::class, 'login']);

    //Rotta inserimento otp

    Route::post('otp', [AuthController::class, 'otp']);

    //Rotte protette da autenticazione
    
    Route::group([
        "middleware"=> ["auth:sanctum"]
    ], function (){
    
             
        //Rotta per accedere a dati utente    
        Route::get('profile', [UsersController::class, 'profile']);

        //Rotta per modificare dati utente
        Route::put('update/{id}', [UsersController::class, 'update']);

        //Rotta per eliminare utente
        Route::delete('destroy/{id}', [UsersController::class, 'destroy']);

        //Rotta per il logout
        Route::get('logout', [AuthController::class, 'logout']);
        
    
    });

});
