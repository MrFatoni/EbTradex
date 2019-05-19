<?php

namespace App\Providers;

use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Services\Api\CoinPaymentApi;
use App\Services\Exchange\StockExchangeService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env("APP_PROTOCOL", 'http') == 'https') {
            URL::forceScheme('https');
        }

        Validator::extend('hash_check', function ($attribute, $value, $parameters) {
            return $value == null ? true : Hash::check($value, $parameters[0]);
        });

        Validator::extend('digits_only', function ($attribute, $value, $parameters) {
            return $value == null ? true : ctype_digit($value);
        });

        Validator::extend('alpha_space', function ($attribute, $value) {
            if ($value == null) {
                return true;
            }
            return is_string($value) && preg_match('/^[\pL\s]+$/u', $value);
        });

        if (function_exists('bcscale')) {
            bcscale(8);
        }

        $this->app->singleton(StockExchangeService::class, function ($app, $parameters) {
            $stockOrderRepository = $app->make(StockOrderInterface::class);
            return new StockExchangeService($parameters[0], $stockOrderRepository);
        });

        if( admin_settings('auto_withdrawal_process') == ACTIVE_STATUS_INACTIVE )
        {
            config([
                'commonconfig.payment_slug' => [
                    'completed' => PAYMENT_COMPLETED,
                    'pending' => PAYMENT_PENDING,
                    'failed' => PAYMENT_FAILED,
                    'reviewing' => PAYMENT_REVIEWING,
                    'declined' => PAYMENT_DECLINED,
                ]
            ]);
        }
        else
        {
            config([
                'commonconfig.payment_slug' => [
                    'completed' => PAYMENT_COMPLETED,
                    'pending' => PAYMENT_PENDING,
                    'failed' => PAYMENT_FAILED,
                ]
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
