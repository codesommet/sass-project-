<?php $page = 'invoices'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
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
        cursor: pointer;
    }
    
    .btn-icon:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #0d6efd;
    }
    
    .btn-icon i {
        font-size: 18px;
    }
    
    /* Status Badges */
    .badge-draft { background: #e2e3e5; color: #383d41; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-sent { background: #cce5ff; color: #004085; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-paid { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-partially-paid { background: #fff3cd; color: #856404; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-cancelled { background: #f8d7da; color: #721c24; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    
    .table-responsive { 
        overflow: visible !important; 
    }
    
    .dropdown-menu {
        z-index: 9999 !important;
        display: block;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
    }
    
    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .form-check {
        display: flex;
        justify-content: center;
        margin: 0;
        padding: 0;
    }
    
    .amount-badge { 
        background: #e8f5e9; 
        color: #2e7d32; 
        padding: 0.35rem 0.75rem; 
        border-radius: 50px; 
        font-weight: 500; 
        white-space: nowrap;
    }
    
    .invoice-info {
        line-height: 1.4;
    }
    
    .invoice-info small {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    /* Filter Collapse */
    .collapse {
        display: none;
    }
    .collapse.show {
        display: block;
    }
    
    /* Page wrapper fix for footer */
    .page-wrapper {
        min-height: calc(100vh - 60px);
        display: flex;
        flex-direction: column;
    }
    
    .content {
        flex: 1 0 auto;
    }
    
    .footer {
        flex-shrink: 0;
        margin-top: auto;
    }

    /* PDF Button */
    .btn-pdf {
        background: #dc3545;
        color: white;
    }
    .btn-pdf:hover {
        background: #bb2d3b;
        color: white;
    }

    /* ============ PAGINATION STYLING ============ */
    .pagination {
        margin: 0;
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: 0.25rem;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        z-index: 3;
    }
    
    .pagination .page-link {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #0d6efd;
        background-color: #fff;
        border: 1px solid #dee2e6;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #0a58ca;
        z-index: 2;
    }
    
    .pagination .page-item:first-child .page-link {
        margin-left: 0;
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
    
    .pagination .page-item:last-child .page-link {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    .pagination-info {
        color: #6c757d;
        font-size: 14px;
    }
    
    .pagination-container {
        display: flex;
        justify-content: flex-end;
    }
    
    @media (max-width: 768px) {
        .pagination-container {
            justify-content: center;
            margin-top: 15px;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content me-4">
        @include('backoffice.invoices.partials._breadcrumbs')

        <form method="GET" id="filterForm" action="{{ request()->url() }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <!-- Sort Dropdown -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" role="button">
                            <i class="ti ti-filter me-1"></i> Trier : 
                            @if(request('sort') == 'oldest') Plus anciennes
                            @elseif(request('sort') == 'date_asc') Date ↑
                            @elseif(request('sort') == 'date_desc') Date ↓
                            @elseif(request('sort') == 'amount_asc') Montant ↑
                            @elseif(request('sort') == 'amount_desc') Montant ↓
                            @else Plus récentes @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">Plus récentes</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}">Plus anciennes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'date_desc']) }}">Date (récente)</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'date_asc']) }}">Date (ancienne)</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'amount_desc']) }}">Montant (plus élevé)</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'amount_asc']) }}">Montant (moins élevé)</a></li>
                        </ul>
                    </div>
                    
                    <!-- Filters Toggle -->
                    <div>
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" role="button">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>
                </div>

                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <!-- Bulk PDF Export Button - visible seulement si permission VIEW (pour voir les PDF) -->
                    @can('invoices.general.view')
                    <div class="mb-0 me-2">
                        <button class="btn btn-danger d-flex align-items-center" id="exportSelectedPDF">
                            <i class="ti ti-file-export me-2"></i>Exporter PDF
                        </button>
                    </div>
                    @endcan

                    <!-- Search - toujours visible -->
                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon"><i class="ti ti-search"></i></span>
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                                   class="form-control" placeholder="Rechercher une facture...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Add Button - contrôlé par permission CREATE -->
                    @can('invoices.general.create')
                    <div class="mb-0">
                        <a href="{{ route('backoffice.invoices.create') }}" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-plus me-2"></i>Nouvelle facture
                        </a>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Filters Panel -->
            <div class="collapse @if(request()->has('status') || request()->has('client_id') || request()->has('date_from') || request()->has('date_to')) show @endif" id="filtercollapse">
                <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Statut</label>
                            <select name="status" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Envoyée</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Payée</option>
                                <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partiellement payée</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Client</label>
                            <select name="client_id" form="filterForm" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->first_name }} {{ $client->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Date début</label>
                            <input type="date" form="filterForm" name="date_from" value="{{ request('date_from') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-medium">Date fin</label>
                            <input type="date" form="filterForm" name="date_to" value="{{ request('date_to') }}" class="form-control" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-3 mt-2 d-flex align-items-end">
                            <a href="{{ route('backoffice.invoices.index') }}" class="btn btn-sm btn-outline-danger w-100">
                                <i class="ti ti-x me-1"></i>Tout effacer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="custom-datatable-filter table-responsive">
            <table class="table align-middle">
                <thead class="thead-light">
                    <tr>
                        {{-- Case à cocher - visible seulement si permission DELETE --}}
                        @can('invoices.general.delete')
                        <th width="50" class="text-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all">
                            </div>
                        </th>
                        @endcan
                        <th>N° Facture</th>
                        <th>Client</th>
                        <th>Contrat</th>
                        <th>Date d'émission</th>
                        <th>Date d'échéance</th>
                        <th>Montant TTC</th>
                        <th>Statut</th>
                        {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                        @canany(['invoices.general.view', 'invoices.general.edit', 'invoices.general.delete'])
                        <th width="140">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        {{-- Case à cocher - visible seulement si permission DELETE --}}
                        @can('invoices.general.delete')
                        <td class="text-center">
                            <div class="form-check">
                                <input class="form-check-input invoice-checkbox" type="checkbox" value="{{ $invoice->id }}">
                            </div>
                        </td>
                        @endcan
                        
                        <td>
                            <div class="invoice-info">
                                {{-- Lien vers show - contrôlé par permission VIEW --}}
                                @can('invoices.general.view')
                                    <a href="{{ route('backoffice.invoices.show', $invoice) }}" class="fw-medium">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="fw-medium">{{ $invoice->invoice_number }}</span>
                                @endcan
                                <br>
                                <small>
                                    <i class="ti ti-calendar me-1"></i>{{ $invoice->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </td>
                        
                        <td>
                            <div class="invoice-info">
                                @if($invoice->client)
                                    {{-- Lien vers client - contrôlé par permission VIEW sur clients --}}
                                    @can('clients.general.view')
                                        <a href="{{ route('backoffice.clients.show', $invoice->client_id) }}" class="fw-medium">
                                            {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}
                                        </a>
                                    @else
                                        <span class="fw-medium">{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</span>
                                    @endcan
                                    <br>
                                    <small>
                                        <i class="ti ti-phone me-1"></i>{{ $invoice->client->phone ?? 'N/A' }}
                                    </small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </td>
                        
                        <td>
                            <div class="invoice-info">
                                @if($invoice->rentalContract)
                                    {{-- Lien vers contrat - contrôlé par permission VIEW sur contrats --}}
                                    @can('rental-contracts.general.view')
                                        <a href="{{ route('backoffice.rental-contracts.show', $invoice->rentalContract) }}" class="fw-medium">
                                            {{ $invoice->rentalContract->contract_number }}
                                        </a>
                                    @else
                                        <span class="fw-medium">{{ $invoice->rentalContract->contract_number }}</span>
                                    @endcan
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </td>
                        
                        <td>
                            <div class="invoice-info">
                                <span>{{ $invoice->formatted_issue_date }}</span>
                            </div>
                        </td>
                        
                        <td>
                            <div class="invoice-info">
                                <span class="@if($invoice->is_overdue) text-danger fw-bold @endif">
                                    {{ $invoice->formatted_due_date }}
                                </span>
                                @if($invoice->is_overdue)
                                    <br><small class="text-danger">En retard</small>
                                @endif
                            </div>
                        </td>
                        
                        <td>
                            <span class="amount-badge">{{ $invoice->formatted_total_ttc }}</span>
                            @if($invoice->paid_amount > 0)
                                <br><small class="text-muted">Payé: {{ number_format($invoice->paid_amount, 2) }} {{ $invoice->currency }}</small>
                            @endif
                        </td>
                        
                        <td>
                            <span class="badge badge-{{ str_replace('_', '-', $invoice->status) }}">
                                {{ $invoice->status_text }}
                            </span>
                        </td>
                        
                        {{-- Actions - visible seulement si au moins une permission d'action --}}
                        @canany(['invoices.general.view', 'invoices.general.edit', 'invoices.general.delete'])
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                    {{-- Voir détails - contrôlé par permission VIEW --}}
                                    @can('invoices.general.view')
                                    <li>
                                        <a class="dropdown-item rounded-1" href="{{ route('backoffice.invoices.show', $invoice) }}">
                                            <i class="ti ti-eye me-2"></i>Voir détails
                                        </a>
                                    </li>
                                    @endcan
                                    
                                    {{-- Modifier - contrôlé par permission EDIT --}}
                                    @can('invoices.general.edit')
                                    <li>
                                        <a class="dropdown-item rounded-1" href="{{ route('backoffice.invoices.edit', $invoice) }}">
                                            <i class="ti ti-edit me-2"></i>Modifier
                                        </a>
                                    </li>
                                    @endcan
                                    
                                    {{-- Exporter PDF - contrôlé par permission VIEW --}}
                                    @can('invoices.general.view')
                                    <li>
                                        <a class="dropdown-item rounded-1" href="{{ route('backoffice.invoices.pdf.single', $invoice->id) }}" target="_blank">
                                            <i class="ti ti-file-text me-2" style="color: #dc3545;"></i>Exporter PDF
                                        </a>
                                    </li>
                                    @endcan
                                    
                                    <!-- WhatsApp Button - contrôlé par permission VIEW -->
                                    @can('invoices.general.view')
                                        @if($invoice->client && $invoice->client->phone)
                                        <li>
                                            <a class="dropdown-item rounded-1 text-success" 
                                               href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->client->phone) }}?text={{ urlencode('Bonjour, voici votre facture #' . $invoice->invoice_number . ' : ' . route('backoffice.invoices.pdf.single', $invoice->id, true)) }}"
                                               target="_blank">
                                                <i class="ti ti-brand-whatsapp me-2"></i>Envoyer vers whatsapp
                                            </a>
                                        </li>
                                        @else
                                        <li>
                                            <a class="dropdown-item rounded-1 text-muted" 
                                               href="javascript:void(0);"
                                               onclick="alert('Ce client n\'a pas de numéro de téléphone')">
                                                <i class="ti ti-brand-whatsapp me-2"></i>Pas de numéro
                                            </a>
                                        </li>
                                        @endif
                                    @endcan
                                    
                                    {{-- Supprimer - contrôlé par permission DELETE --}}
                                    @can('invoices.general.delete')
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item rounded-1 text-danger" 
                                           href="javascript:void(0);"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#deleteInvoiceModal"
                                           data-delete-action="{{ route('backoffice.invoices.destroy', $invoice) }}"
                                           data-delete-details="la facture <strong>{{ $invoice->invoice_number }}</strong>">
                                            <i class="ti ti-trash me-2"></i>Supprimer
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                        @endcanany
                    </tr>
                    @empty
                    <tr>
                        @can('invoices.general.delete')
                        <td></td>
                        @endcan
                        <td colspan="{{ (auth()->user()->can('invoices.general.delete') ? 8 : 7) }}" class="text-center py-5">
                            <div class="text-center">
                                <i class="ti ti-file-text fs-48 text-gray-4 mb-3"></i>
                                <h5 class="mb-2">Aucune facture trouvée</h5>
                                @can('invoices.general.create')
                                    <p class="text-muted mb-3">Commencez par créer une nouvelle facture</p>
                                    <a href="{{ route('backoffice.invoices.create') }}" class="btn btn-primary">
                                        <i class="ti ti-plus me-2"></i>Nouvelle facture
                                    </a>
                                @endcan
                            </div>
                        </td>
                        @canany(['invoices.general.view', 'invoices.general.edit', 'invoices.general.delete'])
                        <td></td>
                        @endcanany
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION - FIXED VERSION -->
        @if(isset($invoices) && $invoices->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
            <div class="pagination-info mb-3 mb-md-0">
                Affichage de <span class="fw-semibold">{{ $invoices->firstItem() }}</span> à <span class="fw-semibold">{{ $invoices->lastItem() }}</span> 
                sur <span class="fw-semibold">{{ $invoices->total() }}</span> factures
            </div>
            <div class="pagination-container">
                @if ($invoices->hasPages())
                    <nav aria-label="Navigation des pages">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            @if ($invoices->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="ti ti-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $invoices->previousPageUrl() }}" rel="prev" aria-label="Précédent">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements - Show 5 pages at a time --}}
                            @php
                                $start = max(1, $invoices->currentPage() - 2);
                                $end = min($invoices->lastPage(), $invoices->currentPage() + 2);
                                
                                // Show first page with ellipsis if needed
                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . $invoices->url(1) . '">1</a></li>';
                                    if ($start > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                // Show pages around current page
                                for ($i = $start; $i <= $end; $i++) {
                                    $active = $i == $invoices->currentPage() ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $invoices->url($i) . '">' . $i . '</a></li>';
                                }
                                
                                // Show last page with ellipsis if needed
                                if ($end < $invoices->lastPage()) {
                                    if ($end < $invoices->lastPage() - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . $invoices->url($invoices->lastPage()) . '">' . $invoices->lastPage() . '</a></li>';
                                }
                            @endphp

                            {{-- Next Page Link --}}
                            @if ($invoices->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $invoices->nextPageUrl() }}" rel="next" aria-label="Suivant">
                                        <i class="ti ti-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="ti ti-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
        @endif

    </div>

    <!-- Footer -->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0"><a href="javascript:void(0);">Privacy Policy</a><a href="javascript:void(0);" class="ms-4">Terms of Use</a></p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);" class="text-secondary">Dreams</a></p>
    </div>
</div>

<!-- Hidden form for bulk PDF export -->
<form id="bulkPDFForm" method="POST" action="{{ route('backoffice.invoices.pdf.multiple') }}" style="display: none;">
    @csrf
    <div id="bulkPDFInputs"></div>
</form>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto search
    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');

    if (form && input) {
        let timer;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => form.submit(), 400);
        });
    }

    // ==================== FILTER TOGGLE FIX ====================
    const filterToggle = document.querySelector('.filtercollapse');
    const filterCollapse = document.getElementById('filtercollapse');

    if (filterToggle && filterCollapse) {
        // Remove Bootstrap's collapse attributes to prevent interference
        filterToggle.removeAttribute('data-bs-toggle');
        
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle the show class
            filterCollapse.classList.toggle('show');
        });
    }

    // Close filter when clicking outside
    document.addEventListener('click', function(e) {
        if (filterCollapse && filterCollapse.classList.contains('show')) {
            if (!filterCollapse.contains(e.target) && !filterToggle.contains(e.target)) {
                filterCollapse.classList.remove('show');
            }
        }
    });

    // ==================== SELECT ALL CHECKBOXES ====================
    @can('invoices.general.delete')
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.invoice-checkbox').forEach(cb => {
                cb.checked = selectAll.checked;
            });
        });
        
        // Update select all when individual checkboxes change
        document.querySelectorAll('.invoice-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const allCheckboxes = document.querySelectorAll('.invoice-checkbox');
                const allChecked = Array.from(allCheckboxes).every(c => c.checked);
                const anyChecked = Array.from(allCheckboxes).some(c => c.checked);
                
                selectAll.checked = allChecked;
                selectAll.indeterminate = anyChecked && !allChecked;
            });
        });
    }
    @endcan

    // ==================== BULK PDF EXPORT ====================
    @can('invoices.general.view')
    const exportBtn = document.getElementById('exportSelectedPDF');
    if (exportBtn) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get all selected checkboxes
            const selected = [];
            document.querySelectorAll('.invoice-checkbox:checked').forEach(cb => {
                selected.push(cb.value);
            });
            
            if (selected.length === 0) {
                alert('Veuillez sélectionner au moins une facture');
                return;
            }
            
            // Create form inputs
            const form = document.getElementById('bulkPDFForm');
            const inputsContainer = document.getElementById('bulkPDFInputs');
            inputsContainer.innerHTML = '';
            
            selected.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                inputsContainer.appendChild(input);
            });
            
            // Submit form
            form.submit();
        });
    }
    @endcan

    // Initialize all dropdowns
    initializeAllDropdowns();
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            closeAllDropdowns();
        }
    });
});

function initializeAllDropdowns() {
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(button => {
        button.removeEventListener('click', dropdownClickHandler);
        button.addEventListener('click', dropdownClickHandler);
    });
}

function dropdownClickHandler(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const button = this;
    const dropdown = button.nextElementSibling;
    const isExpanded = button.getAttribute('aria-expanded') === 'true';
    
    closeAllDropdowns();
    
    if (dropdown && !isExpanded) {
        dropdown.classList.add('show');
        button.setAttribute('aria-expanded', 'true');
    }
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
        menu.classList.remove('show');
        const toggle = menu.previousElementSibling;
        if (toggle && toggle.hasAttribute('data-bs-toggle="dropdown"')) {
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        document.getElementById('filterForm').submit();
    }
}
</script>

@include('backoffice.invoices.partials._modal_delete')
@endsection