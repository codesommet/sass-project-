<?php $page = 'contract-clients'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #6c757d;
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .btn-icon:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #0d6efd;
    }
    
    .btn-icon i {
        font-size: 18px;
    }
    
    .badge-primary { background: #cce5ff; color: #004085; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-secondary { background: #e2e3e5; color: #383d41; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-other { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    
    .table-responsive { 
        overflow: visible !important; 
    }
    
    .dropdown-menu {
        z-index: 9999 !important;
        display: block;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
    }
    
    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .form-check {
        display: flex;
        justify-content: center;
        margin: 0;
        padding: 0;
    }
    
    /* Filter Collapse */
    .collapse {
        display: none;
    }
    .collapse.show {
        display: block;
    }
</style>

<div class="page-wrapper">
    <div class="content me-4">
        @include('backoffice.contract-clients.partials._breadcrumbs')

        <form method="GET" id="filterForm" action="{{ request()->url() }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <!-- Sort Dropdown -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" role="button">
                            <i class="ti ti-filter me-1"></i> Trier : 
                            @if(request('sort') == 'oldest') Plus anciens
                            @elseif(request('sort') == 'order_asc') Ordre ↑
                            @elseif(request('sort') == 'order_desc') Ordre ↓
                            @else Plus récents @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">Plus récents</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}">Plus anciens</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'order_asc']) }}">Ordre ↑</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'order_desc']) }}">Ordre ↓</a></li>
                        </ul>
                    </div>
                    
                    <!-- Filters Toggle -->
                    <div>
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>
                </div>

                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <!-- Search -->
                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                                   class="form-control" placeholder="Rechercher...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Add Button - contrôlé par permission CREATE -->
                    @can('contract-clients.general.create')
                    <div class="mb-0">
                        <a href="{{ route('backoffice.contract-clients.create') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-plus me-2"></i>Ajouter une relation
                        </a>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Filters Panel -->
            <div class="collapse @if(request()->has('role') || request()->has('contract_id')) show @endif" id="filtercollapse">
                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Rôle</label>
                            <select name="role" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                <option value="primary" {{ request('role') == 'primary' ? 'selected' : '' }}>Principal</option>
                                <option value="secondary" {{ request('role') == 'secondary' ? 'selected' : '' }}>Secondaire</option>
                                <option value="other" {{ request('role') == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Contrat</label>
                            <select name="contract_id" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($contracts as $contract)
                                    <option value="{{ $contract->id }}" {{ request('contract_id') == $contract->id ? 'selected' : '' }}>
                                        {{ $contract->contract_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <a href="{{ route('backoffice.contract-clients.index') }}" class="btn btn-sm btn-outline-danger w-100">
                                <i class="ti ti-x me-1"></i>Tout effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="custom-datatable-filter table-responsive">
            @include('backoffice.contract-clients.partials._table', ['contractClients' => $contractClients, 'permissions' => $permissions])
        </div>

        <!-- Pagination -->
        @if(isset($contractClients) && $contractClients->total() > 0)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Affichage de {{ $contractClients->firstItem() }} à {{ $contractClients->lastItem() }} sur {{ $contractClients->total() }} relations
            </div>
            <div>
                {{ $contractClients->withQueryString()->links() }}
            </div>
        </div>
        @endif

    </div>

    <!-- Footer -->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0"><a href="javascript:void(0);">Politique de confidentialité</a><a href="javascript:void(0);" class="ms-4">Conditions d'utilisation</a></p>
        <p>&copy; 2025 Dreamsrent</p>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto search
    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');

    if (form && input) {
        let timer;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => form.submit(), 400);
        });
    }

    // ==================== FILTER TOGGLE FIX ====================
    const filterToggle = document.querySelector('.filtercollapse');
    const filterCollapse = document.getElementById('filtercollapse');

    if (filterToggle && filterCollapse) {
        filterToggle.removeAttribute('data-bs-toggle');
        
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            filterCollapse.classList.toggle('show');
        });
    }

    // Close filter when clicking outside
    document.addEventListener('click', function(e) {
        if (filterCollapse && filterCollapse.classList.contains('show')) {
            if (!filterCollapse.contains(e.target) && !filterToggle.contains(e.target)) {
                filterCollapse.classList.remove('show');
            }
        }
    });

    // Initialize dropdowns
    initializeAllDropdowns();
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });
});

function initializeAllDropdowns() {
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(button => {
        button.removeEventListener('click', dropdownClickHandler);
        button.addEventListener('click', dropdownClickHandler);
    });
}

function dropdownClickHandler(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const button = this;
    const dropdown = button.nextElementSibling;
    const isExpanded = button.getAttribute('aria-expanded') === 'true';
    
    closeAllDropdowns();
    
    if (dropdown && !isExpanded) {
        dropdown.classList.add('show');
        button.setAttribute('aria-expanded', 'true');
    }
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
        menu.classList.remove('show');
        const toggle = menu.previousElementSibling;
        if (toggle && toggle.hasAttribute('data-bs-toggle="dropdown"')) {
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        document.getElementById('filterForm').submit();
    }
}
</script>

@include('backoffice.contract-clients.partials._modal_delete')
@endsection