<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::factory()->create([
            "name" => "ueser",
            "email" => "user@gmail.com",
            "password" => Hash::make("asdffdsa"),
        ]);

        User::factory(10)->create();

        User::factory()->create([
            "name" => "Kyaw Ko Ko",
            "email" => "admin@gmail.com",
            "password" => Hash::make("root"),
            "role" => "admin"
        ]);
    }
}
