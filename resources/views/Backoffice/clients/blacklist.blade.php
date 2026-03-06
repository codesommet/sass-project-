<?php $page = 'clients-blacklist'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .blacklist-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .blacklist-table thead th {
        background: #dc3545;
        color: white;
        font-weight: 600;
        padding: 15px;
    }
    
    .blacklist-table tbody tr:hover {
        background: #fff5f5;
    }
    
    .blacklist-table td {
        padding: 15px;
        vertical-align: middle;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
        border-radius: 12px;
        padding: 20px;
        color: white;
        margin-bottom: 20px;
    }
    
    .badge-urgent {
        background: #dc3545;
        color: white;
        padding: 5px 10px;
        border-radius: 50px;
        font-size: 0.75rem;
    }
</style>

<div class="page-wrapper">
    <div class="content me-4">
        
        @include('backoffice.clients.partials._breadcrumbs_blacklist')

        <!-- Stats Card -->
        @php
            $totalBlacklisted = App\Models\BlacklistedClient::count();
            $todayBlacklisted = App\Models\BlacklistedClient::whereDate('created_at', today())->count();
            $thisWeek = App\Models\BlacklistedClient::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        @endphp

        <div class="stats-card">
            <div class="row">
                <div class="col-md-4 text-center">
                    <h3 class="text-white mb-0">{{ $totalBlacklisted }}</h3>
                    <p class="text-white-50 mb-0">Total blacklistés</p>
                </div>
                <div class="col-md-4 text-center">
                    <h3 class="text-white mb-0">{{ $todayBlacklisted }}</h3>
                    <p class="text-white-50 mb-0">Aujourd'hui</p>
                </div>
                <div class="col-md-4 text-center">
                    <h3 class="text-white mb-0">{{ $thisWeek }}</h3>
                    <p class="text-white-50 mb-0">Cette semaine</p>
                </div>
            </div>
        </div>

        <!-- Blacklist Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-alert-triangle text-danger me-2"></i>
                    Liste des clients blacklistés
                </h5>
                <input type="text" id="searchInput" class="form-control w-25" placeholder="Rechercher...">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table blacklist-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>CIN</th>
                                <th>Blacklisté le</th>
                                <th>Par</th>
                                <th>Agence</th>
                                <th>Raison</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blacklisted as $entry)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-danger bg-opacity-10 me-2">
                                            <i class="ti ti-user text-danger"></i>
                                        </span>
                                        <div>
                                            <strong>{{ $entry->client->full_name }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <small>{{ $entry->client->phone }}</small><br>
                                        <small class="text-muted">{{ $entry->client->email ?? '—' }}</small>
                                    </div>
                                </td>
                                <td><span class="badge bg-light">{{ $entry->client->cin_number ?? '—' }}</span></td>
                                <td>{{ $entry->formatted_created_at }}</td>
                                <td>{{ $entry->blacklistedBy?->name ?? 'Système' }}</td>
                                <td><span class="badge bg-secondary">{{ $entry->agency?->name }}</span></td>
                                <td>
                                    <span class="reason-cell" title="{{ $entry->reason }}">
                                        {{ Str::limit($entry->reason, 50) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="openViewBlacklistModal(
                                                '{{ $entry->client->full_name }}',
                                                '{{ $entry->client->phone }}',
                                                '{{ $entry->client->cin_number }}',
                                                '{{ $entry->formatted_created_at }}',
                                                '{{ $entry->blacklistedBy?->name ?? 'Système' }}',
                                                '{{ $entry->agency?->name }}',
                                                '{{ $entry->reason }}',
                                                '{{ $entry->internal_notes }}'
                                            )">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success" 
                                            onclick="openUnblacklistModal({{ $entry->client_id }}, '{{ $entry->client->full_name }}', '{{ $entry->client->phone }}', '{{ $entry->client->cin_number }}', '{{ $entry->formatted_created_at }}', '{{ $entry->reason }}')">
                                        <i class="ti ti-check"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="ti ti-check-circle fs-48 text-success mb-3"></i>
                                    <h5>Aucun client blacklisté</h5>
                                    <p class="text-muted">Tous les clients sont en règle pour le moment</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($blacklisted->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $blacklisted->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
@include('backoffice.clients.partials._blacklist_modals')

<script>
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    const search = this.value.toLowerCase();
    document.querySelectorAll('.blacklist-table tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
});
</script>
@endsection