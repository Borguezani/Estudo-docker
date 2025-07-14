<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário de teste se não existir
        \App\Models\User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Usuário Teste',
                'email' => 'test@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        // Criar usuário admin se não existir
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
