<?php

use App\Models\Backend\StockItem;
use Illuminate\Database\Seeder;

class StockItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = now();

        $data = [
            [
                'item' => 'BTC',
                'item_name' => 'Bitcoin',
                'item_type' => CURRENCY_CRYPTO,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'item' => 'DASH',
                'item_name' => 'Dash',
                'item_type' => CURRENCY_CRYPTO,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'item' => 'DOGE',
                'item_name' => 'Doge',
                'item_type' => CURRENCY_CRYPTO,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'item' => 'ETC',
                'item_name' => 'Ethereum Classic',
                'item_type' => CURRENCY_CRYPTO,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'item' => 'ETH',
                'item_name' => 'Ethereum',
                'item_type' => CURRENCY_CRYPTO,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'item' => 'LTCT',
                'item_name' => 'Litecoin Test',
                'item_type' => CURRENCY_CRYPTO,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'item' => 'SC',
                'item_name' => 'Siacoin',
                'item_type' => CURRENCY_CRYPTO,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'item' => 'STR',
                'item_name' => 'Stellar',
                'item_type' => CURRENCY_CRYPTO,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'item' => 'USD',
                'item_name' => 'United States Dollar',
                'item_type' => CURRENCY_REAL,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'item' => 'XRP',
                'item_name' => 'Ripple',
                'item_type' => CURRENCY_CRYPTO,
                'created_at' => $date,
                'updated_at' => $date,
            ],
        ];

        StockItem::insert($data);
    }
}
