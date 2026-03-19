<?php $page = 'user-details'; ?>

@extends('layout.mainlayout_admin')

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="mb-3">
                        <a href="{{ route('backoffice.users.index') }}" class="d-inline-flex align-items-center fw-medium">
                            <i class="ti ti-arrow-left me-1"></i>Utilisateurs
                        </a>
                    </div>

                    {{-- BASIC CARD --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="border-bottom mb-3 pb-3">
                                <h5>Détails de base</h5>
                            </div>

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg me-3" style="border-radius: 10px; overflow:hidden;">
                                        <img
                                            src="{{ $user->getFirstMediaUrl('avatar') ?: asset('assets/backoffice/images/placeholders/user.png') }}"
                                            alt="img"
                                            style="width:100%; height:100%; object-fit:cover;"
                                        >
                                    </span>

                                    <div>
                                        <h6 class="mb-1">{{ $user->name }}</h6>
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 me-2">
                                                Ajouté le :
                                                {{ optional($user->created_at)->format('d M Y') ?: '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <span class="badge badge-md bg-info-transparent">
                                        Statut : {{ ucfirst($user->status) }}
                                    </span>
                                    <span class="badge badge-md bg-orange-transparent">
                                        Dernière connexion :
                                        {{ optional($user->last_login_at)->format('d M Y, H:i') ?: '—' }}
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- TABS CARD --}}
                    <div class="card mb-4 mb-xl-0">
                        <div class="card-header py-0">
                            <ul class="nav nav-tabs nav-tabs-bottom tab-dark">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#user-overview" data-bs-toggle="tab">Aperçu</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#user-activity" data-bs-toggle="tab">Activité</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#user-history" data-bs-toggle="tab">Historique</a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">

                                {{-- OVERVIEW --}}
                                <div class="tab-pane fade active show" id="user-overview">
                                    <div class="border-bottom mb-3 pb-3">
                                        <div class="row">

                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Nom</h6>
                                                    <p class="fs-13">{{ $user->name }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Email</h6>
                                                    <p class="fs-13">{{ $user->email }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Téléphone</h6>
                                                    <p class="fs-13">{{ $user->phone ?: '—' }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Statut</h6>
                                                    <p class="fs-13">{{ ucfirst($user->status) }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Agence</h6>
                                                    <p class="fs-13">
                                                        {{ optional($user->agency)->name ?: '—' }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Rôles</h6>
                                                    <p class="fs-13">
                                                        @php($roles = $user->getRoleNames())
                                                        {{ $roles->count() ? $roles->implode(', ') : '—' }}
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                                            @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                                            <div class="col-lg-12">
                                                <a href="javascript:void(0);"
                                                   class="btn btn-primary btn-sm d-inline-flex align-items-center"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#edit_user"
                                                   data-edit-action="{{ route('backoffice.users.update', $user) }}"
                                                   data-user-name="{{ $user->name }}"
                                                   data-user-email="{{ $user->email }}"
                                                   data-user-phone="{{ $user->phone }}"
                                                   data-user-status="{{ $user->status }}"
                                                   data-user-agency="{{ $user->agency_id }}"
                                                   data-user-avatar="{{ $user->getFirstMediaUrl('avatar') }}"
                                                >
                                                    <i class="ti ti-edit me-1"></i>Modifier
                                                </a>
                                            </div>
                                            @endif

                                        </div>
                                    </div>

                                    {{-- DOCUMENTS (placeholder style like template) --}}
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                            <h6>Documents</h6>
                                            @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                                            <a href="javascript:void(0);" class="link-default"><i class="ti ti-edit"></i></a>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center flex-wrap gap-4">
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">
                                                    <img src="{{ URL::asset('admin_assets/img/icons/pdf-icon.svg') }}" alt="img">
                                                </span>
                                                <div>
                                                    <h6 class="fs-14 fw-medium">—</h6>
                                                    <p class="fs-13">Aucun document</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- /OVERVIEW --}}

                                {{-- ACTIVITY (placeholder) --}}
                                <div class="tab-pane fade" id="user-activity">
                                    <div class="text-muted">
                                        Activité (à connecter plus tard aux logs / réservations / actions).
                                    </div>
                                </div>
                                {{-- /ACTIVITY --}}

                                {{-- HISTORY (placeholder like timeline style) --}}
                                <div class="tab-pane fade" id="user-history">
                                    <div class="text-muted">
                                        Historique (à venir).
                                    </div>
                                </div>
                                {{-- /HISTORY --}}

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer-->
        <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
            <p class="mb-0">
                <a href="javascript:void(0);">Politique de confidentialité</a>
                <a href="javascript:void(0);" class="ms-4">Conditions d'utilisation</a>
            </p>
            <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by
                <a href="javascript:void(0);" class="text-secondary">Dreams</a>
            </p>
        </div>
        <!-- /Footer-->
    </div>
    <!-- /Page Wrapper -->

    {{-- Include Modals --}}
    @include('Backoffice.users.partials._modal_edit')
    @include('Backoffice.users.partials._modal_delete')
@endsection