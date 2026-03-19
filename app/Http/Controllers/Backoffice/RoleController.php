<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Role\RoleStoreRequest;
use App\Http\Requests\Backoffice\Role\RoleUpdateRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleController extends Controller
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

    private function isSuperAdmin(): bool
    {
        return auth()->guard('backoffice')->user()->hasRole('super-admin');
    }

    /**
     * Filter permissions query to exclude super-admin-only modules for non-super-admins
     */
    private function filterPermissionIds(array $permissionIds): array
    {
        if ($this->isSuperAdmin()) {
            return $permissionIds;
        }

        return Permission::where('guard_name', 'backoffice')
            ->whereIn('id', $permissionIds)
            ->where(function ($q) {
                foreach (self::SUPER_ADMIN_MODULES as $module) {
                    $q->where('name', 'not like', $module . '.%');
                }
            })
            ->pluck('id')
            ->toArray();
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search'));

        // ROLES
        $rolesQuery = Role::query()
            ->where('guard_name', 'backoffice')
            ->withCount('permissions')
            ->orderBy('name');

        if ($search) {
            $rolesQuery->where('name', 'like', "%{$search}%");
        }

        $roles = $rolesQuery
            ->paginate(15, ['*'], 'roles_page')
            ->withQueryString();

        // PERMISSIONS
        $permissionsQuery = Permission::query()
            ->where('guard_name', 'backoffice')
            ->orderBy('name');

        if ($search) {
            $permissionsQuery->where('name', 'like', "%{$search}%");
        }

        $permissions = $permissionsQuery
            ->paginate(15, ['*'], 'permissions_page')
            ->withQueryString();

        return view('backoffice.roles-permissions.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::query()
            ->where('guard_name', 'backoffice')
            ->orderBy('name')
            ->get();

        return redirect()->route('backoffice.roles-permissions.roles');
    }

    public function store(RoleStoreRequest $request)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $data = $request->validated();

        $role = Role::create([
            'name'       => $data['name'],
            'guard_name' => 'backoffice',
        ]);

        $permissionIds = $this->filterPermissionIds($data['permissions'] ?? []);
        if (!empty($permissionIds)) {
            $permissions = Permission::where('guard_name', 'backoffice')
                ->whereIn('id', $permissionIds)
                ->get();

            $role->syncPermissions($permissions);
        }

        // ADDED: Create notification for store
        $this->createNotification('store', 'role', $role);

        return redirect()
            ->route('backoffice.roles-permissions.index', ['tab' => 'roles'])
            ->with('toast', [
                'title'   => 'Création réussie',
                'message' => "Le rôle « {$role->name} » a été créé avec succès.",
                'dot'     => 'success',
                'delay'   => 4500,
                'time'    => now()->format('H:i'),
            ]);
    }

    public function show(Role $role)
    {
        abort_unless($role->guard_name === 'backoffice', 404);

        $role->load('permissions');

        return redirect()->route('backoffice.roles-permissions.roles');
    }

    public function edit(Role $role)
    {
        abort_unless($role->guard_name === 'backoffice', 404);

        $permissions = Permission::query()
            ->where('guard_name', 'backoffice')
            ->orderBy('name')
            ->get();

        $role->load('permissions');
        $assigned = $role->permissions->pluck('id')->all();

        return redirect()->route('backoffice.roles-permissions.roles');
    }

    public function update(RoleUpdateRequest $request, Role $role)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        abort_unless($role->guard_name === 'backoffice', 404);

        // Non-super-admins cannot edit super-admin or agency-admin roles
        if (in_array($role->name, ['super-admin', 'agency-admin']) && !$this->isSuperAdmin()) {
            abort(403, 'Vous n\'avez pas la permission de modifier ce rôle.');
        }

        $data = $request->validated();

        $role->update([
            'name' => $data['name'],
        ]);

        $permissionIds = $this->filterPermissionIds($data['permissions'] ?? []);
        $permissions = Permission::where('guard_name', 'backoffice')
            ->whereIn('id', $permissionIds)
            ->get();

        $role->syncPermissions($permissions);

        // ADDED: Create notification for update
        $this->createNotification('update', 'role', $role);

        return redirect()
            ->route('backoffice.roles-permissions.index', ['tab' => 'roles'])
            ->with('toast', [
                'title'   => 'Modification réussie',
                'message' => "Le rôle « {$role->name} » a été mis à jour avec succès.",
                'dot'     => 'info',
                'delay'   => 4500,
                'time'    => now()->format('H:i'),
            ]);
    }

    public function destroy(Role $role)
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        abort_unless($role->guard_name === 'backoffice', 404);

        // Non-super-admins cannot delete super-admin or agency-admin roles
        if (in_array($role->name, ['super-admin', 'agency-admin']) && !$this->isSuperAdmin()) {
            abort(403, 'Vous n\'avez pas la permission de supprimer ce rôle.');
        }

        if (in_array($role->name, ['super-admin', 'super_admin', 'Super Admin'], true)) {
            return back()->with('toast', [
                'title'   => 'Action refusée',
                'message' => "Le rôle « {$role->name} » ne peut pas être supprimé.",
                'dot'     => 'warning',
                'delay'   => 5000,
                'time'    => now()->format('H:i'),
            ]);
        }

        $name = $role->name;
        // Store role data for notification before delete
        $roleData = clone $role;
        $role->delete();
        
        // ADDED: Create notification for delete
        $this->createNotification('destroy', 'role', $roleData);

        return redirect()
            ->route('backoffice.roles-permissions.index', ['tab' => 'roles'])
            ->with('toast', [
                'title'   => 'Suppression réussie',
                'message' => "Le rôle « {$name} » a été supprimé avec succès.",
                'dot'     => 'danger',
                'delay'   => 4500,
                'time'    => now()->format('H:i'),
            ]);
    }
}