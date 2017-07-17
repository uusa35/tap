<?php
/**
 * Created by PhpStorm.
 * User: usamaahmed
 * Date: 7/15/17
 * Time: 6:04 PM
 */

Route::group(['middleware' => 'web'], function () {
    Route::get('payment', 'Usama\Tap\TapPaymentController@makePayment');
    Route::get('product/add/{id}', 'Usama\Tap\TapPaymentController@addProduct');
    Route::get('product/remove/{id}', 'Usama\Tap\TapPaymentController@removeProduct');
    Route::get('product/clear', 'Usama\Tap\TapPaymentController@clearProducts');
});

