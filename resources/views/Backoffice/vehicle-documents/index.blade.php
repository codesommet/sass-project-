<?php $page = 'vehicle-documents'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        {{-- Breadcrumb --}}
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Documents Véhicules</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Documents Véhicules</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            @php
                $cards = [
                    ['tab' => 'vignettes', 'icon' => 'ti-ticket', 'color' => '#2e7d32', 'bg' => '#e8f5e9', 'label' => 'Vignettes'],
                    ['tab' => 'insurances', 'icon' => 'ti-shield-check', 'color' => '#0d47a1', 'bg' => '#e3f2fd', 'label' => 'Assurances'],
                    ['tab' => 'technical-checks', 'icon' => 'ti-clipboard-check', 'color' => '#856404', 'bg' => '#fff3cd', 'label' => 'Contrôles Techniques'],
                    ['tab' => 'oil-changes', 'icon' => 'ti-droplet', 'color' => '#7b1fa2', 'bg' => '#f3e5f5', 'label' => 'Vidanges'],
                ];
            @endphp
            @foreach($cards as $card)
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <a href="javascript:void(0);" class="text-decoration-none" onclick="switchDocTab('{{ $card['tab'] }}')">
                    <div class="card border-0 shadow-sm" id="card-{{ $card['tab'] }}">
                        <div class="card-body d-flex align-items-center py-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: {{ $card['bg'] }};">
                                <i class="ti {{ $card['icon'] }} fs-4" style="color: {{ $card['color'] }};"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $counts[$card['tab']] }}</h3>
                                <small class="text-muted">{{ $card['label'] }}</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded-fill" role="tablist" id="docTabs">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'vignettes' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-vignettes" role="tab" onclick="onDocTabSwitch('vignettes')">
                    <i class="ti ti-ticket me-1"></i> Vignettes
                    <span class="badge bg-primary ms-1">{{ $counts['vignettes'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'insurances' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-insurances" role="tab" onclick="onDocTabSwitch('insurances')">
                    <i class="ti ti-shield-check me-1"></i> Assurances
                    <span class="badge bg-primary ms-1">{{ $counts['insurances'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'technical-checks' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-technical-checks" role="tab" onclick="onDocTabSwitch('technical-checks')">
                    <i class="ti ti-clipboard-check me-1"></i> Contrôles Techniques
                    <span class="badge bg-primary ms-1">{{ $counts['technical-checks'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'oil-changes' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-oil-changes" role="tab" onclick="onDocTabSwitch('oil-changes')">
                    <i class="ti ti-droplet me-1"></i> Vidanges
                    <span class="badge bg-primary ms-1">{{ $counts['oil-changes'] }}</span>
                </a>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content mt-3">

            {{-- ============ VIGNETTES TAB ============ --}}
            <div class="tab-pane fade {{ $activeTab === 'vignettes' ? 'show active' : '' }}" id="tab-vignettes" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" id="filterFormVignettes" action="{{ route('backoffice.vehicle-documents.index') }}">
                            <input type="hidden" name="tab" value="vignettes">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                <div class="d-flex align-items-center flex-wrap row-gap-3">
                                    {{-- Sort --}}
                                    <div class="dropdown me-2">
                                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                            <i class="ti ti-arrows-sort me-1"></i> Trier :
                                            @if($activeTab === 'vignettes')
                                                @if(request('sort') == 'oldest') Plus anciennes
                                                @elseif(request('sort') == 'amount_asc') Montant ↑
                                                @elseif(request('sort') == 'amount_desc') Montant ↓
                                                @elseif(request('sort') == 'year_asc') Année ↑
                                                @elseif(request('sort') == 'year_desc') Année ↓
                                                @else Plus récentes @endif
                                            @else Plus récentes @endif
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'vignettes', 'sort' => 'latest']) }}">Plus récentes</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'vignettes', 'sort' => 'oldest']) }}">Plus anciennes</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'vignettes', 'sort' => 'amount_desc']) }}">Montant (plus élevé)</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'vignettes', 'sort' => 'amount_asc']) }}">Montant (moins élevé)</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'vignettes', 'sort' => 'year_desc']) }}">Année (récente)</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'vignettes', 'sort' => 'year_asc']) }}">Année (ancienne)</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <a href="#filtercollapseVignettes" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse">
                                            <i class="ti ti-filter me-1"></i> Filtres
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                    <div class="top-search me-2">
                                        <div class="top-search-group position-relative">
                                            <span class="input-icon"><i class="ti ti-search"></i></span>
                                            <input type="text" name="search" value="{{ $activeTab === 'vignettes' ? request('search') : '' }}" class="form-control doc-search-input" placeholder="Rechercher une vignette..." autocomplete="off">
                                            @if($activeTab === 'vignettes' && request('search'))
                                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;"
                                                    onclick="this.previousElementSibling.value=''; this.closest('form').submit();">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @can('vehicle-vignettes.general.create')
                                        <a href="{{ route('backoffice.vehicle-documents.vignettes.create') }}" class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-plus me-2"></i>Ajouter une vignette
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            {{-- Filters --}}
                            @php $hasVignetteFilters = $activeTab === 'vignettes' && request()->hasAny(['year','date_from','date_to','amount_min','amount_max']); @endphp
                            <div class="collapse {{ $hasVignetteFilters ? 'show' : '' }}" id="filtercollapseVignettes">
                                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                                    <div class="row align-items-end">
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Année</label>
                                            <select name="year" class="form-select" onchange="this.form.submit()">
                                                <option value="">Toutes</option>
                                                @foreach($availableYears ?? [] as $year)
                                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Date du</label>
                                            <input type="date" name="date_from" value="{{ $activeTab === 'vignettes' ? request('date_from') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Date au</label>
                                            <input type="date" name="date_to" value="{{ $activeTab === 'vignettes' ? request('date_to') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Montant min (DH)</label>
                                            <input type="number" name="amount_min" value="{{ $activeTab === 'vignettes' ? request('amount_min') : '' }}" class="form-control" placeholder="0.00" step="0.01" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Montant max (DH)</label>
                                            <input type="number" name="amount_max" value="{{ $activeTab === 'vignettes' ? request('amount_max') : '' }}" class="form-control" placeholder="9999.99" step="0.01" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <a href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'vignettes']) }}" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="ti ti-x me-1"></i>Tout effacer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="custom-datatable-filter table-responsive">
                            @include('Backoffice.vignettes.partials._table', [
                                'vignettes' => $vignettes,
                                'isGlobalView' => true,
                                'vehicle' => null
                            ])
                        </div>

                        @if($vignettes->total() > 0)
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <div class="pagination-info mb-3 mb-md-0" style="color: #6c757d; font-size: 14px;">
                                Affichage de <span class="fw-semibold">{{ $vignettes->firstItem() }}</span> à <span class="fw-semibold">{{ $vignettes->lastItem() }}</span>
                                sur <span class="fw-semibold">{{ $vignettes->total() }}</span> résultats
                            </div>
                            <div>{{ $vignettes->withQueryString()->links() }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============ INSURANCES TAB ============ --}}
            <div class="tab-pane fade {{ $activeTab === 'insurances' ? 'show active' : '' }}" id="tab-insurances" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" id="filterFormInsurances" action="{{ route('backoffice.vehicle-documents.index') }}">
                            <input type="hidden" name="tab" value="insurances">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                <div class="d-flex align-items-center flex-wrap row-gap-3">
                                    <div class="dropdown me-2">
                                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                            <i class="ti ti-arrows-sort me-1"></i> Trier :
                                            @if($activeTab === 'insurances')
                                                @if(request('sort') == 'oldest') Plus anciennes
                                                @elseif(request('sort') == 'amount_asc') Montant ↑
                                                @elseif(request('sort') == 'amount_desc') Montant ↓
                                                @elseif(request('sort') == 'next_date_asc') Échéance ↑
                                                @elseif(request('sort') == 'next_date_desc') Échéance ↓
                                                @else Plus récentes @endif
                                            @else Plus récentes @endif
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'insurances', 'sort' => 'latest']) }}">Plus récentes</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'insurances', 'sort' => 'oldest']) }}">Plus anciennes</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'insurances', 'sort' => 'amount_desc']) }}">Montant (plus élevé)</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'insurances', 'sort' => 'amount_asc']) }}">Montant (moins élevé)</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'insurances', 'sort' => 'next_date_asc']) }}">Échéance (proche)</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'insurances', 'sort' => 'next_date_desc']) }}">Échéance (éloignée)</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <a href="#filtercollapseInsurances" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse">
                                            <i class="ti ti-filter me-1"></i> Filtres
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                    <div class="top-search me-2">
                                        <div class="top-search-group position-relative">
                                            <span class="input-icon"><i class="ti ti-search"></i></span>
                                            <input type="text" name="search" value="{{ $activeTab === 'insurances' ? request('search') : '' }}" class="form-control doc-search-input" placeholder="Rechercher une assurance..." autocomplete="off">
                                            @if($activeTab === 'insurances' && request('search'))
                                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;"
                                                    onclick="this.previousElementSibling.value=''; this.closest('form').submit();">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @can('vehicle-insurances.general.create')
                                        <a href="{{ route('backoffice.vehicle-documents.insurances.create') }}" class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-plus me-2"></i>Ajouter une assurance
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            @php $hasInsuranceFilters = $activeTab === 'insurances' && request()->hasAny(['company','date_from','date_to','next_date_from','next_date_to','amount_min','amount_max']); @endphp
                            <div class="collapse {{ $hasInsuranceFilters ? 'show' : '' }}" id="filtercollapseInsurances">
                                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                                    <div class="row align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label fw-medium">Compagnie</label>
                                            <select name="company" class="form-select" onchange="this.form.submit()">
                                                <option value="">Toutes</option>
                                                @foreach($availableCompanies ?? [] as $company)
                                                    <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>{{ $company }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Date début</label>
                                            <input type="date" name="date_from" value="{{ $activeTab === 'insurances' ? request('date_from') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Date fin</label>
                                            <input type="date" name="date_to" value="{{ $activeTab === 'insurances' ? request('date_to') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Échéance du</label>
                                            <input type="date" name="next_date_from" value="{{ $activeTab === 'insurances' ? request('next_date_from') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label class="form-label fw-medium">Échéance au</label>
                                            <input type="date" name="next_date_to" value="{{ $activeTab === 'insurances' ? request('next_date_to') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label class="form-label fw-medium">Montant min (DH)</label>
                                            <input type="number" name="amount_min" value="{{ $activeTab === 'insurances' ? request('amount_min') : '' }}" class="form-control" placeholder="0.00" step="0.01" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <label class="form-label fw-medium">Montant max (DH)</label>
                                            <input type="number" name="amount_max" value="{{ $activeTab === 'insurances' ? request('amount_max') : '' }}" class="form-control" placeholder="9999.99" step="0.01" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-3 mt-2 d-flex align-items-end">
                                            <a href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'insurances']) }}" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="ti ti-x me-1"></i>Tout effacer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="custom-datatable-filter table-responsive">
                            @include('Backoffice.insurances.partials._table', [
                                'insurances' => $insurances,
                                'isGlobalView' => true,
                                'vehicle' => null,
                                'permissions' => []
                            ])
                        </div>

                        @if($insurances->total() > 0)
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <div class="pagination-info mb-3 mb-md-0" style="color: #6c757d; font-size: 14px;">
                                Affichage de <span class="fw-semibold">{{ $insurances->firstItem() }}</span> à <span class="fw-semibold">{{ $insurances->lastItem() }}</span>
                                sur <span class="fw-semibold">{{ $insurances->total() }}</span> résultats
                            </div>
                            <div>{{ $insurances->withQueryString()->links() }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============ TECHNICAL CHECKS TAB ============ --}}
            <div class="tab-pane fade {{ $activeTab === 'technical-checks' ? 'show active' : '' }}" id="tab-technical-checks" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" id="filterFormChecks" action="{{ route('backoffice.vehicle-documents.index') }}">
                            <input type="hidden" name="tab" value="technical-checks">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                <div class="d-flex align-items-center flex-wrap row-gap-3">
                                    <div class="dropdown me-2">
                                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                            <i class="ti ti-arrows-sort me-1"></i> Trier :
                                            @if($activeTab === 'technical-checks')
                                                @if(request('sort') == 'oldest') Plus anciennes
                                                @elseif(request('sort') == 'amount_asc') Montant ↑
                                                @elseif(request('sort') == 'amount_desc') Montant ↓
                                                @elseif(request('sort') == 'next_date_asc') Échéance ↑
                                                @elseif(request('sort') == 'next_date_desc') Échéance ↓
                                                @else Plus récentes @endif
                                            @else Plus récentes @endif
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'technical-checks', 'sort' => 'latest']) }}">Plus récentes</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'technical-checks', 'sort' => 'oldest']) }}">Plus anciennes</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'technical-checks', 'sort' => 'amount_desc']) }}">Montant (plus élevé)</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'technical-checks', 'sort' => 'amount_asc']) }}">Montant (moins élevé)</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'technical-checks', 'sort' => 'next_date_asc']) }}">Échéance (proche)</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'technical-checks', 'sort' => 'next_date_desc']) }}">Échéance (éloignée)</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <a href="#filtercollapseChecks" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse">
                                            <i class="ti ti-filter me-1"></i> Filtres
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                    <div class="top-search me-2">
                                        <div class="top-search-group position-relative">
                                            <span class="input-icon"><i class="ti ti-search"></i></span>
                                            <input type="text" name="search" value="{{ $activeTab === 'technical-checks' ? request('search') : '' }}" class="form-control doc-search-input" placeholder="Rechercher un contrôle technique..." autocomplete="off">
                                            @if($activeTab === 'technical-checks' && request('search'))
                                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;"
                                                    onclick="this.previousElementSibling.value=''; this.closest('form').submit();">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @can('vehicle-technical-checks.general.create')
                                        <a href="{{ route('backoffice.vehicle-documents.technical-checks.create') }}" class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-plus me-2"></i>Ajouter un contrôle technique
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            @php $hasCheckFilters = $activeTab === 'technical-checks' && request()->hasAny(['date_from','date_to','next_date_from','next_date_to','amount_min','amount_max']); @endphp
                            <div class="collapse {{ $hasCheckFilters ? 'show' : '' }}" id="filtercollapseChecks">
                                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                                    <div class="row align-items-end">
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Date du</label>
                                            <input type="date" name="date_from" value="{{ $activeTab === 'technical-checks' ? request('date_from') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Date au</label>
                                            <input type="date" name="date_to" value="{{ $activeTab === 'technical-checks' ? request('date_to') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Échéance du</label>
                                            <input type="date" name="next_date_from" value="{{ $activeTab === 'technical-checks' ? request('next_date_from') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Échéance au</label>
                                            <input type="date" name="next_date_to" value="{{ $activeTab === 'technical-checks' ? request('next_date_to') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Montant min (DH)</label>
                                            <input type="number" name="amount_min" value="{{ $activeTab === 'technical-checks' ? request('amount_min') : '' }}" class="form-control" placeholder="0.00" step="0.01" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Montant max (DH)</label>
                                            <input type="number" name="amount_max" value="{{ $activeTab === 'technical-checks' ? request('amount_max') : '' }}" class="form-control" placeholder="9999.99" step="0.01" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2 mt-2 d-flex align-items-end">
                                            <a href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'technical-checks']) }}" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="ti ti-x me-1"></i>Tout effacer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="custom-datatable-filter table-responsive">
                            @include('Backoffice.technical-checks.partials._table', [
                                'technicalChecks' => $technicalChecks,
                                'isGlobalView' => true,
                                'vehicle' => null,
                                'permissions' => []
                            ])
                        </div>

                        @if($technicalChecks->total() > 0)
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <div class="pagination-info mb-3 mb-md-0" style="color: #6c757d; font-size: 14px;">
                                Affichage de <span class="fw-semibold">{{ $technicalChecks->firstItem() }}</span> à <span class="fw-semibold">{{ $technicalChecks->lastItem() }}</span>
                                sur <span class="fw-semibold">{{ $technicalChecks->total() }}</span> résultats
                            </div>
                            <div>{{ $technicalChecks->withQueryString()->links() }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============ OIL CHANGES TAB ============ --}}
            <div class="tab-pane fade {{ $activeTab === 'oil-changes' ? 'show active' : '' }}" id="tab-oil-changes" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" id="filterFormOils" action="{{ route('backoffice.vehicle-documents.index') }}">
                            <input type="hidden" name="tab" value="oil-changes">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                <div class="d-flex align-items-center flex-wrap row-gap-3">
                                    <div class="dropdown me-2">
                                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                            <i class="ti ti-arrows-sort me-1"></i> Trier :
                                            @if($activeTab === 'oil-changes')
                                                @if(request('sort') == 'oldest') Plus anciennes
                                                @elseif(request('sort') == 'mileage_asc') Kilométrage ↑
                                                @elseif(request('sort') == 'mileage_desc') Kilométrage ↓
                                                @elseif(request('sort') == 'amount_asc') Montant ↑
                                                @elseif(request('sort') == 'amount_desc') Montant ↓
                                                @else Plus récentes @endif
                                            @else Plus récentes @endif
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end p-2">
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'oil-changes', 'sort' => 'latest']) }}">Plus récentes</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'oil-changes', 'sort' => 'oldest']) }}">Plus anciennes</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'oil-changes', 'sort' => 'amount_desc']) }}">Montant (plus élevé)</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'oil-changes', 'sort' => 'amount_asc']) }}">Montant (moins élevé)</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'oil-changes', 'sort' => 'mileage_desc']) }}">Kilométrage (plus élevé)</a></li>
                                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'oil-changes', 'sort' => 'mileage_asc']) }}">Kilométrage (moins élevé)</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <a href="#filtercollapseOils" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse">
                                            <i class="ti ti-filter me-1"></i> Filtres
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                    <div class="top-search me-2">
                                        <div class="top-search-group position-relative">
                                            <span class="input-icon"><i class="ti ti-search"></i></span>
                                            <input type="text" name="search" value="{{ $activeTab === 'oil-changes' ? request('search') : '' }}" class="form-control doc-search-input" placeholder="Rechercher une vidange..." autocomplete="off">
                                            @if($activeTab === 'oil-changes' && request('search'))
                                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;"
                                                    onclick="this.previousElementSibling.value=''; this.closest('form').submit();">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    @can('vehicle-oil-changes.general.create')
                                        <a href="{{ route('backoffice.vehicle-documents.oil-changes.create') }}" class="btn btn-primary d-flex align-items-center">
                                            <i class="ti ti-plus me-2"></i>Ajouter une vidange
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            @php $hasOilFilters = $activeTab === 'oil-changes' && request()->hasAny(['date_from','date_to','mechanic','mileage_min','mileage_max','amount_min','amount_max']); @endphp
                            <div class="collapse {{ $hasOilFilters ? 'show' : '' }}" id="filtercollapseOils">
                                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                                    <div class="row align-items-end">
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Date du</label>
                                            <input type="date" name="date_from" value="{{ $activeTab === 'oil-changes' ? request('date_from') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Date au</label>
                                            <input type="date" name="date_to" value="{{ $activeTab === 'oil-changes' ? request('date_to') : '' }}" class="form-control" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Mécanicien</label>
                                            <select name="mechanic" class="form-select" onchange="this.form.submit()">
                                                <option value="">Tous</option>
                                                @foreach($availableMechanics ?? [] as $mechanic)
                                                    <option value="{{ $mechanic }}" {{ request('mechanic') == $mechanic ? 'selected' : '' }}>{{ $mechanic }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Km min</label>
                                            <input type="number" name="mileage_min" value="{{ $activeTab === 'oil-changes' ? request('mileage_min') : '' }}" class="form-control" placeholder="0" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Km max</label>
                                            <input type="number" name="mileage_max" value="{{ $activeTab === 'oil-changes' ? request('mileage_max') : '' }}" class="form-control" placeholder="999999" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-medium">Montant min (DH)</label>
                                            <input type="number" name="amount_min" value="{{ $activeTab === 'oil-changes' ? request('amount_min') : '' }}" class="form-control" placeholder="0.00" step="0.01" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2 mt-2">
                                            <label class="form-label fw-medium">Montant max (DH)</label>
                                            <input type="number" name="amount_max" value="{{ $activeTab === 'oil-changes' ? request('amount_max') : '' }}" class="form-control" placeholder="9999.99" step="0.01" onchange="this.form.submit()">
                                        </div>
                                        <div class="col-md-2 mt-2 d-flex align-items-end">
                                            <a href="{{ route('backoffice.vehicle-documents.index', ['tab' => 'oil-changes']) }}" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="ti ti-x me-1"></i>Tout effacer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="custom-datatable-filter table-responsive">
                            @include('Backoffice.oil-changes.partials._table', [
                                'oilChanges' => $oilChanges,
                                'isGlobalView' => true,
                                'vehicle' => null,
                                'permissions' => []
                            ])
                        </div>

                        @if($oilChanges->total() > 0)
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <div class="pagination-info mb-3 mb-md-0" style="color: #6c757d; font-size: 14px;">
                                Affichage de <span class="fw-semibold">{{ $oilChanges->firstItem() }}</span> à <span class="fw-semibold">{{ $oilChanges->lastItem() }}</span>
                                sur <span class="fw-semibold">{{ $oilChanges->total() }}</span> résultats
                            </div>
                            <div>{{ $oilChanges->withQueryString()->links() }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Privacy Policy</a>
            <a href="javascript:void(0);" class="ms-4">Terms of Use</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">&#10084;</span> by
            <a href="javascript:void(0);" class="text-secondary">Dreams</a>
        </p>
    </div>
</div>

{{-- Tab switching + auto-search JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-search with debounce for all tab forms
    document.querySelectorAll('.doc-search-input').forEach(function(input) {
        var form = input.closest('form');
        if (!form) return;
        var debounceTimer;
        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () { form.submit(); }, 400);
        });
    });

    // Highlight active card on load
    updateDocCards('{{ $activeTab }}');
});

