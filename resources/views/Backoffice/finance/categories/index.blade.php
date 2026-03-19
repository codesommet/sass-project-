<?php $page = 'finance-categories'; ?>
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
    
    .badge-income { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-expense { background: #f8d7da; color: #721c24; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-both { background: #cce5ff; color: #004085; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    
    .table-responsive, 
    .custom-datatable-filter, 
    .dataTables_wrapper {
        overflow: visible !important;
    }
    
    .dropdown-menu {
        z-index: 9999 !important;
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
        @include('backoffice.finance.categories.partials._breadcrumbs')

        <form method="GET" id="filterForm" action="{{ request()->url() }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <!-- Sort Dropdown -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" role="button">
                            <i class="ti ti-filter me-1"></i> Trier : 
                            @if(request('sort') == 'oldest') Plus anciennes
                            @elseif(request('sort') == 'name_asc') Nom A-Z
                            @elseif(request('sort') == 'name_desc') Nom Z-A
                            @else Plus récentes @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">Plus récentes</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}">Plus anciennes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}">Nom A-Z</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}">Nom Z-A</a></li>
                        </ul>
                    </div>
                    
                    <!-- Filters Toggle -->
                    <div>
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" role="button">
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
                                   class="form-control" placeholder="Rechercher une catégorie...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Bouton Ajouter - contrôlé par permission CREATE --}}
                    @can('transaction-categories.general.create')
                        <div class="mb-0">
                            <a href="{{ route('backoffice.finance.categories.create') }}" class="btn btn-primary d-flex align-items-center">
                                <i class="ti ti-plus me-2"></i>Nouvelle catégorie
                            </a>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Filters Panel -->
            <div class="collapse @if(request()->has('type')) show @endif" id="filtercollapse">
                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Type</label>
                            <select name="type" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Revenu</option>
                                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Dépense</option>
                                <option value="both" {{ request('type') == 'both' ? 'selected' : '' }}>Les deux</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <a href="{{ route('backoffice.finance.categories.index') }}" class="btn btn-sm btn-outline-danger w-100">
                                <i class="ti ti-x me-1"></i>Tout effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="custom-datatable-filter table-responsive">
            @include('backoffice.finance.categories.partials._table', ['categories' => $categories])
        </div>

        <!-- PAGINATION - FIXED VERSION -->
        @if(isset($categories) && $categories->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
            <div class="pagination-info mb-3 mb-md-0">
                Affichage de <span class="fw-semibold">{{ $categories->firstItem() }}</span> à <span class="fw-semibold">{{ $categories->lastItem() }}</span> 
                sur <span class="fw-semibold">{{ $categories->total() }}</span> catégories
            </div>
            <div class="pagination-container">
                @if ($categories->hasPages())
                    <nav aria-label="Navigation des pages">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            @if ($categories->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="ti ti-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $categories->previousPageUrl() }}" rel="prev" aria-label="Précédent">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements - Show 5 pages at a time --}}
                            @php
                                $start = max(1, $categories->currentPage() - 2);
                                $end = min($categories->lastPage(), $categories->currentPage() + 2);
                                
                                // Show first page with ellipsis if needed
                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . $categories->url(1) . '">1</a></li>';
                                    if ($start > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                // Show pages around current page
                                for ($i = $start; $i <= $end; $i++) {
                                    $active = $i == $categories->currentPage() ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $categories->url($i) . '">' . $i . '</a></li>';
                                }
                                
                                // Show last page with ellipsis if needed
                                if ($end < $categories->lastPage()) {
                                    if ($end < $categories->lastPage() - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . $categories->url($categories->lastPage()) . '">' . $categories->lastPage() . '</a></li>';
                                }
                            @endphp

                            {{-- Next Page Link --}}
                            @if ($categories->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $categories->nextPageUrl() }}" rel="next" aria-label="Suivant">
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

    // Filter toggle
    const filterToggle = document.querySelector('.filtercollapse');
    const filterCollapse = document.getElementById('filtercollapse');

    if (filterToggle && filterCollapse) {
        filterToggle.removeAttribute('data-bs-toggle');
        
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
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

@include('backoffice.finance.categories.partials._modal_delete')
@endsection