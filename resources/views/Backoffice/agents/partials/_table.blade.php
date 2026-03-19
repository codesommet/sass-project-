<head>
    <style>
        .table-responsive,
        .custom-datatable-filter,
        .dataTables_wrapper {
            overflow: visible !important;
        }
        
        /* Agent avatar styles */
        .agent-avatar-table {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
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
            @can('agents.general.delete')
            <th class="no-sort" width="50">
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            @endcan
            <th>Photo</th>
            <th>Agent</th>
            <th>Agence</th>
            <th>Contact</th>
            <th>Utilisateur lié</th>
            <th>Date d'ajout</th>
            {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
            @canany(['agents.general.view', 'agents.general.edit', 'agents.general.delete'])
            <th width="80">Actions</th>
            @endcanany
        </tr>
    </thead>
    <tbody>
        @forelse($agents as $agent)
        <tr>
            {{-- Case à cocher - visible seulement si permission DELETE --}}
            @can('agents.general.delete')
            <td class="text-center">
                <div class="form-check form-check-md">
                    <input class="form-check-input agent-checkbox" type="checkbox" value="{{ $agent->id }}">
                </div>
            </td>
            @endcan
            
            <td>
                @if($agent->avatar_url)
                    <img src="{{ $agent->avatar_url }}" 
                         alt="{{ $agent->full_name }}"
                         class="agent-avatar-table"
                         onerror="this.src='{{ asset('assets/place-holder.webp') }}';">
                @else
                    <img src="{{ asset('assets/place-holder.webp') }}" 
                         alt="{{ $agent->full_name }}"
                         class="agent-avatar-table">
                @endif
            </td>
            <td>
                <div class="d-flex flex-column">
                    <h6 class="fw-medium mb-0">
                        {{-- Lien vers show - visible seulement si permission VIEW --}}
                        @can('agents.general.view')
                            <a href="{{ route('backoffice.agents.show', $agent) }}" class="text-dark">
                                {{ $agent->full_name }}
                            </a>
                        @else
                            <span>{{ $agent->full_name }}</span>
                        @endcan
                    </h6>
                    <small class="text-muted">ID: #{{ $agent->id }}</small>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <i class="ti ti-building me-1 text-gray-4"></i>
                    <span>{{ $agent->agency->name ?? '—' }}</span>
                </div>
            </td>
            <td>
                <div class="d-flex flex-column">
                    @if($agent->email)
                        <a href="mailto:{{ $agent->email }}" class="text-primary small">
                            <i class="ti ti-mail me-1"></i>{{ Str::limit($agent->email, 20) }}
                        </a>
                    @endif
                    @if($agent->phone)
                        <a href="tel:{{ $agent->phone }}" class="text-success small mt-1">
                            <i class="ti ti-phone me-1"></i>{{ $agent->phone }}
                        </a>
                    @endif
                    @if(!$agent->email && !$agent->phone)
                        <span class="text-muted">—</span>
                    @endif
                </div>
            </td>
            <td>
                @if($agent->user)
                    <span class="badge bg-success-transparent">
                        <i class="ti ti-user-check me-1"></i>
                        {{ Str::limit($agent->user->name, 15) }}
                    </span>
                @else
                    <span class="badge bg-light-200">
                        <i class="ti ti-user-x me-1"></i>
                        Non lié
                    </span>
                @endif
            </td>
            <td>
                <div class="d-flex flex-column">
                    <small class="fw-medium">{{ $agent->created_at->format('d/m/Y') }}</small>
                    <small class="text-muted">{{ $agent->created_at->format('H:i') }}</small>
                </div>
            </td>
            
            {{-- Actions - visible seulement si au moins une permission d'action --}}
            @canany(['agents.general.view', 'agents.general.edit', 'agents.general.delete'])
            <td class="text-center">
                @include('backoffice.agents.partials._actions', ['agent' => $agent])
            </td>
            @endcanany
        </tr>
        @empty
        @php
            $emptyColspan = 6
                + (auth()->user()->can('agents.general.delete') ? 1 : 0)
                + (auth()->user()->canAny(['agents.general.view', 'agents.general.edit', 'agents.general.delete']) ? 1 : 0);
        @endphp
        <tr>
            <td colspan="{{ $emptyColspan }}" class="text-center py-5">
                <div class="text-center">
                    <i class="ti ti-users-off fs-48 text-gray-4 mb-3"></i>
                    <h5 class="mb-2">Aucun agent trouvé</h5>
                    @can('agents.general.create')
                        <p class="text-muted mb-3">Commencez par ajouter un agent</p>
                        <a href="{{ route('backoffice.agents.create') }}" class="btn btn-primary mt-3">
                            <i class="ti ti-plus me-2"></i>Ajouter un agent
                        </a>
                    @endcan
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('agents.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.agent-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan