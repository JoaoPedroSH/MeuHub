<?php

namespace Database\Seeders;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => env('APP_ADMIN_EMAIL', 'admin@meuhub.local')],
            [
                'name' => 'Administrador',
                'password' => Hash::make(env('APP_ADMIN_PASSWORD', 'senha123')),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );

        $user = User::updateOrCreate(
            ['email' => env('APP_CLIENT_TESTE_EMAIL', 'client@meuhub.local')],
            [
                'name' => 'Cliente',
                'password' => Hash::make(env('APP_CLIENT_TESTE_PASSWORD', 'senha123')),
                'email_verified_at' => now(),
                'is_admin' => false,
            ]
        );
    }
}
