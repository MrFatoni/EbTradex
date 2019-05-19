<?php

use Faker\Generator as Faker;

use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/
$factory->define(App\Models\User\User::class, function (Faker $faker) {
    return [
        'user_role_management_id' => USER_ROLE_USER,
        'username' => $faker->userName,
        'email' => $faker->unique()->email,
        'password' => Hash::make('user'),
        'is_accessible_under_maintenance' => UNDER_MAINTENANCE_ACCESS_INACTIVE,
        'is_email_verified' => EMAIL_VERIFICATION_STATUS_ACTIVE,
        'is_active' => ACTIVE_STATUS_ACTIVE,
    ];
});
