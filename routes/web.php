<?php

use App\Services\AccountService;
use App\Services\AccountEntryService;
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

//Dashboard
Route::get('/dashboard', 'HomeController@index')->name('dashboard.index');
Route::post('/dashboard', 'HomeController@index')->name('dashboard.months');

//Categories
Route::resource('categories', 'CategoryController');

//Acounts
Route::resource('accounts', 'AccountController');
Route::get('accounts/{account_id}/entries', 'AccountEntryController@index')->name('accounts.entries');
Route::post('accounts/{account_id}/entries', 'AccountEntryController@index')->name('accounts.entries');

//Credit-Card
Route::resource('cards', 'CardController');
Route::get('cards/{card_id}/invoices', 'CardController@invoices')->name('cards.invoices.index');
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

/* Route::get('/payables/create', 'AccountsSchedulingController@payable_create')->name('payables.create');
Route::get('/payables', 'AccountsSchedulingController@payables')->name('payables.payables'); */


Route::get('/update', function() {
    $accountService = app(AccountService::class);

    $account = $accountService->findById(4);

    $accountService->updateBalance($account, '2018-02-01');

    echo "Update finalizado";
});

Route::get('/import', function() {
    //dd("Teste");
    $service = app(AccountEntryService::class);
    $accountService = app(AccountService::class);

    $json_entries = file_get_contents(base_path('public/storage/entries.json'));
    
    $data_entries = json_decode($json_entries, true);
    $entries = [];

    foreach ($data_entries as $value) {
        if ($value['user'] == 2) {
            $entries[] = $value;
        }
    }

    foreach ($entries as $entry) {        

        if ($entry['banco'] == 3) {
            $account = $accountService->findById(4);

            if(in_array($entry['categoria'], [22,28,98])) {
                $category_id = 1;
            } 
            elseif (in_array($entry['categoria'], [27,31])) {
                $category_id = 2;
            }
            elseif (in_array($entry['categoria'], [19])) {
                $category_id = 3;
            }
            elseif (in_array($entry['categoria'], [5,88,90,135,136])) {
                $category_id = 4;
            }
            elseif (in_array($entry['categoria'], [97])) {
                $category_id = 5;
            }
            elseif (in_array($entry['categoria'], [124])) {
                $category_id = 6;
            }
            elseif (in_array($entry['categoria'], [30,37])) {
                $category_id = 7;
            }
            elseif (in_array($entry['categoria'], [8])) {
                $category_id = 8;
            }
            elseif (in_array($entry['categoria'], [1,23,110,121])) {
                $category_id = 9;
            }
    
            elseif (in_array($entry['categoria'], [2,3,6,7,94,132,134])) {
                $category_id = 10;
            }
            elseif (in_array($entry['categoria'], [24,100,104,105])) {
                $category_id = 11;
            }
            elseif (in_array($entry['categoria'], [32,109])) {
                $category_id = 12;
            }
            elseif (in_array($entry['categoria'], [101,122,130])) {
                $category_id = 13;
            }
            elseif (in_array($entry['categoria'], [21,103])) {
                $category_id = 14;
            }
            elseif (in_array($entry['categoria'], [108])) {
                $category_id = 15;
            }
            elseif (in_array($entry['categoria'], [12,15,35,89,133,131])) {
                $category_id = 16;
            }
            elseif (in_array($entry['categoria'], [11,13,17,18,127,129])) {
                $category_id = 17;
            }
            elseif (in_array($entry['categoria'], [14,93,95])) {
                $category_id = 18;
            }
            elseif (in_array($entry['categoria'], [16,25,29,36,106,111,128])) {
                $category_id = 19;
            }
            elseif (in_array($entry['categoria'], [4])) {
                $category_id = 20;
            }
            elseif (in_array($entry['categoria'], [96])) {
                $category_id = 21;
            }
            elseif (in_array($entry['categoria'], [34])) {
                $category_id = 22;
            }
            elseif (in_array($entry['categoria'], [9,102,123])) {
                $category_id = 23;
            }
            elseif (in_array($entry['categoria'], [20])) {
                $category_id = 24;
            }
            elseif (in_array($entry['categoria'], [107])) {
                $category_id = 25;
            }
            elseif (in_array($entry['categoria'], [10])) {
                $category_id = 26;
            }
            elseif (in_array($entry['categoria'], [26])) {
                $category_id = 27;
            }

    
            $data = [
                'date'          => $entry['data'],
                'description'   => $entry['descricao'],
                'value'         => $entry['valor'],
                'category_id'   => $category_id,
            ];
    
            $service->make($account, $data);
        }
    }

    echo "Import finalizado";

});

