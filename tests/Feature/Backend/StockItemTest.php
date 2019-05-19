<?php

namespace Tests\Feature\Backend;

use App\Models\Backend\StockItem;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockItemTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_create_stock_item()
    {
        $stockItem = new StockItem([
            'item' => $this->faker->word,
            'item_name' => $this->faker->word,
            'item_type' => $this->faker->numberBetween(1,4),
            'item_emoji' => $this->faker->word,
            'is_active' => $this->faker->boolean,
            'exchange_status' => $this->faker->boolean,
            'exchange_maker_fee' => 0.1,
            'exchange_taker_fee' => 0.2,
            'deposit_status' => $this->faker->boolean,
            'deposit_fee' => 0,
            'withdrawal_status' => $this->faker->boolean,
            'withdrawal_fee' => 0,
            'api_service' => 1,
            'url' => $this->faker->url,
            'port' => 3000,
            'api_client_id' => $this->faker->word,
            'api_secret_key' => $this->faker->word,
            'wallet_address' => $this->faker->word,
            'network_fee' => 0.01,
        ]);



        $this->assertEquals($stockItem, $stockItem->create());
    }
}
