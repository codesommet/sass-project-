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

<?php $page = 'vignettes'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    /* Pagination styling */
    .pagination {
        margin: 0;
    }
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    .pagination .page-link {
        color: #0d6efd;
        border-radius: 8px;
        margin: 0 3px;
        padding: 8px 12px;
    }
    .pagination .page-link:hover {
        background-color: #e9ecef;
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

        @include('Backoffice.vignettes.partials._breadcrumbs', ['vehicle' => $vehicle ?? null])

        <!-- FILTER + SEARCH FORM -->
        <form method="GET" id="filterForm" action="{{ request()->url() }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    
                    <!-- SORT -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i> Trier : 
                            @if(request('sort') == 'oldest') Plus anciennes
                            @elseif(request('sort') == 'amount_asc') Montant ↑
                            @elseif(request('sort') == 'amount_desc') Montant ↓
                            @elseif(request('sort') == 'year_asc') Année ↑
                            @elseif(request('sort') == 'year_desc') Année ↓
                            @else Plus récentes @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            @if(isset($vehicle) && $vehicle)
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'latest'])) }}">
                                        Plus récentes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'oldest'])) }}">
                                        Plus anciennes
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'amount_desc'])) }}">
                                        Montant (plus élevé)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'amount_asc'])) }}">
                                        Montant (moins élevé)
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'year_desc'])) }}">
                                        Année (récente)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => $vehicle->id], request()->except('sort'), ['sort'=>'year_asc'])) }}">
                                        Année (ancienne)
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => 'all'], request()->except('sort'), ['sort'=>'latest'])) }}">
                                        Plus récentes
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => 'all'], request()->except('sort'), ['sort'=>'oldest'])) }}">
                                        Plus anciennes
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => 'all'], request()->except('sort'), ['sort'=>'amount_desc'])) }}">
                                        Montant (plus élevé)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => 'all'], request()->except('sort'), ['sort'=>'amount_asc'])) }}">
                                        Montant (moins élevé)
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => 'all'], request()->except('sort'), ['sort'=>'year_desc'])) }}">
                                        Année (récente)
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('backoffice.vehicles.vignettes.index', array_merge(['vehicle' => 'all'], request()->except('sort'), ['sort'=>'year_asc'])) }}">
                                        Année (ancienne)
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- FILTER TOGGLE -->
                    <div>
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>
                </div>

                <!-- SEARCH & ACTIONS -->
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control" placeholder="Rechercher une vignette...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton Ajouter - contrôlé par permission CREATE --}}
                    @can('vehicle-vignettes.general.create')
                        <div class="mb-0">
                            @if(isset($isGlobalView) && $isGlobalView)
                                <a href="{{ route('backoffice.vehicle-documents.vignettes.create') }}" class="btn btn-primary d-flex align-items-center">
                                    <i class="ti ti-plus me-2"></i>Ajouter une vignette
                                </a>
                            @else
                                <a href="{{ route('backoffice.vehicles.vignettes.create', ['vehicle' => $vehicle->id]) }}" class="btn btn-primary d-flex align-items-center">
                                    <i class="ti ti-plus me-2"></i>Ajouter une vignette
                                </a>
                            @endif
                        </div>
                    @endcan
                </div>
            </div>

            <!-- FILTER COLLAPSE -->
            <div class="collapse {{ request()->has('year') || request()->has('date_from') || request()->has('date_to') || request()->has('amount_min') || request()->has('amount_max') ? 'show' : '' }}" id="filtercollapse">
                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Année</label>
                            <select name="year" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Toutes</option>
                                @foreach($availableYears ?? [] as $year)
                                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Date du</label>
                            <input type="date" form="filterForm" name="date_from" value="{{ request('date_from') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Date au</label>
                            <input type="date" form="filterForm" name="date_to" value="{{ request('date_to') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Montant min (DH)</label>
                            <input type="number" form="filterForm" name="amount_min" value="{{ request('amount_min') }}" class="form-control" placeholder="0.00" step="0.01" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-medium">Montant max (DH)</label>
                            <input type="number" form="filterForm" name="amount_max" value="{{ request('amount_max') }}" class="form-control" placeholder="9999.99" step="0.01" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="{{ route('backoffice.vehicles.vignettes.index', $isGlobalView ? ['vehicle' => 'all'] : ['vehicle' => $vehicle->id ?? 1]) }}" class="btn btn-sm btn-outline-danger w-100">
                                <i class="ti ti-x me-1"></i>Tout effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- TABLE -->
        <div class="custom-datatable-filter table-responsive">
            @include('Backoffice.vignettes.partials._table', [
                'vignettes' => $vignettes, 
                'isGlobalView' => $isGlobalView ?? false,
                'vehicle' => $vehicle ?? null
            ])
        </div>

        <!-- PAGINATION -->
        @if($vignettes->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
            <div class="pagination-info mb-3 mb-md-0">
                Affichage de <span class="fw-semibold">{{ $vignettes->firstItem() }}</span> à <span class="fw-semibold">{{ $vignettes->lastItem() }}</span> 
                sur <span class="fw-semibold">{{ $vignettes->total() }}</span> vignettes
            </div>
            <div class="pagination-container">
                @if ($vignettes->hasPages())
                    <nav aria-label="Navigation des pages">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            @if ($vignettes->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="ti ti-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $vignettes->previousPageUrl() }}" rel="prev" aria-label="Précédent">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $start = max(1, $vignettes->currentPage() - 2);
                                $end = min($vignettes->lastPage(), $vignettes->currentPage() + 2);
                                
                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . $vignettes->url(1) . '">1</a></li>';
                                    if ($start > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start; $i <= $end; $i++) {
                                    $active = $i == $vignettes->currentPage() ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $vignettes->url($i) . '">' . $i . '</a></li>';
                                }
                                
                                if ($end < $vignettes->lastPage()) {
                                    if ($end < $vignettes->lastPage() - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . $vignettes->url($vignettes->lastPage()) . '">' . $vignettes->lastPage() . '</a></li>';
                                }
                            @endphp

                            {{-- Next Page Link --}}
                            @if ($vignettes->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $vignettes->nextPageUrl() }}" rel="next" aria-label="Suivant">
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

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Privacy Policy</a>
            <a href="javascript:void(0);" class="ms-4">Terms of Use</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by
            <a href="javascript:void(0);" class="text-secondary">Dreams</a>
        </p>
    </div>
</div>

<!-- AUTO SEARCH SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');
    if (!form || !input) return;
    let debounceTimer;
    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { form.submit(); }, 400);
    });
});

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        document.getElementById('filterForm').submit();
    }
}
</script>

@include('Backoffice.vignettes.partials._modal_delete')
@include('Backoffice.vignettes.partials._modals_js')
@endsection