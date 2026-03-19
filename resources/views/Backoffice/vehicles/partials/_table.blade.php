<head>
    <style>
        /* Fix DataTables dropdown clipping */
        .dataTables_wrapper,
        .dataTables_wrapper .table-responsive {
            overflow: visible !important;
        }

        /* Ensure dropdown appears above table */
        .dataTables_wrapper .dropdown-menu {
            z-index: 1055;
        }

        /* Empty state styling */
        .empty-state {
            padding: 40px 20px;
            text-align: center;
        }
        .empty-state i {
            font-size: 48px;
            color: #adb5bd;
            margin-bottom: 16px;
        }
        
        /* Action button styling */
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
        .btn-icon i {
            font-size: 18px;
        }
        
        /* Checkbox centering */
        .form-check {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
        
        /* Table cell vertical alignment */
        .table td, .table th {
            vertical-align: middle;
        }
        
        /* Car image styling */
        .car-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            background-color: #f8f9fa;
        }
        .car-image-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 24px;
        }
    </style>
</head>

<!-- Custom Data Table -->
<div class="custom-datatable-filter">
    <table class="table datatable align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicles.general.delete')
                <th class="no-sort" width="50" style="text-align: center;">
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>VÉHICULE</th>
                <th>IMMATRICULATION</th>
                <th>PRIX/JOUR</th>
                <th>KILOMÉTRAGE</th>
                <th>STATUT</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicles.general.view', 'vehicles.general.edit', 'vehicles.general.delete'])
                <th width="80" style="text-align: center;">ACTIONS</th>
                @endcanany
            </tr>
        </thead>

        <tbody>
            @forelse($vehicles as $vehicle)
                @php
                    // Get car title using the correct relationship name
                    $brandName = optional($vehicle->model?->brand)->name;
                    $modelName = optional($vehicle->model)->name;

                    $carTitle = trim(($brandName ? $brandName . ' ' : '') . ($modelName ?? ''));
                    if ($carTitle === '') {
                        $carTitle = $vehicle->registration_number;
                    }

                    // Get photo URL from media library
                    $photoUrl = $vehicle->getMainPhotoUrlAttribute();
                    
                    // Status
                    $status = $vehicle->status;
                    $statusLabel = match ($status) {
                        'available' => 'Disponible',
                        'unavailable' => 'Indisponible',
                        'maintenance' => 'Maintenance',
                        'sold' => 'Vendu',
                        'booked' => 'Réservé',
                        default => '—',
                    };
                    $statusDot = match ($status) {
                        'available' => 'text-success',
                        'unavailable', 'sold' => 'text-danger',
                        'maintenance' => 'text-warning',
                        'booked' => 'text-info',
                        default => 'text-muted',
                    };
                    $statusBadgeClass = match ($status) {
                        'available' => 'badge-success',
                        'unavailable', 'sold' => 'badge-danger',
                        'maintenance' => 'badge-warning',
                        'booked' => 'badge-info',
                        default => 'badge-secondary',
                    };

                    // Price and mileage formatting
                    $daily = $vehicle->daily_rate !== null ? number_format((float) $vehicle->daily_rate, 2) : null;
                    $mileage = $vehicle->current_mileage !== null ? number_format((int) $vehicle->current_mileage) : null;
                    
                    $canView = auth()->user()->can('vehicles.general.view');
                @endphp

                <tr>
                    {{-- Case à cocher - visible seulement si permission DELETE --}}
                    @can('vehicles.general.delete')
                    <td style="text-align: center; vertical-align: middle;">
                        <div class="form-check form-check-md">
                            <input class="form-check-input row-check vehicle-checkbox" type="checkbox" value="{{ $vehicle->id }}">
                        </div>
                    </td>
                    @endcan

                    <td style="vertical-align: middle;">
                        <div class="d-flex align-items-center">
                            <img src="{{ $photoUrl }}" 
                                 alt="{{ $carTitle }}" 
                                 class="car-image me-2"
                                 onerror="this.onerror=null; this.src='/assets/place-holder.webp';">
                            <div>
                                <h6 class="mb-1">
                                    {{-- Lien vers show - contrôlé par permission VIEW --}}
                                    @if($canView)
                                        <a href="{{ route('backoffice.vehicles.show', $vehicle) }}" class="fs-14 fw-semibold">
                                            {{ $carTitle }}
                                        </a>
                                    @else
                                        <span class="fs-14 fw-semibold">{{ $carTitle }}</span>
                                    @endif
                                </h6>
                                <p class="mb-0 text-muted small">
                                    {{ $vehicle->color ?? 'Couleur N/C' }}
                                    @if ($vehicle->year)
                                        <span class="ms-2">• {{ $vehicle->year }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </td>

                    <td style="vertical-align: middle;">
                        <span class="fw-medium">{{ $vehicle->registration_number }}</span>
                        @if($vehicle->registration_city)
                            <br><small class="text-muted">{{ $vehicle->registration_city }}</small>
                        @endif
                    </td>

                    <td style="vertical-align: middle;">
                        <p class="fs-14 fw-semibold text-success mb-0">
                            @if ($daily !== null)
                                {{ $daily }} MAD
                            @else
                                —
                            @endif
                        </p>
                    </td>

                    <td style="vertical-align: middle;">
                        <p class="text-gray-9 mb-0">
                            @if ($mileage !== null)
                                {{ $mileage }} km
                            @else
                                —
                            @endif
                        </p>
                    </td>

                    <td style="vertical-align: middle;">
                        <span class="badge {{ $statusBadgeClass }}">
                            <i class="ti ti-point-filled {{ $statusDot }} me-1"></i>{{ $statusLabel }}
                        </span>
                    </td>

                    {{-- Actions - visible seulement si au moins une permission d'action --}}
                    @canany(['vehicles.general.view', 'vehicles.general.edit', 'vehicles.general.delete'])
                    <td style="text-align: center; vertical-align: middle;">
                        @include('backoffice.vehicles.partials._actions', [
                            'vehicle' => $vehicle,
                            'carTitle' => $carTitle
                        ])
                    </td>
                    @endcanany
                </tr>
            @empty
                @php
                    $emptyColspan = 5
                        + (auth()->user()->can('vehicles.general.delete') ? 1 : 0)
                        + (auth()->user()->canAny(['vehicles.general.view', 'vehicles.general.edit', 'vehicles.general.delete']) ? 1 : 0);
                @endphp
                <tr>
                    <td colspan="{{ $emptyColspan }}" class="text-center py-5">
                        <div class="empty-state">
                            <i class="ti ti-car-off"></i>
                            <h5 class="mb-2">Aucun véhicule trouvé</h5>
                            @can('vehicles.general.create')
                                <p class="text-muted mb-3">Commencez par ajouter un véhicule</p>
                                <a href="{{ route('backoffice.vehicles.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-2"></i>Ajouter un véhicule
                                </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('vehicles.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkbox
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.vehicle-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }

        // Individual checkbox - update select all state
        const rowCheckboxes = document.querySelectorAll('.vehicle-checkbox');
        if (rowCheckboxes.length > 0 && selectAll) {
            rowCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
                    const anyChecked = Array.from(rowCheckboxes).some(c => c.checked);
                    selectAll.checked = allChecked;
                    selectAll.indeterminate = !allChecked && anyChecked;
                });
            });
        }
    });
</script>
@endcan