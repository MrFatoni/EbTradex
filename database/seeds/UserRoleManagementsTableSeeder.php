<?php

use App\Models\Core\UserRoleManagement;
use Illuminate\Database\Seeder;

class UserRoleManagementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $date = \Carbon\Carbon::now();

        $inputs = [
            [
                'role_name' => 'Super Admin',
                'route_group' => '{"application_managements":{"admin_settings":["reader_access","modifier_access"],"dashboard":["reader_access"],"log_viewer":["reader_access"],"audits":["reader_access"],"system_notice":["reader_access","creation_access","modifier_access","deletion_access"],"id_management":["reader_access","modifier_access"],"stock_management":["reader_access","creation_access","modifier_access","deletion_access"],"stock_pair_management":["reader_access","creation_access","modifier_access","deletion_access"],"review_withdrawals":["reader_access","modifier_access"],"transaction_reports":["reader_access"],"menu_manager":["full_access"]},"user_managements":{"users":["reader_access","creation_access","modifier_access","deletion_access"],"role_managements":["reader_access","creation_access","modifier_access","deletion_access"]},"trade_analyst":{"posts":["reader_access","creation_access","modifier_access","deletion_access"],"questions":["reader_access","answer_access","delete_access"]}}',
                'created_at' => $date,
                'updated_at' => $date

            ],
            [
                'role_name' => 'Trader',
                'route_group' => '{"trader":{"orders":["reader_access","creation_access","deletion_access"],"wallets":["reader_access","deposit_access","withdrawal_access"],"referral":["reader_access","creation_access"],"questions":["reader_access","creation_access"]}}',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'role_name' => 'Trade analyst',
                'route_group' => '{"trade_analyst":{"posts":["reader_access","creation_access","modifier_access","deletion_access"],"questions":["reader_access","answer_access"]},"trader":{"orders":["reader_access","creation_access","deletion_access"],"wallets":["reader_access","deposit_access","withdrawal_access"],"referral":["reader_access","creation_access"],"questions":["reader_access","creation_access"]}}',
                'created_at' => $date,
                'updated_at' => $date
            ]
        ];


        UserRoleManagement::insert($inputs);

        foreach ($inputs as $key => $input) {

            cache()->forever("userRoleManagement" . ($key + 1), json_decode($input['route_group'], true));
        }
    }
}
