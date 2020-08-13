<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            PageSeeder::class,
            // FollowingSeeder::class,
            DenounceSeeder::class,
            NotificationSeeder::class,
            PostSeeder::class,
        ]);
    }
}
