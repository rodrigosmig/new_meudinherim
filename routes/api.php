<?php

use Illuminate\Http\Response;
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
Route::post('/auth/register', 'Api\\AuthController@register');

Route::group([
    'middleware' => ['auth:sanctum']
], function() {
    //Auth
    Route::prefix('auth')
        ->group(function() {
            Route::post('/logout', 'Api\\AuthController@logout');
            Route::get('/me', 'Api\\AuthController@profile');
        }
    );
    
    //User
    Route::prefix('users')
        ->group(function() {
            Route::get('/', 'Api\\UserController@show');
            Route::put('/', 'Api\\UserController@update');
            Route::put('/password', 'Api\\UserController@updatePassword');
            Route::post('/avatar', 'Api\\UserController@updateAvatar');
            
        }
    );

    //Categories
    Route::apiResource('categories', 'Api\\CategoryController', ["as" => "api"]);

    //Accounts
    Route::get('accounts/balance/{id?}', 'Api\\AccountController@balance');
    Route::apiResource('accounts', 'Api\\AccountController', ["as" => "api"]);

    //Credit-Card
    Route::apiResource('cards', 'Api\\CardController', ["as" => "api"]);
    Route::get('cards/{card_id}/invoices', 'Api\\CardController@invoices');
    Route::get('cards/{card_id}/invoices/{invoice_id}', 'Api\\CardController@getInvoice');
    Route::get('cards/invoices/open', 'Api\\CardController@getInvoicesForMenu');
    
    //Payables
    Route::apiResource('payables', 'Api\\PayableController', ["as" => "api"]);
    Route::post('payables/{id}/payment', 'Api\\PayableController@payment');
    Route::post('payables/{id}/cancel-payment', 'Api\\PayableController@cancelPayment');

    //Receivables
    Route::apiResource('receivables', 'Api\\ReceivableController', ["as" => "api"]);
    Route::post('receivables/{id}/receivement', 'Api\\ReceivableController@payment');
    Route::post('receivables/{id}/cancel-receivement', 'Api\\ReceivableController@cancelPayment');

    //Invoice entries
    Route::get('cards/{card_id}/invoices/{invoice_id}/entries', 'Api\\InvoiceEntryController@index');
    Route::post('cards/{card_id}/entries', 'Api\\InvoiceEntryController@store');
    Route::get('invoice-entries/{entry_id}', 'Api\\InvoiceEntryController@show');
    Route::put('invoice-entries/{entry_id}', 'Api\\InvoiceEntryController@update');
    Route::delete('invoice-entries/{entry_id}', 'Api\\InvoiceEntryController@destroy');
    Route::get('invoice_entries/{entry_id}/next-parcels', 'Api\\InvoiceEntryController@nextParcels');
    Route::post('invoice_entries/{entry_id}/anticipate-parcels', 'Api\\InvoiceEntryController@anticipateParcels');

    //Account entries
    Route::get('accounts/{account_id}/entries', 'Api\\AccountEntryController@index');
    Route::post('account-entries', 'Api\\AccountEntryController@store');
    Route::get('account-entries/{entry_id}', 'Api\\AccountEntryController@show');
    Route::put('account-entries/{entry_id}', 'Api\\AccountEntryController@update');
    Route::delete('account-entries/{entry_id}', 'Api\\AccountEntryController@destroy');
    Route::post('account-entries/account-transfer', 'Api\\AccountEntryController@accountTransfer');
});

Route::fallback('Api\\FallbackController@fallback');
