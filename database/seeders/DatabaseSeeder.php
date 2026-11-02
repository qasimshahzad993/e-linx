<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            MarketingServicesSeeder::class,
            ServiceSolutionsSeeder::class,
            FaqSeeder::class,
            CourseSeeder::class,
        ]);
    }
}
