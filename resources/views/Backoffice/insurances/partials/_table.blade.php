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
    .badge-expired { 
        background: #f8d7da; 
        color: #721c24; 
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
                @can('vehicle-insurances.general.delete')
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
                <th>Compagnie</th>
                <th>Police</th>
                <th>Montant</th>
                <th>Échéance</th>
                <th>Statut</th>
                <th>Notes</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-insurances.general.view', 'vehicle-insurances.general.edit', 'vehicle-insurances.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($insurances as $insurance)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-insurances.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input insurance-checkbox" type="checkbox" value="{{ $insurance->id }}">
                    </div>
                </td>
                @endcan
                
                {{-- SHOW VEHICLE COLUMN FOR GLOBAL VIEW --}}
                @if(isset($isGlobalView) && $isGlobalView)
                    <td>
                        {{-- Lien vers véhicule - contrôlé par permission VIEW sur véhicules --}}
                        @can('vehicles.general.view')
                            <a href="{{ route('backoffice.vehicles.show', $insurance->vehicle_id) }}" class="fw-medium">
                                {{ $insurance->vehicle->registration_number ?? 'N/C' }}
                            </a>
                        @else
                            <span class="fw-medium">{{ $insurance->vehicle->registration_number ?? 'N/C' }}</span>
                        @endcan
                        @if($insurance->vehicle)
                            <br><small class="text-muted">{{ $insurance->vehicle->registration_city ?? '' }}</small>
                        @endif
                    </td>
                @endif
                
                <td><span class="fw-medium">{{ $insurance->formatted_date }}</span></td>
                <td>{{ $insurance->company_name ?? '—' }}</td>
                <td>{{ $insurance->policy_number ?? '—' }}</td>
                <td><span class="badge-amount">{{ number_format($insurance->amount, 2, ',', ' ') }} DH</span></td>
                <td>{{ $insurance->formatted_next_date }}</td>
                <td>
                    <span class="badge {{ $insurance->status_badge_class }} text-white">
                        {{ $insurance->status_text }}
                    </span>
                </td>
                <td>
                    @if($insurance->notes)
                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $insurance->notes }}">
                            {{ Str::limit($insurance->notes, 20) }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-insurances.general.view', 'vehicle-insurances.general.edit', 'vehicle-insurances.general.delete'])
                <td class="text-center">
                    @include('Backoffice.insurances.partials._actions', ['insurance' => $insurance])
                </td>
                @endcanany
            </tr>
            @empty
            @php
                $emptyColspan = 7
                    + (isset($isGlobalView) && $isGlobalView ? 1 : 0)
                    + (auth()->user()->can('vehicle-insurances.general.delete') ? 1 : 0)
                    + (auth()->user()->canAny(['vehicle-insurances.general.view', 'vehicle-insurances.general.edit', 'vehicle-insurances.general.delete']) ? 1 : 0);
            @endphp
            <tr>
                <td colspan="{{ $emptyColspan }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-shield-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune assurance trouvée</h5>
                        @if(isset($isGlobalView) && $isGlobalView)
                            @can('vehicle-insurances.general.create')
                                <p class="text-muted mb-3">Commencez par ajouter une assurance</p>
                                <a href="{{ route('backoffice.vehicle-documents.insurances.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-plus me-2"></i>Ajouter une assurance
                                </a>
                            @endcan
                        @else
                            @if(isset($vehicle) && $vehicle)
                                @can('vehicle-insurances.general.create')
                                    <p class="text-muted mb-3">Commencez par ajouter une assurance</p>
                                    <a href="{{ route('backoffice.vehicles.insurances.create', ['vehicle' => $vehicle->id]) }}" class="btn btn-primary mt-3">
                                        <i class="ti ti-plus me-2"></i>Ajouter une assurance
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
@can('vehicle-insurances.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.insurance-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan