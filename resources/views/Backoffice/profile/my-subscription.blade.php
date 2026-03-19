<?php $page = 'profile'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-4">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
            <div class="my-auto mb-2">
                <h3 class="page-title mb-1">Mon Abonnement</h3>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.profile.setting') }}">Profil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Abonnement</li>
                    </ol>
                </nav>
            </div>
        </div>

        @if($subscription)
            @php
                $isActive = $subscription->is_active && (!$subscription->ends_at || $subscription->ends_at >= now());
                $isExpired = $subscription->ends_at && $subscription->ends_at < now();
                $isTrial = $subscription->trial_ends_at && $subscription->trial_ends_at >= now();
                
                if ($isActive && !$isExpired) {
                    $statusClass = 'success';
                    $statusText = 'Actif';
                } elseif ($isExpired) {
                    $statusClass = 'danger';
                    $statusText = 'Expiré';
                } elseif ($isTrial) {
                    $statusClass = 'info';
                    $statusText = 'Période d\'essai';
                } else {
                    $statusClass = 'secondary';
                    $statusText = 'Inactif';
                }
            @endphp

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Détails de l'abonnement</h4>
                    <span class="badge bg-{{ $statusClass }} p-2 fs-14">{{ $statusText }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Agence:</th>
                                    <td><strong>{{ $agency->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Plan:</th>
                                    <td>{{ $subscription->plan_name ?? 'Standard' }}</td>
                                </tr>
                                <tr>
                                    <th>Fournisseur:</th>
                                    <td>{{ $subscription->provider ?? 'DreamRent' }}</td>
                                </tr>
                                <tr>
                                    <th>Date de création:</th>
                                    <td>{{ $subscription->created_at ? $subscription->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Statut:</th>
                                    <td>
                                        @if($subscription->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($subscription->ends_at)
                                <tr>
                                    <th>Date d'expiration:</th>
                                    <td>{{ $subscription->ends_at->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                                @if($subscription->trial_ends_at)
                                <tr>
                                    <th>Fin d'essai:</th>
                                    <td>{{ $subscription->trial_ends_at->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                                @if($subscription->renews_at)
                                <tr>
                                    <th>Prochain renouvellement:</th>
                                    <td>{{ $subscription->renews_at->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($subscription->features)
                    <div class="mt-4">
                        <h5 class="mb-3">Fonctionnalités incluses</h5>
                        <div class="row">
                            @if(is_array($subscription->features))
                                @foreach($subscription->features as $feature)
                                    <div class="col-md-6 mb-2">
                                        <i class="ti ti-check text-success me-2"></i>{{ $feature }}
                                    </div>
                                @endforeach
                            @elseif(is_string($subscription->features))
                                @foreach(json_decode($subscription->features, true) ?? [] as $feature)
                                    <div class="col-md-6 mb-2">
                                        <i class="ti ti-check text-success me-2"></i>{{ $feature }}
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('backoffice.agency-subscriptions.show', $subscription->id) }}" class="btn btn-primary">
                            <i class="ti ti-eye me-2"></i>Voir détails complets
                        </a>
                        @if(!$isActive || $isExpired)
                            <a href="{{ route('backoffice.agency-subscriptions.create', ['agency_id' => $agency->id]) }}" class="btn btn-success ms-2">
                                <i class="ti ti-plus me-2"></i>Souscrire
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Statistiques d'utilisation</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Contrats</h6>
                                <h3>{{ $agency->contracts()->count() ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Véhicules</h6>
                                <h3>{{ $agency->vehicles()->count() ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <h6 class="text-muted mb-2">Clients</h6>
                                <h3>{{ $agency->clients()->count() ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- No Subscription -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ti ti-crown-off fs-64 text-muted mb-4"></i>
                    <h3 class="mb-3">Aucun abonnement actif</h3>
                    <p class="text-muted mb-4">Vous n'avez pas encore d'abonnement. Souscrivez maintenant pour profiter de toutes les fonctionnalités.</p>
                    <!-- <a href="{{ route('backoffice.agency-subscriptions.create', ['agency_id' => $agency->id]) }}" class="btn btn-primary btn-lg">
                        <i class="ti ti-plus me-2"></i>Souscrire maintenant
                    </a> -->
                </div>
            </div>
        @endif
    </div>
</div>
@endsection