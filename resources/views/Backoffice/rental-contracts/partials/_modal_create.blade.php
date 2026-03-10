<?php $page = 'rental-contracts'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
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
    .fieldset {
        display: none;
    }
    .fieldset.active {
        display: block;
    }
    .upload-area {
        background: #f8f9fa;
        transition: all 0.3s;
        min-height: 150px;
        cursor: pointer;
        border: 2px dashed #dee2e6;
    }
    .upload-area:hover {
        background: #e9ecef;
        border-color: #0d6efd !important;
    }
    .upload-area img {
        object-fit: cover;
        border-radius: 4px;
        width: 100%;
        height: 150px;
    }
    .photo-label {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }
    .client-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .client-badge.primary {
        background: #cce5ff;
        color: #004085;
    }
    .client-badge.secondary {
        background: #d4edda;
        color: #155724;
    }
    .secondary-client-row {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        border: 1px solid #dee2e6;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-12">

                <div class="mb-3">
                    <a href="{{ route('backoffice.rental-contracts.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-file-plus me-2"></i>
                            Nouveau contrat de location
                        </h4>
                    </div>

                    <div class="card-body">
                        <!-- Wizard Navigation -->
                        <div class="wizard-nav">
                            <div class="nav-item">
                                <a class="nav-link active" data-tab="1">
                                    <i class="ti ti-user"></i>
                                    Client & Véhicule
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="2">
                                    <i class="ti ti-calendar"></i>
                                    Dates & Lieux
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="3">
                                    <i class="ti ti-currency-dollar"></i>
                                    Tarifs & Observations
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="4">
                                    <i class="ti ti-photo"></i>
                                    Photos du véhicule
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.rental-contracts.store') }}" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
                            @csrf

                            <!-- Tab 1: Client & Véhicule -->
                            <fieldset class="fieldset active" id="tab1">
                                <div class="row">
                                    <!-- Contract Number (Auto-generated) -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">N° Contrat</label>
                                            <input type="text" class="form-control" value="{{ $contractNumber }}" readonly>
                                            <small class="text-muted">Généré automatiquement</small>
                                        </div>
                                    </div>

                                    <!-- Vehicle -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Véhicule <span class="text-danger">*</span>
                                            </label>
                                            <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un véhicule</option>
                                                @foreach($vehicles as $vehicle)
                                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                        {{ $vehicle->registration_number }} - 
                                                        {{ optional($vehicle->model)->name ?? 'N/C' }}
                                                        ({{ $vehicle->daily_rate }} MAD/jour)
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('vehicle_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Clients Section -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h5 class="mb-3">Clients</h5>
                                        <div class="alert alert-info">
                                            <i class="ti ti-info-circle me-2"></i>
                                            Sélectionnez un client principal et ajoutez des clients secondaires si nécessaire.
                                        </div>
                                    </div>

                                    <!-- Primary Client -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Client principal <span class="text-danger">*</span>
                                            </label>
                                            <select name="clients[primary][client_id]" class="form-select @error('clients.primary.client_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un client principal</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ old('clients.primary.client_id') == $client->id ? 'selected' : '' }}>
                                                        {{ $client->first_name }} {{ $client->last_name }}
                                                        @if($client->phone) - {{ $client->phone }} @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="clients[primary][role]" value="primary">
                                            <input type="hidden" name="clients[primary][order]" value="1">
                                            @error('clients.primary.client_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Secondary Clients Container -->
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6>Clients secondaires</h6>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSecondaryClient()">
                                                <i class="ti ti-plus me-1"></i>Ajouter un client secondaire
                                            </button>
                                        </div>
                                        <div id="secondary-clients-container">
                                            @if(old('clients.secondary'))
                                                @foreach(old('clients.secondary') as $index => $secondary)
                                                    <div class="secondary-client-row">
                                                        <div class="row">
                                                            <div class="col-md-10">
                                                                <select name="clients[secondary][{{ $index }}][client_id]" class="form-select">
                                                                    <option value="">Sélectionner un client</option>
                                                                    @foreach($clients as $client)
                                                                        <option value="{{ $client->id }}" {{ $secondary['client_id'] == $client->id ? 'selected' : '' }}>
                                                                            {{ $client->first_name }} {{ $client->last_name }}
                                                                            @if($client->phone) - {{ $client->phone }} @endif
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="hidden" name="clients[secondary][{{ $index }}][role]" value="secondary">
                                                                <input type="hidden" name="clients[secondary][{{ $index }}][order]" value="{{ $index + 2 }}">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-sm btn-danger w-100" onclick="this.closest('.secondary-client-row').remove()">
                                                                    <i class="ti ti-trash me-1"></i>Supprimer
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-primary next-tab" data-next="2">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- Tab 2: Dates & Lieux -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- Start Date -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Date de début <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" 
                                                   class="form-control @error('start_date') is-invalid @enderror" 
                                                   min="{{ date('Y-m-d') }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Start Time -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Heure de début <span class="text-danger">*</span>
                                            </label>
                                            <input type="time" name="start_time" value="{{ old('start_time', '10:00') }}" 
                                                   class="form-control @error('start_time') is-invalid @enderror" required>
                                            @error('start_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- End Date -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Date de fin <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+1 day'))) }}" 
                                                   class="form-control @error('end_date') is-invalid @enderror" 
                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- End Time -->
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label">Heure de fin</label>
                                            <input type="time" name="end_time" value="{{ old('end_time', '10:00') }}" 
                                                   class="form-control @error('end_time') is-invalid @enderror">
                                            @error('end_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Optionnel (défaut: 10:00)</small>
                                        </div>
                                    </div>

                                    <!-- Pickup Location -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Lieu de prise en charge <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="pickup_location" value="{{ old('pickup_location') }}" 
                                                   class="form-control @error('pickup_location') is-invalid @enderror" 
                                                   placeholder="Adresse de prise en charge" required>
                                            @error('pickup_location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Dropoff Location -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Lieu de restitution <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" name="dropoff_location" value="{{ old('dropoff_location') }}" 
                                                   class="form-control @error('dropoff_location') is-invalid @enderror" 
                                                   placeholder="Adresse de restitution" required>
                                            @error('dropoff_location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="1">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="button" class="btn btn-primary next-tab" data-next="3">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- Tab 3: Tarifs & Observations -->
                            <fieldset class="fieldset" id="tab3">
                                <div class="row">
                                    <!-- Daily Rate (auto-filled from vehicle) -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Tarif journalier (MAD) <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="daily_rate" id="daily_rate" value="{{ old('daily_rate') }}" 
                                                       class="form-control @error('daily_rate') is-invalid @enderror" 
                                                       step="0.01" min="0" required>
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                            @error('daily_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Discount -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Remise (MAD)</label>
                                            <div class="input-group">
                                                <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" 
                                                       class="form-control @error('discount_amount') is-invalid @enderror" 
                                                       step="0.01" min="0">
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                            @error('discount_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Deposit -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Dépôt de garantie (MAD)</label>
                                            <div class="input-group">
                                                <input type="number" name="deposit_amount" value="{{ old('deposit_amount') }}" 
                                                       class="form-control @error('deposit_amount') is-invalid @enderror" 
                                                       step="0.01" min="0">
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                            @error('deposit_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Total Amount (calculated) -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Montant total</label>
                                            <div class="input-group">
                                                <input type="text" id="total_amount_display" class="form-control" readonly value="0.00 MAD">
                                                <input type="hidden" name="total_amount" id="total_amount" value="0">
                                            </div>
                                            <small class="text-muted">Calculé automatiquement</small>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Statut initial</label>
                                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Source -->
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Source</label>
                                            <select name="source" class="form-select @error('source') is-invalid @enderror">
                                                <option value="backoffice" {{ old('source', 'backoffice') == 'backoffice' ? 'selected' : '' }}>Backoffice</option>
                                                <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Site web</option>
                                                <option value="mobile" {{ old('source') == 'mobile' ? 'selected' : '' }}>Application mobile</option>
                                                <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                            @error('source')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Observations -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Observations</label>
                                            <textarea name="observations" class="form-control @error('observations') is-invalid @enderror" 
                                                      rows="4" placeholder="Informations complémentaires...">{{ old('observations') }}</textarea>
                                            @error('observations')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="2">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="button" class="btn btn-primary next-tab" data-next="4">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- Tab 4: Photos du véhicule -->
                            <fieldset class="fieldset" id="tab4">
                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <h5 class="mb-2">Photos du véhicule (8 angles)</h5>
                                        <p class="text-muted small">Téléchargez des photos du véhicule sous différents angles pour documenter son état</p>
                                    </div>
                                    
                                    <!-- Front View -->
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <span class="photo-label">Face avant</span>
                                            <div class="upload-area rounded p-2 text-center" onclick="document.getElementById('front_image').click()">
                                                <img id="front_preview" src="#" alt="Aperçu face avant" style="display: none;">
                                                <div id="front_placeholder" class="py-4">
                                                    <i class="ti ti-photo-plus fs-32 text-muted"></i>
                                                    <p class="text-muted small mb-0">Cliquez pour uploader</p>
                                                </div>
                                            </div>
                                            <input type="file" id="front_image" name="front_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'front_preview', 'front_placeholder')">
                                            <small class="text-muted d-block mt-1">Face avant du véhicule</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Rear View -->
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <span class="photo-label">Face arrière</span>
                                            <div class="upload-area rounded p-2 text-center" onclick="document.getElementById('rear_image').click()">
                                                <img id="rear_preview" src="#" alt="Aperçu face arrière" style="display: none;">
                                                <div id="rear_placeholder" class="py-4">
                                                    <i class="ti ti-photo-plus fs-32 text-muted"></i>
                                                    <p class="text-muted small mb-0">Cliquez pour uploader</p>
                                                </div>
                                            </div>
                                            <input type="file" id="rear_image" name="rear_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'rear_preview', 'rear_placeholder')">
                                            <small class="text-muted d-block mt-1">Face arrière du véhicule</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Left Side View -->
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <span class="photo-label">Côté gauche</span>
                                            <div class="upload-area rounded p-2 text-center" onclick="document.getElementById('left_image').click()">
                                                <img id="left_preview" src="#" alt="Aperçu côté gauche" style="display: none;">
                                                <div id="left_placeholder" class="py-4">
                                                    <i class="ti ti-photo-plus fs-32 text-muted"></i>
                                                    <p class="text-muted small mb-0">Cliquez pour uploader</p>
                                                </div>
                                            </div>
                                            <input type="file" id="left_image" name="left_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'left_preview', 'left_placeholder')">
                                            <small class="text-muted d-block mt-1">Côté gauche du véhicule</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Side View -->
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <span class="photo-label">Côté droit</span>
                                            <div class="upload-area rounded p-2 text-center" onclick="document.getElementById('right_image').click()">
                                                <img id="right_preview" src="#" alt="Aperçu côté droit" style="display: none;">
                                                <div id="right_placeholder" class="py-4">
                                                    <i class="ti ti-photo-plus fs-32 text-muted"></i>
                                                    <p class="text-muted small mb-0">Cliquez pour uploader</p>
                                                </div>
                                            </div>
                                            <input type="file" id="right_image" name="right_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'right_preview', 'right_placeholder')">
                                            <small class="text-muted d-block mt-1">Côté droit du véhicule</small>
                                        </div>
                                    </div>

                                    <!-- Dashboard/Interior View -->
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <span class="photo-label">Tableau de bord</span>
                                            <div class="upload-area rounded p-2 text-center" onclick="document.getElementById('dashboard_image').click()">
                                                <img id="dashboard_preview" src="#" alt="Aperçu tableau de bord" style="display: none;">
                                                <div id="dashboard_placeholder" class="py-4">
                                                    <i class="ti ti-photo-plus fs-32 text-muted"></i>
                                                    <p class="text-muted small mb-0">Cliquez pour uploader</p>
                                                </div>
                                            </div>
                                            <input type="file" id="dashboard_image" name="dashboard_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'dashboard_preview', 'dashboard_placeholder')">
                                            <small class="text-muted d-block mt-1">Tableau de bord / Intérieur</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Odometer Reading -->
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <span class="photo-label">Compteur kilométrique</span>
                                            <div class="upload-area rounded p-2 text-center" onclick="document.getElementById('odometer_image').click()">
                                                <img id="odometer_preview" src="#" alt="Aperçu compteur" style="display: none;">
                                                <div id="odometer_placeholder" class="py-4">
                                                    <i class="ti ti-photo-plus fs-32 text-muted"></i>
                                                    <p class="text-muted small mb-0">Cliquez pour uploader</p>
                                                </div>
                                            </div>
                                            <input type="file" id="odometer_image" name="odometer_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'odometer_preview', 'odometer_placeholder')">
                                            <small class="text-muted d-block mt-1">Lecture du compteur</small>
                                        </div>
                                    </div>

                                    <!-- Damage Photos -->
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <span class="photo-label">Dégâts existants</span>
                                            <div class="upload-area rounded p-2 text-center" onclick="document.getElementById('damage_image').click()">
                                                <img id="damage_preview" src="#" alt="Aperçu dégâts" style="display: none;">
                                                <div id="damage_placeholder" class="py-4">
                                                    <i class="ti ti-photo-plus fs-32 text-muted"></i>
                                                    <p class="text-muted small mb-0">Cliquez pour uploader</p>
                                                </div>
                                            </div>
                                            <input type="file" id="damage_image" name="damage_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'damage_preview', 'damage_placeholder')">
                                            <small class="text-muted d-block mt-1">Dégâts existants (optionnel)</small>
                                        </div>
                                    </div>

                                    <!-- Extra View -->
                                    <div class="col-md-3">
                                        <div class="mb-4">
                                            <span class="photo-label">Autre angle</span>
                                            <div class="upload-area rounded p-2 text-center" onclick="document.getElementById('extra_image').click()">
                                                <img id="extra_preview" src="#" alt="Aperçu autre angle" style="display: none;">
                                                <div id="extra_placeholder" class="py-4">
                                                    <i class="ti ti-photo-plus fs-32 text-muted"></i>
                                                    <p class="text-muted small mb-0">Cliquez pour uploader</p>
                                                </div>
                                            </div>
                                            <input type="file" id="extra_image" name="extra_image" accept="image/*" style="display: none;" onchange="previewImage(this, 'extra_preview', 'extra_placeholder')">
                                            <small class="text-muted d-block mt-1">Autre angle (optionnel)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-2">
                                    <i class="ti ti-info-circle me-2"></i>
                                    Les photos aident à documenter l'état du véhicule avant la location. Maximum 5MB par photo.
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="3">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="ti ti-device-floppy me-1"></i> Créer le contrat
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let secondaryClientCount = {{ old('clients.secondary') ? count(old('clients.secondary')) : 0 }};

document.addEventListener('DOMContentLoaded', function() {
    // Tab Navigation
    const tabs = document.querySelectorAll('.nav-link[data-tab]');
    const fieldsets = document.querySelectorAll('.fieldset');
    
    function showTab(tabNumber) {
        fieldsets.forEach(f => f.classList.remove('active'));
        document.getElementById(`tab${tabNumber}`).classList.add('active');
        
        tabs.forEach(t => t.classList.remove('active'));
        document.querySelector(`.nav-link[data-tab="${tabNumber}"]`).classList.add('active');
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            showTab(this.getAttribute('data-tab'));
        });
    });

    document.querySelectorAll('.next-tab').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            showTab(this.getAttribute('data-next'));
        });
    });

    document.querySelectorAll('.prev-tab').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            showTab(this.getAttribute('data-prev'));
        });
    });

    // Bootstrap validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Auto-fill daily rate from vehicle selection
    const vehicleSelect = document.querySelector('[name="vehicle_id"]');
    const dailyRateInput = document.getElementById('daily_rate');
    
    if (vehicleSelect && dailyRateInput) {
        vehicleSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.text) {
                const match = selectedOption.text.match(/(\d+(?:\.\d+)?)\s*MAD/);
                if (match) {
                    dailyRateInput.value = match[1];
                    calculateTotal();
                }
            }
        });
    }

    // Calculate total amount
    const startDate = document.querySelector('[name="start_date"]');
    const endDate = document.querySelector('[name="end_date"]');
    const discountInput = document.getElementById('discount_amount');
    
    function calculateTotal() {
        if (startDate && startDate.value && endDate && endDate.value && dailyRateInput && dailyRateInput.value) {
            const start = new Date(startDate.value);
            const end = new Date(endDate.value);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            const dailyRate = parseFloat(dailyRateInput.value) || 0;
            const discount = parseFloat(discountInput?.value) || 0;
            const total = (diffDays * dailyRate) - discount;
            
            document.getElementById('total_amount_display').value = total.toFixed(2) + ' MAD';
            document.getElementById('total_amount').value = total.toFixed(2);
        }
    }

    if (startDate) startDate.addEventListener('change', calculateTotal);
    if (endDate) endDate.addEventListener('change', calculateTotal);
    if (dailyRateInput) dailyRateInput.addEventListener('input', calculateTotal);
    if (discountInput) discountInput.addEventListener('input', calculateTotal);
});

