<?php $page = 'agencies'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('backoffice.agencies.partials._breadcrumbs')

        <!-- FILTER FORM -->
        <form method="GET" id="filterForm">

            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">

                <div class="d-flex align-items-center flex-wrap row-gap-3">

                    <!-- SORT -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                           data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>
                            Trier :
                            @if(request('sort') == 'az')
                                A → Z
                            @elseif(request('sort') == 'za')
                                Z → A
                            @else
                                Derniers
                            @endif
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.agencies.index', array_merge(request()->all(), ['sort' => 'az'])) }}">
                                    A → Z
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.agencies.index', array_merge(request()->all(), ['sort' => 'za'])) }}">
                                    Z → A
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.agencies.index') }}">
                                    Derniers
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- FILTER TOGGLE -->
                    <div>
                        <a href="#filtercollapse"
                           class="filtercollapse coloumn d-inline-flex align-items-center"
                           data-bs-toggle="collapse">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>

                </div>

                <!-- SEARCH & ACTIONS -->
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">

                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   id="searchInput"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Rechercher une agence...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton Ajouter - contrôlé par permission CREATE --}}
                    @can('agencies.general.create')
                        <div class="mb-0">
                            <a href="javascript:void(0);"
                               class="btn btn-primary d-flex align-items-center"
                               data-bs-toggle="modal"
                               data-bs-target="#add_agency">
                                <i class="ti ti-plus me-2"></i>Ajouter une agence
                            </a>
                        </div>
                    @endcan

                </div>
            </div>
        </form>

        <!-- FILTER COLLAPSE -->
        <div class="collapse {{ request()->has('status') ? 'show' : '' }}" id="filtercollapse">
            <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Statut</label>
                        <select name="status" form="filterForm" class="form-select" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Actif</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-2 d-flex align-items-end">
                        <a href="{{ route('backoffice.agencies.index') }}" class="btn btn-sm btn-outline-danger w-100">
                            <i class="ti ti-x me-1"></i>Tout effacer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="custom-datatable-filter table-responsive">
            @if($agencies->count())
                @include('backoffice.agencies.partials._table', ['agencies' => $agencies, 'permissions' => $permissions])
            @else
                <div class="text-center py-5">
                    <i class="ti ti-building-off fs-48 text-gray-4 mb-3"></i>
                    <h5 class="mb-2">Aucune agence trouvée</h5>
                    @can('agencies.general.create')
                        <p class="text-muted mb-3">Commencez par créer une agence</p>
                        <a href="javascript:void(0);"
                           class="btn btn-primary mt-3"
                           data-bs-toggle="modal"
                           data-bs-target="#add_agency">
                            <i class="ti ti-plus me-2"></i>Ajouter une agence
                        </a>
                    @endcan
                </div>
            @endif
        </div>

        <!-- PAGINATION -->
        @if($agencies->total() > 0)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Affichage de {{ $agencies->firstItem() }} à {{ $agencies->lastItem() }} sur {{ $agencies->total() }} agences
            </div>
            <div>
                {{ $agencies->withQueryString()->links() }}
            </div>
        </div>
        @endif

    </div>

    <!-- Footer -->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Politique de confidentialité</a>
            <a href="javascript:void(0);" class="ms-4">Conditions d'utilisation</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);" class="text-secondary">Dreams</a></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');

    if (form && searchInput) {
        let timer;
        searchInput.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                form.submit();
            }, 400);
        });
    }
});

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        document.getElementById('filterForm').submit();
    }
}
</script>

@include('backoffice.agencies.partials._modal_create')
@include('backoffice.agencies.partials._modal_edit')
@include('backoffice.agencies.partials._modal_delete')
@include('backoffice.agencies.partials._modals_js')

@endsection