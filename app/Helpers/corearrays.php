<?php
/**
 * Created by PhpStorm.
 * User: zahid
 * Date: 2018-07-31
 * Time: 2:24 PM
 */
if (!function_exists('maintenance_status')) {
    function maintenance_status($input = null) {
        $output = [
            UNDER_MAINTENANCE_MODE_ACTIVE => __('Enabled'),
            UNDER_MAINTENANCE_MODE_INACTIVE => __('Disabled')
        ];
        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('email_status')) {
    function email_status($input = null) {
        $output = [
            EMAIL_VERIFICATION_STATUS_ACTIVE => __('Verified'),
            EMAIL_VERIFICATION_STATUS_INACTIVE => __('Unverified')
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('financial_status')) {
    function financial_status($input = null) {
        $output = [
            FINANCIAL_STATUS_ACTIVE => __('Active'),
            FINANCIAL_STATUS_INACTIVE => __('Suspended')
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('maintenance_accessible_status')) {
    function maintenance_accessible_status($input = null) {
        $output = [
            UNDER_MAINTENANCE_ACCESS_ACTIVE => __('Active'),
            UNDER_MAINTENANCE_ACCESS_INACTIVE => __('Inactive')
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('account_status')) {
    function account_status($input = null) {
        $output = [
            ACCOUNT_STATUS_ACTIVE => __('Active'),
            ACCOUNT_STATUS_INACTIVE => __('Suspended'),
            ACCOUNT_STATUS_DELETED => __('Deleted')
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('active_status')) {
    function active_status($input = null) {
        $output = [
            ACTIVE_STATUS_ACTIVE => __('Active'),
            ACTIVE_STATUS_INACTIVE => __('Inactive'),
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('stock_item_types')) {
    function stock_item_types($input = null) {
        $output = [
            CURRENCY_REAL => __('Real Currency'),
            CURRENCY_CRYPTO => __('Crypto Currency'),
            CURRENCY_VIRTUAL => __('Virtual Currency'),
//            CURRENCY_COMPANY_SHARE => __('Company Share'),
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('crypto_currency_api_services')) {
    function crypto_currency_api_services($input = null) {
        $output = [
            API_COINPAYMENT => __('Coinpayments API'),
            API_BITCOIN => __('Bitcoin API'),
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('real_currency_api_services')) {
    function real_currency_api_services($input = null) {
        $output = [
            API_PAYPAL => __('PayPal Rest API'),
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('api_services')) {
    function api_services($input = null) {

        $output = real_currency_api_services() + crypto_currency_api_services();

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('api_classes')) {
    function api_classes($input = null) {
        $output = [
            API_PAYPAL => 'PaypalRestApi',
            API_COINPAYMENT => 'CoinPaymentApi',
            API_BITCOIN => 'BitcoinApi',
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('payment_status')) {
    function payment_status($input = null) {
        $output = [
            PAYMENT_COMPLETED => __('Completed'),
            PAYMENT_PENDING => __('Pending'),
            PAYMENT_FAILED => __('Failed'),
            PAYMENT_DECLINED => __('Declined'),
            PAYMENT_REVIEWING => __('Reviewing'),
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('api_permission')) {
    function api_permission($input = null) {
        $output = [
            ROUTE_REDIRECT_TO_UNAUTHORIZED => 'unauthorized',
            ROUTE_REDIRECT_TO_UNDER_MAINTENANCE => 'under_maintenance',
            ROUTE_REDIRECT_TO_EMAIL_UNVERIFIED => 'email_unverified',
            ROUTE_REDIRECT_TO_ACCOUNT_SUSPENDED => 'account_suspension',
            ROUTE_REDIRECT_TO_FINANCIAL_ACCOUNT_SUSPENDED => 'financial_suspension',
            REDIRECT_ROUTE_TO_USER_AFTER_LOGIN => 'login_success',
            REDIRECT_ROUTE_TO_LOGIN => 'login',
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('id_status')) {
    function id_status($input = null) {
        $output = [
            ID_STATUS_UNVERIFIED => __('Unverified'),
            ID_STATUS_PENDING => __('Pending'),
            ID_STATUS_VERIFIED => __('Verified'),
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('id_type')) {
    function id_type($input = null) {
        $output = [
            ID_PASSPORT => __('Passport'),
            ID_NID => __('NID'),
            ID_DRIVER_LICENSE => __('Driver License'),
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('exchange_type')) {
    function exchange_type($input = null) {
        $output = [
            EXCHANGE_BUY => __('Buy'),
            EXCHANGE_SELL => __('Sell'),
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('category_type')) {
    function category_type($input = null) {
        $output = [
            CATEGORY_EXCHANGE => __('Exchange'),
//            CATEGORY_MARGIN => __('Margin'),
//            CATEGORY_LENDING => __('Lending'),
            CATEGORY_ICO => __('ICO'),
        ];

        return is_null($input) ? $output : $output[$input];
    }
}

if (!function_exists('allowed_public_api_command')) {
    function allowed_public_api_command() {
        return ['returnTicker', 'returnChartData'];
    }
}

if (!function_exists('chart_data_interval')) {
    function chart_data_interval() {
        return $intervals = [5 => '5min', 15 => '15min', 30 => '30min', 120 => '2hr', 240 => '4hr', 1440 => '1day'];
    }
}