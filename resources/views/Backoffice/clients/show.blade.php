<?php $page = 'client-details'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .document-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #0d6efd;
    }
    .document-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        cursor: pointer;
        transition: transform 0.3s;
    }
    .document-image:hover {
        transform: scale(1.05);
    }
    .document-icon {
        font-size: 3rem;
        color: #6c757d;
    }
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    .info-value {
        font-weight: 500;
        margin-bottom: 1rem;
    }
    .avatar-large {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .document-thumbnail {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 1px solid #dee2e6;
        transition: transform 0.3s;
    }
    .document-thumbnail:hover {
        transform: scale(1.1);
    }

    /* Blacklist Warning Styles */
    .blacklist-warning {
        background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
        border-left: 5px solid #dc3545;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    
    .status-badge {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }
    
    .blacklist-icon {
        font-size: 3rem;
        color: #dc3545;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row">
            <div class="col-lg-12">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.clients.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div class="d-flex gap-2">
                        {{-- Blacklist/Unblacklist Buttons - contrôlé par permission EDIT --}}
                        @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                            @if($client->status !== 'blacklisted')
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#blacklistClientModal">
                                <i class="ti ti-alert-triangle me-1"></i>
                                Blacklister
                            </button>
                            @else
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#unblacklistClientModal">
                                <i class="ti ti-check me-1"></i>
                                Retirer blacklist
                            </button>
                            @endif
                            
                            <a href="{{ route('backoffice.clients.edit', $client) }}" class="btn btn-primary">
                                <i class="ti ti-edit me-1"></i>
                                Modifier
                            </a>
                        @endif
                    </div>
                </div>

                <!-- BLACKLIST WARNING BANNER -->
                @if($client->status === 'blacklisted')
                <div class="alert alert-danger blacklist-warning alert-dismissible fade show mb-4 border-start border-5 border-danger" role="alert">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-4">
                            <span class="avatar avatar-lg bg-danger text-white rounded-circle">
                                <i class="ti ti-alert-triangle fs-26"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h3 class="alert-heading text-danger mb-0">
                                    <i class="ti ti-shield-x me-2"></i>
                                    ⚠️ CLIENT BLACKLISTÉ ⚠️
                                </h3>
                                <span class="badge bg-danger status-badge">BLACKLISTÉ</span>
                            </div>
                            
                            <p class="mb-3 fs-5">Ce client est actuellement <strong class="text-danger">blacklisté</strong>. Toute interaction doit être effectuée avec une extrême prudence.</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                                        <h6 class="text-danger mb-3">
                                            <i class="ti ti-user me-2"></i>
                                            Informations client
                                        </h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted">Nom complet:</td>
                                                <td><strong class="text-danger">{{ $client->first_name }} {{ $client->last_name }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Téléphone:</td>
                                                <td><strong>{{ $client->phone }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Email:</td>
                                                <td><strong>{{ $client->email ?? 'Non renseigné' }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">CIN:</td>
                                                <td><strong>{{ $client->cin_number ?? 'Non renseigné' }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                                        <h6 class="text-danger mb-3">
                                            <i class="ti ti-history me-2"></i>
                                            Historique blacklist
                                        </h6>
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td class="text-muted">Blacklisté le:</td>
                                                <td><strong>{{ $client->updated_at->format('d/m/Y H:i') }}</strong></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Par:</td>
                                                <td><strong>Système</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            @if($client->notes)
                            <div class="mt-3 p-3 bg-danger bg-opacity-10 rounded">
                                <h6 class="text-danger mb-2">
                                    <i class="ti ti-message me-2"></i>
                                    Raison du blacklistage:
                                </h6>
                                <p class="mb-0 text-dark">{{ $client->notes }}</p>
                            </div>
                            @endif
                            
                            <hr class="border-danger">
                            
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <span class="badge bg-danger p-2">⚠️ ACTION REQUISE</span>
                                <p class="mb-0 small">
                                    <i class="ti ti-alert-triangle me-1"></i>
                                    Vérifiez tous les documents avant toute transaction. Contactez votre responsable si nécessaire.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 text-center">
                                @if($client->getFirstMediaUrl('avatar'))
                                    <img src="{{ $client->getFirstMediaUrl('avatar') }}" 
                                         alt="{{ $client->first_name }} {{ $client->last_name }}" 
                                         class="avatar-large">
                                @else
                                    <div class="avatar-large bg-primary text-white d-flex align-items-center justify-content-center" 
                                         style="font-size: 3rem; border-radius: 50%; margin: 0 auto;">
                                        {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h3 class="mb-2">
                                    {{ $client->first_name }} {{ $client->last_name }}
                                    @if($client->status === 'blacklisted')
                                        <span class="badge bg-danger ms-2">BLACKLISTÉ</span>
                                    @endif
                                </h3>
                                <p class="text-muted mb-1">
                                    <i class="ti ti-mail me-2"></i>{{ $client->email ?? 'Non renseigné' }}
                                </p>
                                <p class="text-muted mb-1">
                                    <i class="ti ti-phone me-2"></i>{{ $client->phone }}
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="ti ti-map-pin me-2"></i>{{ $client->address ?? 'Adresse non renseignée' }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                @php
                                    $statusColors = [
                                        'active' => 'success',
                                        'inactive' => 'secondary',
                                        'blacklisted' => 'danger'
                                    ];
                                    $statusTexts = [
                                        'active' => 'Actif',
                                        'inactive' => 'Inactif',
                                        'blacklisted' => 'Blacklisté'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$client->status] ?? 'secondary' }} fs-6 p-2">
                                    {{ $statusTexts[$client->status] ?? $client->status }}
                                </span>
                                <p class="mt-2 text-muted">
                                    <i class="ti ti-calendar me-1"></i>
                                    Membre depuis {{ $client->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations Personnelles -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-user me-2"></i>
                            Informations personnelles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-label">Agence</div>
                                <div class="info-value">{{ $client->agency->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Date de naissance</div>
                                <div class="info-value">{{ $client->birth_date ? $client->birth_date->format('d/m/Y') : 'Non renseignée' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Nationalité</div>
                                <div class="info-value">{{ $client->nationality ?? 'Non renseignée' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Téléphone</div>
                                <div class="info-value">{{ $client->phone }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                @if($client->address || $client->city || $client->country)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-map-pin me-2"></i>
                            Adresse
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($client->address)
                            <div class="col-md-4">
                                <div class="info-label">Adresse</div>
                                <div class="info-value">{{ $client->address }}</div>
                            </div>
                            @endif
                            @if($client->city)
                            <div class="col-md-3">
                                <div class="info-label">Ville</div>
                                <div class="info-value">{{ $client->city }}</div>
                            </div>
                            @endif
                            @if($client->country)
                            <div class="col-md-3">
                                <div class="info-label">Pays</div>
                                <div class="info-value">{{ $client->country }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Documents d'identité -->
                @if($client->cin_number || $client->getFirstMedia('cin_front') || $client->getFirstMedia('cin_back') ||
                    $client->passport_number || $client->getFirstMedia('passport') ||
                    $client->driving_license_number || $client->getFirstMedia('license'))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-id me-2"></i>
                            Documents d'identité
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- CIN -->
                            @if($client->cin_number || $client->getFirstMedia('cin_front') || $client->getFirstMedia('cin_back'))
                            <div class="col-md-6">
                                <div class="document-card">
                                    <h6 class="mb-3">
                                        <i class="ti ti-card me-2"></i>
                                        Carte d'Identité Nationale (CIN)
                                    </h6>
                                    @if($client->cin_number)
                                    <p><strong>Numéro:</strong> {{ $client->cin_number }}</p>
                                    @endif
                                    @if($client->cin_valid_until)
                                    <p><strong>Valide jusqu'au:</strong> {{ $client->cin_valid_until->format('d/m/Y') }}</p>
                                    @endif
                                    <div class="row mt-3">
                                        @if($client->getFirstMedia('cin_front'))
                                        <div class="col-6 text-center">
                                            <img src="{{ $client->getFirstMediaUrl('cin_front') }}" 
                                                 class="document-thumbnail" 
                                                 alt="Recto CIN"
                                                 onclick="openImageModal('{{ $client->getFirstMediaUrl('cin_front') }}', 'Recto CIN')">
                                            <p class="mt-1"><small>Recto</small></p>
                                        </div>
                                        @endif
                                        @if($client->getFirstMedia('cin_back'))
                                        <div class="col-6 text-center">
                                            <img src="{{ $client->getFirstMediaUrl('cin_back') }}" 
                                                 class="document-thumbnail" 
                                                 alt="Verso CIN"
                                                 onclick="openImageModal('{{ $client->getFirstMediaUrl('cin_back') }}', 'Verso CIN')">
                                            <p class="mt-1"><small>Verso</small></p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Passeport -->
                            @if($client->passport_number || $client->getFirstMedia('passport'))
                            <div class="col-md-6">
                                <div class="document-card">
                                    <h6 class="mb-3">
                                        <i class="ti ti-book me-2"></i>
                                        Passeport
                                    </h6>
                                    @if($client->passport_number)
                                    <p><strong>Numéro:</strong> {{ $client->passport_number }}</p>
                                    @endif
                                    @if($client->passport_issue_date)
                                    <p><strong>Délivré le:</strong> {{ $client->passport_issue_date->format('d/m/Y') }}</p>
                                    @endif
                                    @if($client->getFirstMedia('passport'))
                                    <div class="mt-3 text-center">
                                        <img src="{{ $client->getFirstMediaUrl('passport') }}" 
                                             class="document-thumbnail" 
                                             alt="Passeport"
                                             onclick="openImageModal('{{ $client->getFirstMediaUrl('passport') }}', 'Passeport')">
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Permis de Conduire -->
                            @if($client->driving_license_number || $client->getFirstMedia('license'))
                            <div class="col-md-6">
                                <div class="document-card">
                                    <h6 class="mb-3">
                                        <i class="ti ti-car me-2"></i>
                                        Permis de Conduire
                                    </h6>
                                    @if($client->driving_license_number)
                                    <p><strong>Numéro:</strong> {{ $client->driving_license_number }}</p>
                                    @endif
                                    @if($client->driving_license_issue_date)
                                    <p><strong>Délivré le:</strong> {{ $client->driving_license_issue_date->format('d/m/Y') }}</p>
                                    @endif
                                    @if($client->getFirstMedia('license'))
                                    <div class="mt-3 text-center">
                                        <img src="{{ $client->getFirstMediaUrl('license') }}" 
                                             class="document-thumbnail" 
                                             alt="Permis de Conduire"
                                             onclick="openImageModal('{{ $client->getFirstMediaUrl('license') }}', 'Permis de Conduire')">
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notes -->
                @if($client->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-notes me-2"></i>
                            Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $client->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Blacklist History (if blacklisted) -->
                @if($client->status === 'blacklisted' && $client->notes)
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0 text-white">
                            <i class="ti ti-history me-2"></i>
                            Historique du blacklistage
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <span class="badge bg-danger rounded-circle p-2">
                                        <i class="ti ti-alert-triangle text-white"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6>Client blacklisté</h6>
                                    <p class="text-muted mb-1">{{ $client->updated_at->format('d/m/Y H:i') }}</p>
                                    <p>{{ $client->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<!-- Blacklist Client Modal -->
<div class="modal fade" id="blacklistClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Blacklister le client
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('backoffice.clients.add-to-blacklist', $client) }}" method="POST">
                @csrf
                {{-- REMOVE @method('PATCH') - NOT NEEDED FOR POST --}}
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="ti ti-alert-triangle fs-48 text-warning mb-3"></i>
                        <h5 class="mb-2">Confirmation de blacklistage</h5>
                        <p>Vous êtes sur le point de blacklister le client <strong>{{ $client->first_name }} {{ $client->last_name }}</strong>.</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Attention !</strong> Cette action aura les conséquences suivantes :
                        <ul class="mt-2 mb-0">
                            <li>Le client ne pourra plus effectuer de réservations</li>
                            <li>Un avertissement apparaîtra dans toutes les agences</li>
                            <li>Cette action est réversible</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Raison du blacklistage <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required 
                                  placeholder="Expliquez la raison du blacklistage..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ti ti-alert-triangle me-1"></i>
                        Confirmer le blacklistage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unblacklist Client Modal -->
<div class="modal fade" id="unblacklistClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">
                    <i class="ti ti-check me-2"></i>
                    Retirer de la blacklist
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('backoffice.clients.remove-from-blacklist', $client) }}" method="POST">
                @csrf
                @method('DELETE') {{-- This is correct for DELETE method --}}
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="ti ti-check-circle fs-48 text-success mb-3"></i>
                        <h5 class="mb-2">Confirmation de retrait</h5>
                        <p>Vous êtes sur le point de retirer le client <strong>{{ $client->first_name }} {{ $client->last_name }}</strong> de la blacklist.</p>
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="ti ti-info-circle me-2"></i>
                        Le client pourra à nouveau effectuer des réservations normalement.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Raison du retrait (optionnel)</label>
                        <textarea name="unblacklist_reason" class="form-control" rows="3" 
                                  placeholder="Expliquez pourquoi ce client est retiré de la blacklist..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i>
                        Confirmer le retrait
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<script>
    function openImageModal(imageUrl, title) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModalTitle').textContent = title;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            if (alert && !alert.classList.contains('alert-warning')) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
</script>
@endsection