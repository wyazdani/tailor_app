<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix'  =>  'v1'],function (){
    Route::post('login', 'Api\AuthController@login'); /*Login*/
    Route::post('register', 'Api\AuthController@register'); /*Register*/
    Route::post('reset-password', 'Api\ResetPasswordController@create'); /*Reset Password Email*/
    Route::post('reset-password-store', 'Api\ResetPasswordController@store'); /*Reset Password Store*/
});
Route::group(['prefix'  =>  'v1','middleware'=>['authApi']],function (){
    Route::post('users', 'Api\UserController@users'); /*Users Listing*/
    Route::post('order-store', 'Api\OrderController@store'); /*Order Store*/
    Route::post('orders', 'Api\OrderController@index'); /*Orders Listing*/
    Route::post('order-edit', 'Api\OrderController@edit'); /*Edit Order*/
    Route::post('order-edit-store', 'Api\OrderController@update'); /*Update Order*/
    Route::post('order-status-update', 'Api\OrderController@order_status'); /*Order Status Update */
    Route::post('order-completed', 'Api\OrderController@order_complete'); /*Order Status Complete */
    Route::post('my-sizes', 'Api\SizeController@index'); /*Sizes Listing*/
    Route::post('store-my-size', 'Api\SizeController@store'); /*Sizes Store*/
    Route::post('affiliates', 'Api\AffiliateController@index'); /*Affiliates Listing*/
});
