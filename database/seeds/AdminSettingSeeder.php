<?php

use App\Models\Core\AdminSetting;
use Illuminate\Database\Seeder;

class AdminSettingSeeder extends Seeder
{
    /**
     * @developer: M.G. Rabbi
     * @date: 2018-08-11 12:25 PM
     * @description:
     * @throws Exception
     */
    public function run()
    {
        $date_time = date('Y-m-d H:i:s');
        $adminSettingArray = [
            'lang' => 'en',
            'registration_active_status' => ACTIVE_STATUS_ACTIVE,
            'default_role_to_register' => 2,
            'signupable_user_roles' => [2],
            'require_email_verification' => ACTIVE_STATUS_ACTIVE,
            'company_name' => 'Cryptomania',
            'company_logo' => 'cryptomania.png',
            'auto_withdrawal_process' => ACTIVE_STATUS_ACTIVE,
            'referral' => ACTIVE_STATUS_INACTIVE,
            'referral_percentage' => 0,
            'item_per_page' => 10,
            'maintenance_mode' => 0,
            'exchange_maker_fee' => 0.1,
            'exchange_taker_fee' => 0.2,
            'ico_fee' => 0.1,
            'min_ico_amount_buy' => 1,
            'display_google_captcha' => ACTIVE_STATUS_INACTIVE,
            'admin_receive_email' => 'support@codemen.org'
        ];

        $jsonFields = ['signupable_user_roles'];

        $adminSetting = [];
        foreach ($adminSettingArray as $key => $value) {
            $adminSetting[] = [
                'slug' => $key,
                'value' => in_array($key, $jsonFields) ? json_encode($value, true) : $value,
                'created_at' => $date_time,
                'updated_at' => $date_time
            ];
        }
        AdminSetting::insert($adminSetting);

        cache()->forever("admin_settings", $adminSettingArray);
    }
}
