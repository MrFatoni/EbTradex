<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\User\UserInfo::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'address' => $faker->address,
        'phone' => $faker->phoneNumber,
    ];
});
