<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class Agency2Seeder extends Seeder
{
    public function run(): void
    {
        $agency2 = Agency::where('email', 'contact@agency2.com')->first();
        
        if (!$agency2) {
            $this->command->error('❌ Agence 2 introuvable !');
            return;
        }

        // Agency Admin for Agency 2
        User::firstOrCreate(
            ['email' => 'admin@agency2.com'],
            [
                'agency_id' => $agency2->id,
                'name' => 'Admin Agence 2',
                'password' => Hash::make('password123'),
                'phone' => '+212600000020',
            ]
        );

        // Agency Manager for Agency 2
        User::firstOrCreate(
            ['email' => 'manager@agency2.com'],
            [
                'agency_id' => $agency2->id,
                'name' => 'Manager Agence 2',
                'password' => Hash::make('password123'),
                'phone' => '+212600000021',
            ]
        );

        // Agency Staff for Agency 2
        User::firstOrCreate(
            ['email' => 'staff@agency2.com'],
            [
                'agency_id' => $agency2->id,
                'name' => 'Employé Agence 2',
                'password' => Hash::make('password123'),
                'phone' => '+212600000022',
            ]
        );

        $this->command->info('✅ Utilisateurs de l\'Agence 2 créés');
    }
}