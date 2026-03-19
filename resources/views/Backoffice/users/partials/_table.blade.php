<head>
    <style>
        /* GLOBAL FIX for action dropdowns in tables */
        .table-responsive,
        .custom-datatable-filter,
        .dataTables_wrapper {
            overflow: visible !important;
        }
        
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #6c757d;
            background: transparent;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-icon:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #0d6efd;
        }
        .form-check {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<table class="table datatable align-middle">
    <thead class="thead-light">
        <tr>
            {{-- Case à cocher - visible seulement si permission DELETE --}}
            @can('users.general.delete')
            <th class="no-sort" width="50">
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            @endcan
            <th>NOM</th>
            <th>EMAIL</th>
            <th>TÉLÉPHONE</th>
            <th>STATUT</th>
            @auth('backoffice')
                @if (auth('backoffice')->user()->hasRole('super-admin'))
                    <th>AGENCE</th>
                @endif
            @endauth
            {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
            @canany(['users.general.view', 'users.general.edit', 'users.general.delete'])
            <th width="80"></th>
            @endcanany
        </tr>
    </thead>

    <tbody>
        @forelse($users as $user)
            @php
                $isActive = ($user->status ?? 'active') === 'active';
                $isInactive = ($user->status ?? 'active') === 'inactive';
                $isBlocked = ($user->status ?? 'active') === 'blocked';
                
                $canView = auth()->user()->can('users.general.view');
            @endphp

            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('users.general.delete')
                <td>
                    <div class="form-check form-check-md">
                        <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                    </div>
                </td>
                @endcan

                <td>
                    <div class="d-flex align-items-center file-name-icon">
                        <a href="javascript:void(0);" class="avatar avatar-lg border">
                            <span class="avatar-title bg-light text-dark fw-bold">
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            </span>
                        </a>
                        <div class="ms-2">
                            <h6 class="fw-medium mb-0">
                                {{-- Lien vers show - contrôlé par permission VIEW --}}
                                @if($canView)
                                    <a href="{{ route('backoffice.users.show', $user) }}">{{ $user->name }}</a>
                                @else
                                    <span>{{ $user->name }}</span>
                                @endif
                            </h6>
                        </div>
                    </div>
                </td>

                <td>{{ $user->email }}</td>

                <td>{{ $user->phone ?? '—' }}</td>

                <td>
                    @if ($isActive)
                        <span class="badge badge-dark-transparent">
                            <i class="ti ti-point-filled text-success me-1"></i>Actif
                        </span>
                    @elseif($isInactive)
                        <span class="badge badge-dark-transparent">
                            <i class="ti ti-point-filled text-warning me-1"></i>Inactif
                        </span>
                    @elseif($isBlocked)
                        <span class="badge badge-dark-transparent">
                            <i class="ti ti-point-filled text-danger me-1"></i>Bloqué
                        </span>
                    @endif
                </td>

                @auth('backoffice')
                    @if (auth('backoffice')->user()->hasRole('super-admin'))
                        <td>
                            {{ $user->agency?->name ?? '—' }}
                        </td>
                    @endif
                @endauth

                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['users.general.view', 'users.general.edit', 'users.general.delete'])
                <td>
                    @include('backoffice.users.partials._actions', ['user' => $user])
                </td>
                @endcanany
            </tr>
        @empty
            @php
                $emptyColspan = 4
                    + (auth()->user()->can('users.general.delete') ? 1 : 0)
                    + (auth('backoffice')->user()->hasRole('super-admin') ? 1 : 0)
                    + (auth()->user()->canAny(['users.general.view', 'users.general.edit', 'users.general.delete']) ? 1 : 0);
            @endphp
            <tr>
                <td colspan="{{ $emptyColspan }}" class="text-center py-4">
                    <i class="ti ti-users-off fs-24 text-muted mb-2"></i>
                    <p class="text-muted mb-0">Aucun utilisateur trouvé.</p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('users.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.user-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan