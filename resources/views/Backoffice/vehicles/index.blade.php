@section('content')
<script>
// Override the native alert function to block DataTables warnings
(function() {
    // Store the original alert
    var originalAlert = window.alert;
    
    // Replace with filtered version
    window.alert = function(message) {
        // Check if this is a DataTables warning
        if (message && (message.includes('DataTables') || message.includes('datatables'))) {
            console.log('DataTables warning blocked:', message);
            return; // Block the alert
        }
        // Allow other alerts through
        originalAlert(message);
    };
    
    // Also set DataTables error mode if available
    if (window.$ && $.fn && $.fn.dataTable) {
        $.fn.dataTable.ext.errMode = 'none';
    }
})();
</script>
<?php $page = 'cars'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<head>
    <style>
        .dataTables_wrapper,
        .dataTables_wrapper .table-responsive {
            overflow: visible !important;
        }
        .dataTables_wrapper .dropdown-menu {
            z-index: 1055;
        }
        .filter-row {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .filter-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-label {
            font-weight: 500;
            color: #6c757d;
            min-width: 80px;
        }
        
        /* Pagination styling */
        .pagination {
            margin: 0;
        }
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .pagination .page-link {
            color: #0d6efd;
            border-radius: 8px;
            margin: 0 3px;
        }
        .pagination-info {
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>

<div class="page-wrapper">
    <div class="content me-4">

        {{-- Breadcrumb + top buttons (Print/Export/Add) --}}
        @include('backoffice.vehicles.partials._breadcrumbs')

        <!-- FILTER FORM -->
        <form method="GET" id="filterForm" action="{{ route('backoffice.vehicles.index') }}">
            
            <!-- Top Row: Sort By, Date Range, Filter Button -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    
                    <!-- SORT BY DROPDOWN -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                           data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>
                            Trier par : 
                            @if(request('sort') == 'az')
                                A → Z
                            @elseif(request('sort') == 'za')
                                Z → A
                            @elseif(request('sort') == 'price_asc')
                                Prix (croissant)
                            @elseif(request('sort') == 'price_desc')
                                Prix (décroissant)
                            @elseif(request('sort') == 'mileage_asc')
                                Kilométrage (croissant)
                            @elseif(request('sort') == 'mileage_desc')
                                Kilométrage (décroissant)
                            @else
                                Plus récents
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.index', array_merge(request()->except('sort', 'page'), ['sort' => 'latest'])) }}">Plus récents</a></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.index', array_merge(request()->except('sort', 'page'), ['sort' => 'az'])) }}">A → Z</a></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.index', array_merge(request()->except('sort', 'page'), ['sort' => 'za'])) }}">Z → A</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.index', array_merge(request()->except('sort', 'page'), ['sort' => 'price_asc'])) }}">Prix (croissant)</a></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.index', array_merge(request()->except('sort', 'page'), ['sort' => 'price_desc'])) }}">Prix (décroissant)</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.index', array_merge(request()->except('sort', 'page'), ['sort' => 'mileage_asc'])) }}">Kilométrage (croissant)</a></li>
                            <li><a class="dropdown-item" href="{{ route('backoffice.vehicles.index', array_merge(request()->except('sort', 'page'), ['sort' => 'mileage_desc'])) }}">Kilométrage (décroissant)</a></li>
                        </ul>
                    </div>

                    <!-- DATE RANGE -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                           data-bs-toggle="dropdown">
                            <i class="ti ti-calendar me-1"></i>
                            {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') : now()->subDays(7)->format('d/m/Y') }} - 
                            {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') : now()->format('d/m/Y') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;">
                            <div class="mb-3">
                                <label class="form-label">Date début</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->subDays(7)->format('Y-m-d')) }}" form="filterForm">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date fin</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}" form="filterForm">
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-sm" form="filterForm">Appliquer</button>
                            </div>
                        </div>
                    </div>

                    <!-- FILTER BUTTON -->
                    <div>
                        <a href="#filtercollapse"
                           class="filtercollapse coloumn d-inline-flex align-items-center"
                           data-bs-toggle="collapse"
                           role="button">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>
                </div>

                <!-- SEARCH & ACTIONS - FIXED ADD BUTTON -->
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input 
                                type="text" 
                                name="search"
                                id="searchInput"
                                class="form-control" 
                                placeholder="Rechercher..." 
                                value="{{ request('search') }}" 
                                autocomplete="off"
                            >
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Add Button - FIXED: Now links to create page instead of modal -->
                    @can('vehicles.general.create')
                    <div class="mb-0">
                        <a href="{{ route('backoffice.vehicles.create') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-plus me-2"></i>Ajouter un véhicule
                        </a>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- FILTERS ROW - HIDDEN BY DEFAULT -->
            <div class="filter-row collapse {{ request()->has('select_cars') || request()->has('type') || request()->has('location') || request()->has('status') || request()->has('model_id') ? 'show' : '' }}" id="filtercollapse">
                
                <!-- Select Model -->
                <div class="filter-item">
                    <span class="filter-label">Modèle:</span>
                    <select name="model_id" class="form-select" style="width: 200px;" onchange="this.form.submit()">
                        <option value="">Tous les modèles</option>
                        @foreach($models as $model)
                            <option value="{{ $model->id }}" {{ request('model_id') == $model->id ? 'selected' : '' }}>
                                {{ $model->brand->name ?? '' }} {{ $model->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type (Brand/Model search) -->
                <div class="filter-item">
                    <span class="filter-label">Marque/Modèle:</span>
                    <input type="text" name="type" class="form-control" style="width: 150px;" placeholder="Rechercher" value="{{ request('type') }}" onchange="this.form.submit()">
                </div>

                <!-- Location -->
                <div class="filter-item">
                    <span class="filter-label">Localisation:</span>
                    <input type="text" name="location" class="form-control" style="width: 150px;" placeholder="Ville" value="{{ request('location') }}" onchange="this.form.submit()">
                </div>

                <!-- Status -->
                <div class="filter-item">
                    <span class="filter-label">Statut:</span>
                    <select name="status" class="form-select" style="width: 150px;" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Indisponible</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Vendu</option>
                    </select>
                </div>

                <!-- Apply Button -->
                <div class="filter-item">
                    <button type="submit" class="btn btn-primary">Appliquer</button>
                </div>

                <!-- Clear All -->
                <div class="filter-item">
                    <a href="{{ route('backoffice.vehicles.index') }}" class="btn btn-light">Effacer tout</a>
                </div>
            </div>
        </form>
        <!-- /FILTER FORM -->

        {{-- Data table --}}
        @include('backoffice.vehicles.partials._table', ['vehicles' => $vehicles, 'permissions' => $permissions])

        <!-- Pagination with counter -->
        @if(isset($vehicles) && $vehicles->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
            <div class="pagination-info mb-3 mb-md-0">
                Affichage de <span class="fw-semibold">{{ $vehicles->firstItem() }}</span> à <span class="fw-semibold">{{ $vehicles->lastItem() }}</span> 
                sur <span class="fw-semibold">{{ $vehicles->total() }}</span> véhicules
            </div>
            <div class="pagination-container">
                @if ($vehicles->hasPages())
                    <nav aria-label="Navigation des pages">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            @if ($vehicles->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">&laquo;</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $vehicles->previousPageUrl() }}" rel="prev" aria-label="Précédent">&laquo;</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $start = max(1, $vehicles->currentPage() - 2);
                                $end = min($vehicles->lastPage(), $vehicles->currentPage() + 2);
                                
                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . $vehicles->url(1) . '">1</a></li>';
                                    if ($start > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start; $i <= $end; $i++) {
                                    $active = $i == $vehicles->currentPage() ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $vehicles->url($i) . '">' . $i . '</a></li>';
                                }
                                
                                if ($end < $vehicles->lastPage()) {
                                    if ($end < $vehicles->lastPage() - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . $vehicles->url($vehicles->lastPage()) . '">' . $vehicles->lastPage() . '</a></li>';
                                }
                            @endphp

                            {{-- Next Page Link --}}
                            @if ($vehicles->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $vehicles->nextPageUrl() }}" rel="next" aria-label="Suivant">&raquo;</a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">&raquo;</span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- Footer theme --}}
    @include('backoffice.vehicles.partials._footer')
</div>

<!-- Delete Vehicle Modal -->
<div class="modal fade" id="delete_vehicle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="deleteVehicleForm">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">Supprimer le véhicule</h4>
                    <p class="mb-3" id="deleteVehicleText">Êtes-vous sûr de vouloir supprimer ce véhicule ?</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('Backoffice.vignettes.partials._modals_js')

<!-- AUTO SEARCH SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');

    if (form && searchInput) {
        let timer;
        searchInput.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => form.submit(), 400);
        });
    }

    // Delete modal handler
    const deleteModal = document.getElementById('delete_vehicle');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            if (button) {
                const action = button.getAttribute('data-delete-action');
                const details = button.getAttribute('data-delete-details') || 'ce véhicule';
                
                const form = document.getElementById('deleteVehicleForm');
                const text = document.getElementById('deleteVehicleText');
                
                if (action && form) {
                    form.action = action;
                }
                
                if (text && details) {
                    text.innerHTML = 'Êtes-vous sûr de vouloir supprimer ' + details + ' ?';
                }
            }
        });
    }
});

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        document.getElementById('filterForm').submit();
    }
}
</script>
@endsection