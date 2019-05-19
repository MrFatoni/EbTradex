<?php
require_once('admin.php');
require_once('trader.php');
require_once('trade_analyst.php');

Route::get('profile/google-2fa', 'User\Google2faController@create')->name('profile.google-2fa.create');
Route::put('profile/google-2fa/{googleCode}/store', 'User\Google2faController@store')->name('profile.google-2fa.store');
Route::put('profile/google-2fa/destroy', 'User\Google2faController@destroy')->name('profile.google-2fa.destroy');

Route::get('profile/referral', 'User\ProfileController@referral')->name('profile.referral');
Route::get('profile/generate-referral-link', 'User\ProfileController@generateReferralLink')->name('profile.referral.generate');