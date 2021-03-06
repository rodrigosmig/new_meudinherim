<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'DashboardController@home');

Auth::routes();

Route::group([
    'middleware' => ['auth', 'verified']
], function () {
    //Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard.index');
    Route::post('/dashboard', 'DashboardController@index')->name('dashboard.months');

    //Categories
    Route::resource('categories', 'CategoryController');

    //Acounts
    Route::get('accounts/transfer', 'AccountController@transfer')->name('accounts.transfer');
    Route::post('accounts/transfer', 'AccountController@transferStore')->name('accounts.transfer_store');
    Route::resource('accounts', 'AccountController');
    Route::get('accounts/{account_id}/entries', 'AccountEntryController@index')->name('accounts.entries');
    Route::post('accounts/{account_id}/entries', 'AccountEntryController@index')->name('accounts.entries.filter');

    //Credit-Card
    Route::resource('cards', 'CardController');
    Route::get('cards/{card_id}/invoices', 'CardController@invoices')->name('cards.invoices.index');
    Route::get('cards/{card_id}/invoices/{invoice_id}/generate-payment', 'CardController@generatePayment')->name('cards.invoices.generate-payment');
    Route::get('/cards/{card_id}/invoices/{invoice_id}/entries', 'InvoiceEntryController@index')->name('invoice_entries.index');

    //Account entries
    Route::get('account_entries/{account_entry}/edit', 'AccountEntryController@edit')->name('account_entries.edit');
    Route::put('account_entries/{account_entry}', 'AccountEntryController@update')->name('account_entries.update');
    Route::delete('account_entries/{account_entry}', 'AccountEntryController@destroy')->name('account_entries.destroy');
    Route::get('account_entries/create', 'AccountEntryController@create')->name('account_entries.create');
    Route::post('account_entries', 'AccountEntryController@store')->name('account_entries.store');

    //Invoice Entries
    Route::get('invoice_entries', 'InvoiceEntryController@create')->name('invoice_entries.create');
    Route::post('invoice_entries', 'InvoiceEntryController@store')->name('invoice_entries.store');
    Route::get('invoice_entries/{entry_id}/edit', 'InvoiceEntryController@edit')->name('invoice_entries.edit');
    Route::put('invoice_entries/{entry_id}/update', 'InvoiceEntryController@update')->name('invoice_entries.update');
    Route::delete('invoice_entries/{entry_id}', 'InvoiceEntryController@destroy')->name('invoice_entries.delete');

    //Accounts Payable
    Route::resource('payables', 'PayableController');
    Route::post('/payables/filter', 'PayableController@index')->name('payables.filter');
    Route::post('/payables/{id}/payment', 'PayableController@payment')->name('payables.payment');
    Route::get('/payables/{id}/cancel', 'PayableController@cancelPayment')->name('payables.cancel');

    //Accounts Receivable
    Route::resource('receivables', 'ReceivableController');
    Route::post('/receivables/filter', 'ReceivableController@index')->name('receivables.filter');
    Route::post('/receivables/{id}/receivement', 'ReceivableController@receivement')->name('receivables.receivement');
    Route::get('/receivables/{id}/cancel', 'ReceivableController@cancelreceivement')->name('receivables.cancel');

    //Reports
    Route::prefix('reports')
        ->as('reports.')
        ->group(function() {
            Route::get('/payables', 'ReportsController@payables')->name('payables');
            Route::post('/payables', 'ReportsController@payables')->name('payables.filter');
            Route::get('/receivables', 'ReportsController@receivables')->name('receivables');
            Route::post('/receivables', 'ReportsController@receivables')->name('receivables.filter');
            Route::get('/total-by-category', 'ReportsController@totalByCategory')->name('total_by_category');
            Route::post('/total-by-category', 'ReportsController@totalByCategory')->name('total_by_category.filter');
            Route::get('/total-by-category/ajax', 'ReportsController@ajaxtotalByCategory');
        });

    //Profile
    Route::get('profile', 'ProfileController@profile')->name('profile.index');
    Route::put('profile/password', 'ProfileController@updatePassword')->name('profile.password');
    Route::put('profile/update', 'ProfileController@updateProfile')->name('profile.update');
    Route::put('profile/avatar', 'ProfileController@updateAvatar')->name('profile.avatar');
    
    //Notifications
    Route::get('notifications', 'NotificationController@all_read')->name('notifications.all_read');
    Route::get('notifications/{notification_id}/account/{account_id}', 'NotificationController@show')->name('notifications.as_read');
});
