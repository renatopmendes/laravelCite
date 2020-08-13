<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Notification;
use Faker\Generator as Faker;

$factory->define(Notification::class, function (Faker $faker) {
    $user = \App\Models\User::inRandomOrder()->first();

    return [
        'user_id' => $user->id,
        'subject' => $faker->text(20),
        'message' => $faker->text(200)
    ];
});
