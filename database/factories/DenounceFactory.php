<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Denounce;
use Faker\Generator as Faker;

$factory->define(Denounce::class, function (Faker $faker) {
    $page = \App\Models\Page::inRandomOrder()->first();
    $user = \App\Models\User::where('id', '<>', $page->user_id)->inRandomOrder()->first();

    return [
        'user_id' => $page->user_id,
        'page_id' => $page->id,
        'denouncer_id' => $user->id,
        'denounce' => $faker->text(200)
    ];
});
