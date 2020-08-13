<?php

use Illuminate\Database\Seeder;

class DenounceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Denounce::class, 200)->create();
    }
}
