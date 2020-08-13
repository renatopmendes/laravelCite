<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Page::class, 20)->create();

        $dir = 'public/pages/';
        $pages = App\Models\Page::all();
        foreach ($pages as $page) {
            if (Storage::exists($dir . $page->avatar)) {
                // Storage::makeDirectory($dir . $page->id);
                Storage::move($dir . $page->avatar, $dir . $page->id . '/' . $page->avatar);
            }
        }
    }
}
