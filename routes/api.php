<?php

use Illuminate\Http\Request;

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


//for shop owners
Route::post('shop/register','ShopController@register');
Route::post('shop/login','ShopController@login');
Route::middleware('auth:owner_api')->post('shop','ShopController@store');
Route::middleware('auth:owner_api')->put('shop/{id}','ShopController@update');
Route::middleware('auth:owner_api')->delete('shop/{id}','ShopController@destroy');

// information of shop
Route::get('shop','ShopController@index');

//for consumers
Route::post('consumer/register','ConsumerController@register');
Route::post('consumer/login','ConsumerController@login');


Route::middleware('auth:api')->get('consumer','ConsumerController@index');
Route::middleware('auth:api')->post('consumer','ConsumerController@buy');

