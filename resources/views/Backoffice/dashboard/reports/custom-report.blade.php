
<?php $page = 'custom-report'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content">
        
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Rapport personnalisé</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Tableau de bord</a></li>
                        <li class="breadcrumb-item active">Rapport personnalisé</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5>Générer un rapport</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('backoffice.dashboard.reports.custom') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type de rapport</label>
                            <select class="form-select" name="type">
                                <option value="bookings">Réservations</option>
                                <option value="payments">Paiements</option>
                                <option value="vehicles">Véhicules</option>
                                <option value="clients">Clients</option>
                                <option value="revenue">Revenus</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Période</label>
                            <select class="form-select" name="period">
                                <option value="daily">Journalier</option>
                                <option value="weekly">Hebdomadaire</option>
                                <option value="monthly">Mensuel</option>
                                <option value="quarterly">Trimestriel</option>
                                <option value="yearly">Annuel</option>
                                <option value="custom">Personnalisé</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date début</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date fin</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Format d'export</label>
                            <select class="form-select" name="format">
                                <option value="preview">Aperçu</option>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('backoffice.dashboard') }}" class="btn btn-light me-2">Annuler</a>
                        <button type="submit" class="btn btn-primary">Générer le rapport</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Preview section -->
        @if(request()->has('type'))
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Aperçu du rapport</h5>
                    <div>
                        <a href="{{ route('backoffice.dashboard.export.pdf', request()->all()) }}" class="btn btn-sm btn-danger me-1">
                            <i class="ti ti-file-pdf me-1"></i>PDF
                        </a>
                        <a href="{{ route('backoffice.dashboard.export.excel', request()->all()) }}" class="btn btn-sm btn-success">
                            <i class="ti ti-file-spreadsheet me-1"></i>Excel
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('Backoffice.dashboard.exports.preview')
                </div>
            </div>
        @endif
        
    </div>
</div>
@endsection