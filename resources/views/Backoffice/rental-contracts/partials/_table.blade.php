<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('rental-contracts.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>N° Contrat</th>
                <th>Client</th>
                <th>Véhicule</th>
                <th>Dates</th>
                <th>Montant</th>
                <th>Statut</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['rental-contracts.general.view', 'rental-contracts.general.edit', 'rental-contracts.general.delete'])
                <th width="120">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($contracts as $contract)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('rental-contracts.general.delete')
                <td class="text-center">
                    <div class="form-check">
                        <input class="form-check-input contract-checkbox" type="checkbox" value="{{ $contract->id }}">
                    </div>
                </td>
                @endcan
                
                <td>
                    <div class="contract-info">
                        {{-- Lien vers show - contrôlé par permission VIEW --}}
                        @can('rental-contracts.general.view')
                            <a href="{{ route('backoffice.rental-contracts.show', $contract) }}" class="fw-medium">
                                {{ $contract->contract_number }}
                            </a>
                        @else
                            <span class="fw-medium">{{ $contract->contract_number }}</span>
                        @endcan
                        <br>
                        <small>
                            <i class="ti ti-calendar me-1"></i>{{ $contract->created_at->format('d/m/Y') }}
                        </small>
                    </div>
                </td>
                
                <td>
                    <div class="contract-info">
                        @if($contract->primaryClient)
                            {{-- Lien vers client - contrôlé par permission VIEW sur clients --}}
                            @can('clients.general.view')
                                <a href="{{ route('backoffice.clients.show', $contract->primary_client_id) }}" class="fw-medium">
                                    {{ $contract->primaryClient->first_name ?? '' }} {{ $contract->primaryClient->last_name ?? '' }}
                                </a>
                            @else
                                <span class="fw-medium">{{ $contract->primaryClient->first_name ?? '' }} {{ $contract->primaryClient->last_name ?? '' }}</span>
                            @endcan
                            <br>
                            <small>
                                <i class="ti ti-phone me-1"></i>{{ $contract->primaryClient->phone ?? 'N/C' }}
                            </small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </td>
                
                <td>
                    <div class="contract-info">
                        @if($contract->vehicle)
                            {{-- Lien vers véhicule - contrôlé par permission VIEW sur véhicules --}}
                            @can('vehicles.general.view')
                                <a href="{{ route('backoffice.vehicles.show', $contract->vehicle_id) }}" class="fw-medium">
                                    {{ $contract->vehicle->registration_number ?? 'N/C' }}
                                </a>
                            @else
                                <span class="fw-medium">{{ $contract->vehicle->registration_number ?? 'N/C' }}</span>
                            @endcan
                            <br>
                            <small>{{ $contract->vehicle->model->name ?? '' }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </td>
                
                <td>
                    <div class="contract-info">
                        <span><i class="ti ti-calendar-check me-1"></i>{{ $contract->formatted_start_date }}</span>
                        <br>
                        <span><i class="ti ti-calendar-x me-1"></i>{{ $contract->formatted_end_date }}</span>
                    </div>
                </td>
                
                <td>
                    <span class="amount-badge">{{ $contract->formatted_total_amount }}</span>
                    @if($contract->deposit_amount)
                        <br><small class="text-muted">Caution : {{ $contract->formatted_deposit }}</small>
                    @endif
                </td>
                
                <td>
                    <span class="badge badge-{{ str_replace('_', '-', $contract->status) }}">
                        {{ $contract->status_text }}
                    </span>
                    <br>
                    <small>
                        <i class="ti ti-clock me-1"></i>{{ $contract->acceptance_text }}
                    </small>
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['rental-contracts.general.view', 'rental-contracts.general.edit', 'rental-contracts.general.delete'])
                <td class="text-center">
                    @include('backoffice.rental-contracts.partials._actions', ['contract' => $contract])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('rental-contracts.general.delete')
                <td></td>
                @endcan
                <td colspan="{{ (auth()->user()->can('rental-contracts.general.delete') ? 7 : 6) }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-file-text-off fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucun contrat trouvé</h5>
                        @can('rental-contracts.general.create')
                            <p class="text-muted mb-3">Commencez par créer un nouveau contrat</p>
                            <a href="{{ route('backoffice.rental-contracts.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouveau contrat
                            </a>
                        @endcan
                    </div>
                </td>
                @canany(['rental-contracts.general.view', 'rental-contracts.general.edit', 'rental-contracts.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('rental-contracts.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.contract-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
            
            // Update select all when individual checkboxes change
            document.querySelectorAll('.contract-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {
                    const allCheckboxes = document.querySelectorAll('.contract-checkbox');
                    const allChecked = Array.from(allCheckboxes).every(c => c.checked);
                    const anyChecked = Array.from(allCheckboxes).some(c => c.checked);
                    
                    selectAll.checked = allChecked;
                    selectAll.indeterminate = anyChecked && !allChecked;
                });
            });
        }
    });
</script>
@endcan