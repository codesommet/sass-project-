@props(['vignettes', 'isGlobalView' => false, 'vehicle' => null])

<style>
    .table-responsive, 
    .custom-datatable-filter, 
    .dataTables_wrapper {
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
                @can('vehicle-vignettes.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                
                {{-- VEHICLE COLUMN FOR GLOBAL VIEW --}}
                @if($isGlobalView)
                    <th>Véhicule</th>
                @endif
                
                <th>Date</th>
                <th>Année</th>
                <th>Montant</th>
                <th>Notes</th>
                <th>Date d'ajout</th>
                
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-vignettes.general.view', 'vehicle-vignettes.general.edit', 'vehicle-vignettes.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($vignettes as $vignette)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-vignettes.general.delete')
                <td class="text-center">
                    <div class="form-check form-check-md">
                        <input class="form-check-input vignette-checkbox" type="checkbox" value="{{ $vignette->id }}">
                    </div>
                </td>
                @endcan
                
                {{-- SHOW VEHICLE COLUMN FOR GLOBAL VIEW --}}
                @if($isGlobalView)
                    <td>
                        @can('vehicles.general.view')
                            <a href="{{ route('backoffice.vehicles.show', $vignette->vehicle_id) }}" class="fw-medium">
                                {{ $vignette->vehicle->registration_number ?? 'N/C' }}
                            </a>
                        @else
                            <span class="fw-medium">{{ $vignette->vehicle->registration_number ?? 'N/C' }}</span>
                        @endcan
                        @if($vignette->vehicle)
                            <br><small class="text-muted">{{ $vignette->vehicle->registration_city ?? '' }}</small>
                        @endif
                    </td>
                @endif
                
                <td><span class="fw-medium">{{ $vignette->date->format('d/m/Y') }}</span></td>
                <td><span class="fw-medium">{{ $vignette->year }}</span></td>
                <td><span class="badge-amount">{{ number_format($vignette->amount, 2, ',', ' ') }} DH</span></td>
                <td>
                    @if($vignette->notes)
                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $vignette->notes }}">
                            {{ Str::limit($vignette->notes, 30) }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <small class="fw-medium">{{ $vignette->created_at->format('d/m/Y') }}</small>
                    <br><small class="text-muted">{{ $vignette->created_at->format('H:i') }}</small>
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-vignettes.general.view', 'vehicle-vignettes.general.edit', 'vehicle-vignettes.general.delete'])
                <td class="text-center">
                    @include('Backoffice.vignettes.partials._actions', ['vignette' => $vignette])
                </td>
                @endcanany
            </tr>
            @empty
            @php
                $emptyColspan = 5
                    + ($isGlobalView ? 1 : 0)
                    + (auth()->user()->can('vehicle-vignettes.general.delete') ? 1 : 0)
                    + (auth()->user()->canAny(['vehicle-vignettes.general.view', 'vehicle-vignettes.general.edit', 'vehicle-vignettes.general.delete']) ? 1 : 0);
            @endphp
            <tr>
                <td colspan="{{ $emptyColspan }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-ticket-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune vignette trouvée</h5>
                        
                        @if($isGlobalView)
                            @can('vehicle-vignettes.general.create')
                                <p class="text-muted mb-3">Commencez par ajouter une vignette</p>
                                <a href="{{ route('backoffice.vehicle-documents.vignettes.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-plus me-2"></i>Ajouter une vignette
                                </a>
                            @endcan
                        @else
                            @if(isset($vehicle) && $vehicle)
                                @can('vehicle-vignettes.general.create')
                                    <p class="text-muted mb-3">Commencez par ajouter une vignette</p>
                                    <a href="{{ route('backoffice.vehicles.vignettes.create', ['vehicle' => $vehicle->id]) }}" class="btn btn-primary mt-3">
                                        <i class="ti ti-plus me-2"></i>Ajouter une vignette
                                    </a>
                                @endcan
                            @else
                                @can('vehicles.general.create')
                                    <p class="text-muted mb-3">Aucun véhicule trouvé</p>
                                    <a href="{{ route('backoffice.vehicles.create') }}" class="btn btn-primary mt-3">
                                        <i class="ti ti-plus me-2"></i>Créer un véhicule
                                    </a>
                                @endcan
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
@can('vehicle-vignettes.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.vignette-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan