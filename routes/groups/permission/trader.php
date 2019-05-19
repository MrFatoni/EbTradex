<?php
Route::group(['namespace' => 'User\Trader'], function () {
    Route::resource('upload-id', 'IdController')->only(['index', 'store'])->parameter('upload-id', 'id')->names('trader.upload-id');

    Route::get('wallets/{id}/deposit', 'WalletController@createDeposit')->name('trader.wallets.deposit');
    Route::post('wallets/{id}/deposit/store', 'WalletController@storeDeposit')->name('trader.wallets.deposit.store');
    Route::get('wallets/{id}/withdrawal', 'WalletController@createWithdrawal')->name('trader.wallets.withdrawal');
    Route::post('wallets/{id}/withdrawal/store', 'WalletController@storeWithdrawal')->name('trader.wallets.withdrawal.store');
    Route::resource('wallets', 'WalletController')->only(['index'])->parameter('wallets', 'id')->names('trader.wallets');

    Route::resource('orders', 'OrdersController')->only(['store', 'destroy'])->parameter('orders', 'id')->names('trader.orders');

    Route::get('my-open-orders', 'OrdersController@openOrders')->name('trader.orders.open-orders');
    Route::resource('questions', 'QuestionsController')->only(['index', 'create', 'store'])->parameter('questions', 'id')->names('trader.questions');
});