<?php

use App\Models\Backend\StockPair;
use Illuminate\Database\Seeder;

class StockPairTableSeeder extends Seeder
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
                'base_item_id' => 1,
                'stock_item_id' => 2,
                'last_price' => 0.01854998,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_ACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 1,
                'stock_item_id' => 3,
                'last_price' => 0.00000062,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 1,
                'stock_item_id' => 4,
                'last_price' => 0.00115798,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 1,
                'stock_item_id' => 5,
                'last_price' => 0.02605001,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 1,
                'stock_item_id' => 7,
                'last_price' => 0.00000069,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 1,
                'stock_item_id' => 8,
                'last_price' => 0.00003143,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 1,
                'stock_item_id' => 10,
                'last_price' => 0.00009008,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],


            [
                'base_item_id' => 9,
                'stock_item_id' => 1,
                'last_price' => 3309.99999999,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 9,
                'stock_item_id' => 2,
                'last_price' => 61.38080125,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 9,
                'stock_item_id' => 3,
                'last_price' => 0.00205700,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 9,
                'stock_item_id' => 4,
                'last_price' => 3.81841327,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 9,
                'stock_item_id' => 5,
                'last_price' => 86.31641001,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 9,
                'stock_item_id' => 7,
                'last_price' => 0.00226250,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 9,
                'stock_item_id' => 8,
                'last_price' => 0.10399180,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'base_item_id' => 9,
                'stock_item_id' => 10,
                'last_price' => 0.29880000,
                'exchange_24' => json_encode([]),
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'created_at' => $date,
                'updated_at' => $date,
            ]
        ];

        StockPair::insert($data);
    }
}
