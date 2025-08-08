<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PipelinesSeeder::class,
            TemplatesSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
