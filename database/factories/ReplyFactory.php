<?php

use Faker\Generator as Faker;
/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Models\Reply::class, function (Faker $faker) {
    $time = $faker->dateTimeThisMonth();
    return [
        'body' => $faker->sentence,
        'created_at' => $time,
        'updated_at' => $time,
        'topic_id'  =>  rand(1, 100),
        'user_id'   =>  rand(1, 10)
    ];
});