// Add secondary client
function addSecondaryClient() {
    secondaryClientCount++;
    const container = document.getElementById('secondary-clients-container');
    const row = document.createElement('div');
    row.className = 'secondary-client-row';
    row.innerHTML = `
        <div class="row">
            <div class="col-md-10">
                <select name="clients[secondary][${secondaryClientCount}][client_id]" class="form-select">
                    <option value="">Sélectionner un client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }} @if($client->phone) - {{ $client->phone }} @endif</option>
                    @endforeach
                </select>
                <input type="hidden" name="clients[secondary][${secondaryClientCount}][role]" value="secondary">
                <input type="hidden" name="clients[secondary][${secondaryClientCount}][order]" value="${secondaryClientCount + 1}">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger w-100" onclick="this.closest('.secondary-client-row').remove()">
                    <i class="ti ti-trash me-1"></i>Supprimer
                </button>
            </div>
        </div>
    `;
    container.appendChild(row);
}

// Image preview function
function previewImage(input, previewId, placeholderId) {
    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            preview.style.width = '100%';
            preview.style.height = '150px';
            placeholder.style.display = 'none';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '#';
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }
}

// File size validation
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const fileSize = this.files[0].size / 1024 / 1024; // in MB
            if (fileSize > 5) {
                alert('Le fichier est trop volumineux. Maximum 5MB.');
                this.value = '';
                const previewId = this.id.replace('_image', '_preview');
                const placeholderId = this.id.replace('_image', '_placeholder');
                document.getElementById(previewId).style.display = 'none';
                document.getElementById(placeholderId).style.display = 'block';
            }
        }
    });
});
</script>
@endsection