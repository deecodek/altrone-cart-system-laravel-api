<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {

        $this->call([
            UserSeeder::class,
            VendorSeeder::class,
            ProductSeeder::class,
            CartSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
