<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\ApiController;

Route::post('save-domain', [ApiController::class, 'saveDomain']);
Route::get('/packages', [ApiController::class, 'packages']);
Route::get('/domain-lists', [ApiController::class, 'domainLists']);
Route::post('domain-details', [ApiController::class, 'domainDetails']);
Route::post('search-domain', [ApiController::class, 'searchDomain']);
Route::post('add-temp-user', [ApiController::class, 'addTempUser']);


Route::post('sliders', [ApiController::class, 'sliders']);

Route::post('products', [ApiController::class, 'products']);

Route::post('reviews', [ApiController::class, 'reviews']);

Route::post('get-video', [ApiController::class, 'getVideo']);

Route::post('save-order', [ApiController::class, 'saveOrder']);

Route::post('accept-courier-order', [ApiController::class, 'acceptCourierOrder']);

Route::post('search-domain', [ApiController::class, 'searchDomain']);

Route::get('/packages', [ApiController::class, 'packages']);

Route::post('privacy-policy', [ApiController::class, 'privacyPolicy']);

Route::post('contact-us', [ApiController::class, 'contactUs']);

Route::post('about-us', [ApiController::class, 'aboutUs']);

Route::post('admin-info', [ApiController::class, 'adminInfo']);