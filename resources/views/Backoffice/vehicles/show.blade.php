<?php $page = 'vehicle-details'; ?>

@extends('layout.mainlayout_admin')

@section('content')
<style>
    .equipment-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: #f8f9fa;
        border-radius: 50px;
        margin: 0.25rem;
        font-size: 0.9rem;
    }
    .equipment-badge i {
        margin-right: 0.5rem;
    }
    .equipment-badge.yes {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .equipment-badge.no {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        opacity: 0.7;
    }
    .info-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        height: 100%;
    }
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-size: 1.1rem;
        font-weight: 500;
    }
    .car-image-large {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
        border-radius: 10px;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row">
            <div class="col-lg-12">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.vehicles.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                        <a href="{{ route('backoffice.vehicles.edit', $vehicle) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Modifier
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Header Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3" style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-car"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">{{ $vehicle->registration_number }}</h4>
                                    <p class="mb-0 text-muted">
                                        {{ optional($vehicle->model?->brand)->name ?? 'Marque' }} - {{ $vehicle->model->name ?? 'Modèle' }} ({{ $vehicle->year }})
                                    </p>
                                </div>
                            </div>
                            <div>
                                @php
                                    $statusColors = [
                                        'available' => 'success',
                                        'unavailable' => 'danger',
                                        'maintenance' => 'warning',
                                        'sold' => 'secondary',
                                        'booked' => 'info'
                                    ];
                                    $statusTexts = [
                                        'available' => 'Disponible',
                                        'unavailable' => 'Indisponible',
                                        'maintenance' => 'En maintenance',
                                        'sold' => 'Vendu',
                                        'booked' => 'Réservé'
                                    ];
                                    $statusColor = $statusColors[$vehicle->status] ?? 'secondary';
                                    $statusText = $statusTexts[$vehicle->status] ?? ucfirst($vehicle->status);
                                @endphp
                                <span class="badge bg-{{ $statusColor }} fs-6 p-2">
                                    {{ $statusText }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    {{-- Left Column - Basic Info --}}
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-info-circle me-2"></i>
                                    Informations de base
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Modèle</div>
                                        <div class="info-value">
                                            {{ $vehicle->model->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Marque</div>
                                        <div class="info-value">
                                            {{ optional($vehicle->model?->brand)->name ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Année</div>
                                        <div class="info-value">{{ $vehicle->year }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Couleur</div>
                                        <div class="info-value">
                                            <span class="badge" style="background-color: {{ $vehicle->color }}; width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;"></span>
                                            {{ ucfirst($vehicle->color) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Immatriculation</div>
                                        <div class="info-value">{{ $vehicle->registration_number }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Ville d'immatriculation</div>
                                        <div class="info-value">{{ $vehicle->registration_city ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">VIN</div>
                                        <div class="info-value">{{ $vehicle->vin ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Kilométrage actuel</div>
                                        <div class="info-value">{{ number_format($vehicle->current_mileage) }} km</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column - Pricing & Fuel --}}
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-currency-dollar me-2"></i>
                                    Tarifs & Carburant
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Prix / jour</div>
                                        <div class="info-value text-success fw-bold">{{ number_format($vehicle->daily_rate, 2) }} MAD</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Caution</div>
                                        <div class="info-value">{{ $vehicle->deposit_amount ? number_format($vehicle->deposit_amount, 2) . ' MAD' : 'Non définie' }}</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="info-label">Politique carburant</div>
                                        <div class="info-value">
                                            @switch($vehicle->fuel_policy)
                                                @case('full_to_full')
                                                    Plein à plein
                                                    @break
                                                @case('same_to_same')
                                                    Même niveau
                                                    @break
                                                @default
                                                    {{ $vehicle->fuel_policy ?? 'Non définie' }}
                                            @endswitch
                                        </div>
                                    </div>
                                    @if($vehicle->fuel_level_out !== null || $vehicle->fuel_level_in !== null)
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Niveau sortie</div>
                                        <div class="info-value">{{ $vehicle->fuel_level_out !== null ? ($vehicle->fuel_level_out * 100) . '%' : 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="info-label">Niveau retour</div>
                                        <div class="info-value">{{ $vehicle->fuel_level_in !== null ? ($vehicle->fuel_level_in * 100) . '%' : 'N/A' }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Equipment Section - All 7 Options --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-tools me-2"></i>
                            Équipements (7 options)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $equipment = [
                                    'has_gps' => ['label' => 'GPS', 'icon' => 'ti ti-map-pin', 'color' => 'primary'],
                                    'has_air_conditioning' => ['label' => 'Climatisation', 'icon' => 'ti ti-snowflake', 'color' => 'info'],
                                    'has_bluetooth' => ['label' => 'Bluetooth', 'icon' => 'ti ti-bluetooth', 'color' => 'primary'],
                                    'has_baby_seat' => ['label' => 'Siège bébé', 'icon' => 'ti ti-baby-carriage', 'color' => 'success'],
                                    'has_camera_recul' => ['label' => 'Caméra de recul', 'icon' => 'ti ti-camera', 'color' => 'warning'],
                                    'has_regulateur_vitesse' => ['label' => 'Régulateur de vitesse', 'icon' => 'ti ti-speedometer', 'color' => 'danger'],
                                    'has_siege_chauffant' => ['label' => 'Sièges chauffants', 'icon' => 'ti ti-heat', 'color' => 'warning'],
                                ];
                            @endphp
                            
                            @foreach($equipment as $field => $details)
                                <div class="col-md-3 mb-3">
                                    <div class="equipment-badge {{ $vehicle->$field ? 'yes' : 'no' }}">
                                        <i class="{{ $details['icon'] }} text-{{ $details['color'] }}"></i>
                                        {{ $details['label'] }}
                                        @if($vehicle->$field)
                                            <i class="ti ti-check ms-2 text-success"></i>
                                        @else
                                            <i class="ti ti-x ms-2 text-danger"></i>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @php
                            $equipmentCount = 0;
                            foreach(array_keys($equipment) as $field) {
                                if($vehicle->$field) $equipmentCount++;
                            }
                        @endphp
                        <div class="mt-3 text-muted">
                            <i class="ti ti-info-circle me-1"></i>
                            Total: {{ $equipmentCount }}/7 équipements
                        </div>
                    </div>
                </div>

                {{-- Photos Section --}}
                @if($vehicle->photos->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-photo me-2"></i>
                            Photos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($vehicle->photos as $photo)
                            <div class="col-md-3 mb-3">
                                <img src="{{ $photo->getUrl() }}" alt="Vehicle photo" class="img-fluid rounded" style="width: 100%; height: 150px; object-fit: cover;">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- Notes Section --}}
                @if($vehicle->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-notes me-2"></i>
                            Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $vehicle->notes }}</p>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Politique de confidentialité</a>
            <a href="javascript:void(0);" class="ms-4">Conditions d'utilisation</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);" class="text-secondary">Dreams</a></p>
    </div>
</div>
@endsection