function switchDocTab(tab) {
    var tabLink = document.querySelector('#docTabs a[href="#tab-' + tab + '"]');
    if (tabLink) {
        var bsTab = new bootstrap.Tab(tabLink);
        bsTab.show();
    }
    onDocTabSwitch(tab);
}

function onDocTabSwitch(tab) {
    var url = new URL(window.location.href);
    url.searchParams.set('tab', tab);
    // Clear filters that belong to other tabs
    ['search','sort','year','date_from','date_to','amount_min','amount_max','company','next_date_from','next_date_to','mechanic','mileage_min','mileage_max',
     'vignettes_page','insurances_page','checks_page','oils_page'].forEach(function(p) {
        url.searchParams.delete(p);
    });
    window.history.pushState({}, '', url);
    updateDocCards(tab);
}

function updateDocCards(tab) {
    ['vignettes', 'insurances', 'technical-checks', 'oil-changes'].forEach(function(t) {
        var card = document.getElementById('card-' + t);
        if (card) {
            card.className = 'card border-0 shadow-sm' + (t === tab ? ' border-start border-primary border-3' : '');
        }
    });
}
</script>

{{-- Modals — always load all since all tabs are rendered --}}
@include('Backoffice.vignettes.partials._modal_delete')
@include('Backoffice.vignettes.partials._modals_js')
@include('Backoffice.insurances.partials._modal_delete')
@include('Backoffice.insurances.partials._modals_js')
@include('Backoffice.technical-checks.partials._modal_delete')
@include('Backoffice.technical-checks.partials._modals_js')
@include('Backoffice.oil-changes.partials._modal_delete')
@include('Backoffice.oil-changes.partials._modals_js')
@endsection
