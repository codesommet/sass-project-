<?php $page = 'credit-details'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.vehicle-credits.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i>
                        Retour à la liste
                    </a>
                </div>

                {{-- BASIC CARD --}}
                <div class="card">
                    <div class="card-body">
                        <div class="border-bottom mb-3 pb-3">
                            <h5>Détails du crédit</h5>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-lg me-3" style="border-radius: 10px; background-color: #f0f3f8;">
                                    <span class="avatar-title fw-bold fs-24 text-primary">
                                        <i class="ti ti-credit-card"></i>
                                    </span>
                                </span>

                                <div>
                                    <h6 class="mb-1">{{ $credit->creditor_name }}</h6>
                                    <div class="d-flex align-items-center">
                                        <p class="mb-0 me-2">
                                            <i class="ti ti-hash me-1"></i>
                                            {{ $credit->credit_number }}
                                        </p>
                                        <p class="mb-0">
                                            <i class="ti ti-calendar me-1"></i>
                                            {{ $credit->start_date->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center flex-wrap gap-3">
                                @php
                                    $statusClass = match($credit->status) {
                                        'active' => 'success',
                                        'completed' => 'info',
                                        'defaulted' => 'danger',
                                        'pending' => 'warning',
                                        default => 'secondary'
                                    };
                                    $statusText = match($credit->status) {
                                        'active' => 'Actif',
                                        'completed' => 'Terminé',
                                        'defaulted' => 'En défaut',
                                        'pending' => 'En attente',
                                        default => $credit->status
                                    };
                                @endphp
                                <span class="badge badge-md bg-{{ $statusClass }} text-white">
                                    {{ $statusText }}
                                </span>
                                @if($credit->contract_file)
                                    <a href="{{ Storage::url($credit->contract_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="ti ti-file-text me-1"></i>Contrat
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Progression du remboursement</span>
                                <span class="fw-bold">{{ $credit->progress_percentage }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $credit->progress_percentage >= 75 ? 'success' : ($credit->progress_percentage >= 50 ? 'info' : ($credit->progress_percentage >= 25 ? 'warning' : 'danger')) }}" 
                                     style="width: {{ $credit->progress_percentage }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">{{ $credit->paid_months }} mois payés</small>
                                <small class="text-muted">{{ $credit->remaining_months }} mois restants</small>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- DETAILS CARD --}}
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header py-0">
                        <ul class="nav nav-tabs nav-tabs-bottom tab-dark">
                            <li class="nav-item">
                                <a class="nav-link active" href="#credit-overview" data-bs-toggle="tab">Overview</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#payment-schedule" data-bs-toggle="tab">Échéancier</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#credit-notes" data-bs-toggle="tab">Notes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#credit-history" data-bs-toggle="tab">History</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">

                            {{-- OVERVIEW --}}
                            <div class="tab-pane fade active show" id="credit-overview">
                                <div class="border-bottom mb-3 pb-3">
                                    <div class="row">

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Véhicule</h6>
                                                <p class="fs-13">
                                                    {{-- Lien vers véhicule - contrôlé par permission VIEW sur véhicules --}}
                                                    @can('vehicles.general.view')
                                                        <a href="{{ route('backoffice.vehicles.show', $credit->vehicle_id) }}" class="text-primary">
                                                            {{ $credit->vehicle->registration_number ?? 'N/C' }}
                                                        </a>
                                                    @else
                                                        <span>{{ $credit->vehicle->registration_number ?? 'N/C' }}</span>
                                                    @endcan
                                                    @if($credit->vehicle)
                                                        <br><small>{{ $credit->vehicle->brand->name ?? '' }} {{ $credit->vehicle->model->name ?? '' }}</small>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Créancier</h6>
                                                <p class="fs-13">{{ $credit->creditor_name }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Montant total</h6>
                                                <p class="fs-13 fw-bold text-primary">{{ number_format($credit->total_amount, 2, ',', ' ') }} DH</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Apport</h6>
                                                <p class="fs-13">{{ number_format($credit->down_payment, 2, ',', ' ') }} DH</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Montant financé</h6>
                                                <p class="fs-13">{{ number_format($credit->total_amount - $credit->down_payment, 2, ',', ' ') }} DH</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Mensualité</h6>
                                                <p class="fs-13">{{ number_format($credit->monthly_payment, 2, ',', ' ') }} DH</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Durée</h6>
                                                <p class="fs-13">{{ $credit->duration_months }} mois</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Taux d'intérêt</h6>
                                                <p class="fs-13">{{ $credit->interest_rate }}%</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Date de début</h6>
                                                <p class="fs-13">{{ $credit->start_date->format('d/m/Y') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Date de fin prévue</h6>
                                                <p class="fs-13">{{ $credit->end_date->format('d/m/Y') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Montant restant</h6>
                                                <p class="fs-13 fw-bold text-warning">{{ number_format($credit->remaining_amount, 2, ',', ' ') }} DH</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-sm-6">
                                            <div class="mb-3">
                                                <h6 class="fs-14 fw-semibold mb-1">Mois restants</h6>
                                                <p class="fs-13">{{ $credit->remaining_months }} mois</p>
                                            </div>
                                        </div>

                                        {{-- Boutons d'action - contrôlés par permissions --}}
                                        <div class="col-lg-12">
                                            @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                                            <a href="{{ route('backoffice.vehicle-credits.edit', $credit->id) }}"
                                               class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                                <i class="ti ti-edit me-1"></i>
                                                Modifier
                                            </a>
                                            @endif
                                            
                                            @if(isset($permissions['can_record_payment']) && $permissions['can_record_payment'])
                                            <button type="button"
                                                    class="btn btn-success btn-sm d-inline-flex align-items-center ms-2"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#record_payment">
                                                <i class="ti ti-currency-dollar me-1"></i>
                                                Enregistrer un paiement
                                            </button>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                            {{-- /OVERVIEW --}}

                            {{-- PAYMENT SCHEDULE --}}
                            <div class="tab-pane fade" id="payment-schedule">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>N°</th>
                                                <th>Date échéance</th>
                                                <th>Montant</th>
                                                <th>Capital</th>
                                                <th>Intérêts</th>
                                                <th>Statut</th>
                                                <th>Date paiement</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($credit->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_number }}</td>
                                                <td>{{ $payment->due_date->format('d/m/Y') }}</td>
                                                <td>{{ number_format($payment->amount, 2, ',', ' ') }} DH</td>
                                                <td>{{ number_format($payment->principal, 2, ',', ' ') }} DH</td>
                                                <td>{{ number_format($payment->interest, 2, ',', ' ') }} DH</td>
                                                <td>
                                                    @php
                                                        $paymentStatusClass = match($payment->status) {
                                                            'paid' => 'success',
                                                            'late' => 'danger',
                                                            'pending' => 'warning',
                                                            default => 'secondary'
                                                        };
                                                        $paymentStatusText = match($payment->status) {
                                                            'paid' => 'Payé',
                                                            'late' => 'En retard',
                                                            'pending' => 'En attente',
                                                            default => $payment->status
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $paymentStatusClass }} text-white">
                                                        {{ $paymentStatusText }}
                                                    </span>
                                                </td>
                                                <td>{{ $payment->paid_date ? $payment->paid_date->format('d/m/Y') : '-' }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-3">
                                                    <p class="text-muted mb-0">Aucun paiement trouvé</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- /PAYMENT SCHEDULE --}}

                            {{-- NOTES --}}
                            <div class="tab-pane fade" id="credit-notes">
                                <div class="text-muted">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6>Notes internes</h6>
                                        @if(isset($permissions['can_edit']) && $permissions['can_edit'])
                                        <a href="{{ route('backoffice.vehicle-credits.edit', $credit->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="ti ti-edit me-1"></i>Éditer
                                        </a>
                                        @endif
                                    </div>
                                    @if($credit->notes)
                                        <div class="p-3 bg-light-100 rounded">
                                            {{ $credit->notes }}
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

                            {{-- HISTORY --}}
                            <div class="tab-pane fade" id="credit-history">
                                <div class="activity-timeline">
                                    <div class="d-flex align-items-start mb-3">
                                        <span class="badge bg-success rounded-circle p-2 me-3 mt-1">
                                            <i class="ti ti-plus fs-12"></i>
                                        </span>
                                        <div>
                                            <p class="mb-1 fw-medium">Crédit créé</p>
                                            <small class="text-muted">{{ $credit->created_at->format('d M Y, H:i') }}</small>
                                        </div>
                                    </div>
                                    @if($credit->updated_at && $credit->updated_at != $credit->created_at)
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="badge bg-info rounded-circle p-2 me-3 mt-1">
                                                <i class="ti ti-edit fs-12"></i>
                                            </span>
                                            <div>
                                                <p class="mb-1 fw-medium">Dernière modification</p>
                                                <small class="text-muted">{{ $credit->updated_at->format('d M Y, H:i') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                    @foreach($credit->payments()->where('status', 'paid')->latest()->take(5)->get() as $payment)
                                        <div class="d-flex align-items-start mb-3">
                                            <span class="badge bg-success rounded-circle p-2 me-3 mt-1">
                                                <i class="ti ti-currency-dollar fs-12"></i>
                                            </span>
                                            <div>
                                                <p class="mb-1 fw-medium">Paiement #{{ $payment->payment_number }} reçu</p>
                                                <small class="text-muted">
                                                    @if($payment->paid_date)
                                                        {{ $payment->paid_date->format('d M Y, H:i') }}
                                                    @else
                                                        Date non enregistrée
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- /HISTORY --}}

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">2025 © Dreamsrent. All rights reserved.</p>
        <p class="mb-0">v1.0</p>
    </div>
</div>

{{-- Record Payment Modal --}}
@if(isset($permissions['can_record_payment']) && $permissions['can_record_payment'])
<div class="modal fade" id="record_payment">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('backoffice.vehicle-credits.record-payment', $credit->id) }}" class="needs-validation" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Enregistrer un paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Numéro de mensualité</label>
                        <select name="payment_number" class="form-select" required>
                            <option value="">Sélectionner</option>
                            @foreach($credit->payments()->where('status', 'pending')->get() as $payment)
                                <option value="{{ $payment->payment_number }}">
                                    Mensualité #{{ $payment->payment_number }} - {{ $payment->due_date->format('d/m/Y') }} - {{ number_format($payment->amount, 2) }} DH
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date de paiement</label>
                        <input type="date" name="paid_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pénalité (si retard)</label>
                        <div class="input-group">
                            <input type="number" name="penalty" class="form-control" value="0" step="0.01" min="0">
                            <span class="input-group-text">DH</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@include('Backoffice.vehicle-credits.partials._modal_delete')
@endsection