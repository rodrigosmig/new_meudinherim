<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('categories', 'CategoryController');
Route::resource('accounts', 'AccountController');
Route::resource('cards', 'CardController');
Route::resource('account_entries', 'AccountEntryController');

Route::get('/invoices', 'InvoiceController@index')->name('invoices.index');

Route::get('cards/{card_id}/invoices', 'CardController@invoices')->name('cards.invoices.index');
Route::get('invoices/{invoice_id}/cards/{card_id}/entries', 'InvoiceEntryController@index')->name('invoice_entries.index');

Route::get('invoice_entries', 'InvoiceEntryController@create')->name('invoice_entries.create');
Route::post('invoice_entries', 'InvoiceEntryController@store')->name('invoice_entries.store');
Route::get('invoice_entries/{entry_id}/edit', 'InvoiceEntryController@edit')->name('invoice_entries.edit');
Route::put('invoice_entries/{entry_id}/update', 'InvoiceEntryController@update')->name('invoice_entries.update');
Route::delete('invoice_entries/{entry_id}', 'InvoiceEntryController@destroy')->name('invoice_entries.delete');


