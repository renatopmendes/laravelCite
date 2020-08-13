<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Following;
use Faker\Generator as Faker;

$factory->define(Following::class, function (Faker $faker) {
    $user = \App\Models\User::inRandomOrder()->first();
    $page = \App\Models\Page::where('user_id', '<>', $user->id)->inRandomOrder()->first();

    return [
        'user_id' => $user->id,
        'page_id' => $page->id
    ];
});
