<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        Company::create([
            'user_id' => $user->id,
            'name' => "admin's Company",
            'primary_color' => '#0d6efd',
            'secondary_color' => '#111827',
        ]);
    }
}
