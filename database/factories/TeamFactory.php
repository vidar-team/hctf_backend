<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Team::class, function (Faker\Generator $faker) {
    return [
        'team_name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt(hash('sha256', str_random(32))),
        'token' => str_random(32)
    ];
});