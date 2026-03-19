@section('content')
<script>
// Suppress DataTables warnings
(function() {
    var originalAlert = window.alert;
    window.alert = function(message) {
        if (message && (message.includes('DataTables') || message.includes('datatables'))) {
            console.log('DataTables warning blocked:', message);
            return;
        }
        originalAlert(message);
    };
    if (window.$ && $.fn && $.fn.dataTable) {
        $.fn.dataTable.ext.errMode = 'none';
    }
})();
</script>
<?php $page = 'clients'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    /* Blacklist-specific styles (module-specific, stays here) */
    .cin-check-container {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .client-row-blacklisted {
        background-color: rgba(220, 53, 69, 0.05);
        border-left: 3px solid #dc3545;
    }
    .client-row-blacklisted:hover {
        background-color: rgba(220, 53, 69, 0.1);
    }
</style>

<div class="page-wrapper">
    <div class="content me-4">

        {{-- Stats Cards --}}
        @include('backoffice.clients.partials._breadcrumbs')

        {{-- Blacklist CIN Check --}}
        <div class="cin-check-container">
            <label class="form-label fw-medium mb-2">
                <i class="ti ti-shield-search text-danger me-1"></i>
                Vérifier si un client est blacklisté
            </label>
            <div class="d-flex gap-2">
                <input type="text"
                       class="form-control"
                       id="cinCheckInput"
                       placeholder="Saisissez le numéro de CIN..."
                       maxlength="50">
                <button type="button" class="btn btn-danger" id="checkCinButton">
                    <i class="ti ti-search me-1"></i>Vérifier
                </button>
            </div>
            <small class="text-muted mt-1 d-block">Recherche dans toutes les agences</small>
        </div>

        {{-- Filter Bar (reusable component) --}}
        <x-backoffice.filter-bar
            :route="route('backoffice.clients.index')"
            search-placeholder="Rechercher un client (nom, téléphone, CIN...)"
            :create-url="route('backoffice.clients.create')"
            create-label="Nouveau client"
            create-permission="clients.general.create"
            :sort-options="['latest' => 'Plus récents', 'az' => 'A → Z', 'za' => 'Z → A']"
        >
            <x-slot name="filters">
                <div class="col-md-3">
                    <label class="form-label fw-medium">Agence</label>
                    <select name="agency_id" form="filterForm" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les agences</option>
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
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        <option value="blacklisted" {{ request('status') == 'blacklisted' ? 'selected' : '' }}>Blacklisté</option>
                    </select>
                </div>
            </x-slot>
        </x-backoffice.filter-bar>

        {{-- Data Table --}}
        <div class="table-responsive">
            @include('backoffice.clients.partials._table', ['clients' => $clients, 'permissions' => $permissions])
        </div>

        {{-- Pagination (reusable component) --}}
        <x-backoffice.smart-pagination :paginator="$clients" label="clients" />

    </div>

    {{-- Footer --}}
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">2025 &copy; Dreamsrent</p>
    </div>
</div>

{{-- Blacklist Warning Modal --}}
<div class="modal fade" id="blacklistWarningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white">
                    <i class="ti ti-alert-triangle me-2"></i>Client Blacklisté
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="blacklistWarningContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal (reusable component) --}}
<x-backoffice.delete-modal
    id="delete_client"
    title="Supprimer le client"
    form-id="deleteClientForm"
    name-id="deleteClientName"
    warning="Le client et toutes ses données seront définitivement supprimés."
/>

{{-- CIN Check + Delete Modal Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cinInput = document.getElementById('cinCheckInput');
    const checkBtn = document.getElementById('checkCinButton');

    // CIN Blacklist Check
    if (checkBtn && cinInput) {
        checkBtn.addEventListener('click', function() {
            const cin = cinInput.value.trim();
            if (!cin) {
                cinInput.focus();
                cinInput.classList.add('is-invalid');
                setTimeout(() => cinInput.classList.remove('is-invalid'), 2000);
                return;
            }

            checkBtn.disabled = true;
            checkBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Vérification...';

            fetch('{{ route("backoffice.clients.check-blacklist") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cin: cin })
            })
            .then(r => r.json())
            .then(data => {
                if (data.blacklisted) {
                    showBlacklistWarning(data);
                } else {
                    showToast('Aucun client blacklisté trouvé avec ce numéro de CIN', 'success');
                }
            })
            .catch(() => showToast('Erreur lors de la vérification', 'danger'))
            .finally(() => {
                checkBtn.disabled = false;
                checkBtn.innerHTML = '<i class="ti ti-search me-1"></i>Vérifier';
            });
        });

        cinInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); checkBtn.click(); }
        });
    }

    // Delete modal handler
    const deleteModal = document.getElementById('delete_client');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const form = document.getElementById('deleteClientForm');
            const name = document.getElementById('deleteClientName');
            if (form) form.action = btn.getAttribute('data-delete-action') || '#';
            if (name) name.textContent = btn.getAttribute('data-delete-name') || '—';
        });
    }

    function showBlacklistWarning(data) {
        const content = document.getElementById('blacklistWarningContent');
        let agencyHtml = '';
        if (data.agency) {
            agencyHtml = `
                <p><strong>Agence :</strong> ${data.agency.name}</p>
                <p><strong>Ville :</strong> ${data.agency.city || '—'}</p>
                <p><strong>Téléphone :</strong> ${data.agency.phone || '—'}</p>`;
        }
        content.innerHTML = `
            <div class="text-center mb-3">
                <i class="ti ti-alert-triangle fs-48 text-danger"></i>
                <h5 class="text-danger mt-2">Ce client est blacklisté !</h5>
            </div>
            <div class="alert alert-danger mb-2">
                <p class="mb-1"><strong>Nom :</strong> ${data.client.first_name} ${data.client.last_name}</p>
                <p class="mb-1"><strong>CIN :</strong> ${data.client.cin_number}</p>
                <p class="mb-1"><strong>Téléphone :</strong> ${data.client.phone || '—'}</p>
                ${agencyHtml}
                ${data.client.notes ? `<p class="mb-0"><strong>Notes :</strong> ${data.client.notes}</p>` : ''}
            </div>
            <p class="text-muted small mb-0">
                <i class="ti ti-info-circle me-1"></i>
                Blacklisté le ${new Date(data.client.updated_at).toLocaleDateString('fr-FR')}.
            </p>`;
        new bootstrap.Modal(document.getElementById('blacklistWarningModal')).show();
    }

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3 shadow`;
        toast.style.zIndex = '9999';
        toast.style.animation = 'slideInRight 0.3s ease';
        toast.innerHTML = `<i class="ti ti-${type === 'success' ? 'check-circle' : 'alert-circle'} me-2"></i>${message}`;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.animation = 'slideOutRight 0.3s ease'; setTimeout(() => toast.remove(), 300); }, 3000);
    }
});
</script>

@include('backoffice.clients.partials._modals_js')
@include('backoffice.clients.partials._blacklist_modals')

@endsection
