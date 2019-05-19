<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\User\UserSetting::class, function (Faker $faker) {
    return [
        'language' => config('app.locale'),
        'timezone' => $faker->timezone,
    ];
});
