<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AgencyUserSeeder extends Seeder
{
    public function run(): void
    {
        // Agencies (update or create)
        $agency1 = Agency::updateOrCreate(
            ['email' => 'contact@agency1.com'],
            [
                'name' => 'Agency Demo 1',
                'legal_name' => 'Agency Demo 1 SARL',
                'phone' => '+212600000001',
                'address' => '123 Rue Demo 1',
                'city' => 'Casablanca',
                'country' => 'Morocco',
            ]
        );

        $agency2 = Agency::updateOrCreate(
            ['email' => 'contact@agency2.com'],
            [
                'name' => 'Agency Demo 2',
                'legal_name' => 'Agency Demo 2 SARL',
                'phone' => '+212600000002',
                'address' => '456 Rue Demo 2',
                'city' => 'Rabat',
                'country' => 'Morocco',
            ]
        );

        // Create users (roles will be assigned in AgencyRolesPermissionsSeeder)
        User::updateOrCreate(
            ['email' => 'super@admin.com'],
            [
                'agency_id' => null,
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'phone' => '+212600000999',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@agency1.com'],
            [
                'agency_id' => $agency1->id,
                'name' => 'Agency Admin',
                'password' => Hash::make('password123'),
                'phone' => '+212600000003',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@agency1.com'],
            [
                'agency_id' => $agency1->id,
                'name' => 'Agency Manager',
                'password' => Hash::make('password123'),
                'phone' => '+212600000010',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@agency1.com'],
            [
                'agency_id' => $agency1->id,
                'name' => 'Agency Staff',
                'password' => Hash::make('password123'),
                'phone' => '+212600000011',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'inactive@agency1.com'],
            [
                'agency_id' => $agency1->id,
                'name' => 'Inactive User',
                'password' => Hash::make('password123'),
                'phone' => '+212600000004',
                'status' => 'inactive',
            ]
        );

        User::updateOrCreate(
            ['email' => 'blocked@agency2.com'],
            [
                'agency_id' => $agency2->id,
                'name' => 'Blocked User',
                'password' => Hash::make('password123'),
                'phone' => '+212600000005',
                'status' => 'blocked',
            ]
        );

        $this->command?->info("✅ Utilisateurs de l'agence créés avec succès");
        $this->command?->info("");
        $this->command?->info("📋 Comptes de test (les rôles seront attribués dans le prochain seeder) :");
        $this->command?->info("  super@admin.com / password123");
        $this->command?->info("  admin@agency1.com / password123");
        $this->command?->info("  manager@agency1.com / password123");
        $this->command?->info("  staff@agency1.com / password123");
        $this->command?->info("");
        $this->command?->info("👉 Suivant : Exécuter RolesAndSuperAdminSeeder puis AgencyRolesPermissionsSeeder");
    }
}