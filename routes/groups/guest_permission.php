<?php
/**
 * Created by PhpStorm.
 * User: zahid
 * Date: 2018-07-29
 * Time: 9:13 PM
 */

Route::get('login','Guest\AuthController@loginForm')->name('login');
Route::post('login','Guest\AuthController@login')->name('login');
Route::get('register','Guest\AuthController@register')->name('register.index')->middleware('registration.permission');
Route::post('register/store','Guest\AuthController@storeUser')->name('register.store')->middleware('registration.permission');
Route::get('forget-password','Guest\AuthController@forgetPassword')->name('forget-password.index');
Route::post('forget-password/send-mail','Guest\AuthController@sendPasswordResetMail')->name('forget-password.send-mail');
Route::get('reset-password/{id}','Guest\AuthController@resetPassword')->name('reset-password.index');
Route::post('reset-password/{id}/update','Guest\AuthController@updatePassword')->name('reset-password.update');