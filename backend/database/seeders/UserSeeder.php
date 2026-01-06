<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Freelancer;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar freelancer de teste
        $freelancer = User::create([
            'name' => 'João Desenvolvedor',
            'email' => 'freelancer@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'freelancer',
        ]);

        Freelancer::create([
            'user_id' => $freelancer->id,
            'title' => 'Desenvolvedor Full Stack',
            'description' => 'Especialista em Laravel e Vue.js com 5 anos de experiência',
            'hourly_rate' => 150.00,
            'availability' => true,
        ]);

        // Criar cliente de teste
        $client = User::create([
            'name' => 'Maria Cliente',
            'email' => 'cliente@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'client',
        ]);

        Client::create([
            'user_id' => $client->id,
        ]);

        // Criar admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
        ]);
    }
}
