<?php $page = 'agent-details'; ?>
@extends('layout.mainlayout_admin')

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="mb-3">
                        <a href="{{ route('backoffice.agents.index') }}" class="d-inline-flex align-items-center fw-medium">
                            <i class="ti ti-arrow-left me-1"></i>Agents
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
                                    <span class="avatar avatar-lg me-3" style="border-radius: 10px; overflow:hidden; background-color: #f0f3f8;">
                                        @if($agent->avatar_url)
                                            <img src="{{ $agent->avatar_url }}" alt="{{ $agent->full_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <span class="avatar-title fw-bold fs-24 text-primary">
                                                {{ strtoupper(mb_substr($agent->full_name, 0, 2)) }}
                                            </span>
                                        @endif
                                    </span>

                                    <div>
                                        <h6 class="mb-1">{{ $agent->full_name }}</h6>
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 me-2">
                                                <i class="ti ti-building me-1"></i>
                                                {{ $agent->agency->name ?? '—' }}
                                            </p>
                                            <p class="mb-0">
                                                Ajouté le :
                                                {{ optional($agent->created_at)->format('d M Y') ?: '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    @if($agent->email)
                                        <span class="badge badge-md bg-info-transparent">
                                            <i class="ti ti-mail me-1"></i>{{ $agent->email }}
                                        </span>
                                    @endif
                                    @if($agent->phone)
                                        <span class="badge badge-md bg-success-transparent">
                                            <i class="ti ti-phone me-1"></i>{{ $agent->phone }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- TABS CARD --}}
                    <div class="card mb-4 mb-xl-0">
                        <div class="card-header py-0">
                            <ul class="nav nav-tabs nav-tabs-bottom tab-dark">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#agent-overview" data-bs-toggle="tab">Aperçu</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#agent-notes" data-bs-toggle="tab">Notes</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#agent-history" data-bs-toggle="tab">Historique</a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">

                                {{-- OVERVIEW --}}
                                <div class="tab-pane fade active show" id="agent-overview">
                                    <div class="border-bottom mb-3 pb-3">
                                        <div class="row">

                                            <div class="col-md-6 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Nom complet</h6>
                                                    <p class="fs-13">{{ $agent->full_name }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Email</h6>
                                                    <p class="fs-13">{{ $agent->email ?: '—' }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Téléphone</h6>
                                                    <p class="fs-13">{{ $agent->phone ?: '—' }}</p>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Agence</h6>
                                                    <p class="fs-13">
                                                        {{ optional($agent->agency)->name ?: '—' }}
                                                        @if($agent->agency)
                                                            <span class="badge bg-light-200 ms-2">{{ $agent->agency->city ?? '' }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Compte utilisateur lié</h6>
                                                    <p class="fs-13">
                                                        @if($agent->user)
                                                            <a href="{{ route('backoffice.users.show', $agent->user) }}" class="text-primary">
                                                                {{ $agent->user->name }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">— Aucun utilisateur lié</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">Date de création</h6>
                                                    <p class="fs-13">{{ optional($agent->created_at)->format('d M Y, H:i') ?: '—' }}</p>
                                                </div>
                                            </div>

                                            {{-- Bouton Modifier - contrôlé par permission EDIT --}}
                                            @can('agents.general.edit')
                                            <div class="col-lg-12">
                                                <a href="{{ route('backoffice.agents.edit', $agent) }}"
                                                   class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                                    <i class="ti ti-edit me-1"></i>
                                                    Modifier
                                                </a>
                                            </div>
                                            @endcan

                                        </div>
                                    </div>

                                    @if($agent->notes)
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                                <h6>Notes</h6>
                                            </div>
                                            <div class="p-3 bg-light-100 rounded">
                                                <p class="mb-0">{{ $agent->notes }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                {{-- /OVERVIEW --}}

                                {{-- NOTES --}}
                                <div class="tab-pane fade" id="agent-notes">
                                    <div class="text-muted">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6>Notes internes</h6>
                                            {{-- Bouton Éditer - contrôlé par permission EDIT --}}
                                            @can('agents.general.edit')
                                            <a href="javascript:void(0);" 
                                               class="btn btn-sm btn-primary"
                                               data-bs-toggle="modal"
                                               data-bs-target="#edit_agent"
                                               data-edit-action="{{ route('backoffice.agents.update', $agent) }}"
                                               data-agent-notes="{{ $agent->notes }}">
                                                <i class="ti ti-edit me-1"></i>Éditer
                                            </a>
                                            @endcan
                                        </div>
                                        @if($agent->notes)
                                            <div class="p-3 bg-light-100 rounded">
                                                {{ $agent->notes }}
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="ti ti-notes fs-40 text-gray-3 mb-2"></i>
                                                <p class="mb-0">Aucune note disponible</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {{-- /NOTES --}}

                                {{-- HISTORY (timeline style) --}}
                                <div class="tab-pane fade" id="agent-history">
                                    <div class="activity-timeline">
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="badge bg-success rounded-circle p-2 me-3 mt-1">
                                                <i class="ti ti-plus fs-12"></i>
                                            </span>
                                            <div>
                                                <p class="mb-1 fw-medium">Agent créé</p>
                                                <small class="text-muted">{{ optional($agent->created_at)->format('d M Y, H:i') }}</small>
                                            </div>
                                        </div>
                                        @if($agent->updated_at && $agent->updated_at != $agent->created_at)
                                            <div class="d-flex align-items-start mb-3">
                                                <span class="badge bg-info rounded-circle p-2 me-3 mt-1">
                                                    <i class="ti ti-edit fs-12"></i>
                                                </span>
                                                <div>
                                                    <p class="mb-1 fw-medium">Dernière modification</p>
                                                    <small class="text-muted">{{ $agent->updated_at->format('d M Y, H:i') }}</small>
                                                </div>
                                            </div>
                                        @endif
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
    @include('backoffice.agents.partials._modal_edit')
    @include('backoffice.agents.partials._modal_delete')
@endsection