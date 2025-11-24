<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Importante para a password

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar o SUPER ADMIN (Para tu usares)
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@hireup.pt',
            'password' => Hash::make('12345678'), // A tua password de testes
            'type' => 'ADM',      // O tal tipo novo que criámos
            'is_blocked' => false,
        ]);

        // 2. Criar um RECRUTADOR de teste
        User::factory()->create([
            'name' => 'Recrutador Exemplo',
            'email' => 'recruiter@company.com',
            'password' => Hash::make('12345678'),
            'type' => 'RCR',
        ]);

        // 3. Criar um CANDIDATO (Job Seeker) de teste
        User::factory()->create([
            'name' => 'Candidato Exemplo',
            'email' => 'seeker@email.com',
            'password' => Hash::make('12345678'),
            'type' => 'JSK',
        ]);

        // 4. Criar 10 utilizadores aleatórios (para encher chouriços)
        User::factory(10)->create([
            'type' => 'JSK' // Cria 10 candidatos aleatórios
        ]);
        
        echo "Base de dados populada com sucesso! \n";
        echo "Admin Login: admin@hireup.pt / 12345678 \n";
    }
}