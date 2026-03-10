<?php $page = 'finance-transaction-details'; ?>
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
    .badge-income { background: #d4edda; color: #155724; }
    .badge-expense { background: #f8d7da; color: #721c24; }
    .amount-large {
        font-size: 2rem;
        font-weight: 700;
    }
    .amount-income { color: #198754; }
    .amount-expense { color: #dc3545; }
    
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
    
    .metadata-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    .metadata-item {
        display: flex;
        margin-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
    }
    .metadata-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .metadata-key {
        font-weight: 600;
        width: 40%;
        color: #495057;
    }
    .metadata-value {
        width: 60%;
        color: #212529;
    }
    
    .source-badge-lg {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
    }
    .source-badge-lg i {
        margin-right: 0.5rem;
        font-size: 1.2rem;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <a href="{{ route('backoffice.finance.transactions.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <div>
                        {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                        @can('financial-transactions.general.edit')
                            <a href="{{ route('backoffice.finance.transactions.edit', $financialTransaction) }}" class="btn btn-primary">
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
                                        <i class="ti ti-transfer"></i>
                                    </span>
                                </span>
                                <div>
                                    <h4 class="mb-1">Transaction #{{ $financialTransaction->id }}</h4>
                                    <p class="mb-0 text-muted">
                                        <i class="ti ti-calendar me-1"></i>
                                        {{ $financialTransaction->formatted_date }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge {{ $financialTransaction->type_badge_class }} fs-6 p-2">
                                    {{ $financialTransaction->type_text }}
                                </span>
                                @if($financialTransaction->source_type)
                                    @php
                                        $sourceInfo = $financialTransaction->source_info;
                                    @endphp
                                    <span class="source-badge-lg" style="background-color: {{ $sourceInfo['color'] }}20; color: {{ $sourceInfo['color'] }}; border: 1px solid {{ $sourceInfo['color'] }}40;">
                                        <i class="{{ $sourceInfo['icon'] }}"></i>
                                        {{ $sourceInfo['label'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Tabs -->
                <div class="wizard-nav">
                    <div class="nav-item">
                        <a class="nav-link active" data-panel="1">
                            <i class="ti ti-info-circle"></i>
                            Détails
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="2">
                            <i class="ti ti-building-bank"></i>
                            Compte
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link" data-panel="3">
                            <i class="ti ti-category"></i>
                            Catégorie
                        </a>
                    </div>
                    @if($financialTransaction->metadata)
                    <div class="nav-item">
                        <a class="nav-link" data-panel="4">
                            <i class="ti ti-database"></i>
                            Métadonnées
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Panel 1: Détails -->
                <div class="info-panel active" id="panel1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Montant</h5>
                                </div>
                                <div class="card-body">
                                    <div class="amount-large {{ $financialTransaction->type === 'income' ? 'amount-income' : 'amount-expense' }}">
                                        {{ $financialTransaction->formatted_amount }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Description</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $financialTransaction->description ?? 'Aucune description' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Référence</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $financialTransaction->reference ?? 'Non renseignée' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Créé par</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $financialTransaction->createdBy->name ?? 'Système' }}</p>
                                    <small class="text-muted">{{ $financialTransaction->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Compte -->
                <div class="info-panel" id="panel2">
                    <div class="card mb-4">
                        <div class="card-body">
                            @if($financialTransaction->account)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-label">Nom du compte</div>
                                        <div class="info-value">
                                            @can('financial-accounts.general.view')
                                                <a href="{{ route('backoffice.finance.accounts.show', $financialTransaction->financial_account_id) }}">
                                                    {{ $financialTransaction->account->name }}
                                                </a>
                                            @else
                                                {{ $financialTransaction->account->name }}
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Type</div>
                                        <div class="info-value">
                                            <span class="badge {{ $financialTransaction->account->type_badge_class }}">
                                                {{ $financialTransaction->account->type_text }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Solde actuel</div>
                                        <div class="info-value">{{ $financialTransaction->account->formatted_current_balance }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">RIB</div>
                                        <div class="info-value">{{ $financialTransaction->account->rib ?? 'Non renseigné' }}</div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">Compte non trouvé</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Catégorie -->
                <div class="info-panel" id="panel3">
                    <div class="card mb-4">
                        <div class="card-body">
                            @if($financialTransaction->category)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-label">Nom de la catégorie</div>
                                        <div class="info-value">{{ $financialTransaction->category->name }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Type</div>
                                        <div class="info-value">
                                            <span class="badge {{ $financialTransaction->category->type_badge_class }}">
                                                {{ $financialTransaction->category->type_text }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">Aucune catégorie associée</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panel 4: Métadonnées -->
                @if($financialTransaction->metadata)
                <div class="info-panel" id="panel4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-database me-2"></i>
                                Métadonnées de la transaction
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="metadata-card">
                                @foreach($financialTransaction->metadata_array as $key => $value)
                                    @if(!is_null($value) && $value !== '')
                                    <div class="metadata-item">
                                        <div class="metadata-key">
                                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                                        </div>
                                        <div class="metadata-value">
                                            @if(is_array($value))
                                                {{ json_encode($value) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

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
</script>

@include('backoffice.finance.transactions.partials._modal_delete')
@endsection