<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserInfosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon\Carbon::now();
        $userInfos = [
            [
                'user_id' => 1,
                'first_name' => "Super",
                'last_name' => "Admin",
                'phone' => '01114548545',
                'is_id_verified' => 2,
                'id_type' => ID_NID,
                'id_card_front' => 'id-front.jpg',
                'id_card_back' => 'id-back.jpg',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'user_id' => 2,
                'first_name' => "Mr",
                'last_name' => "Trader",
                'phone' => '01114548545',
                'is_id_verified' => 2,
                'id_type' => ID_PASSPORT,
                'id_card_front' => 'passport.jpg',
                'id_card_back' => null,
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'user_id' => 3,
                'first_name' => "Mr",
                'last_name' => "Trader 2",
                'phone' => '01114548545',
                'is_id_verified' => 2,
                'id_type' => ID_NID,
                'id_card_front' => 'id-front.jpg',
                'id_card_back' => 'id-back.jpg',
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'user_id' => 4,
                'first_name' => "Mr",
                'last_name' => "Trade Analyst",
                'phone' => '01114548545',
                'is_id_verified' => 2,
                'id_type' => ID_DRIVER_LICENSE,
                'id_card_front' => 'id-front.jpg',
                'id_card_back' => 'id-back.jpg',
                'created_at' => $date,
                'updated_at' => $date
            ],
        ];

        DB::table('user_infos')->insert($userInfos);
    }
}
