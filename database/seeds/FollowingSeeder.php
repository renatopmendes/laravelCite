<?php

use Illuminate\Database\Seeder;

class FollowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Following::class, 4)->create();
    }
}
