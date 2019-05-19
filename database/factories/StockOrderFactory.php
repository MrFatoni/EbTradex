<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\User\StockOrder::class, function (Faker $faker) {
    return [
        'user_id' => $faker->numberBetween(1, 4),
        'stock_pair_id' => 1,
        'category' => CATEGORY_EXCHANGE,
        'exchange_type' => $faker->numberBetween(1, 2),
        'status' => 1,
        'price' => $faker->numerify('##'),
        'amount' => $faker->randomDigit,
        'exchanged' => 0,
        'canceled' => 0,
        'stop_limit' => null,
        'maker_fee' => 0.15,
        'taker_fee' => 0.23,
    ];
});
