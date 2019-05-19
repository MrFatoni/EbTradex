<?php
Route::group(['namespace' => 'User\Admin'], function () {

    Route::get('/dashboard', 'DashboardController')->name('dashboard');

    Route::get('users/{id}/wallets', 'UsersController@wallets')->name('admin.users.wallets');

    Route::put('coins/{id}/toggle-status', 'StockItemController@toggleActiveStatus')->name('admin.stock-items.toggle-status');

    Route::resource('coins', 'StockItemController')->parameter('coins', 'id')->names('admin.stock-items');

    Route::put('coins-pairs/{id}/toggle-status', 'StockPairController@toggleActiveStatus')->name('admin.stock-pairs.toggle-status');

    Route::put('coins-pairs/{id}/make-status-default', 'StockPairController@makeStatusDefault')->name('admin.stock-pairs.make-status-default');

    Route::resource('coins-pairs', 'StockPairController')->parameter('coins-pairs', 'id')->names('admin.stock-pairs');

    Route::get('review-withdrawals', 'WithdrawalController@index')->name('admin.review-withdrawals.index');
    Route::get('review-withdrawals/{id}/show', 'WithdrawalController@show')->name('admin.review-withdrawals.show');
    Route::put('review-withdrawals/{id}/approve', 'WithdrawalController@approve')->name('admin.review-withdrawals.approve');
    Route::put('review-withdrawals/{id}/decline', 'WithdrawalController@decline')->name('admin.review-withdrawals.decline');

    Route::put('id-management/{id}/approve', 'IdManagementController@approve')->name('admin.id-management.approve');
    Route::put('id-management/{id}/decline', 'IdManagementController@decline')->name('admin.id-management.decline');
    Route::resource('id-management', 'IdManagementController')->only(['index', 'show'])->parameter('id-management', 'id')->names('admin.id-management');
});
