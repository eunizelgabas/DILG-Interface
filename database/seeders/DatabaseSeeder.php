<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Test Standard',
            'email' => 'standard@example.com',
            'password' => bcrypt('password123'),
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Test Sample',
            'email' => 'sample@example.com',
            'password' => bcrypt('password123'),
        ]);


        $this->call([
            RolesandPermissionSeeder::class,
            // UserSeeder::class
        ]);

        $this->call(LegalOpinionsSeeder::class);
    }
}
