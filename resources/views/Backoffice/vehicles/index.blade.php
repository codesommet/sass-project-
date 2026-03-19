@section('content')
<script>
(function() {
    var originalAlert = window.alert;
    window.alert = function(message) {
        if (message && (message.includes('DataTables') || message.includes('datatables'))) {
            console.log('DataTables warning blocked:', message);
            return;
        }
        originalAlert(message);
    };
    if (window.$ && $.fn && $.fn.dataTable) $.fn.dataTable.ext.errMode = 'none';
})();
</script>
<?php $page = 'cars'; ?>
@extends('layout.mainlayout_admin')

@section('content')

<div class="page-wrapper">
    <div class="content me-4">

        {{-- Breadcrumb + Stats --}}
        @include('backoffice.vehicles.partials._breadcrumbs')

        {{-- Filter Bar (reusable component) --}}
        <x-backoffice.filter-bar
            :route="route('backoffice.vehicles.index')"
            search-placeholder="Rechercher un véhicule (immatriculation, marque, modèle...)"
            :create-url="route('backoffice.vehicles.create')"
            create-label="Nouveau véhicule"
            create-permission="vehicles.general.create"
            :sort-options="[
                'latest' => 'Plus récents',
                'az' => 'A → Z',
                'za' => 'Z → A',
                'price_asc' => 'Prix croissant',
                'price_desc' => 'Prix décroissant',
                'mileage_asc' => 'Km croissant',
                'mileage_desc' => 'Km décroissant',
            ]"
        >
            <x-slot name="filters">
                <div class="col-md-3">
                    <label class="form-label fw-medium">Modèle</label>
                    <select name="model_id" form="filterForm" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les modèles</option>
                        @foreach($models as $model)
                            <option value="{{ $model->id }}" {{ request('model_id') == $model->id ? 'selected' : '' }}>
                                {{ $model->brand->name ?? '' }} {{ $model->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">Statut</label>
                    <select name="status" form="filterForm" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Indisponible</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Vendu</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">Localisation</label>
                    <input type="text" name="location" form="filterForm" class="form-control"
                           placeholder="Ville..." value="{{ request('location') }}"
                           onchange="this.form.submit()">
                </div>
            </x-slot>
        </x-backoffice.filter-bar>

        {{-- Data Table --}}
        @include('backoffice.vehicles.partials._table', ['vehicles' => $vehicles, 'permissions' => $permissions])

        {{-- Pagination (reusable component) --}}
        <x-backoffice.smart-pagination :paginator="$vehicles" label="véhicules" />

    </div>

    @include('backoffice.vehicles.partials._footer')
</div>

{{-- Delete Modal (reusable component) --}}
<x-backoffice.delete-modal
    id="delete_vehicle"
    title="Supprimer le véhicule"
    form-id="deleteVehicleForm"
    name-id="deleteVehicleName"
    warning="Le véhicule et tous ses documents associés seront supprimés."
/>

@include('Backoffice.vignettes.partials._modals_js')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete modal handler
    const deleteModal = document.getElementById('delete_vehicle');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const form = document.getElementById('deleteVehicleForm');
            const name = document.getElementById('deleteVehicleName');
            if (form) form.action = btn.getAttribute('data-delete-action') || '#';
            if (name) name.textContent = btn.getAttribute('data-delete-name') || '—';
        });
    }
});
</script>
@endsection
