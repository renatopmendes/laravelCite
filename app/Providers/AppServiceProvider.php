<?php

namespace App\Providers;

use App\Observers\UserObserver;
use App\Models\User;
use App\Observers\PageObserver;
use App\Models\Page;
use App\Observers\PostObserver;
use App\Models\Post;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Page::observe(PageObserver::class);
        Post::observe(PostObserver::class);
    }
}
