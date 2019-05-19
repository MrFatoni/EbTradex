<?php

use App\Repositories\Core\Interfaces\AdminSettingInterface;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Services\Core\NavService;
use App\Services\User\ProfileService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

if (!function_exists('company_name')) {
    function company_name()
    {
        $companyName = admin_settings('company_name');
        return isset($companyName) && !empty($companyName) ? $companyName : env('APP_NAME');
    }
}

if (!function_exists('random_string')) {
    function random_string($length = 10, $characters = null)
    {
        if ($characters == null) {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        }
        $output = '';
        for ($i = 0; $i < $length; $i++) {
            $y = rand(0, strlen($characters) - 1);
            $output .= substr($characters, $y, 1);
        }
        return $output;
    }
}

if (!function_exists('admin_settings')) {

    function admin_settings($admin_setting_field = null, $database = false)
    {
        if ($database) {
            if (is_null($admin_setting_field)) {
                $adminSettings = app(AdminSettingInterface::class)->getAll();
                $adminSettings = $adminSettings->pluck('value', 'slug')->toArray();
                foreach ($adminSettings as $key => $val) {
                    if (is_json($val)) {
                        $arrayConfig[$key] = json_decode($val, true);
                    } else {
                        $arrayConfig[$key] = $val;
                    }
                }
            } else {
                if (is_array($admin_setting_field) && count($admin_setting_field) > 0) {
                    $arrayConfig = app(AdminSettingInterface::class)->getBySlugs($admin_setting_field);
                } else {
                    $arrayConfig = app(AdminSettingInterface::class)->getBySlug($admin_setting_field);
                }

            }
            return $arrayConfig;
        }

        $arrayConfig = cache()->get('admin_settings');

        if (is_array($arrayConfig)) {
            if ($admin_setting_field != null) {
                if (is_array($admin_setting_field) && count($admin_setting_field) > 0) {
                    return array_intersect_key($arrayConfig, array_flip($admin_setting_field));
                } elseif (!is_array($admin_setting_field) && isset($arrayConfig[$admin_setting_field])) {
                    return $arrayConfig[$admin_setting_field];
                } else {
                    return null;
                }
            } else {
                return $arrayConfig;
            }
        }
        return false;
    }
}

if (!function_exists('check_language')) {
    function check_language($language)
    {
        $languages = language();
        if (array_key_exists($language, $languages) == true) {
            return $language;
        }
        return null;
    }
}

if (!function_exists('set_language')) {
    function set_language($language, $default = null)
    {
        $languages = language();
        if (!array_key_exists($language, $languages)) {
            if (isset($_COOKIE['lang']) && check_language($_COOKIE['lang']) != null && array_key_exists($_COOKIE['lang'], $languages)) {
                $language = $_COOKIE['lang'];
            } else {
                if ($default != null && array_key_exists($default, $languages)) {
                    $language = $default;
                } else {
                    $language = admin_settings('lang');
                }
                if ($language != false && array_key_exists($language, $languages)) {
                    setcookie("lang", $language, time() + (86400 * 30), '/');
                } else {
                    $language = LANGUAGE_DEFAULT;
                }
            }
        }
        App()->setlocale($language);
    }
}

if (!function_exists('language')) {
    function language($language = null, $langtype = "json")
    {//json
        $directories = array();
        $path = base_path('resources/lang');
        if ($langtype == 'json') {
            $path .= '/';
            $initial = opendir($path);
            if ($dh = $initial) {
                while (($file = readdir($dh)) !== false) {
                    if (strlen($file) == 7 && substr($file, 2) == '.json') {
                        $ab = substr($file, 0, 2);
                        $directories[$ab] = $ab;
                    }
                }
                closedir($dh);
            }
        } else {
            $initial = scandir($path);
            foreach ($initial as $init) {
                if ($init != '.' && $init != '..' && strlen($init) == 2 && strpos($init, '.') !== true) {
                    $directories[$init] = $init;
                }
            }
        }
        if ($language == null) {
            return $directories;
        } else {
            return $directories[$language];
        }
    }
}


