<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AgencyRolesPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Get or create the agency
        $agency = Agency::where('email', 'contact@agency1.com')->first();

        if (!$agency) {
            $this->command?->error('❌ Agence introuvable. Veuillez exécuter AgencyUserSeeder d\'abord.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Create or Update Users with Different Roles
        |--------------------------------------------------------------------------
        */

        // SUPER ADMIN User - super-admin role (not tied to agency)
        $superAdminUser = User::updateOrCreate(
            ['email' => 'super@admin.com'],
            [
                'agency_id' => null,
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'phone' => '+212600000999',
                'status' => 'active',
            ]
        );

        // AGENCY ADMIN User - agency-admin role
        $agencyAdminUser = User::updateOrCreate(
            ['email' => 'admin@agency1.com'],
            [
                'agency_id' => $agency->id,
                'name' => 'Agency Admin',
                'password' => Hash::make('password123'),
                'phone' => '+212600000003',
                'status' => 'active',
            ]
        );

        // AGENCY MANAGER User - agency-manager role
        $managerUser = User::updateOrCreate(
            ['email' => 'manager@agency1.com'],
            [
                'agency_id' => $agency->id,
                'name' => 'Agency Manager',
                'password' => Hash::make('password123'),
                'phone' => '+212600000010',
                'status' => 'active',
            ]
        );

        // AGENCY STAFF User - agency-staff role
        $staffUser = User::updateOrCreate(
            ['email' => 'staff@agency1.com'],
            [
                'agency_id' => $agency->id,
                'name' => 'Agency Staff',
                'password' => Hash::make('password123'),
                'phone' => '+212600000011',
                'status' => 'active',
            ]
        );

        // Additional test users
        User::updateOrCreate(
            ['email' => 'inactive@agency1.com'],
            [
                'agency_id' => $agency->id,
                'name' => 'Inactive User',
                'password' => Hash::make('password123'),
                'phone' => '+212600000004',
                'status' => 'inactive',
            ]
        );

        User::updateOrCreate(
            ['email' => 'blocked@agency2.com'],
            [
                'agency_id' => null,
                'name' => 'Blocked User',
                'password' => Hash::make('password123'),
                'phone' => '+212600000005',
                'status' => 'blocked',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Assign Roles to Users
        |--------------------------------------------------------------------------
        */

        // Assign super-admin role to super admin user
        $superAdminUser->syncRoles(['super-admin']);

        // Assign agency-admin role to agency admin user
        $agencyAdminUser->syncRoles(['agency-admin']);

        // Assign agency-manager role to manager user
        $managerUser->syncRoles(['agency-manager']);

        // Assign agency-staff role to staff user
        $staffUser->syncRoles(['agency-staff']);

        // Clear cache after seeding
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Output Information
        |--------------------------------------------------------------------------
        */
        $this->command?->info('✅ Rôles & permissions de l\'agence créés avec succès');
        $this->command?->info('');
        $this->command?->info('📋 Utilisateurs créés avec rôles :');
        $this->command?->info('  🔴 SUPER ADMINISTRATEUR');
        $this->command?->info('     Email : super@admin.com | Mot de passe : password123');
        $this->command?->info('     Accès : ACCÈS TOTAL - toutes les agences, tous les modules');
        $this->command?->info('');
        $this->command?->info('  🔵 ADMIN AGENCE');
        $this->command?->info('     Email : admin@agency1.com | Mot de passe : password123');
        $this->command?->info('     Accès : Accès complet dans l\'Agence 1');
        $this->command?->info('');
        $this->command?->info('  🟢 MANAGER AGENCE');
        $this->command?->info('     Email : manager@agency1.com | Mot de passe : password123');
        $this->command?->info('     Accès : Voir/Créer/Modifier (pas de suppression) dans l\'Agence 1');
        $this->command?->info('');
        $this->command?->info('  ⚪ EMPLOYÉ AGENCE');
        $this->command?->info('     Email : staff@agency1.com | Mot de passe : password123');
        $this->command?->info('     Accès : Consultation uniquement dans l\'Agence 1');
    }
}