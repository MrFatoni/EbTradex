<?php

use Illuminate\Database\Seeder;

class SystemNoticesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Core\SystemNotice::class, 3)->create();
    }
}
