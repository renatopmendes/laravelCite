<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    $page = \App\Models\Page::inRandomOrder()->first();

    // Ignorando vídeos
    switch (rand(0, 2)) {
        case 0:
            $arr = [
                'page_id' => $page->id,
                'color' => 'deep-purple',
                'family' => 'Roboto',
                'textSize' => 'text-h3',
                'message' => $faker->text(50),
                // 'commentary' => $faker->text(255),
                'views' => $faker->randomNumber()
            ];
            break;
        case 1:
            $arr = [
                'page_id' => $page->id,
                'youtube' => 'BM-mnklMWCQ',
                'commentary' => $faker->text(255),
                'views' => $faker->randomNumber()
            ];
            break;
        case 2:
            $arr = [
                'page_id' => $page->id,
                'image' => $faker->image('storage/app/public/pages', 640, 480, $faker->numberBetween($min = 10, $max = 100), false),
                'message' => $faker->text(50),
                'commentary' => $faker->text(255),
                'views' => $faker->randomNumber()
            ];
            break;
        case 3:
            $arr = [
                'page_id' => $page->id,
                'image' => $faker->image('storage/app/public/pages', 640, 480, $faker->numberBetween($min = 10, $max = 100), false),
                'video' => '123.m4v',
                'commentary' => $faker->text(255),
                'views' => $faker->randomNumber()
            ];
            break;
    }

    return $arr;
});
