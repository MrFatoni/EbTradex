<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = \Carbon\Carbon::now();

        $userSettings = [
            [
                'user_id' => 1,
                'language' => config('app.locale'),
                'timezone' => config('app.timezone'),
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'user_id' => 2,
                'language' => config('app.locale'),
                'timezone' => config('app.timezone'),
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'user_id' => 3,
                'language' => config('app.locale'),
                'timezone' => config('app.timezone'),
                'created_at' => $date,
                'updated_at' => $date
            ],
            [
                'user_id' => 4,
                'language' => config('app.locale'),
                'timezone' => config('app.timezone'),
                'created_at' => $date,
                'updated_at' => $date
            ],
        ];

        DB::table('user_settings')->insert($userSettings);
    }
}
