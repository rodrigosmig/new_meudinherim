<?php

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

Route::post('auth/login', 'Api\\AuthController@login');

Route::group([
    'middleware' => ['apiJwt']
], function() {
    //Auth
    Route::prefix('auth')
        ->group(function() {
            Route::post('/logout', 'Api\\AuthController@logout');
            Route::post('/refresh', 'Api\\AuthController@refresh');
        });
    
    //User
    Route::prefix('users')
        ->group(function() {
            Route::post('/', 'Api\\UserController@store');
            Route::get('/', 'Api\\UserController@show');
            Route::put('/', 'Api\\UserController@update');
            Route::post('/avatar', 'Api\\UserController@updateAvatar');
            
        });
        
});
