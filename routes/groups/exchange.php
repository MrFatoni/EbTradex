<?php

Route::get('/{id}/get-24hr-pair-detail', 'ExchangeDashboardController@get24HrPairDetail')->name('exchange.get-24hr-pair-detail');
Route::get('get-coin-market', 'ExchangeDashboardController@getStockMarket')->name('exchange.get-stock-market');
Route::get('get-orders', 'ExchangeDashboardController@getOrders')->name('exchange.get-orders');
Route::get('get-chart-data', 'ExchangeDashboardController@getChartData')->name('exchange.get-chart-data');
Route::get('get-trade-histories', 'ExchangeDashboardController@getTradeHistories')->name('exchange.get-trade-histories');
Route::get('ico', 'IcoController@index')->name('exchange.ico.index');

Route::group(['middleware' => 'permission'], function () {
    Route::get('get-my-open-order', 'ExchangeDashboardController@getMyOpenOrders')->name('exchange.get-my-open-orders');
    Route::get('get-my-trade', 'ExchangeDashboardController@getMyTrade')->name('exchange.get-my-trade');
    Route::get('get-wallet-summary', 'ExchangeDashboardController@getWalletSummary')->name('exchange.get-wallet-summary');

    Route::get('ico/{id}/buy', 'IcoController@buy')->name('exchange.ico.buy');
    Route::post('ico/store', 'IcoController@store')->name('exchange.ico.store');
});

//This route must be at the bottom
Route::get('/{pair?}', 'ExchangeDashboardController@index')->name('exchange.index');
