<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Post::class, 100)->create();

        $dir = 'public/pages/';
        $posts = App\Models\Post::all();
        foreach ($posts as $post) {
            if (isset($post->image) && Storage::exists($dir . $post->image)) {
                Storage::move($dir . $post->image, $dir . $post->page_id . '/' . $post->id . '/' . $post->image);
            }
        }
    }
}
