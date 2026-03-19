@if(($mode ?? 'roles') === 'roles')
    <table class="table">
        <thead class="thead-light">
        <tr>
            <th>RÔLE</th>
            <th>DATE DE CRÉATION</th>
            <th>STATUT</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($roles as $role)
            @php
                $permIds = $role->permissions ? $role->permissions->pluck('id')->values() : collect();
            @endphp
            <tr>
                <td>
                    <p class="text-gray-9">{{ $role->name }}</p>
                </td>
                <td>
                    <p class="text-gray-9">{{ optional($role->created_at)->format('d M Y') }}</p>
                </td>
                <td>
                    <span class="badge badge-dark-transparent">
                        <i class="ti ti-point-filled text-success me-1"></i>Actif
                    </span>
                </td>
                <td>
                    @include('Backoffice.roles-permissions.partials._actions', [
                        'type' => 'role',
                        'item' => $role,
                        'perm_ids' => $permIds
                    ])
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <p class="text-gray-5 mb-0">Aucun rôle trouvé.</p>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
@else
    <table class="table">
        <thead class="thead-light">
        <tr>
            <th>PERMISSION</th>
            <th>GUARD</th>
            <th>DATE DE CRÉATION</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($permissions as $permission)
            <tr>
                <td>
                    <p class="text-gray-9">{{ $permission->name }}</p>
                </td>
                <td>
                    <p class="text-gray-5">{{ $permission->guard_name }}</p>
                </td>
                <td>
                    <p class="text-gray-5">{{ optional($permission->created_at)->format('d M Y') }}</p>
                </td>
                <td>
                    @include('Backoffice.roles-permissions.partials._actions', [
                        'type' => 'permission',
                        'item' => $permission
                    ])
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">
                    <p class="text-gray-5 mb-0">Aucune permission trouvée.</p>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
@endif
