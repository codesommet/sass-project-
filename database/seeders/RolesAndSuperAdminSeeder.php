<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Define ALL Modules from Sidebar with Resources and Actions
        |--------------------------------------------------------------------------
        | Format: "module.resource.action"
        | Actions: view, create, edit, delete
        */
        $modulesConfig = [
            // Main
            'dashboard' => [
                'general' => ['view'],
            ],
            
            // AGENCIES SECTION
            'agencies' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'agency-subscriptions' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'agency-settings' => [
                'general' => ['view', 'edit'],
            ],
            
            // ACCÈS SECTION
            'roles-permissions' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // EMPLOYEE SECTION
            'agents' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // CLIENTS SECTION
            'clients' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // MANAGEMENT SECTION
            'users' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // VÉHICULE SUIVI SECTION
            'vehicle-vignettes' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'vehicle-insurances' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'vehicle-oil-changes' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'vehicle-technical-checks' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'vehicle-controls' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'vehicle-control-items' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // CONTRATS SECTION
            'rental-contracts' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'contract-clients' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // VÉHICULES SECTION
            'vehicles' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'vehicle-credits' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'vehicle-brands' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'vehicle-models' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // RÉSERVATIONS SECTION
            'bookings' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // FINANCE SECTION
            'financial-accounts' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'transaction-categories' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'financial-transactions' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // FACTURATION SECTION
            'invoices' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            'invoice-items' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // PAIEMENTS SECTION
            'payments' => [
                'general' => ['view', 'create', 'edit', 'delete'],
            ],
            
            // REPORTS SECTION
            'reports' => [
                'general' => ['view'],
            ],

            // NOTIFICATIONS SECTION
            'notifications' => [
                'general' => ['view'],
            ],

            // TRASH SECTION
            'trash' => [
                'general' => ['view', 'restore', 'delete'],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Generate All Permissions (CRUD Strategy)
        |--------------------------------------------------------------------------
        */
        $allPermissions = [];
        foreach ($modulesConfig as $module => $resources) {
            foreach ($resources as $resource => $actions) {
                foreach ($actions as $action) {
                    $permissionName = "{$module}.{$resource}.{$action}";

                    Permission::firstOrCreate([
                        'name' => $permissionName,
                        'guard_name' => 'backoffice',
                    ]);

                    $allPermissions[] = $permissionName;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Create Roles (THE 4 ROLES YOU NEED)
        |--------------------------------------------------------------------------
        */
        $roles = [
            'super-admin',      // Full access to everything
            'agency-admin',      // Full access within their agency
            'agency-manager',    // Can manage operations (no delete)
            'agency-staff',      // View only
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'backoffice',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Assign Permissions to Roles
        |--------------------------------------------------------------------------
        */

        // 1. SUPER-ADMIN: ALL permissions (everything)
        Role::findByName('super-admin', 'backoffice')
            ->syncPermissions($allPermissions);

        // 2. AGENCY-ADMIN: All permissions EXCEPT super-admin-only modules
        $agencyAdminPerms = array_filter($allPermissions, function ($perm) {
            // Exclude only super-admin-only modules
            return !str_starts_with($perm, 'agencies.') &&
                   !str_starts_with($perm, 'agency-subscriptions.') &&
                   !str_starts_with($perm, 'trash.');
        });
        Role::findByName('agency-admin', 'backoffice')
            ->syncPermissions(array_values($agencyAdminPerms));

        // 3. AGENCY-MANAGER: View, Create, Edit (NO DELETE)
        $agencyManagerPerms = array_filter($allPermissions, function ($perm) {
            // Exclude global modules
            if (str_starts_with($perm, 'agencies.') ||
                str_starts_with($perm, 'agency-subscriptions.') ||
                str_starts_with($perm, 'users.') ||
                str_starts_with($perm, 'roles-permissions.') ||
                str_starts_with($perm, 'trash.')) {
                return false;
            }
            
            // Include view, create, edit but EXCLUDE delete
            return str_contains($perm, '.view') || 
                   str_contains($perm, '.create') || 
                   str_contains($perm, '.edit');
        });
        Role::findByName('agency-manager', 'backoffice')
            ->syncPermissions(array_values($agencyManagerPerms));

        // 4. AGENCY-STAFF: View ONLY
        $agencyStaffPerms = array_filter($allPermissions, function ($perm) {
            // Exclude global modules
            if (str_starts_with($perm, 'agencies.') ||
                str_starts_with($perm, 'agency-subscriptions.') ||
                str_starts_with($perm, 'users.') ||
                str_starts_with($perm, 'roles-permissions.') ||
                str_starts_with($perm, 'trash.')) {
                return false;
            }
            
            // Include ONLY view permissions
            return str_contains($perm, '.view');
        });
        Role::findByName('agency-staff', 'backoffice')
            ->syncPermissions(array_values($agencyStaffPerms));

        // Clear cache after seeding
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Output Information
        |--------------------------------------------------------------------------
        */
        $this->command?->info('✅ Rôles & permissions créés avec succès');
        $this->command?->info("✅ " . count($allPermissions) . " permissions créées");
        $this->command?->info('');
        $this->command?->info('📋 Modules avec permissions :');
        $modules = array_keys($modulesConfig);
        foreach ($modules as $module) {
            $this->command?->info("  • {$module}");
        }

        $this->command?->info('');
        $this->command?->info('📋 Rôles créés :');
        $this->command?->info('  • super-admin - ACCÈS TOTAL à tout le système');
        $this->command?->info('  • agency-admin - Accès complet dans l\'agence');
        $this->command?->info('  • agency-manager - Voir/Créer/Modifier (pas de suppression)');
        $this->command?->info('  • agency-staff - Consultation uniquement');

        $this->command?->info('');
        $this->command?->info('📋 Nombre de permissions par rôle :');
        $this->command?->info('  • super-admin: ' . count($allPermissions) . ' permissions');
        $this->command?->info('  • agency-admin: ' . count($agencyAdminPerms) . ' permissions');
        $this->command?->info('  • agency-manager: ' . count($agencyManagerPerms) . ' permissions');
        $this->command?->info('  • agency-staff: ' . count($agencyStaffPerms) . ' permissions');
    }
}