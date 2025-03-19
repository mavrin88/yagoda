<?php

use App\Http\Controllers\Api\v1\OrdersAutoUpdatePageController;
use App\Http\Controllers\Api\v1\OrganizationsController;
use App\Http\Controllers\Api\v1\SmsConfirmationController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DaDataController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('webhook', [WebhookController::class, 'webhook'])
    ->name('webhook');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//todo: должен быть в web.php
Route::post('/organizations/select', [OrganizationsController::class, 'selectOrganization'])
    ->name('api.organizations.select');


// SmsConfirmation
Route::post('/sendSmsCode', [SmsConfirmationController::class, 'sendSmsCode'])
    ->name('sendSmsCode');

Route::post('/verifySmsCode', [SmsConfirmationController::class, 'verifySmsCode'])
    ->name('verifySmsCode');

Route::post('/checkIsOpenOrder', [OrderController::class, 'checkIsOpenOrder'])
    ->name('checkIsOpenOrder');


// Orders
//todo: должен быть в web.php ? Проверить
Route::post('/createDraftOrder', [OrderController::class, 'createDraftOrder']);
//todo: должен быть в web.php ? Проверить
Route::post('/saveOrder', [OrderController::class, 'saveOrder']);

//todo: должен быть в web.php ? Проверить
Route::post('/sendPayTips', [OrderController::class, 'sendPayTips']);

// Products in Categories
//todo: должен быть в web.php
Route::middleware('web')->post('/productsNames', [ProductController::class, 'renameProductsNames']);

//todo: должен быть в web.php ? Проверить
Route::post('/daData', [DaDataController::class, 'findById']);

// Bill
//todo: должен быть в web.php
Route::middleware('web')->post('/selectedBill', [BillController::class, 'saveSelectedBill']);

//todo: должен быть в web.php
Route::post('/save-links-settings', [OrganizationsController::class, 'saveLinksSettings']);
//todo: должен быть в web.php
Route::post('/submit-review', [OrganizationsController::class, 'submitReview']);


// CLIENTS
//todo: должен быть в web.php? Проверить
Route::get('/search-clients', [ClientController::class, 'searchClients']);
//todo: должен быть в web.php? Проверить
Route::post('/save-client', [ClientController::class, 'saveClient']);

// Сохранить гео в заказе. Должно быть в api.php без авторизации
Route::post('/order/geo', [OrderController::class, 'orderGeoSave']);

