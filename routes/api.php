<?php

use App\Http\Controllers\NewsController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('register',[\App\Http\Controllers\AuthController::class,'register']);
// Route::post('login',[\App\Http\Controllers\AuthController::class,'login']);
// Route::post('login2',[\App\Http\Controllers\Api\Auth\LoginController::class,'login']);

// Route::middleware('auth:sanctum')->group(function(){
//     Route::get('user',[\App\Http\Controllers\AuthController::class,'user']);
// });

Route::post('register', 'App\Http\Controllers\AuthController@register');
Route::post('login', 'App\Http\Controllers\AuthController@login');
Route::post('logout', 'App\Http\Controllers\AuthController@logout');
Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
Route::post('me', 'App\Http\Controllers\AuthController@me');

 //News
//  Route::get('/news', 'App\Http\Controllers\NewsController@show');
 Route::resource('/news', 'App\Http\Controllers\NewsController');
 Route::resource('/products', 'App\Http\Controllers\ProductsController');
 Route::group(['middleware' => 'checkjwt'], function () {
    Route::resource('/orders', 'App\Http\Controllers\OrdersController');
 });
//  Route::post('/news/{news}', 'App\Http\Controllers\NewsController@update');
//  Route::post('/table_news', 'App\Http\Controllers\NewsController@table');


