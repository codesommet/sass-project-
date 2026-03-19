<?php $page = 'contract-client-details'; ?>
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
    .badge-primary { background: #cce5ff; color: #004085; }
    .badge-secondary { background: #e2e3e5; color: #383d41; }
    .badge-other { background: #d4edda; color: #155724; }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.contract-clients.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                        <a href="{{ route('backoffice.contract-clients.edit', $contractClient) }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Modifier
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Header Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3" style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-link"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">Relation #{{ $contractClient->id }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        Créé le {{ $contractClient->created_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <span class="badge {{ $contractClient->role_badge_class }} fs-6 p-2">
                                    {{ $contractClient->role_text }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Contract Information -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-file-text me-2"></i>
                                    Contrat
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-label">N° Contrat</div>
                                        <div class="info-value">
                                            {{-- Lien vers contrat - contrôlé par permission VIEW sur contrats --}}
                                            @can('rental-contracts.general.view')
                                                <a href="{{ route('backoffice.rental-contracts.show', $contractClient->rental_contract_id) }}">
                                                    {{ $contractClient->rentalContract->contract_number ?? 'N/A' }}
                                                </a>
                                            @else
                                                {{ $contractClient->rentalContract->contract_number ?? 'N/A' }}
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Véhicule</div>
                                        <div class="info-value">{{ $contractClient->rentalContract->vehicle->registration_number ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Date début</div>
                                        <div class="info-value">{{ $contractClient->rentalContract->formatted_start_date ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Client Information -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ti ti-user me-2"></i>
                                    Client
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-label">Nom</div>
                                        <div class="info-value">
                                            {{-- Lien vers client - contrôlé par permission VIEW sur clients --}}
                                            @can('clients.general.view')
                                                <a href="{{ route('backoffice.clients.show', $contractClient->client_id) }}">
                                                    {{ $contractClient->client->first_name ?? '' }} {{ $contractClient->client->last_name ?? '' }}
                                                </a>
                                            @else
                                                {{ $contractClient->client->first_name ?? '' }} {{ $contractClient->client->last_name ?? '' }}
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Téléphone</div>
                                        <div class="info-value">{{ $contractClient->client->phone ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">E-mail</div>
                                        <div class="info-value">{{ $contractClient->client->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relationship Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-link me-2"></i>
                            Détails de la relation
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-label">Rôle</div>
                                <div class="info-value">
                                    <span class="badge {{ $contractClient->role_badge_class }}">
                                        {{ $contractClient->role_text }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Ordre</div>
                                <div class="info-value">{{ $contractClient->order }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Dernière modification</div>
                                <div class="info-value">{{ $contractClient->updated_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@include('backoffice.contract-clients.partials._modal_delete')
@endsection