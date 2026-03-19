<?php $page = 'change-password'; ?>
@extends('layout.mainlayout_admin')

@section('content')
    <div class="page-wrapper">

        <div class="content me-4 pb-0">

            <!-- Fil d'Ariane -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">Changer le mot de passe</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('backoffice.dashboard') }}">Accueil</a>
                            </li>
                            <li class="breadcrumb-item active">Changer le mot de passe</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Fil d'Ariane -->

            <div class="row">
                <!-- Barre latérale -->
                <div class="col-lg-3 mb-3 mb-lg-0">
                    @include('Backoffice.profile.partials._agency_settings_sidebar', [
                        'active' => 'change-password',
                        'agency' => $agency,
                    ])
                </div>

                <!-- Contenu principal -->
                <div class="col-lg-9">

                    <div class="card">
                        <div class="card-header">
                            <h5>Paramètres de sécurité</h5>
                        </div>

                        <div class="card-body">

                            <!-- SECTION MOT DE PASSE -->
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="row gy-3 align-items-center">
                                        <div class="col-lg-9">
                                            <div class="row gy-3 align-items-center">
                                                <div class="col-md-6">
                                                    <div>
                                                        <h6 class="fs-14 fw-medium">Mot de passe</h6>
                                                        <p class="fs-13">Définissez un mot de passe unique pour sécuriser le compte</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div>
                                                        @if (auth('backoffice')->user()->password_changed_at)
                                                            <p class="mb-0">
                                                                <i class="ti ti-circle-check-filled text-success me-1"></i>
                                                                Dernière modification le
                                                                {{ auth('backoffice')->user()->password_changed_at->format('d/m/Y à H:i') }}
                                                            </p>
                                                        @else
                                                            <p class="mb-0">
                                                                <i class="ti ti-alert-circle text-warning me-1"></i>
                                                                Mot de passe jamais modifié
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="d-flex justify-content-lg-end">
                                                <a href="javascript:void(0);" class="btn btn-dark" data-bs-toggle="modal"
                                                    data-bs-target="#change_password">
                                                    Modifier
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /SECTION MOT DE PASSE -->

                            <!-- MODALE CHANGER MOT DE PASSE -->
                            <div class="modal fade" id="change_password" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Changer le mot de passe</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Fermer"></button>
                                        </div>

                                        <form method="POST" action="{{ route('backoffice.profile.update-password') }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <label class="form-label">Mot de passe actuel</label>
                                                    <input type="password" name="current_password"
                                                        class="form-control @error('current_password') is-invalid @enderror"
                                                        autocomplete="current-password">
                                                    @error('current_password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Nouveau mot de passe</label>
                                                    <input type="password" name="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        autocomplete="new-password">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-0">
                                                    <label class="form-label">Confirmer le nouveau mot de passe</label>
                                                    <input type="password" name="password_confirmation" class="form-control"
                                                        autocomplete="new-password">
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                    Annuler
                                                </button>
                                                <button type="submit" class="btn btn-dark">
                                                    Mettre à jour le mot de passe
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            <!-- /MODALE CHANGER MOT DE PASSE -->

                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Pied de page -->
        <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
            <p class="mb-0">
                <a href="javascript:void(0);">Politique de confidentialité</a>
                <a href="javascript:void(0);" class="ms-4">Conditions d'utilisation</a>
            </p>
            <p>&copy; 2025 Dreamsrent, Fait avec
                <span class="text-danger">❤</span>
                par <a href="javascript:void(0);" class="text-secondary">Dreams</a>
            </p>
        </div>

    </div>

    {{-- Toast de succès --}}
    @if (session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <i class="ti ti-check-circle me-2"></i>
                    <strong class="me-auto">Succès</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                        aria-label="Fermer"></button>
                </div>
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toastElements = document.querySelectorAll('.toast');
                toastElements.forEach(function(toastEl) {
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                    setTimeout(() => toast.hide(), 5000);
                });
            });
        </script>
    @endif

    {{-- Ouvrir automatiquement la modale si la validation a échoué --}}
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = document.getElementById('change_password');
                if (modalEl && window.bootstrap) {
                    new bootstrap.Modal(modalEl).show();
                }
            });
        </script>
    @endif
@endsection
