<?php $page = 'signatures-setting'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4 pb-0">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Signatures & Branding</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Accueil</a></li>
                        <li class="breadcrumb-item">Paramètres</li>
                        <li class="breadcrumb-item active" aria-current="page">Signatures & Branding</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if(session('toast'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Succès!</strong> {{ session('toast')['message'] }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-3">
                @include('Backoffice.profile.partials._agency_settings_sidebar', [
                    'agency' => $agency,
                    'active' => 'signatures',
                ])
            </div>

            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold mb-1">Signatures & Branding</h5>
                        <p class="text-muted mb-0">Gérez le logo, la signature et le cachet de votre agence pour les documents PDF</p>
                    </div>

                    <form action="{{ route('backoffice.agencies.settings.update', $agency) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="card-body">
                            <div class="row g-4">
                                <!-- Logo -->
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <h6 class="fw-bold mb-3"><i class="ti ti-photo me-2"></i>Logo de l'agence</h6>
                                        @if($agency->getFirstMediaUrl('logo'))
                                            <div class="mb-3 text-center">
                                                <img src="{{ $agency->getFirstMediaUrl('logo') }}" alt="Logo" class="img-fluid rounded" style="max-height: 120px;">
                                            </div>
                                            <div class="d-flex gap-2">
                                                <label class="btn btn-sm btn-outline-primary flex-fill">
                                                    <i class="ti ti-upload me-1"></i>Changer
                                                    <input type="file" name="logo" accept="image/*" class="d-none">
                                                </label>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Supprimer le logo ?')) document.getElementById('delete-logo-form').submit();">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-center py-4 bg-light rounded mb-3">
                                                <i class="ti ti-photo-off fs-1 text-muted"></i>
                                                <p class="text-muted small mb-0 mt-2">Aucun logo</p>
                                            </div>
                                            <label class="btn btn-sm btn-outline-primary w-100">
                                                <i class="ti ti-upload me-1"></i>Uploader un logo
                                                <input type="file" name="logo" accept="image/*" class="d-none">
                                            </label>
                                        @endif
                                        <small class="text-muted d-block mt-2">Formats: JPG, PNG. Max: 5MB. Recommandé: 300x100px</small>
                                    </div>
                                </div>

                                <!-- Signature -->
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <h6 class="fw-bold mb-3"><i class="ti ti-writing-sign me-2"></i>Signature / Cachet</h6>
                                        @if($agency->getFirstMediaUrl('signature'))
                                            <div class="mb-3 text-center">
                                                <img src="{{ $agency->getFirstMediaUrl('signature') }}" alt="Signature" class="img-fluid rounded" style="max-height: 120px;">
                                            </div>
                                            <div class="d-flex gap-2">
                                                <label class="btn btn-sm btn-outline-primary flex-fill">
                                                    <i class="ti ti-upload me-1"></i>Changer
                                                    <input type="file" name="signature" accept="image/*" class="d-none">
                                                </label>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Supprimer la signature ?')) document.getElementById('delete-signature-form').submit();">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="text-center py-4 bg-light rounded mb-3">
                                                <i class="ti ti-writing-sign-off fs-1 text-muted"></i>
                                                <p class="text-muted small mb-0 mt-2">Aucune signature</p>
                                            </div>
                                            <label class="btn btn-sm btn-outline-primary w-100">
                                                <i class="ti ti-upload me-1"></i>Uploader une signature
                                                <input type="file" name="signature" accept="image/*" class="d-none">
                                            </label>
                                        @endif
                                        <small class="text-muted d-block mt-2">Formats: JPG, PNG. Max: 5MB. Recommandé: 200x80px</small>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-4">
                                <i class="ti ti-info-circle me-2"></i>
                                Le logo et la signature seront automatiquement inclus dans vos exports PDF (factures, contrats).
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete forms -->
<form id="delete-logo-form" action="{{ route('backoffice.agencies.settings.delete-logo', $agency) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
<form id="delete-signature-form" action="{{ route('backoffice.agencies.settings.delete-signature', $agency) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection
