@section('content')
<script>
// Override the native alert function to block DataTables warnings
(function() {
    // Store the original alert
    var originalAlert = window.alert;
    
    // Replace with filtered version
    window.alert = function(message) {
        // Check if this is a DataTables warning
        if (message && (message.includes('DataTables') || message.includes('datatables'))) {
            console.log('DataTables warning blocked:', message);
            return; // Block the alert
        }
        // Allow other alerts through
        originalAlert(message);
    };
    
    // Also set DataTables error mode if available
    if (window.$ && $.fn && $.fn.dataTable) {
        $.fn.dataTable.ext.errMode = 'none';
    }
})();
</script>
<?php $page = 'clients'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    /* Pagination styling */
    .pagination {
        margin: 0;
    }
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    .pagination .page-link {
        color: #0d6efd;
        border-radius: 8px;
        margin: 0 3px;
        padding: 8px 12px;
    }
    .pagination .page-link:hover {
        background-color: #e9ecef;
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

    /* Blacklist Warning Styles */
    .blacklist-warning {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        border-left: 4px solid #dc3545;
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .blacklist-warning .warning-header {
        background: #dc3545;
        color: white;
        padding: 12px 20px;
        border-radius: 12px 12px 0 0;
        font-weight: 600;
    }
    
    .blacklist-warning .warning-body {
        padding: 20px;
    }
    
    .blacklist-warning .warning-footer {
        padding: 15px 20px;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .blacklisted-badge {
        background: #dc3545;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        margin-left: 5px;
    }
    
    .cin-check-container {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .cin-check-container .form-group {
        flex: 1;
    }
    
    .cin-check-container button {
        white-space: nowrap;
    }
    
    .blacklist-warning-icon {
        color: #dc3545;
        font-size: 24px;
        margin-right: 10px;
    }
    
    .client-row-blacklisted {
        background-color: rgba(220, 53, 69, 0.05);
        border-left: 3px solid #dc3545;
    }
    
    .client-row-blacklisted:hover {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    /* Tooltip for blacklist info */
    .blacklist-tooltip {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }
    
    .blacklist-tooltip .tooltip-text {
        visibility: hidden;
        width: 250px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 8px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -125px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.8rem;
        pointer-events: none;
    }
    
    .blacklist-tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>

<div class="page-wrapper">
    <div class="content me-4">

        @include('backoffice.clients.partials._breadcrumbs')

        <!-- ALERTS -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3">
                <i class="ti ti-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3">
                <i class="ti ti-alert-circle me-1"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Blacklist Check Container -->
        <div class="cin-check-container">
            <div class="form-group">
                <label class="form-label fw-medium mb-2">
                    <i class="ti ti-alert-triangle text-danger me-1"></i>
                    Vérifier client blacklisté par CIN
                </label>
                <div class="d-flex gap-2">
                    <input type="text" 
                           class="form-control" 
                           id="cinCheckInput" 
                           placeholder="Entrez le numéro de CIN à vérifier..."
                           maxlength="50">
                    <button type="button" class="btn btn-danger" id="checkCinButton">
                        <i class="ti ti-search me-1"></i>
                        Vérifier
                    </button>
                </div>
                <small class="text-muted">Vérifie dans toutes les agences si ce client est blacklisté</small>
            </div>
        </div>

        <!-- FILTER FORM -->
        <form method="GET" id="filterForm">

            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">

                <div class="d-flex align-items-center flex-wrap row-gap-3">

                    <!-- SORT -->
                    <div class="dropdown me-2">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                           data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>
                            Trier :
                            @if(request('sort') == 'az')
                                A → Z
                            @elseif(request('sort') == 'za')
                                Z → A
                            @else
                                Derniers
                            @endif
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.clients.index', array_merge(request()->all(), ['sort'=>'az'])) }}">
                                    A → Z
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.clients.index', array_merge(request()->all(), ['sort'=>'za'])) }}">
                                    Z → A
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('backoffice.clients.index') }}">
                                    Derniers
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- FILTER TOGGLE -->
                    <div>
                        <a href="#filtercollapse"
                           class="filtercollapse coloumn d-inline-flex align-items-center"
                           data-bs-toggle="collapse">
                            <i class="ti ti-filter me-1"></i> Filtres
                        </a>
                    </div>

                </div>

                <!-- SEARCH & ACTIONS -->
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">

                    <div class="top-search me-2">
                        <div class="top-search-group position-relative">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text"
                                   name="search"
                                   id="searchInput"
                                   value="{{ request('search') }}"
                                   class="form-control"
                                   placeholder="Rechercher un client...">
                            @if(request('search'))
                                <button type="button" class="btn btn-link position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); padding: 0; color: #6c757d; z-index: 10;" onclick="clearSearch()">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton Ajouter - contrôlé par permission CREATE --}}
                    @can('clients.general.create')
                        <div class="mb-0">
                            <a href="{{ route('backoffice.clients.create') }}"
                               class="btn btn-primary d-flex align-items-center">
                                <i class="ti ti-plus me-2"></i>Ajouter un client
                            </a>
                        </div>
                    @endcan

                </div>

            </div>

        </form>
        <!-- END HEADER -->


        <!-- FILTER COLLAPSE -->
        <div class="collapse {{ request()->has('agency_id') || request()->has('status') ? 'show' : '' }}" id="filtercollapse">
            <div class="filterbox p-3 mb-3 bg-light-100 rounded">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Agence</label>
                        <select name="agency_id" form="filterForm" class="form-select" onchange="this.form.submit()">
                            <option value="">Toutes</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                                    {{ $agency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium">Statut</label>
                        <select name="status" form="filterForm" class="form-select" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="blacklisted" {{ request('status') == 'blacklisted' ? 'selected' : '' }}>Blacklisté</option>
                        </select>
                    </div>
                    <div class="col-md-3 mt-2 d-flex align-items-end">
                        <a href="{{ route('backoffice.clients.index') }}" class="btn btn-sm btn-outline-danger w-100">
                            <i class="ti ti-x me-1"></i>Tout effacer
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <!-- TABLE -->
        <div class="table-responsive">
            @include('backoffice.clients.partials._table', ['clients' => $clients, 'permissions' => $permissions])
        </div>

        <!-- PAGINATION -->
        @if($clients->total() > 0)
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
            <div class="pagination-info mb-3 mb-md-0">
                Affichage de <span class="fw-semibold">{{ $clients->firstItem() }}</span> à <span class="fw-semibold">{{ $clients->lastItem() }}</span> 
                sur <span class="fw-semibold">{{ $clients->total() }}</span> clients
            </div>
            <div class="pagination-container">
                @if ($clients->hasPages())
                    <nav aria-label="Navigation des pages">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- Previous Page Link --}}
                            @if ($clients->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="ti ti-chevron-left"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $clients->previousPageUrl() }}" rel="prev" aria-label="Précédent">
                                        <i class="ti ti-chevron-left"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $start = max(1, $clients->currentPage() - 2);
                                $end = min($clients->lastPage(), $clients->currentPage() + 2);
                                
                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="' . $clients->url(1) . '">1</a></li>';
                                    if ($start > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start; $i <= $end; $i++) {
                                    $active = $i == $clients->currentPage() ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $clients->url($i) . '">' . $i . '</a></li>';
                                }
                                
                                if ($end < $clients->lastPage()) {
                                    if ($end < $clients->lastPage() - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . $clients->url($clients->lastPage()) . '">' . $clients->lastPage() . '</a></li>';
                                }
                            @endphp

                            {{-- Next Page Link --}}
                            @if ($clients->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $clients->nextPageUrl() }}" rel="next" aria-label="Suivant">
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

    <!-- FOOTER -->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">2025 © Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="#" class="text-secondary">Dreams</a></p>
    </div>