if (!function_exists('fake_field')) {
    function fake_field($fieldname, $reverse = false)
    {
        if ($reverse === true) {
            return array_flip(config('fakefields.table_keys'))[$fieldname];
        }
        return config()->get('fakefields.table_keys.' . $fieldname, $fieldname);
    }
}

if (!function_exists('base_key')) {
    function base_key()
    {
        return encode_decode(config('fakefields.base_key'));
    }
}

if (!function_exists('encode_decode')) {
    function encode_decode($data, $decryption = false)
    {
        $code = ['x', 'f', 'z', 's', 'b', 'h', 'n', 'a', 'c', 'm'];
        if ($decryption == true) {
            $code = array_flip($code);
        }
        $output = '';
        $length = strlen($data);
        try {
            for ($i = 0; $i < $length; $i++) {
                $y = substr($data, $i, 1);
                $output .= $code[$y];
            }
        } catch (\Exception $e) {
            $output = null;
        }
        return $output;
    }
}

if (!function_exists('validate_date')) {
    function validate_date($date, $seperator = '-')
    {
        $datepart = explode($seperator, $date);
        return strlen($date) == 10 && count($datepart) == 3 && strlen($datepart[0]) == 4 && strlen($datepart[1]) == 2 && strlen($datepart[2]) == 2 && ctype_digit($datepart[0]) && ctype_digit($datepart[1]) && ctype_digit($datepart[2]) && checkdate($datepart[1], $datepart[2], $datepart[0]);
    }
}

