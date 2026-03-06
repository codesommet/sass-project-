@props(['vehicle', 'carTitle'])

<div class="dropdown d-inline-block">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('vehicles.general.view')
        <li>
            <a class="dropdown-item rounded-1"
               href="{{ route('backoffice.vehicles.show', $vehicle) }}">
                <i class="ti ti-eye me-1"></i> Voir détails
            </a>
        </li>
        @endcan

        {{-- Vehicle Follow-up (Suivi Véhicule) - Links to the most recent credit --}}
        @can('vehicle-credits.general.view')
            @php
                // Get the most recent credit for this vehicle
                $latestCredit = $vehicle->credits()->latest()->first();
            @endphp
            
            @if($latestCredit)
                <li>
                    <a class="dropdown-item rounded-1"
                       href="{{ route('backoffice.vehicle-credits.show', ['vehicleCredit' => $latestCredit->id]) }}">
                        <i class="ti ti-credit-card me-1 text-info"></i> Suivi Véhicule
                        @if($latestCredit->status === 'active')
                            <span class="badge bg-success ms-2">Actif</span>
                        @elseif($latestCredit->status === 'completed')
                            <span class="badge bg-info ms-2">Terminé</span>
                        @elseif($latestCredit->status === 'defaulted')
                            <span class="badge bg-danger ms-2">Défaut</span>
                        @endif
                    </a>
                </li>
            @else
                <li>
                    <span class="dropdown-item text-muted rounded-1" style="cursor: not-allowed;">
                        <i class="ti ti-credit-card me-1"></i> Aucun crédit
                    </span>
                </li>
            @endif
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('vehicles.general.edit')
        <li>
            <a class="dropdown-item rounded-1"
               href="{{ route('backoffice.vehicles.edit', $vehicle) }}">
                <i class="ti ti-edit me-1"></i> Modifier
            </a>
        </li>
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('vehicles.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item text-danger rounded-1" 
               href="javascript:void(0);"
               data-bs-toggle="modal" 
               data-bs-target="#delete_vehicle"
               data-delete-action="{{ route('backoffice.vehicles.destroy', $vehicle) }}"
               data-delete-details="Véhicule <strong>{{ $carTitle }}</strong> ({{ $vehicle->registration_number }})">
                <i class="ti ti-trash me-1"></i> Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>