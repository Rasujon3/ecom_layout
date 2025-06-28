<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReferController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\ReportController;

Route::group(['middleware' => 'prevent-back-history'],function(){

	//sliders

    Route::resource('sliders', SliderController::class);

  //expenses

    Route::resource('expenses', ExpenseController::class);

  //units

    Route::resource('units', UnitController::class);

  //products

    Route::resource('products', ProductController::class);

  //revieews

    Route::resource('reviews', ReviewController::class);

  //video

    Route::get('/add-video', [VideoController::class, 'addVideo']);

    Route::post('save-video', [VideoController::class, 'saveVideo']);

  //report

    Route::get('/sales-report', [ReportController::class, 'salesReport']);
    Route::get('/finance-report', [ReportController::class, 'financeReport']);

  //orders

    Route::get('/orders', [OrderController::class, 'orders'])->name('my.orders');

    Route::delete('/delete-order/{id}', [OrderController::class, 'deleteOrder']);

    Route::get('/show-invoice/{id}', [OrderController::class, 'showInvoice']);

    Route::get('/print-invoice/{id}', [OrderController::class, 'printInvoice']);

    Route::get('/search-courier-order', [OrderController::class, 'searchCourierOrder']);


  
  //settings

    Route::get('/refer-settings', [ReferController::class, 'referSettings']);
    Route::post('settings-refer', [ReferController::class, 'settingsRefer']);
    Route::get('/info-settings', [InfoController::class, 'infoSettings']);
    Route::post('settings-info', [InfoController::class, 'settingsInfo']);
    Route::get('/meta-pixel-settings', [SettingController::class, 'metaPixelSettings']);
    Route::get('/set-delivery-charge', [SettingController::class, 'setDelveryCharge']);
    Route::get('/app-settings', [SettingController::class, 'appSettings']);
    Route::post('settings-app', [SettingController::class, 'settingApp']);
    Route::get('/password-change', [SettingController::class, 'passwordChange']);
    Route::post('change-password', [SettingController::class, 'changePassword']);
    
});