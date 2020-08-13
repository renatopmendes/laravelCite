<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Page;
use Faker\Generator as Faker;

$factory->define(Page::class, function (Faker $faker) {
    $user = \App\Models\User::inRandomOrder()->first();

    return [
        'user_id' => $user->id,
        'name' => $faker->name,
        'avatar' => $faker->image('storage/app/public/pages', 80, 80, $faker->numberBetween($min = 10, $max = 100), false),
        'link' => $faker->url,
        'about' => $faker->text(2056),
        'views' => $faker->randomNumber()
    ];
});
