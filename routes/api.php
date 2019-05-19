<?php

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

Route::any('/ipn', 'Api\IpnController@ipn');
Route::any('/bitcoin/ipn/{currency}', 'Api\IpnController@bitcoinIpn');
Route::get('/public', 'Api\PublicApiController@command');