</div>

<!-- Blacklist Warning Modal -->
<div class="modal fade" id="blacklistWarningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Client Blacklisté
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="blacklistWarningContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- AUTO SEARCH SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');
    const cinInput = document.getElementById('cinCheckInput');
    const checkBtn = document.getElementById('checkCinButton');

    if (form && input) {
        let debounceTimer;
        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                form.submit();
            }, 400);
        });
    }

    // CIN Check Function
    if (checkBtn && cinInput) {
        checkBtn.addEventListener('click', function() {
            const cin = cinInput.value.trim();
            if (!cin) {
                alert('Veuillez entrer un numéro de CIN');
                return;
            }
            
            // Show loading state
            checkBtn.disabled = true;
            checkBtn.innerHTML = '<i class="ti ti-loader me-1"></i> Vérification...';
            
            // Make AJAX request
            fetch('{{ route("backoffice.clients.check-blacklist") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cin: cin })
            })
            .then(response => response.json())
            .then(data => {
                if (data.blacklisted) {
                    showBlacklistWarning(data);
                } else {
                    showSuccessMessage('Aucun client blacklisté trouvé avec ce CIN');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors de la vérification');
            })
            .finally(() => {
                checkBtn.disabled = false;
                checkBtn.innerHTML = '<i class="ti ti-search me-1"></i> Vérifier';
            });
        });
        
        // Allow Enter key to trigger check
        cinInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkBtn.click();
            }
        });
    }

    function showBlacklistWarning(data) {
        const modal = new bootstrap.Modal(document.getElementById('blacklistWarningModal'));
        const content = document.getElementById('blacklistWarningContent');
        
        let agencyInfo = '';
        if (data.agency) {
            agencyInfo = `
                <p><strong>Agence:</strong> ${data.agency.name}</p>
                <p><strong>Ville:</strong> ${data.agency.city || 'N/A'}</p>
                <p><strong>Téléphone:</strong> ${data.agency.phone || 'N/A'}</p>
            `;
        }
        
        content.innerHTML = `
            <div class="text-center mb-3">
                <i class="ti ti-alert-triangle fs-48 text-danger mb-3"></i>
                <h5 class="text-danger">⚠️ ATTENTION ⚠️</h5>
                <p class="lead">Ce client est blacklisté dans une autre agence !</p>
            </div>
            <div class="alert alert-danger">
                <p><strong>Nom:</strong> ${data.client.first_name} ${data.client.last_name}</p>
                <p><strong>CIN:</strong> ${data.client.cin_number}</p>
                <p><strong>Téléphone:</strong> ${data.client.phone || 'N/A'}</p>
                ${agencyInfo}
                ${data.client.notes ? `<p><strong>Notes:</strong> ${data.client.notes}</p>` : ''}
            </div>
            <p class="text-muted small mt-2">
                <i class="ti ti-info-circle me-1"></i>
                Ce client a été blacklisté le ${new Date(data.client.updated_at).toLocaleDateString('fr-FR')}. 
                Soyez vigilant avant de traiter avec ce client.
            </p>
        `;
        
        modal.show();
    }

    function showSuccessMessage(message) {
        // Create temporary success toast
        const toast = document.createElement('div');
        toast.className = 'alert alert-success position-fixed top-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.style.animation = 'slideIn 0.3s ease';
        toast.innerHTML = `
            <i class="ti ti-check-circle me-2"></i>
            ${message}
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});

function clearSearch() {
    const input = document.getElementById('searchInput');
    if (input) {
        input.value = '';
        document.getElementById('filterForm').submit();
    }
}

// Add slideOut animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

@include('backoffice.clients.partials._modal_delete')
@include('backoffice.clients.partials._modals_js')
<!-- Include Blacklist Modals -->
@include('backoffice.clients.partials._blacklist_modals')

@endsection