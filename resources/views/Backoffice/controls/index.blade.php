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
<?php $page = 'vehicle-controls'; ?>
@extends('layout.mainlayout_admin')

@section('content')

<div class="page-wrapper">
    <div class="content me-4">

        {{-- Breadcrumb --}}
        @include('Backoffice.controls.partials._breadcrumbs')

        {{-- Filter Bar --}}
        <x-backoffice.filter-bar
            :route="route('backoffice.controls.index')"
            search-placeholder="Rechercher un contrôle (numéro, véhicule...)"
            :create-url="route('backoffice.controls.create')"
            create-label="Nouveau contrôle"
            create-permission="vehicle-controls.general.create"
            :sort-options="[
                'latest' => 'Plus récents',
                'oldest' => 'Plus anciens',
                'control_number_asc' => 'N° croissant',
                'control_number_desc' => 'N° décroissant',
                'mileage_asc' => 'Km croissant',
                'mileage_desc' => 'Km décroissant',
            ]"
        >
            <x-slot name="filters">
                <div class="col-md-3">
                    <label class="form-label fw-medium">Véhicule</label>
                    <select name="vehicle_id" form="filterForm" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ request('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->registration_number }}
                                @if($vehicle->model && $vehicle->model->brand)
                                    — {{ $vehicle->model->brand->name }} {{ $vehicle->model->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">Statut</label>
                    <select name="status" form="filterForm" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En cours</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">Du</label>
                    <input type="date" name="date_from" form="filterForm" class="form-control"
                           value="{{ request('date_from') }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">Au</label>
                    <input type="date" name="date_to" form="filterForm" class="form-control"
                           value="{{ request('date_to') }}" onchange="this.form.submit()">
                </div>
            </x-slot>
        </x-backoffice.filter-bar>

        {{-- Data Table --}}
        @include('Backoffice.controls.partials._table', ['controls' => $controls, 'permissions' => $permissions])

        {{-- Pagination --}}
        <x-backoffice.smart-pagination :paginator="$controls" label="contrôles" />

    </div>

    {{-- Footer --}}
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">2025 &copy; Dreamsrent</p>
    </div>
</div>

{{-- Delete Modal --}}
@include('Backoffice.controls.partials._modal_delete')

{{-- Create/Edit Modals JS --}}
@include('Backoffice.controls.partials._modals_js')

@endsection
