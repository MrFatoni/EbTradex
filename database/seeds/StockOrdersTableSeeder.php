<?php

use App\Models\User\StockOrder;
use Illuminate\Database\Seeder;

class StockOrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $order = [
            ['price' => 4617.14685006, 'amount' => 0.02600000, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4617.14685005, 'amount' => 0.01239353, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4617.04685024, 'amount' => 0.01240371, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4617.04241630, 'amount' => 1.13610000, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4617.04241629, 'amount' => 0.38676837, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4616.92289110, 'amount' => 3.80817000, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4615.71275693, 'amount' => 0.23460675, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4610.53098388, 'amount' => 0.02478706, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4632.30509000, 'amount' => 1.76224000, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4638.21990980, 'amount' => 0.30628038, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4638.21990982, 'amount' => 0.00204602, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4638.21990984, 'amount' => 0.00139906, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4638.25763942, 'amount' => 0.00312352, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4639.44387492, 'amount' => 0.00333828, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4621.15274598, 'amount' => 2.33255800, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4621.12878196, 'amount' => 2.00000000, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4619.39111398, 'amount' => 0.01239353, 'exchange_type'=> EXCHANGE_BUY, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],


            ['price' => 4609.48315766, 'amount' => 2.00000000, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4607.11963583, 'amount' => 0.38504210, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4606.39371000, 'amount' => 0.38635225, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4634.39339282, 'amount' => 0.26694333, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4638.21990981, 'amount' => 0.23800000, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4638.21990983, 'amount' => 0.00183033, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4638.21990985, 'amount' => 0.00096780, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4639.44387491, 'amount' => 0.00290756, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4621.21152000, 'amount' => 1.75766000, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4621.15274582, 'amount' => 0.40000000, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
            ['price' => 4620.81501471, 'amount' => 0.26759050, 'exchange_type'=> EXCHANGE_SELL, 'category'=>CATEGORY_EXCHANGE, 'stock_pair_id' =>1, 'user_id' => rand(1,4), 'maker_fee'=> 0.1, 'taker_fee'=> 0.2, 'created_at'=> date('Y-m-d H:i:s',rand(strtotime('yesterday'), strtotime('now'))), 'updated_at'=> date('Y-m-d H:i:s'),],
        ];

        StockOrder::insert($order);
    }
}
