<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Log::class, function(Faker\Generator $faker){
    return [
      'challenge_id' => 1,
      'team_id' => random_int(1, 51),
        'level_id' => 1,
        'status' => 'correct',
        'flag' => 'fake_flag',
        'score' => random_int(100, 300),
        'created_at' => $faker->dateTimeBetween('+7 days', '+9 days')
    ];
});