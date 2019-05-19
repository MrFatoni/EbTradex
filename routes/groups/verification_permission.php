<?php
Route::get('verification','Core\VerificationController@verify')->name('account.verification');
Route::get('verification/email','Core\VerificationController@resendForm')->name('verification.form');
Route::post('verification/email','Core\VerificationController@send')->name('verification.send');
