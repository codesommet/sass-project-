<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionsController extends Controller
{
    /**
     * Super-admin-only permission modules — hidden from non-super-admins
     */
    private const SUPER_ADMIN_MODULES = [
        'agencies',
        'agency-subscriptions',
        'users',
        'roles-permissions',
        'trash',
    ];

    /**
     * Check if current user is super-admin
     */
    private function isSuperAdmin(): bool
    {
        return auth()->guard('backoffice')->user()->hasRole('super-admin');
    }

    /**
     * Filter out super-admin-only permissions for non-super-admins
     */
    private function filterPermissionsForUser($permissions)
    {
        if ($this->isSuperAdmin()) {
            return $permissions;
        }

        return $permissions->filter(function ($perm) {
            $module = explode('.', $perm->name)[0] ?? '';
            return !in_array($module, self::SUPER_ADMIN_MODULES);
        });
    }

    /**
     * Show roles index page
     */
    public function indexRoles(Request $request)
    {
        // Vérifier la permission VIEW
        if (!auth()->guard('backoffice')->user()->can('roles-permissions.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les rôles et permissions.');
        }

        $user = auth()->guard('backoffice')->user();
        $isSuperAdmin = $this->isSuperAdmin();
        $q = trim((string) $request->get('q', ''));

        $rolesQuery = Role::query()
            ->where('guard_name', 'backoffice')
            ->with(['permissions:id,name,guard_name'])
            ->orderBy('name');

        // Non-super-admins cannot see the super-admin role
        if (!$isSuperAdmin) {
            $rolesQuery->where('name', '!=', 'super-admin');
        }

        if ($q !== '') {
            $rolesQuery->where('name', 'like', "%{$q}%");
        }

        $roles = $rolesQuery->paginate(15)->withQueryString();

        // Get permissions for the modal — filter out super-admin-only modules for non-super-admins
        $allPermissions = $this->filterPermissionsForUser(
            Permission::query()
                ->where('guard_name', 'backoffice')
                ->orderBy('name')
                ->get()
        );

        // Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->guard('backoffice')->user()->can('roles-permissions.general.view'),
            'can_create' => auth()->guard('backoffice')->user()->can('roles-permissions.general.create'),
            'can_edit' => auth()->guard('backoffice')->user()->can('roles-permissions.general.edit'),
            'can_delete' => auth()->guard('backoffice')->user()->can('roles-permissions.general.delete'),
        ];

        return view('backoffice.roles-permissions.roles', compact('roles', 'allPermissions', 'permissions', 'isSuperAdmin'));
    }

    /**
     * Show permissions management page for a specific role
     * Builds matrix with CRUD strategy: module.resource.action
     */
    public function showPermissions(Role $role, Request $request)
    {
        // Vérifier la permission VIEW
        if (!auth()->guard('backoffice')->user()->can('roles-permissions.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les permissions.');
        }

        // Ensure the role is from backoffice guard
        if ($role->guard_name !== 'backoffice') {
            abort(404);
        }

        // Non-super-admins cannot view super-admin role permissions
        if ($role->name === 'super-admin' && !$this->isSuperAdmin()) {
            abort(403, 'Vous n\'avez pas la permission de voir les permissions du super-admin.');
        }

        // Get permissions — filter out super-admin-only modules for non-super-admins
        $allPermissions = $this->filterPermissionsForUser(
            Permission::query()
                ->where('guard_name', 'backoffice')
                ->orderBy('name')
                ->get()
        );

        // Get permissions already assigned to this role
        $rolePermissionIds = $role->permissions()->pluck('id')->toArray();

        // Build matrix grouped by module
        // Structure: $matrix[module][resource][action] = ['id' => ..., 'checked' => ...]
        $matrix = [];

        foreach ($allPermissions as $perm) {
            // Parse permission name: "module.resource.action"
            $parts = explode('.', $perm->name);

            if (count($parts) < 3) {
                // Skip malformed permissions
                continue;
            }

            $module = $parts[0];       // e.g., "agencies"
            $resource = $parts[1];     // e.g., "general"
            $action = $parts[2];       // e.g., "view", "create", "edit", "delete"

            // Initialize module if not exists
            if (!isset($matrix[$module])) {
                $matrix[$module] = [];
            }

            // Initialize resource if not exists
            if (!isset($matrix[$module][$resource])) {
                $matrix[$module][$resource] = [];
            }

            // Check if permission is assigned to role
            $isChecked = in_array($perm->id, $rolePermissionIds);

            // Store permission data
            $matrix[$module][$resource][$action] = [
                'id' => $perm->id,
                'name' => $perm->name,
                'checked' => $isChecked,
            ];
        }

        // Sort matrix by module
        ksort($matrix);

        // Sort each resource within module
        foreach ($matrix as &$resources) {
            ksort($resources);
        }

        // Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->guard('backoffice')->user()->can('roles-permissions.general.edit'),
        ];

        $isSuperAdmin = $this->isSuperAdmin();

        // agency-admin permissions are read-only for non-super-admins
        $readOnly = !$isSuperAdmin && $role->name === 'agency-admin';

        return view('backoffice.roles-permissions.permissions', compact('role', 'matrix', 'allPermissions', 'permissions', 'isSuperAdmin', 'readOnly'));
    }

    /**
     * Update permissions for a role
     * Expects permissions array in format: permissions[id] = 1
     */
    public function updatePermissions(Role $role, Request $request)
    {
        // Vérifier la permission EDIT
        if (!auth()->guard('backoffice')->user()->can('roles-permissions.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les permissions.');
        }

        // Ensure the role is from backoffice guard
        if ($role->guard_name !== 'backoffice') {
            abort(404);
        }

        // Non-super-admins cannot edit super-admin or agency-admin roles
        if (in_array($role->name, ['super-admin', 'agency-admin']) && !$this->isSuperAdmin()) {
            abort(403, 'Vous n\'avez pas la permission de modifier les permissions de ce rôle.');
        }

        try {
            // Get array of permission IDs from request
            // Format: permissions = [id1 => 1, id2 => 1, ...]
            $permissionIds = array_keys($request->input('permissions', []) ?? []);

            // Validate that all permission IDs exist
            $validPermissionsQuery = Permission::query()
                ->where('guard_name', 'backoffice')
                ->whereIn('id', $permissionIds);

            // Non-super-admins cannot assign super-admin-only module permissions
            if (!$this->isSuperAdmin()) {
                $validPermissionsQuery->where(function ($q) {
                    foreach (self::SUPER_ADMIN_MODULES as $module) {
                        $q->where('name', 'not like', $module . '.%');
                    }
                });
            }

            $validPermissions = $validPermissionsQuery->pluck('id')->toArray();

            // Sync permissions: remove old, add new (only valid ones)
            $role->syncPermissions($validPermissions);

            // Clear cached permissions after sync
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            // Create notification
            $this->createNotification('update', 'role-permissions', $role);

            return redirect()
                ->route('backoffice.roles-permissions.permissions', $role->id)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Permissions mises à jour avec succès.',
                    'dot' => '#0d6efd',
                    'delay' => 3500,
                    'time' => 'now',
                ]);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('toast', [
                    'title' => 'Erreur',
                    'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        }
    }
}
