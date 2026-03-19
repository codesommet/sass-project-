<head>
    <style>
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
            @can('agencies.general.delete')
            <th class="no-sort" width="50">
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            @endcan
            <th>NOM</th>
            <th>TOTAL VOITURES</th>
            <th>STATUT</th>
            {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
            @canany(['agencies.general.view', 'agencies.general.edit', 'agencies.general.delete'])
            <th width="80"></th>
            @endcanany
        </tr>
    </thead>

    <tbody>
        @forelse($agencies as $agency)
            @php
                $carsCount = method_exists($agency, 'vehicles') ? $agency->vehicles()->count() : 0;
                $isActive = ($agency->status ?? 'active') === 'active';
            @endphp

            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('agencies.general.delete')
                <td class="text-center">
                    <div class="form-check form-check-md">
                        <input class="form-check-input agency-checkbox" type="checkbox" value="{{ $agency->id }}">
                    </div>
                </td>
                @endcan

                <td>
                    <div class="d-flex align-items-center file-name-icon">
                        <a href="javascript:void(0);" class="avatar avatar-lg border">
                            <span class="avatar-title bg-light text-dark fw-bold">
                                {{ strtoupper(mb_substr($agency->name, 0, 1)) }}
                            </span>
                        </a>
                        <div class="ms-2">
                            <h6 class="fw-medium mb-0">
                                {{-- Lien vers show - visible seulement si permission VIEW --}}
                                @can('agencies.general.view')
                                    <a href="{{ route('backoffice.agencies.show', $agency) }}">{{ $agency->name }}</a>
                                @else
                                    <span>{{ $agency->name }}</span>
                                @endcan
                            </h6>
                            @if(!empty($agency->email))
                                <small class="text-muted">{{ $agency->email }}</small>
                            @endif
                        </div>
                    </div>
                </td>

                <td>{{ $carsCount }}</td>

                <td>
                    @if($isActive)
                        <span class="badge badge-dark-transparent">
                            <i class="ti ti-point-filled text-success me-1"></i>Actif
                        </span>
                    @else
                        <span class="badge badge-dark-transparent">
                            <i class="ti ti-point-filled text-danger me-1"></i>Inactif
                        </span>
                    @endif
                </td>

                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['agencies.general.view', 'agencies.general.edit', 'agencies.general.delete'])
                <td class="text-center">
                    @include('backoffice.agencies.partials._actions', ['agency' => $agency])
                </td>
                @endcanany
            </tr>
        @empty
            @php
                $emptyColspan = 3
                    + (auth()->user()->can('agencies.general.delete') ? 1 : 0)
                    + (auth()->user()->canAny(['agencies.general.view', 'agencies.general.edit', 'agencies.general.delete']) ? 1 : 0);
            @endphp
            <tr>
                <td colspan="{{ $emptyColspan }}" class="text-center py-4">
                    <i class="ti ti-building-off fs-24 text-muted mb-2"></i>
                    <p class="text-muted mb-0">Aucune agence trouvée.</p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('agencies.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.agency-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan