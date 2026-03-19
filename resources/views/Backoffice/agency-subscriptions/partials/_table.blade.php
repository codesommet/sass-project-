<head>
    <style>
        .table-responsive,
        .custom-datatable-filter,
        .dataTables_wrapper {
            overflow: visible !important;
        }
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #6c757d;
            background: transparent;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-icon:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #0d6efd;
        }
        .form-check {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('agency-subscriptions.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                <th>#ID</th>
                <th>Agence</th>
                <th>Plan</th>
                <th>Statut</th>
                <th>Période</th>
                <th>Essai</th>
                <th>Cycle</th>
                <th>Fournisseur</th>
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['agency-subscriptions.general.view', 'agency-subscriptions.general.edit', 'agency-subscriptions.general.delete'])
                <th class="text-end">Actions</th>
                @endcanany
            </tr>
        </thead>

        <tbody>
            @forelse($subscriptions as $subscription)
                @php
                    $agencyName = $subscription->agency?->name ?? '—';

                    $starts = $subscription->starts_at ? $subscription->starts_at->format('d/m/Y') : '—';
                    $ends   = $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : '—';
                    $trial  = $subscription->trial_ends_at ? $subscription->trial_ends_at->format('d/m/Y') : '—';

                    $isExpired = method_exists($subscription, 'isExpired') ? $subscription->isExpired() : false;
                    $isOnTrial = method_exists($subscription, 'isOnTrial') ? $subscription->isOnTrial() : false;

                    $statusLabel = $subscription->is_active ? 'Actif' : 'Inactif';
                    $statusClass = $subscription->is_active ? 'bg-success' : 'bg-secondary';

                    if ($subscription->is_active && $isExpired) {
                        $statusLabel = 'Expiré';
                        $statusClass = 'bg-danger';
                    }

                    if ($subscription->is_active && $isOnTrial) {
                        $statusLabel = 'Essai';
                        $statusClass = 'bg-warning text-dark';
                    }
                @endphp

                <tr>
                    {{-- Case à cocher - visible seulement si permission DELETE --}}
                    @can('agency-subscriptions.general.delete')
                    <td class="text-center">
                        <div class="form-check">
                            <input class="form-check-input subscription-checkbox" type="checkbox" value="{{ $subscription->id }}">
                        </div>
                    </td>
                    @endcan
                    
                    <td class="fw-semibold">#{{ $subscription->id }}</td>
                    
                    <td>
                        {{-- Lien vers show - visible seulement si permission VIEW --}}
                        @can('agency-subscriptions.general.view')
                            <a href="{{ route('backoffice.agency-subscriptions.show', $subscription) }}" class="fw-medium">
                                {{ $agencyName }}
                            </a>
                        @else
                            {{ $agencyName }}
                        @endcan
                    </td>
                    
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $subscription->plan_name ?? 'basic' }}
                        </span>
                    </td>
                    
                    <td>
                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </td>
                    
                    <td>
                        <div class="small text-muted">
                            <div><span class="fw-semibold">Début:</span> {{ $starts }}</div>
                            <div><span class="fw-semibold">Fin:</span> {{ $ends }}</div>
                        </div>
                    </td>
                    
                    <td>{{ $trial }}</td>
                    <td>{{ $subscription->billing_cycle ?? '—' }}</td>
                    
                    <td>
                        <div class="small">
                            <div class="fw-semibold">{{ $subscription->provider ?? 'manual' }}</div>
                            <div class="text-muted">{{ $subscription->provider_subscription_id ?? '—' }}</div>
                        </div>
                    </td>

                    {{-- Actions - visible seulement si au moins une permission d'action --}}
                    @canany(['agency-subscriptions.general.view', 'agency-subscriptions.general.edit', 'agency-subscriptions.general.delete'])
                    <td class="text-end">
                        @include('Backoffice.agency-subscriptions.partials._actions', ['subscription' => $subscription])
                    </td>
                    @endcanany
                </tr>
            @empty
                <tr>
                    @can('agency-subscriptions.general.delete')
                    <td></td>
                    @endcan
                    <td colspan="{{ (auth()->user()->can('agency-subscriptions.general.delete') ? 9 : 8) }}" class="text-center text-muted py-4">
                        <i class="ti ti-credit-card-off fs-24 text-muted mb-2"></i>
                        <p class="mb-0">Aucun abonnement trouvé.</p>
                    </td>
                    @canany(['agency-subscriptions.general.view', 'agency-subscriptions.general.edit', 'agency-subscriptions.general.delete'])
                    <td></td>
                    @endcanany
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(method_exists($subscriptions, 'links'))
    <div class="mt-3">
        {{ $subscriptions->links() }}
    </div>
@endif

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('agency-subscriptions.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.subscription-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan