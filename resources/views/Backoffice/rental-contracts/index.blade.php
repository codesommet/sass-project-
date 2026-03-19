<?php $page = 'rental-contracts'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    /* Button Icon */
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
    
    /* Status Badges */
    .badge-draft { background: #e2e3e5; color: #383d41; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-pending { background: #fff3cd; color: #856404; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-accepted { background: #cce5ff; color: #004085; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-in_progress { background: #d1ecf1; color: #0c5460; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-completed { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-cancelled { background: #f8d7da; color: #721c24; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    
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
    
    .amount-badge { 
        background: #e8f5e9; 
        color: #2e7d32; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
        white-space: nowrap;
    }
    
    .contract-info {
        line-height: 1.4;
    }
    
    .contract-info small {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    /* Filter Collapse */
    .collapse {
        display: none;
    }
    .collapse.show {
        display: block;
    }
    
    /* Page wrapper fix for footer */
    .page-wrapper {
        min-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
    }
    
    .content {
        flex: 1 0 auto;
    }
    
    .footer {
        flex-shrink: 0;
        margin-top: auto;
    }

    /* PDF Button */
    .btn-pdf {
        background: #dc3545;
        color: white;
    }
    .btn-pdf:hover {
        background: #bb2d3b;
        color: white;
    }

    /* ============ PAGINATION STYLING ============ */
    .pagination {
        margin: 0;
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: 0.25rem;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        z-index: 3;
    }
    
    .pagination .page-link {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #0d6efd;
        background-color: #fff;
        border: 1px solid #dee2e6;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #0a58ca;
        z-index: 2;
    }
    
    .pagination .page-item:first-child .page-link {
        margin-left: 0;
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
    
    .pagination .page-item:last-child .page-link {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    .pagination-info {
        color: #6c757d;
        font-size: 14px;
    }
    
    .pagination-container {
        display: flex;
        justify-content: flex-end;
    }
    
    @media (max-width: 768px) {
        .pagination-container {
            justify-content: center;
            margin-top: 15px;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content me-4">
        @include('backoffice.rental-contracts.partials._breadcrumbs')

        <form method="GET" id="filterForm" action="{{ request()->url() }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <!-- Sort Dropdown -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" role="button">
                            <i class="ti ti-filter me-1"></i> Trier : 
                            @if(request('sort') == 'oldest') Plus anciens
                            @elseif(request('sort') == 'start_date_asc') Date début ↑
                            @elseif(request('sort') == 'start_date_desc') Date début ↓
                            @elseif(request('sort') == 'amount_asc') Montant ↑
                            @elseif(request('sort') == 'amount_desc') Montant ↓
                            @else Plus récents @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">Plus récents</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}">Plus anciens</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'start_date_desc']) }}">Date début (récente)</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'start_date_asc']) }}">Date début (ancienne)</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'amount_desc']) }}">Montant (plus élevé)</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'amount_asc']) }}">Montant (moins élevé)</a></li>
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
                    <!-- Bulk PDF Export Button - visible seulement si permission VIEW -->
                    @can('rental-contracts.general.view')
                    <div class="mb-0 me-2">
                        <button class="btn btn-danger d-flex align-items-center" id="exportSelectedPDF">
                            <i class="ti ti-file-export me-2"></i>Exporter PDF
                        </button>
                    </div>
                    @endcan

                    <!-- Search -->
                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                                   class="form-control" placeholder="Rechercher un contrat...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Add Button - contrôlé par permission CREATE -->
                    @can('rental-contracts.general.create')
                    <div class="mb-0">
                        <a href="{{ route('backoffice.rental-contracts.create') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-plus me-2"></i>Nouveau contrat
                        </a>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Filters Panel -->
            <div class="collapse @if(request()->has('status') || request()->has('vehicle_id') || request()->has('client_id') || request()->has('date_from') || request()->has('date_to')) show @endif" id="filtercollapse">
                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Statut</label>
                            <select name="status" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepté</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Véhicule</label>
                            <select name="vehicle_id" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->registration_number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Client</label>
                            <select name="client_id" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->first_name }} {{ $client->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Date début</label>
                            <input type="date" form="filterForm" name="date_from" value="{{ request('date_from') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-label fw-medium">Date fin</label>
                            <input type="date" form="filterForm" name="date_to" value="{{ request('date_to') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2 d-flex align-items-end">
                            <a href="{{ route('backoffice.rental-contracts.index') }}" class="btn btn-sm btn-outline-danger w-100">
                                <i class="ti ti-x me-1"></i>Tout effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="custom-datatable-filter table-responsive">
            @include('backoffice.rental-contracts.partials._table', ['contracts' => $contracts, 'permissions' => $permissions])
        </div>

        <!-- PAGINATION - FIXED VERSION -->
        @if(isset($contracts) && $contracts->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
            <div class="pagination-info mb-3 mb-md-0">
                Affichage de <span class="fw-semibold">{{ $contracts->firstItem() }}</span> à <span class="fw-semibold">{{ $contracts->lastItem() }}</span> 
                sur <span class="fw-semibold">{{ $contracts->total() }}</span> contrats
            </div>
            <div class="pagination-container">
                @if ($contracts->hasPages())
                    <nav aria-label="Navigation des pages">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            @if ($contracts->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="ti ti-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $contracts->previousPageUrl() }}" rel="prev" aria-label="Précédent">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements - Show 5 pages at a time --}}
                            @php
                                $start = max(1, $contracts->currentPage() - 2);
                                $end = min($contracts->lastPage(), $contracts->currentPage() + 2);
                                
                                // Show first page with ellipsis if needed
                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . $contracts->url(1) . '">1</a></li>';
                                    if ($start > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                // Show pages around current page
                                for ($i = $start; $i <= $end; $i++) {
                                    $active = $i == $contracts->currentPage() ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $contracts->url($i) . '">' . $i . '</a></li>';
                                }
                                
                                // Show last page with ellipsis if needed
                                if ($end < $contracts->lastPage()) {
                                    if ($end < $contracts->lastPage() - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . $contracts->url($contracts->lastPage()) . '">' . $contracts->lastPage() . '</a></li>';
                                }
                            @endphp

                            {{-- Next Page Link --}}
                            @if ($contracts->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $contracts->nextPageUrl() }}" rel="next" aria-label="Suivant">
                                        <i class="ti ti-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="ti ti-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
        @endif

    </div>

    <!-- Footer -->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0"><a href="javascript:void(0);">Politique de confidentialité</a><a href="javascript:void(0);" class="ms-4">Conditions d'utilisation</a></p>
        <p>&copy; 2025 Dreamsrent, Fait avec <span class="text-danger">❤</span> par <a href="javascript:void(0);" class="text-secondary">Dreams</a></p>
    </div>
</div>

<!-- Hidden form for bulk PDF export -->
<form id="bulkPDFForm" method="POST" action="{{ route('backoffice.contracts.pdf.multiple') }}" style="display: none;">
    @csrf
    <div id="bulkPDFInputs"></div>
</form>

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

    // ==================== SELECT ALL CHECKBOXES ====================
    @can('rental-contracts.general.delete')
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.contract-checkbox').forEach(cb => {
                cb.checked = selectAll.checked;
            });
        });
        
        // Update select all when individual checkboxes change
        document.querySelectorAll('.contract-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const allCheckboxes = document.querySelectorAll('.contract-checkbox');
                const allChecked = Array.from(allCheckboxes).every(c => c.checked);
                const anyChecked = Array.from(allCheckboxes).some(c => c.checked);
                
                selectAll.checked = allChecked;
                selectAll.indeterminate = anyChecked && !allChecked;
            });
        });
    }
    @endcan

    // ==================== BULK PDF EXPORT ====================
    @can('rental-contracts.general.view')
    const exportBtn = document.getElementById('exportSelectedPDF');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get all selected checkboxes
            const selected = [];
            document.querySelectorAll('.contract-checkbox:checked').forEach(cb => {
                selected.push(cb.value);
            });
            
            if (selected.length === 0) {
                alert('Veuillez sélectionner au moins un contrat');
                return;
            }
            
            // Create form inputs
            const form = document.getElementById('bulkPDFForm');
            const inputsContainer = document.getElementById('bulkPDFInputs');
            inputsContainer.innerHTML = '';
            
            selected.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                inputsContainer.appendChild(input);
            });
            
            // Submit form
            form.submit();
        });
    }
    @endcan

    // Initialize all dropdowns
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

@include('backoffice.rental-contracts.partials._modal_delete')
@endsection