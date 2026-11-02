<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (Admin::exists()) {
            return;
        }

        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password || strlen($password) < 12) {
            throw new RuntimeException('Set ADMIN_EMAIL and an ADMIN_PASSWORD of at least 12 characters before seeding.');
        }

        Admin::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'e-Linx Admin'),
                'password' => Hash::make($password),
            ],
        );
    }
}
