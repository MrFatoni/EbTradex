<?php
Route::get('/', 'Guest\HomeController')->name('home');
Route::post('google-2fa/verify', 'User\Google2faController@verify')->name('profile.google-2fa.verify');

Route::any('wallets/deposit/paypal/response/return-url', 'User\Trader\WalletController@completePayment')->name('frontend.wallets.deposit.paypal.return-url');
Route::any('wallets/deposit/paypal/response/cancel-url', 'User\Trader\WalletController@cancelPayment')->name('frontend.wallets.deposit.paypal.cancel-url');

Route::group(['namespace' => 'TradingView'], function () {
    Route::get('faq', 'FaqController@index')->name('faq.index');
    Route::get('faq/{id}', 'FaqController@show')->name('faq.show');
    Route::get('trading-views', 'TradingViewController@index')->name('trading-views.index');
    Route::get('trading-views/{id}', 'TradingViewController@show')->name('trading-views.show');
    Route::post('trading-views/{id}/comment', 'TradingViewController@comment')->name('trading-views.comment');
});

//Test Route
Route::any('test', 'TestController@test')->name('test');