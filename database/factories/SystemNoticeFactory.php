<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Core\SystemNotice::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'description' => $faker->paragraph,
        'type' => $faker->randomElement(['success', 'warning', 'danger', 'info']),
        'start_at' => $faker->dateTimeThisYear,
        'end_at' => $faker->dateTimeThisYear,
        'status' => $faker->boolean
    ];
});
