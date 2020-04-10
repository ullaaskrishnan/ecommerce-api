<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('products', 'ProductController');
Route::apiResource('carts', 'CartController')->except(['update', 'index']);
Route::post('/carts/{cart}', 'CartController@addProducts')->name('carts.addProducts');
Route::post('/carts/{cart}/checkout', 'CartController@checkout')->name('carts.checkout');

Route::prefix('user')->group(function(){
	Route::post('login', 'UserloginController@login');
    Route::post('signup', 'UserloginController@signup');
    Route::post('logout', 'UserloginController@logout');
    
});

Route::prefix('admin')->group(function(){
	Route::post('login', 'AdminloginController@login');
    Route::post('logout', 'AdminloginController@logout');
    Route::apiResource('orders', 'AdminController')->except(['update', 'destroy','store'])->middleware('auth:api_admin');
    
});

Route::apiResource('orders', 'OrderController')->except(['update', 'destroy','store'])->middleware('auth:api');