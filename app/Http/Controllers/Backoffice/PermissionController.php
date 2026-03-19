<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Permission\PermissionStoreRequest;
use App\Http\Requests\Backoffice\Permission\PermissionUpdateRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    private const SUPER_ADMIN_MODULES = [
        'agencies',
        'agency-subscriptions',
        'users',
        'roles-permissions',
        'trash',
    ];

    private function isSuperAdminModule(string $permName): bool
    {
        $module = explode('.', $permName)[0] ?? '';
        return in_array($module, self::SUPER_ADMIN_MODULES);
    }

    private function isSuperAdmin(): bool
    {
        return auth()->guard('backoffice')->user()->hasRole('super-admin');
    }

    public function index(Request $request)
    {
        $query = Permission::query()
            ->where('guard_name', 'backoffice')
            ->orderBy('name');

        if ($search = trim((string) $request->get('q'))) {
            $query->where('name', 'like', "%{$search}%");
        }

        $permissions = $query->paginate(200)->withQueryString();

        return view('backoffice.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return redirect()->route('backoffice.roles-permissions.roles');
    }

    public function store(PermissionStoreRequest $request)
    {
        $data = $request->validated();

        // Non-super-admins cannot create permissions for super-admin-only modules
        if (!$this->isSuperAdmin() && $this->isSuperAdminModule($data['name'])) {
            abort(403, 'Vous n\'avez pas la permission de créer des permissions pour ce module.');
        }

        $permission = Permission::create([
            'name'       => $data['name'],
            'guard_name' => 'backoffice',
        ]);
        
        // FIXED: Use correct module name 'permission' and the actual permission object
        $this->createNotification('store', 'permission', $permission);

        return redirect()
            ->route('backoffice.roles-permissions.index', ['tab' => 'permissions'])
            ->with('toast', [
                'title'   => 'Création réussie',
                'message' => "La permission « {$permission->name} » a été créée avec succès.",
                'dot'     => 'success',
                'delay'   => 4500,
                'time'    => now()->format('H:i'),
            ]);
    }

    public function show(Permission $permission)
    {
        abort_unless($permission->guard_name === 'backoffice', 404);

        return redirect()->route('backoffice.roles-permissions.roles');
    }

    public function edit(Permission $permission)
    {
        abort_unless($permission->guard_name === 'backoffice', 404);

        return redirect()->route('backoffice.roles-permissions.roles');
    }

    public function update(PermissionUpdateRequest $request, Permission $permission)
    {
        abort_unless($permission->guard_name === 'backoffice', 404);

        // Non-super-admins cannot edit super-admin-only module permissions
        if (!$this->isSuperAdmin() && $this->isSuperAdminModule($permission->name)) {
            abort(403, 'Vous n\'avez pas la permission de modifier cette permission.');
        }

        $data = $request->validated();

        $permission->update([
            'name' => $data['name'],
        ]);
        
        // ADDED: Create notification for update
        $this->createNotification('update', 'permission', $permission);

        return redirect()
            ->route('backoffice.roles-permissions.index', ['tab' => 'permissions'])
            ->with('toast', [
                'title'   => 'Modification réussie',
                'message' => "La permission « {$permission->name} » a été mise à jour avec succès.",
                'dot'     => 'info',
                'delay'   => 4500,
                'time'    => now()->format('H:i'),
            ]);
    }

    public function destroy(Permission $permission)
    {
        abort_unless($permission->guard_name === 'backoffice', 404);

        // Non-super-admins cannot delete super-admin-only module permissions
        if (!$this->isSuperAdmin() && $this->isSuperAdminModule($permission->name)) {
            abort(403, 'Vous n\'avez pas la permission de supprimer cette permission.');
        }

        $name = $permission->name;
        // Store permission data for notification before delete
        $permissionData = clone $permission;
        $permission->delete();
        
        // ADDED: Create notification for delete
        $this->createNotification('destroy', 'permission', $permissionData);

        return redirect()
            ->route('backoffice.roles-permissions.index', ['tab' => 'permissions'])
            ->with('toast', [
                'title'   => 'Suppression réussie',
                'message' => "La permission « {$name} » a été supprimée avec succès.",
                'dot'     => 'danger',
                'delay'   => 4500,
                'time'    => now()->format('H:i'),
            ]);
    }
}