<?php $page = 'rental-contract-details'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-weight: 500;
        margin-bottom: 1rem;
    }
    .badge-draft { background: #e2e3e5; color: #383d41; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-accepted { background: #cce5ff; color: #004085; }
    .badge-in_progress { background: #d1ecf1; color: #0c5460; }
    .badge-completed { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
    .amount-display {
        font-size: 1.5rem;
        font-weight: 600;
        color: #198754;
    }
    .wizard-nav {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .wizard-nav .nav-item {
        flex: 1;
        min-width: 150px;
    }
    .wizard-nav .nav-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        color: #6c757d;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s;
        border: 1px solid #dee2e6;
        cursor: pointer;
    }
    .wizard-nav .nav-link i {
        margin-right: 8px;
        font-size: 1.2rem;
    }
    .wizard-nav .nav-link.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    .info-panel {
        display: none;
    }
    .info-panel.active {
        display: block;
    }
    
    /* Client badges */
    .client-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-left: 8px;
    }
    .client-badge.primary {
        background: #cce5ff;
        color: #004085;
    }
    .client-badge.secondary {
        background: #d4edda;
        color: #155724;
    }
    
    .client-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
    }
    .client-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .client-card .client-role {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* PDF Preview Styles */
    .pdf-preview-container {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 20px;
    }
    
    .pdf-preview-header {
        background: #f8f9fa;
        padding: 12px 20px;
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .pdf-preview {
        width: 100%;
        height: 600px;
        border: none;
    }
    
    /* Photo Gallery Styles */
    .photo-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        padding: 20px;
    }
    
    .photo-item {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s;
        cursor: pointer;
    }
    
    .photo-item:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .photo-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
    
    .photo-caption {
        padding: 10px;
        background: #f8f9fa;
        text-align: center;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    /* Modal Styles */
    .modal-fullscreen {
        max-width: 90vw;
        max-height: 90vh;
    }
    
    .modal-fullscreen img {
        width: 100%;
        height: auto;
        max-height: 80vh;
        object-fit: contain;
    }
    
    /* Transaction styles */
    .transaction-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }
    .transaction-card .amount-large {
        font-size: 2rem;
        font-weight: 700;
    }
    .transaction-link {
        color: white;
        text-decoration: underline;
    }
    .transaction-link:hover {
        color: #f8f9fa;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.rental-contracts.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div class="d-flex gap-2">
                        {{-- Bouton PDF - contrôlé par permission VIEW --}}
                        @can('rental-contracts.general.view')
                        <a href="{{ route('backoffice.contracts.pdf.single', $rentalContract->id) }}" class="btn btn-danger" target="_blank">
                            <i class="ti ti-file-text me-1"></i>Télécharger PDF
                        </a>
                        @endcan
                        
                        {{-- WhatsApp Button for Primary Client --}}
                        @can('rental-contracts.general.view')
                            @php
                                $primaryClient = $rentalContract->clients()->wherePivot('role', 'primary')->first();
                            @endphp
                            @if($primaryClient && $primaryClient->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $primaryClient->phone) }}?text={{ urlencode('Bonjour, voici votre contrat #' . $rentalContract->contract_number . ' : ' . route('backoffice.contracts.pdf.single', $rentalContract->id, true)) }}" 
                               class="btn btn-success" target="_blank">
                                <i class="ti ti-brand-whatsapp me-1"></i>WhatsApp
                            </a>
                            @endif
                        @endcan
                        
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @can('rental-contracts.general.edit')
                        <a href="{{ route('backoffice.rental-contracts.edit', $rentalContract) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Modifier
                        </a>
                        @endcan
                    </div>
                </div>

                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3" style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-file-text"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">{{ $rentalContract->contract_number }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Créé le {{ $rentalContract->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-{{ str_replace('_', '-', $rentalContract->status) }} fs-6 p-2">
                                    {{ $rentalContract->status_text }}
                                </span>
                                <span class="badge {{ $rentalContract->acceptance_badge_class }} fs-6 p-2">
                                    {{ $rentalContract->acceptance_text }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Card -->
                @if(isset($transaction) && $transaction)
                <div class="transaction-card mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-white mb-2">Transaction financière</h5>
                            <div class="amount-large">
                                {{ $transaction->formatted_amount }}
                            </div>
                            <p class="mb-0 mt-2">
                                <i class="ti ti-calendar me-1"></i> {{ $transaction->formatted_date }}
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-white text-dark mb-2">{{ $transaction->type_text }}</span>
                            <br>
                            <a href="{{ route('backoffice.finance.transactions.show', $transaction) }}" class="transaction-link">
                                <i class="ti ti-eye me-1"></i>Voir la transaction
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Info Tabs -->
                <div class="wizard-nav">
                    <div class="nav-item">
                        <a class="nav-link active" data-panel="1">
                            <i class="ti ti-user"></i>
                            Clients
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="2">
                            <i class="ti ti-car"></i>
                            Véhicule
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="3">
                            <i class="ti ti-calendar"></i>
                            Dates & Lieux
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="4">
                            <i class="ti ti-currency-dollar"></i>
                            Tarifs
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="5">
                            <i class="ti ti-photo"></i>
                            Photos
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="6">
                            <i class="ti ti-file-text"></i>
                            Aperçu PDF
                        </a>
                    </div>
                </div>

                <!-- Panel 1: Clients -->
                <div class="info-panel active" id="panel1">
                    <div class="row">
                        @php
                            $clients = $rentalContract->clients()->orderBy('pivot_order')->get();
                        @endphp

                        @foreach($clients as $client)
                            <div class="col-md-6">
                                <div class="client-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="client-role text-muted">
                                            @if($client->pivot->role == 'primary')
                                                <span class="client-badge primary">Client principal</span>
                                            @else
                                                <span class="client-badge secondary">Client secondaire</span>
                                            @endif
                                        </span>
                                        @can('clients.general.view')
                                        <a href="{{ route('backoffice.clients.show', $client->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @endcan
                                    </div>
                                    
                                    <h5 class="mb-2">{{ $client->first_name }} {{ $client->last_name }}</h5>
                                    
                                    @if($client->phone)
                                    <div class="mb-1">
                                        <i class="ti ti-phone me-2 text-muted"></i>
                                        <a href="tel:{{ $client->phone }}">{{ $client->phone }}</a>
                                        @can('rental-contracts.general.view')
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $client->phone) }}" class="ms-2 text-success" target="_blank">
                                            <i class="ti ti-brand-whatsapp"></i>
                                        </a>
                                        @endcan
                                    </div>
                                    @endif
                                    
                                    @if($client->email)
                                    <div class="mb-1">
                                        <i class="ti ti-mail me-2 text-muted"></i>
                                        <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                                    </div>
                                    @endif
                                    
                                    @if($client->cin_number)
                                    <div class="mb-1">
                                        <i class="ti ti-id me-2 text-muted"></i>
                                        CIN: {{ $client->cin_number }}
                                    </div>
                                    @endif
                                    
                                    @if($client->driving_license_number)
                                    <div class="mb-1">
                                        <i class="ti ti-license me-2 text-muted"></i>
                                        Permis: {{ $client->driving_license_number }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Panel 2: Véhicule -->
                <div class="info-panel" id="panel2">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-label">Véhicule</div>
                                    <div class="info-value">
                                        @can('vehicles.general.view')
                                            <a href="{{ route('backoffice.vehicles.show', $rentalContract->vehicle_id) }}" class="fw-medium">
                                                {{ $rentalContract->vehicle->registration_number ?? 'N/A' }}
                                            </a>
                                        @else
                                            <span>{{ $rentalContract->vehicle->registration_number ?? 'N/A' }}</span>
                                        @endcan
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Modèle</div>
                                    <div class="info-value">
                                        @if($rentalContract->vehicle && $rentalContract->vehicle->model)
                                            {{ $rentalContract->vehicle->model->brand->name ?? '' }} {{ $rentalContract->vehicle->model->name ?? '' }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Année</div>
                                    <div class="info-value">{{ $rentalContract->vehicle->year ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Couleur</div>
                                    <div class="info-value">{{ ucfirst($rentalContract->vehicle->color ?? 'N/A') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Kilométrage</div>
                                    <div class="info-value">{{ number_format($rentalContract->vehicle->current_mileage ?? 0) }} km</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Statut</div>
                                    <div class="info-value">
                                        <span class="badge {{ $rentalContract->vehicle->status_badge_class ?? 'badge-secondary' }}">
                                            {{ $rentalContract->vehicle->status_text ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Dates & Lieux -->
                <div class="info-panel" id="panel3">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-calendar me-2"></i>
                                        Dates et heures
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Début prévu</div>
                                            <div class="info-value">
                                                {{ $rentalContract->formatted_start_date }} à {{ $rentalContract->formatted_start_time }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Fin prévue</div>
                                            <div class="info-value">
                                                {{ $rentalContract->formatted_end_date }} à {{ $rentalContract->formatted_end_time }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Durée prévue</div>
                                            <div class="info-value">{{ $rentalContract->planned_days }} jour(s)</div>
                                        </div>
                                        @if($rentalContract->actual_start_at)
                                        <div class="col-md-6">
                                            <div class="info-label">Début réel</div>
                                            <div class="info-value">{{ $rentalContract->actual_start_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        @endif
                                        @if($rentalContract->actual_end_at)
                                        <div class="col-md-6">
                                            <div class="info-label">Fin réelle</div>
                                            <div class="info-value">{{ $rentalContract->actual_end_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-map-pin me-2"></i>
                                        Lieux
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Prise en charge</div>
                                            <div class="info-value">{{ $rentalContract->pickup_location }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Restitution</div>
                                            <div class="info-value">{{ $rentalContract->dropoff_location }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 4: Tarifs -->
                <div class="info-panel" id="panel4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-currency-dollar me-2"></i>
                                        Informations financières
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Tarif journalier</div>
                                            <div class="info-value">{{ $rentalContract->formatted_daily_rate }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Remise</div>
                                            <div class="info-value">{{ number_format($rentalContract->discount_amount, 2) }} MAD</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Total</div>
                                            <div class="info-value amount-display">{{ $rentalContract->formatted_total_amount }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Dépôt de garantie</div>
                                            <div class="info-value">{{ $rentalContract->formatted_deposit }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            @if($rentalContract->observations)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-notes me-2"></i>
                                        Observations
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $rentalContract->observations }}</p>
                                </div>
                            </div>
                            @endif

                            @if($rentalContract->cancelled_at)
                            <div class="card mb-4 border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="ti ti-alert-triangle me-2"></i>
                                        Annulation
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label">Date d'annulation</div>
                                            <div class="info-value">{{ $rentalContract->cancelled_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label">Raison</div>
                                            <div class="info-value">{{ $rentalContract->cancellation_reason }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panel 5: Photos -->
                <div class="info-panel" id="panel5">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-photo me-2"></i>
                                Photos du véhicule
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $imageFields = [
                                    'front' => 'Face avant',
                                    'rear' => 'Face arrière',
                                    'left' => 'Côté gauche',
                                    'right' => 'Côté droit',
                                    'dashboard' => 'Tableau de bord',
                                    'odometer' => 'Compteur',
                                    'damage' => 'Dégâts',
                                    'extra' => 'Autre'
                                ];
                                $hasPhotos = false;
                            @endphp

                            <div class="photo-gallery">
                                @foreach($imageFields as $field => $label)
                                    @php
                                        $photoPath = $rentalContract->{$field . '_image'} ?? null;
                                    @endphp
                                    @if($photoPath)
                                        @php $hasPhotos = true; @endphp
                                        <div class="photo-item" onclick="showPhotoModal('{{ asset('storage/'.$photoPath) }}', '{{ $label }}')">
                                            <img src="{{ asset('storage/'.$photoPath) }}" alt="{{ $label }}">
                                            <div class="photo-caption">{{ $label }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            @if(!$hasPhotos)
                                <div class="text-center py-5">
                                    <i class="ti ti-photo-off fs-48 text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucune photo disponible</h5>
                                    <p class="text-muted">Ce contrat n'a pas de photos du véhicule.</p>
                                    @can('rental-contracts.general.edit')
                                    <a href="{{ route('backoffice.rental-contracts.edit', $rentalContract) }}" class="btn btn-primary mt-2">
                                        <i class="ti ti-upload me-1"></i>Ajouter des photos
                                    </a>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panel 6: Aperçu PDF -->
                <div class="info-panel" id="panel6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-file-text me-2"></i>
                                Aperçu PDF
                            </h5>
                            @can('rental-contracts.general.view')
                            <div class="d-flex gap-2">
                                <a href="{{ route('backoffice.contracts.pdf.single', $rentalContract->id) }}" class="btn btn-sm btn-danger" target="_blank">
                                    <i class="ti ti-download me-1"></i>Télécharger
                                </a>
                                @php
                                    $primaryClient = $rentalContract->clients()->wherePivot('role', 'primary')->first();
                                @endphp
                                @if($primaryClient && $primaryClient->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $primaryClient->phone) }}?text={{ urlencode('Bonjour, voici votre contrat #' . $rentalContract->contract_number . ' : ' . route('backoffice.contracts.pdf.single', $rentalContract->id, true)) }}" 
                                   class="btn btn-sm btn-success" target="_blank">
                                    <i class="ti ti-brand-whatsapp me-1"></i>Partager
                                </a>
                                @endif
                            </div>
                            @endcan
                        </div>
                        <div class="card-body">
                            <div class="pdf-preview-container">
                                <div class="pdf-preview-header">
                                    <span><i class="ti ti-file-text text-danger me-2"></i>{{ $rentalContract->contract_number }}.pdf</span>
                                    <span class="text-muted small">{{ $rentalContract->formatted_total_amount }}</span>
                                </div>
                                <iframe src="{{ route('backoffice.contracts.pdf.view', $rentalContract->id) }}" class="pdf-preview"></iframe>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Photo Modal -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Photo du véhicule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalPhoto" src="" alt="Photo du véhicule" style="max-width: 100%; max-height: 80vh;">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Panel Navigation
    const panels = document.querySelectorAll('.nav-link[data-panel]');
    const infoPanels = document.querySelectorAll('.info-panel');
    
    function showPanel(panelNumber) {
        infoPanels.forEach(p => p.classList.remove('active'));
        document.getElementById(`panel${panelNumber}`).classList.add('active');
        
        panels.forEach(p => p.classList.remove('active'));
        document.querySelector(`.nav-link[data-panel="${panelNumber}"]`).classList.add('active');
    }

    panels.forEach(panel => {
        panel.addEventListener('click', function(e) {
            e.preventDefault();
            showPanel(this.getAttribute('data-panel'));
        });
    });
});

// Photo Modal Function
function showPhotoModal(imageSrc, caption) {
    const modal = new bootstrap.Modal(document.getElementById('photoModal'));
    document.getElementById('modalPhoto').src = imageSrc;
    document.getElementById('photoModalLabel').textContent = caption;
    modal.show();
}
</script>

@include('backoffice.rental-contracts.partials._modal_delete')
@endsection