if (!function_exists('has_permission')) {
    function has_permission($routeName, $userId = null, $is_link = true, $is_api = false)
    {
        $configPath = $is_api ? 'permissionApiRoutes' : 'permissionRoutes';
        $isAccessible = $is_link ? false : ROUTE_REDIRECT_TO_UNAUTHORIZED;
        if (is_null($userId)) {
            $user = auth()->user();
        } else {
            $user = app(UserInterface::class)->getFirstById($userId);
        }
        if (empty($user)) {
            config()->set($configPath . '.all_accessible_routes', []);
            return $isAccessible;
        }
        $allAccessibleRoutes = config($configPath . '.all_accessible_routes');
        $routeConfig = config($configPath);
        if (is_null($allAccessibleRoutes)) {
            $allAccessibleRoutes = [];
            $permissionGroups = cache()->get("userRoleManagement{$user->user_role_management_id}");
            if (is_null($permissionGroups)) {
                $permissionGroups = app(UserRoleManagementInterface::class)->getFirstById($user->user_role_management_id)->route_group;
                cache()->forever("userRoleManagement{$user->user_role_management_id}", $permissionGroups);
            }
            if (empty($permissionGroups)) {
                config()->set($configPath . '.all_accessible_routes', config($configPath . '.private_routes'));
            } else {
                foreach ($permissionGroups as $permissionGroupName => $permissionGroup) {
                    foreach ($permissionGroup as $groupName => $groupAccessName) {
                        foreach ($groupAccessName as $accessName) {
                            try {
                                $routes = $routeConfig["configurable_routes"][$permissionGroupName][$groupName][$accessName];
                                if (in_array($routeName, $routes)) {
                                    $isAccessible = true;
                                }
                                $allAccessibleRoutes = array_merge($allAccessibleRoutes, $routes);
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
                $allAccessibleRoutes = array_merge($allAccessibleRoutes, $routeConfig['private_routes']);
                config()->set($configPath . '.all_accessible_routes', $allAccessibleRoutes);
            }
        }
        if (admin_settings('maintenance_mode') == UNDER_MAINTENANCE_MODE_ACTIVE && $user->is_accessible_under_maintenance != UNDER_MAINTENANCE_ACCESS_ACTIVE) {
            if (
                !empty($allAccessibleRoutes) && in_array($routeName, $allAccessibleRoutes) &&
                in_array($routeName, $routeConfig['avoidable_maintenance_routes'])
            ) {
                $isAccessible = true;
            } else {
                $isAccessible = $is_link ? false : ROUTE_REDIRECT_TO_UNDER_MAINTENANCE;
            }
        } elseif (in_array($routeName, $routeConfig['private_routes'])) {
            $isAccessible = true;
        } else if (!empty($allAccessibleRoutes) && in_array($routeName, $allAccessibleRoutes)) {
            if (in_array($routeName, $routeConfig['avoidable_unverified_routes'])) {
                $isAccessible = true;
            } elseif (in_array($routeName, $routeConfig['avoidable_suspended_routes'])) {
                $isAccessible = true;
            } elseif (in_array($routeName, $routeConfig['financial_routes'])) {
                if ($user->is_financial_active == FINANCIAL_STATUS_ACTIVE) {
                    $isAccessible = true;
                } else {
                    $isAccessible = $is_link ? false : ROUTE_REDIRECT_TO_FINANCIAL_ACCOUNT_SUSPENDED;
                }
            } elseif (
                (
                    $user->is_email_verified == EMAIL_VERIFICATION_STATUS_ACTIVE ||
                    admin_settings('require_email_verification') == ACTIVE_STATUS_INACTIVE
                ) && $user->is_active == ACCOUNT_STATUS_ACTIVE
            ) {
                $isAccessible = true;
            } else {
                if ($user->is_email_verified != EMAIL_VERIFICATION_STATUS_ACTIVE &&
                    admin_settings('require_email_verification') == ACTIVE_STATUS_ACTIVE) {
                    $isAccessible = $is_link ? false : ROUTE_REDIRECT_TO_EMAIL_UNVERIFIED;
                } elseif ($user->is_active != ACCOUNT_STATUS_ACTIVE) {
                    $isAccessible = $is_link ? false : ROUTE_REDIRECT_TO_ACCOUNT_SUSPENDED;
                }
            }
        }
        return $isAccessible;
    }

}

if (!function_exists('is_json')) {
    function is_json($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}

if (!function_exists('is_current_route')) {
    function is_current_route($route_name, $active_class_name = 'active', $must_have_route_parameters = null, $optional_route_parameters = null)
    {
        if (!is_array($route_name)) {
            $is_selected = \Route::currentRouteName() == $route_name;
        } else {
            $is_selected = in_array(\Route::currentRouteName(), $route_name);
        }
        if ($is_selected) {
            if ($optional_route_parameters) {
                if (is_array($must_have_route_parameters)) {
                    $is_selected = available_in_parameters($must_have_route_parameters);
                }
                if (is_array($optional_route_parameters)) {
                    $is_selected = available_in_parameters($optional_route_parameters, true);
                }
            } elseif (is_array($must_have_route_parameters)) {
                $is_selected = available_in_parameters($must_have_route_parameters);
            }
        }
        return $is_selected == true ? $active_class_name : '';
    }

    function available_in_parameters($route_parameter, $optional = false)
    {
        $is_selected = true;
        foreach ($route_parameter as $key => $val) {
            if (is_array($val)) {
                $current_route_parameter = \Request::route()->parameter($val[0]);
                if ($val[1] == '<') {
                    $is_selected = $current_route_parameter < $val[2];
                } elseif ($val[1] == '<=') {
                    $is_selected = $current_route_parameter <= $val[2];
                } elseif ($val[1] == '>') {
                    $is_selected = $current_route_parameter > $val[2];
                } elseif ($val[1] == '>=') {
                    $is_selected = $current_route_parameter >= $val[2];
                } elseif ($val[1] == '!=') {
                    $is_selected = $current_route_parameter != $val[2];
                } else {
                    $param = isset($val[2]) ? $val[2] : $val[1];
                    $is_selected = $current_route_parameter == $param;
                }
            } else {
                $current_route_parameter = \Request::route()->parameter($key);
                if ($optional && $current_route_parameter !== 0 && empty($current_route_parameter)) {
                    continue;
                }
                $is_selected = $current_route_parameter == $val;
            }
            if ($is_selected == false) {
                break;
            }
        }
        return $is_selected;
    }
}

if (!function_exists('return_get')) {
    function return_get($key, $val = '')
    {
        $output = '';
        if ( isset($_GET[$key]) && $_GET[$key] === (string)$val && $val !== '' ){
            $output = ' selected';
        } elseif (isset($_GET[$key]) && $val == '') {
            $output = $_GET[$key];
        }
        return $output;
    }
}

if (!function_exists('array_to_string')) {
    function array_to_string($array, $separator = ',', $key = true, $is_seperator_at_ends = false)
    {
        if ($key == true) {
            $output = implode($separator, array_keys($array));
        } else {
            $output = implode($separator, array_values($array));
        }
        return $is_seperator_at_ends ? $separator . $output . $separator : $output;
    }
}

if (!function_exists('valid_image')) {
    function valid_image($imagePath, $image)
    {
        $extension = explode('.', $image);
        $isExtensionAvailable = in_array(end($extension), config('commonconfig.image_extensions'));

        return $isExtensionAvailable && file_exists(public_path($imagePath . $image));
    }
}

if (!function_exists('get_avatar')) {
    function get_avatar($avatar)
    {
        $avatarPath = 'storage/' . config('commonconfig.path_profile_image');

        $avatar = valid_image($avatarPath, $avatar) ? $avatarPath . $avatar : $avatarPath . 'avatar.jpg';

        return asset($avatar);
    }
}

if (!function_exists('get_item_emoji')) {
    function get_item_emoji($image)
    {
        $emojiPath = 'storage/' . config('commonconfig.path_stock_item_emoji');

        if (valid_image($emojiPath, $image)) {
            return asset($emojiPath . $image);
        }

        return null;
    }
}

if (!function_exists('get_id_image')) {
    function get_id_image($image)
    {
        $idCardPath = 'storage/' . config('commonconfig.path_id_image');
        if (valid_image($idCardPath, $image)) {
            return asset($idCardPath . $image);
        }

        return null;
    }
}

if (!function_exists('get_image')) {
    function get_image($image)
    {
        $imagePath = 'storage/' . config('commonconfig.path_image');
        if (valid_image($imagePath, $image)) {
            return asset($imagePath . $image);
        }

        return null;
    }
}

if (!function_exists('get_post_image')) {
    function get_post_image($image)
    {
        $postImage = 'storage/' . config('commonconfig.path_post');

        if (valid_image($postImage, $image)) {
            return asset($postImage . $image);
        }

        return null;
    }
}

if (!function_exists('get_user_specific_notice')) {

    function get_user_specific_notice($userId = null)
    {
        if (is_null($userId)) {
            $userId = Auth::id();
        }

        $notificationRepository = app(NotificationInterface::class);
        return [
            'list' => $notificationRepository->getLastFive($userId),
            'count_unread' => $notificationRepository->countUnread($userId)
        ];
    }
}

if (!function_exists('get_nav')) {
    function get_nav($slug, $template = 'default_nav')
    {
        return app(NavService::class)->navigationSingle($slug, $template);
    }
}

if (!function_exists('get_breadcrumbs')) {
    function get_breadcrumbs()
    {
        $routeList = Route::getRoutes()->getRoutesByMethod()['GET'];
        $baseUrl = url('/');
        $segments = Request::segments();
        $routeUries = explode('/', Route::current()->uri());
        $breadcrumbs = [];

        foreach ($segments as $key => $segment) {

            $displayUrl = true;
            $lastBreadcrumb = end($breadcrumbs);
            if (empty($lastBreadcrumb)) {
                $url = $baseUrl . '/' . $segment;
            } else {
                $url = $lastBreadcrumb['url'] . '/' . $segment;

            }

            if (!array_key_exists(implode('/', array_slice($routeUries, 0, $key + 1)), $routeList)) {
                $displayUrl = false;
            }
            $breadcrumbs[] = [
                'name' => title_case(str_replace('-', ' ', $segment)),
                'url' => $url,
                'display_url' => $displayUrl
            ];

        }
        return $breadcrumbs;
    }
}

if (!function_exists('get_system_notices')) {
    function get_system_notices()
    {
        $systemNoticeInterface = app(\App\Repositories\Core\Interfaces\SystemNoticeInterface::class);
        $date = Carbon::now();
        $totalMinutes = $date->diffInMinutes($date->copy()->endOfDay());

        if (Cache::has('systemNotices')) {
            $systemNotices = Cache::get('systemNotices');
        } else {
            $systemNotices = $systemNoticeInterface->todaysNotifications();
            Cache::put('systemNotices', $systemNotices, $totalMinutes);
        }

        if ($systemNotices->isEmpty()) {
            return $systemNotices;
        }

        $systemNoticeIds = $systemNotices->pluck('updated_at', 'id')->toArray();
        if (session()->has('seenSystemNotices')) {
            $seenSystemNotices = session()->get('seenSystemNotices');
            $systemNotices = $systemNotices->filter(function ($systemNotice, $key) use ($seenSystemNotices) {
                $date = now();
                if (!($systemNotice->start_at <= $date && $systemNotice->end_at >= $date)) {
                    return false;
                }


                if (array_key_exists($systemNotice->id, $seenSystemNotices) && $systemNotice->updated_at->eq($seenSystemNotices[$systemNotice->id])) {
                    return false;
                }
                return $systemNotice;
            });
        }
//        dd($systemNotices);

        session()->put('seenSystemNotices', $systemNoticeIds);
        return $systemNotices;
    }
}


if (!function_exists('get_available_timezones')) {
    function get_available_timezones()
    {
        return [
            'UTC' => __('Default'),
            'BST' => __('Bangladesh Standard Time'),
        ];
    }
}

if (!function_exists('get_minimum_order_total')) {
    function get_minimum_order_total($minimumFee = MINIMUM_TRANSACTION_FEE_CRYPTO)
    {
        $adminSettings = admin_settings(['exchange_maker_fee', 'exchange_taker_fee']);
        $comparison = bccomp($adminSettings['exchange_taker_fee'], $adminSettings['exchange_maker_fee']) > 0;
        $feeInPercentage = $comparison ? $adminSettings['exchange_maker_fee'] : $adminSettings['exchange_taker_fee'];

        if(bccomp('0', $feeInPercentage) >= 0){
            return $minimumFee;
        }
        return bcdiv(bcmul('100', $minimumFee), $feeInPercentage);
    }
}

if (!function_exists('get_transaction_type')) {
    function get_transaction_type($key = null)
    {
        $transactionType = [
            TRANSACTION_TYPE_DEBIT => __('Debit'),
            TRANSACTION_TYPE_CREDIT => __('Credit')
        ];

        return is_null($key) ? $transactionType : $transactionType[$key];
    }
}

if (!function_exists('calculate_exchange_fee')) {
    function calculate_exchange_fee($amount, $feeInPercentage)
    {
        return bcdiv(bcmul($amount, $feeInPercentage), '100');
    }
}

if (!function_exists('profileRoutes')) {
    function profileRoutes($identifier, $userId)
    {
        $userService = app(ProfileService::class);
        if ($identifier == 'admin') {
            return $userService->routesForAdmin($userId);
        } else {
            return $userService->routesForUser($userId);
        }
    }
}

if (!function_exists('social_media_link')) {
    function social_media_link($socialMediaName)
    {
        $fieldName = str_replace('_', '-', $socialMediaName);
        if (!empty(admin_settings($socialMediaName . '_link'))) {
            return '<li><a href="' . admin_settings($socialMediaName . '_link') . '"><i class="fa fa-' . strtolower($fieldName) . '-square font-20"></i></a></li>';
        }
    }
}

if (!function_exists('channel_prefix')) {
    function channel_prefix()
    {
        return str_slug(env('APP_NAME', 'laravel'), '_').'_';
    }
}
