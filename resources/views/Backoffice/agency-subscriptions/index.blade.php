<?php $page = 'agency-subscriptions'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">

        @include('Backoffice.agency-subscriptions.partials._breadcrumbs')

        <!-- FILTER FORM -->
        <form method="GET" id="filterForm">

            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">

                <div class="d-flex align-items-center flex-wrap row-gap-3">

                    <!-- SORT -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                           data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>
                            Trier :
                            @if(request('sort') == 'az')
                                Plan A → Z
                            @elseif(request('sort') == 'za')
                                Plan Z → A
                            @else
                                Derniers
                            @endif
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <a class="dropdown-item rounded-1"
                                   href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['sort'=>'az'])) }}">
                                    Plan A → Z
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-1"
                                   href="{{ route('backoffice.agency-subscriptions.index', array_merge(request()->all(), ['sort'=>'za'])) }}">
                                    Plan Z → A
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-1"
                                   href="{{ route('backoffice.agency-subscriptions.index') }}">
                                    Derniers
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- FILTER TOGGLE -->
                    <div class="dropdown">
                        <a href="#filtercollapse"
                           class="filtercollapse coloumn d-inline-flex align-items-center"
                           data-bs-toggle="collapse">
                            <i class="ti ti-filter me-1"></i> Filtres
                            <span class="badge badge-xs rounded-pill bg-danger ms-2">
                                {{ collect(request()->only(['status','provider','search','sort']))->filter()->count() }}
                            </span>
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
                                   placeholder="Rechercher">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton Ajouter - contrôlé par permission CREATE --}}
                    @can('agency-subscriptions.general.create')
                        <div class="mb-0">
                            <a href="javascript:void(0);"
                               class="btn btn-primary d-flex align-items-center"
                               data-bs-toggle="modal"
                               data-bs-target="#create_subscription">
                                <i class="ti ti-plus me-2"></i>Ajouter un abonnement
                            </a>
                        </div>
                    @endcan
                </div>

            </div>
        </form>
        <!-- /Table Header -->


        <!-- FILTER COLLAPSE -->
        <div class="collapse {{ request()->has('status') || request()->has('provider') ? 'show' : '' }}" id="filtercollapse">
            <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Statut</label>
                        <select name="status" form="filterForm" class="form-select" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expiré</option>
                            <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Essai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Fournisseur</label>
                        <select name="provider" form="filterForm" class="form-select" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <option value="manual" {{ request('provider') == 'manual' ? 'selected' : '' }}>Manuel</option>
                            <option value="stripe" {{ request('provider') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                            <option value="paypal" {{ request('provider') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="other" {{ request('provider') == 'other' ? 'selected' : '' }}>Autre</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-2 d-flex align-items-end">
                        <a href="{{ route('backoffice.agency-subscriptions.index') }}" class="btn btn-sm btn-outline-danger w-100">
                            <i class="ti ti-x me-1"></i>Tout effacer
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <!-- TABLE -->
        <div class="custom-datatable-filter table-responsive">
            @include('Backoffice.agency-subscriptions.partials._table', ['subscriptions' => $subscriptions, 'permissions' => $permissions])
        </div>

        <!-- PAGINATION -->
        @if($subscriptions->total() > 0)
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Affichage de {{ $subscriptions->firstItem() }} à {{ $subscriptions->lastItem() }} sur {{ $subscriptions->total() }} abonnements
            </div>
            <div>
                {{ $subscriptions->withQueryString()->links() }}
            </div>
        </div>
        @endif

    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="#">Politique de confidentialité</a>
            <a href="#" class="ms-4">Conditions d'utilisation</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="#" class="text-secondary">Dreams</a></p>
    </div>
</div>


<!-- AUTO SEARCH -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchInput');
    const form = document.getElementById('filterForm');

    if (searchInput && form) {
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

<script>
document.addEventListener('DOMContentLoaded', function () {

    const deleteModal = document.getElementById('delete_subscription');

    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {

            const button = event.relatedTarget;

            const action = button.getAttribute('data-delete-action');
            const plan   = button.getAttribute('data-subscription-name');

            const form = document.getElementById('deleteSubscriptionForm');
            form.setAttribute('action', action);

            document.getElementById('deletePlanName').textContent = plan;
        });
    }

});
</script>

@include('Backoffice.agency-subscriptions.partials._modal_create')
@include('Backoffice.agency-subscriptions.partials._modal_edit')
@include('Backoffice.agency-subscriptions.partials._modal_delete')
@include('Backoffice.agency-subscriptions.partials._modals_js')

@endsection