<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Api\BitcoinApi;
use App\Services\Api\CoinPaymentApi;
use App\Services\User\Trader\WalletService;
use Illuminate\Http\Request;

class IpnController extends Controller
{
    public function ipn(Request $request)
    {
        $ipnRequest = $request->all();

        if(env('APP_ENV') != 'production' && env('APP_DEBUG') == true) {
            logs()->info($ipnRequest);
        }

        if( empty($ipnRequest) || !isset($ipnRequest['currency']) )
        {
            logs()->error('log: Invalid coinpayment IPN request.');

            return null;
        }

        $coinpayment = new CoinPaymentApi($ipnRequest['currency']);
        $ipnResponse = $coinpayment->validateIPN($ipnRequest, $request->server());

        if( $ipnResponse['error'] == 'ok')
        {
            app(WalletService::class)->updateTransaction($ipnResponse);

            return null;
        }
        else
        {
            logs()->error($ipnResponse['error']);

            return null;
        }
    }

    public function bitcoinIpn(Request $request, $currency)
    {
        try {
            $bitcoin = new BitcoinApi($currency);
            $ipnResponse = $bitcoin->validateIPN($request->all(), $request->server());

            if( $ipnResponse['error'] == 'ok')
            {
                app(WalletService::class)->updateTransaction($ipnResponse);
            }
            else{
                logs()->error($ipnResponse['error']);

                return null;
            }
        }
        catch (\Exception $exception)
        {
            logs()->error( $exception->getMessage() );

            return null;
        }
    }
}