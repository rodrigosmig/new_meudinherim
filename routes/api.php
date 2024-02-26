<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\PayableController;
use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\FallbackController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReceivableController;
use App\Http\Controllers\Api\AccountEntryController;
use App\Http\Controllers\Api\InvoiceEntryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\TagController;

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

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [ForgotPasswordController::class, 'resetPassword']);

Route::get('/auth/verify-email/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/auth/resend-email', [VerificationController::class, 'resend'])->name('verification.resend');

Route::group([
    'middleware' => ['auth:sanctum', 'verified']
], function() {
    //Dashboard
    Route::prefix('dashboard')
        ->group(function() {
            Route::get('/', [DashboardController::class, 'dashboard']);
        }
    );

    //Auth
    Route::prefix('auth')
        ->group(function() {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'profile']);
        }
    );
    
    //User
    Route::prefix('users')
        ->group(function() {
            Route::get('/', [UserController::class, 'show']);
            Route::put('/', [UserController::class, 'update']);
            Route::put('/password', [UserController::class, 'updatePassword']);
            Route::post('/avatar', [UserController::class, 'updateAvatar']);
            
        }
    );

    //Categories
    Route::apiResource('categories', 'Api\\CategoryController', ["as" => "api"]);

    //Accounts
    Route::get('accounts/balance/{id?}', [AccountController::class, 'balance']);
    Route::apiResource('accounts', 'Api\\AccountController', ["as" => "api"]);

    //Credit-Card
    Route::apiResource('cards', 'Api\\CardController', ["as" => "api"]);
    Route::get('cards/{card_id}/invoices', [CardController::class, 'invoices']);
    Route::get('cards/{card_id}/invoices/{invoice_id}', [CardController::class, 'getInvoice']);
    Route::get('cards/invoices/open', [CardController::class, 'getInvoicesForMenu']);
    Route::post('cards/invoices/partial-payment', [InvoiceController::class, 'partialPayment']);
    Route::put('cards/invoices/{invoice_id}/paid', [InvoiceController::class, 'setAsPaid']);
    
    //Payables
    Route::apiResource('payables', 'Api\\PayableController', ["as" => "api"]);
    Route::post('payables/{id}/payment', [PayableController::class, 'payment']);
    Route::post('payables/{id}/cancel-payment', [PayableController::class, 'cancelPayment']);

    //Receivables
    Route::apiResource('receivables', 'Api\\ReceivableController', ["as" => "api"]);
    Route::post('receivables/{id}/receivement', [ReceivableController::class, 'payment']);
    Route::post('receivables/{id}/cancel-receivement', [ReceivableController::class, 'cancelPayment']);

    //Invoice entries
    Route::get('cards/{card_id}/invoices/{invoice_id}/entries', [InvoiceEntryController::class, 'index']);
    Route::post('cards/{card_id}/entries', [InvoiceEntryController::class, 'store']);
    Route::get('invoice-entries/{entry_id}', [InvoiceEntryController::class, 'show']);
    Route::put('invoice-entries/{entry_id}', [InvoiceEntryController::class, 'update']);
    Route::delete('invoice-entries/{entry_id}', [InvoiceEntryController::class, 'destroy']);
    Route::get('invoice_entries/{entry_id}/next-parcels', [InvoiceEntryController::class, 'nextParcels']);
    Route::post('invoice_entries/{entry_id}/anticipate-parcels', [InvoiceEntryController::class, 'anticipateParcels']);

    //Account entries
    Route::get('accounts/{account_id}/entries', [AccountEntryController::class, 'index']);
    Route::post('account-entries', [AccountEntryController::class, 'store']);
    Route::get('account-entries/{entry_id}', [AccountEntryController::class, 'show']);
    Route::put('account-entries/{entry_id}', [AccountEntryController::class, 'update']);
    Route::delete('account-entries/{entry_id}', [AccountEntryController::class, 'destroy']);
    Route::post('account-entries/account-transfer', [AccountEntryController::class, 'accountTransfer']);

    //Reports
    Route::prefix('reports')
        ->group(function () {
            Route::get('/accounts', [ReportsController::class, 'accounts']);
            Route::get('/total-account-by-category', [ReportsController::class, 'getTotalAccountByCategory']);
            Route::get('/total-credit-by-category', [ReportsController::class, 'getTotalCreditByCategory']);
            Route::get('/total-by-category/details', [ReportsController::class, 'getTotalByCategoryDetailed']);
        });

    //Notifications
    Route::prefix('notifications')
        ->group(function () {
            Route::get('', [NotificationController::class, 'index']);
            Route::get('/all-as-read', [NotificationController::class, 'markAllAsRead']);
            Route::put('/{id}', [NotificationController::class, 'markAsRead']);
        });
    
    //Tags
    Route::prefix('tags')
        ->group(function () {
            Route::get('', [TagController::class, 'index']);
        });
});

Route::fallback([FallbackController::class, 'fallback']);
