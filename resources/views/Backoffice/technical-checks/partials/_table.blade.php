<style>
    .table-responsive { 
        overflow: visible !important; 
    }
    .badge-amount { 
        background: #e8f5e9; 
        color: #2e7d32; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
        white-space: nowrap;
    }
    .badge-valid { 
        background: #d4edda; 
        color: #155724; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
    }
    .badge-expiring { 
        background: #fff3cd; 
        color: #856404; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
    }
    .badge-expired { 
        background: #f8d7da; 
        color: #721c24; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
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
    .btn-icon i { 
        font-size: 18px; 
    }
    th:last-child, td:last-child { 
        width: 80px; 
        text-align: center !important; 
        vertical-align: middle !important;
    }
    .form-check {
        display: flex;
        justify-content: center;
        margin: 0;
        padding: 0;
    }
    .dropdown-menu {
        z-index: 9999 !important;
    }
</style>

<div class="table-responsive">
    <table class="table datatable align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-technical-checks.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                
                {{-- VEHICLE COLUMN FOR GLOBAL VIEW --}}
                @if(isset($isGlobalView) && $isGlobalView)
                    <th>Véhicule</th>
                @endif
                
                <th>Date</th>
                <th>Montant</th>
                <th>Prochain contrôle</th>
                <th>Statut</th>
                <th>Notes</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-technical-checks.general.view', 'vehicle-technical-checks.general.edit', 'vehicle-technical-checks.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($technicalChecks as $technicalCheck)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-technical-checks.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input technical-check-checkbox" type="checkbox" value="{{ $technicalCheck->id }}">
                    </div>
                </td>
                @endcan
                
                {{-- SHOW VEHICLE COLUMN FOR GLOBAL VIEW --}}
                @if(isset($isGlobalView) && $isGlobalView)
                    <td>
                        {{-- Lien vers véhicule - contrôlé par permission VIEW sur véhicules --}}
                        @can('vehicles.general.view')
                            <a href="{{ route('backoffice.vehicles.show', $technicalCheck->vehicle_id) }}" class="fw-medium">
                                {{ $technicalCheck->vehicle->registration_number ?? 'N/C' }}
                            </a>
                        @else
                            <span class="fw-medium">{{ $technicalCheck->vehicle->registration_number ?? 'N/C' }}</span>
                        @endcan
                        @if($technicalCheck->vehicle)
                            <br><small class="text-muted">{{ $technicalCheck->vehicle->registration_city ?? '' }}</small>
                        @endif
                    </td>
                @endif
                
                <td><span class="fw-medium">{{ $technicalCheck->formatted_date }}</span></td>
                <td><span class="badge-amount">{{ number_format($technicalCheck->amount, 2, ',', ' ') }} DH</span></td>
                <td><span class="fw-medium">{{ $technicalCheck->formatted_next_date }}</span></td>
                <td>
                    <span class="badge {{ $technicalCheck->status_badge_class }} text-white">
                        {{ $technicalCheck->status_text }}
                    </span>
                </td>
                <td>
                    @if($technicalCheck->notes)
                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $technicalCheck->notes }}">
                            {{ Str::limit($technicalCheck->notes, 20) }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-technical-checks.general.view', 'vehicle-technical-checks.general.edit', 'vehicle-technical-checks.general.delete'])
                <td class="text-center">
                    @include('Backoffice.technical-checks.partials._actions', ['technicalCheck' => $technicalCheck])
                </td>
                @endcanany
            </tr>
            @empty
            @php
                $emptyColspan = 5
                    + (isset($isGlobalView) && $isGlobalView ? 1 : 0)
                    + (auth()->user()->can('vehicle-technical-checks.general.delete') ? 1 : 0)
                    + (auth()->user()->canAny(['vehicle-technical-checks.general.view', 'vehicle-technical-checks.general.edit', 'vehicle-technical-checks.general.delete']) ? 1 : 0);
            @endphp
            <tr>
                <td colspan="{{ $emptyColspan }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-clipboard-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun contrôle technique trouvé</h5>
                        @if(isset($isGlobalView) && $isGlobalView)
                            @can('vehicle-technical-checks.general.create')
                                <p class="text-muted mb-3">Commencez par ajouter un contrôle technique</p>
                                <a href="{{ route('backoffice.vehicle-documents.technical-checks.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-plus me-2"></i>Ajouter un contrôle technique
                                </a>
                            @endcan
                        @else
                            @if(isset($vehicle) && $vehicle)
                                @can('vehicle-technical-checks.general.create')
                                    <p class="text-muted mb-3">Commencez par ajouter un contrôle technique</p>
                                    <a href="{{ route('backoffice.vehicles.technical-checks.create', ['vehicle' => $vehicle->id]) }}" class="btn btn-primary mt-3">
                                        <i class="ti ti-plus me-2"></i>Ajouter un contrôle technique
                                    </a>
                                @endcan
                            @else
                                <p class="text-muted mb-3">Aucun véhicule trouvé</p>
                                <a href="{{ route('backoffice.vehicles.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-plus me-2"></i>Créer un véhicule
                                </a>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('vehicle-technical-checks.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.technical-check-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan