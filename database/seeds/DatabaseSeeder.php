<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(AdminSettingSeeder::class);
        $this->call(UserRoleManagementsTableSeeder::class);
        $this->call(SystemNoticesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(UserSettingsTableSeeder::class);
        $this->call(UserInfosTableSeeder::class);
        $this->call(NavigationTableSeeder::class);
        $this->call(StockItemTableSeeder::class);
        $this->call(StockPairTableSeeder::class);
//        $this->call(StockOrdersTableSeeder::class);
        $this->call(WalletTableSeeder::class);
        $this->call(PostsTableSeeder::class);
        $this->call(QuestionsTableSeeder::class);
    }
}
