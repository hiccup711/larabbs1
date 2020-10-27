<?php

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

$factory->define(App\Models\Topic::class, function (Faker $faker) {
    $sentence = $faker->sentence();
    $updated_at = $faker->dateTimeThisMonth();
    $created_at = $faker->dateTimeThisMonth($updated_at);
    return [
        'title' => $sentence,
        'content' => $faker->text(),
        'excerpt' => $sentence,
        'user_id' => $faker->randomElement([1,2,3,4,5,6,7,8,9,10]),
        'category_id' => $faker->randomElement([1,2,3,4]),
        'created_at' => $created_at,
        'updated_at' => $updated_at
    ];
});
