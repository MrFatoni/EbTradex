<?php

use App\Models\User\User;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use Illuminate\Database\Seeder;

class WalletTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activeStockItems = app(StockItemInterface::class)->getActiveList()->pluck('id');
        $walletParameters = [];
        $date = date('Y-m-d h:i:s');

        $users = User::all();

        foreach($users as $user) {
            foreach($activeStockItems as $stockItemID) {
                $walletParameters[] = [
                    'user_id' => $user->id,
                    'stock_item_id' => $stockItemID,
                    'primary_balance' => 100000,
                    'created_at' => $date,
                    'updated_at' => $date,
                ];
            }
        }

        app(WalletInterface::class)->insert($walletParameters);
    }
}
