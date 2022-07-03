<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;


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

Route::post('register', [ApiController::class, 'register']);

Route::post('login', [ApiController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function(){
    //All secure URL's
    Route::get('show', [ApiController::class, 'show']);
    Route::delete('delete/{id}',[ApiController::class, 'delete']);
    Route::put('update',[ApiController::class, 'update']);
});

Route::post('display',[ApiController::class, 'display']);


