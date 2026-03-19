<?php $page = 'brands-models'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .table-responsive, .custom-datatable-filter { overflow: visible !important; }
</style>

<div class="page-wrapper">
    <div class="content me-4">

        {{-- Breadcrumb --}}
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Marques & Modèles</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}"><i class="ti ti-smart-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Marques & Modèles</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-6 col-sm-6 mb-3 mb-md-0">
                <a href="javascript:void(0);" class="text-decoration-none" onclick="switchTab('brands')">
                    <div class="card border-0 shadow-sm" id="card-brands">
                        <div class="card-body d-flex align-items-center py-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: #e3f2fd;">
                                <i class="ti ti-building-factory fs-4" style="color: #0d47a1;"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $counts['brands'] }}</h3>
                                <small class="text-muted">Marques</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-sm-6">
                <a href="javascript:void(0);" class="text-decoration-none" onclick="switchTab('models')">
                    <div class="card border-0 shadow-sm" id="card-models">
                        <div class="card-body d-flex align-items-center py-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: #e8f5e9;">
                                <i class="ti ti-car fs-4" style="color: #2e7d32;"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $counts['models'] }}</h3>
                                <small class="text-muted">Modèles</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded-fill" role="tablist" id="brandsModelsTabs">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'brands' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-brands" role="tab" onclick="onTabSwitch('brands')">
                    <i class="ti ti-building-factory me-1"></i> Marques
                    <span class="badge bg-primary ms-1">{{ $counts['brands'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === 'models' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-models" role="tab" onclick="onTabSwitch('models')">
                    <i class="ti ti-car me-1"></i> Modèles
                    <span class="badge bg-primary ms-1">{{ $counts['models'] }}</span>
                </a>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content mt-3">

            {{-- ============ BRANDS TAB ============ --}}
            <div class="tab-pane fade {{ $activeTab === 'brands' ? 'show active' : '' }}" id="tab-brands" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        {{-- Search + Add --}}
                        <form method="GET" id="filterFormBrands" action="{{ route('backoffice.brands-models.index') }}">
                            <input type="hidden" name="tab" value="brands">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                <div class="top-search me-2">
                                    <div class="top-search-group position-relative">
                                        <span class="input-icon"><i class="ti ti-search"></i></span>
                                        <input type="text" name="search" value="{{ $activeTab === 'brands' ? request('search') : '' }}" class="form-control"
                                            placeholder="Rechercher une marque..." autocomplete="off">
                                        @if($activeTab === 'brands' && request('search'))
                                            <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;"
                                                onclick="this.previousElementSibling.value=''; this.closest('form').submit();">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @can('vehicle-brands.general.create')
                                    <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_brand">
                                        <i class="ti ti-plus me-2"></i>Ajouter une marque
                                    </a>
                                @endcan
                            </div>
                        </form>

                        {{-- Table --}}
                        <div class="custom-datatable-filter table-responsive">
                            @include('backoffice.vehicle-brands.partials._table', [
                                'brands' => $brands,
                                'permissions' => $brandsPermissions ?? []
                            ])
                        </div>

                        {{-- Pagination --}}
                        @if($brands->total() > 0)
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <div class="text-muted mb-3 mb-md-0" style="font-size: 14px;">
                                Affichage de <span class="fw-semibold">{{ $brands->firstItem() }}</span> à <span class="fw-semibold">{{ $brands->lastItem() }}</span>
                                sur <span class="fw-semibold">{{ $brands->total() }}</span> marques
                            </div>
                            <div>
                                {{ $brands->withQueryString()->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============ MODELS TAB ============ --}}
            <div class="tab-pane fade {{ $activeTab === 'models' ? 'show active' : '' }}" id="tab-models" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        {{-- Search + Add --}}
                        <form method="GET" id="filterFormModels" action="{{ route('backoffice.brands-models.index') }}">
                            <input type="hidden" name="tab" value="models">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                <div class="top-search me-2">
                                    <div class="top-search-group position-relative">
                                        <span class="input-icon"><i class="ti ti-search"></i></span>
                                        <input type="text" name="search" value="{{ $activeTab === 'models' ? request('search') : '' }}" class="form-control"
                                            placeholder="Rechercher un modèle ou une marque..." autocomplete="off">
                                        @if($activeTab === 'models' && request('search'))
                                            <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;"
                                                onclick="this.previousElementSibling.value=''; this.closest('form').submit();">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @can('vehicle-models.general.create')
                                    <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_model">
                                        <i class="ti ti-plus me-2"></i>Ajouter un modèle
                                    </a>
                                @endcan
                            </div>
                        </form>

                        {{-- Table --}}
                        <div class="custom-datatable-filter table-responsive">
                            @include('backoffice.vehicle-models.partials._table', [
                                'models' => $models,
                                'permissions' => $modelsPermissions ?? []
                            ])
                        </div>

                        {{-- Pagination --}}
                        @if($models->total() > 0)
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
                            <div class="text-muted mb-3 mb-md-0" style="font-size: 14px;">
                                Affichage de <span class="fw-semibold">{{ $models->firstItem() }}</span> à <span class="fw-semibold">{{ $models->lastItem() }}</span>
                                sur <span class="fw-semibold">{{ $models->total() }}</span> modèles
                            </div>
                            <div>
                                {{ $models->withQueryString()->links() }}
                            </div>
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

{{-- Tab switching JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-search with debounce for both forms
    ['filterFormBrands', 'filterFormModels'].forEach(function(formId) {
        const form = document.getElementById(formId);
        if (!form) return;
        const input = form.querySelector('input[name="search"]');
        if (!input) return;
        let debounceTimer;
        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () { form.submit(); }, 400);
        });
    });

    // Highlight active card on load
    updateCards('{{ $activeTab }}');
});

function switchTab(tab) {
    // Trigger the Bootstrap tab
    var tabLink = document.querySelector('#brandsModelsTabs a[href="#tab-' + tab + '"]');
    if (tabLink) {
        var bsTab = new bootstrap.Tab(tabLink);
        bsTab.show();
    }
    onTabSwitch(tab);
}

function onTabSwitch(tab) {
    // Update URL without reload
    var url = new URL(window.location.href);
    url.searchParams.set('tab', tab);
    url.searchParams.delete('search');
    url.searchParams.delete('brands_page');
    url.searchParams.delete('models_page');
    window.history.pushState({}, '', url);

    // Update card highlights
    updateCards(tab);
}

function updateCards(tab) {
    var cardBrands = document.getElementById('card-brands');
    var cardModels = document.getElementById('card-models');
    if (cardBrands) {
        cardBrands.className = 'card border-0 shadow-sm' + (tab === 'brands' ? ' border-start border-primary border-3' : '');
    }
    if (cardModels) {
        cardModels.className = 'card border-0 shadow-sm' + (tab === 'models' ? ' border-start border-primary border-3' : '');
    }
}
</script>

{{-- Modals — always load both since both tabs are rendered --}}
@include('backoffice.vehicle-brands.partials._modal_create')
@include('backoffice.vehicle-brands.partials._modal_edit')
@include('backoffice.vehicle-brands.partials._modal_delete')
@include('backoffice.vehicle-brands.partials._modals_js')

@include('backoffice.vehicle-models.partials._modal_create', ['brands' => $allBrands ?? []])
@include('backoffice.vehicle-models.partials._modal_edit')
@include('backoffice.vehicle-models.partials._modal_delete')
@include('backoffice.vehicle-models.partials._modals_js')
@endsection
