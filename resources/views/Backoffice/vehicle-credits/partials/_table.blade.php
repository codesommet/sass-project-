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
    .badge-remaining { 
        background: #fff3e0; 
        color: #e65100; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
    }
    .badge-defaulted { 
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
    .progress {
        height: 6px;
        border-radius: 3px;
    }
    .progress-bar {
        border-radius: 3px;
    }
</style>

<div class="table-responsive">
    <table class="table datatable align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-credits.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                
                {{-- VEHICLE COLUMN FOR GLOBAL VIEW --}}
                @if(!isset($vehicle) || !$vehicle)
                    <th>Véhicule</th>
                @endif
                
                <th>N° Crédit</th>
                <th>Créancier</th>
                <th>Total</th>
                <th>Restant</th>
                <th>Mensualité</th>
                <th>Progression</th>
                <th>Statut</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-credits.general.view', 'vehicle-credits.general.edit', 'vehicle-credits.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($credits as $credit)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-credits.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input credit-checkbox" type="checkbox" value="{{ $credit->id }}">
                    </div>
                </td>
                @endcan
                
                {{-- SHOW VEHICLE COLUMN FOR GLOBAL VIEW --}}
                @if(!isset($vehicle) || !$vehicle)
                    <td>
                        {{-- Lien vers véhicule - contrôlé par permission VIEW sur véhicules --}}
                        @can('vehicles.general.view')
                            <a href="{{ route('backoffice.vehicles.show', $credit->vehicle_id) }}" class="fw-medium">
                                {{ $credit->vehicle->registration_number ?? 'N/C' }}
                            </a>
                        @else
                            <span class="fw-medium">{{ $credit->vehicle->registration_number ?? 'N/C' }}</span>
                        @endcan
                        @if($credit->vehicle)
                            <br><small class="text-muted">{{ $credit->vehicle->brand->name ?? '' }} {{ $credit->vehicle->model->name ?? '' }}</small>
                        @endif
                    </td>
                @endif
                
                <td>
                    {{-- Lien vers show - contrôlé par permission VIEW --}}
                    @can('vehicle-credits.general.view')
                        <a href="{{ route('backoffice.vehicle-credits.show', $credit->id) }}" class="fw-medium">
                            {{ $credit->credit_number }}
                        </a>
                    @else
                        <span class="fw-medium">{{ $credit->credit_number }}</span>
                    @endcan
                    <br><small class="text-muted">{{ $credit->start_date->format('d/m/Y') }}</small>
                </td>
                
                <td>{{ $credit->creditor_name }}</td>
                
                <td><span class="badge-amount">{{ number_format($credit->total_amount, 2, ',', ' ') }} DH</span></td>
                
                <td><span class="badge-remaining">{{ number_format($credit->remaining_amount, 2, ',', ' ') }} DH</span></td>
                
                <td>{{ number_format($credit->monthly_payment, 2, ',', ' ') }} DH</td>
                
                <td style="min-width: 120px;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1">
                            <div class="progress-bar bg-{{ $credit->progress_percentage >= 75 ? 'success' : ($credit->progress_percentage >= 50 ? 'info' : ($credit->progress_percentage >= 25 ? 'warning' : 'danger')) }}" 
                                 style="width: {{ $credit->progress_percentage }}%"></div>
                        </div>
                        <small>{{ $credit->progress_percentage }}%</small>
                    </div>
                    <small class="text-muted">{{ $credit->paid_months }}/{{ $credit->duration_months }} mois</small>
                </td>
                
                <td>
                    @php
                        $statusClass = match($credit->status) {
                            'active' => 'success',
                            'completed' => 'info',
                            'defaulted' => 'danger',
                            'pending' => 'warning',
                            default => 'secondary'
                        };
                        $statusText = match($credit->status) {
                            'active' => 'Actif',
                            'completed' => 'Terminé',
                            'defaulted' => 'En défaut',
                            'pending' => 'En attente',
                            default => $credit->status
                        };
                    @endphp
                    <span class="badge bg-{{ $statusClass }} text-white">
                        {{ $statusText }}
                    </span>
                    @if($credit->late_payments > 0)
                        <br><small class="text-danger">{{ $credit->late_payments }} retard(s)</small>
                    @endif
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-credits.general.view', 'vehicle-credits.general.edit', 'vehicle-credits.general.delete'])
                <td class="text-center">
                    @include('Backoffice.vehicle-credits.partials._actions', ['credit' => $credit])
                </td>
                @endcanany
            </tr>
            @empty
            @php
                $emptyColspan = 7
                    + (!isset($vehicle) || !$vehicle ? 1 : 0)
                    + (auth()->user()->can('vehicle-credits.general.delete') ? 1 : 0)
                    + (auth()->user()->canAny(['vehicle-credits.general.view', 'vehicle-credits.general.edit', 'vehicle-credits.general.delete']) ? 1 : 0);
            @endphp
            <tr>
                <td colspan="{{ $emptyColspan }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-credit-card-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun crédit trouvé</h5>
                        @can('vehicle-credits.general.create')
                            <p class="text-muted mb-3">Commencez par ajouter un crédit</p>
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#add_credit">
                                <i class="ti ti-plus me-2"></i>Ajouter un crédit
                            </button>
                        @endcan
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('vehicle-credits.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.credit-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan