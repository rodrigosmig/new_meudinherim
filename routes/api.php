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
//Route::post('/users', 'Api\\UserController@store');
Route::post('/auth/register', 'Api\\AuthController@register');

Route::group([
    'middleware' => ['apiJwt']
], function() {
    //Auth
    Route::prefix('auth')
        ->group(function() {
            Route::post('/logout', 'Api\\AuthController@logout');
            Route::post('/refresh', 'Api\\AuthController@refresh');
        }
    );
    
    //User
    Route::prefix('users')
        ->group(function() {
            Route::get('/', 'Api\\UserController@show');
            Route::put('/', 'Api\\UserController@update');
            Route::post('/avatar', 'Api\\UserController@updateAvatar');
            
        }
    );

    //Categories
    Route::prefix('categories')
        ->group(function() {
            Route::post('/', 'Api\\CategoryController@store');
            Route::get('/', 'Api\\CategoryController@index');
            Route::get('/{id}', 'Api\\CategoryController@show');
            Route::put('/{id}', 'Api\\CategoryController@update');
            Route::delete('/{id}', 'Api\\CategoryController@destroy');
        }
    );

    Route::prefix('accounts')
        ->group(function() {
            Route::post('/', 'Api\\AccountController@store');
            Route::get('/', 'Api\\AccountController@index');
            Route::get('/{id}', 'Api\\AccountController@show');
            Route::put('/{id}', 'Api\\AccountController@update');
            Route::delete('/{id}', 'Api\\AccountController@destroy');
        }
    );

    //Cards
    Route::apiResource('cards', 'Api\\CardController');        
});
