<?php $page = 'invoice-settings'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .settings-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .settings-section h6 {
        color: #0d6efd;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    .required:after {
        content: " *";
        color: #dc3545;
    }
    .logo-preview {
        width: 180px;
        height: 180px;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        background: #f8f9fa;
        overflow: hidden;
    }
    .logo-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .logo-preview .placeholder {
        text-align: center;
        color: #6c757d;
    }
    .logo-preview .placeholder i {
        font-size: 48px;
        margin-bottom: 10px;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0 pb-0 me-lg-4">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Paramètres de l'agence</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.dashboard') }}">Accueil</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.agencies.index') }}">Agences</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Paramètres de facturation</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                @include('Backoffice.profile.partials._agency_settings_sidebar', [
                    'agency' => $agency,
                    'active' => 'invoice-settings',
                ])
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold">Paramètres de facturation</h5>
                    </div>
                    <form action="{{ route('backoffice.agencies.settings.invoice-settings.update', $agency) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            
                            <!-- Invoice Logo Section -->
                            <div class="settings-section">
                                <h6>📎 Logo de facture</h6>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label required">Logo de facture</label>
                                        <div class="logo-preview" id="logoPreview">
                                            @if($agency->getFirstMediaUrl('logo'))
                                                <img src="{{ $agency->getFirstMediaUrl('logo') }}" alt="Invoice Logo">
                                            @else
                                                <div class="placeholder">
                                                    <i class="ti ti-photo"></i>
                                                    <p>Aucun logo téléchargé</p>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" name="invoice_logo" class="form-control" accept="image/*" onchange="previewLogo(this)">
                                        <small class="text-muted">Taille de l'image 180×180, max 5 Mo</small>
                                        @error('invoice_logo')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Format Section -->
                            <div class="settings-section">
                                <h6>📄 Format de facture</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Préfixe de facture</label>
                                        <input type="text" name="invoice_prefix" class="form-control"
                                               value="{{ old('invoice_prefix', $agency->settings['invoice']['invoice_prefix'] ?? 'INV-') }}"
                                               placeholder="ex. : INV-, FCT-">
                                        <small class="text-muted">Préfixe avant le numéro de facture</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Numéro de début</label>
                                        <input type="number" name="invoice_start" class="form-control"
                                               value="{{ old('invoice_start', $agency->settings['invoice']['invoice_start'] ?? '1') }}"
                                               min="1">
                                        <small class="text-muted">Numéro de départ pour les nouvelles factures</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Échéance de facture</label>
                                        <div class="input-group">
                                            <input type="number" name="invoice_due_days" class="form-control"
                                                   value="{{ old('invoice_due_days', $agency->settings['invoice']['invoice_due_days'] ?? '15') }}"
                                                   min="0" max="90">
                                            <span class="input-group-text">Jours</span>
                                        </div>
                                        <small class="text-muted">Nombre de jours avant l'échéance</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">Arrondi de facture</label>
                                        <select name="invoice_round_off" class="form-select">
                                            <option value="0" {{ (old('invoice_round_off', $agency->settings['invoice']['invoice_round_off'] ?? '0') == '0') ? 'selected' : '' }}>Pas d'arrondi</option>
                                            <option value="0.5" {{ (old('invoice_round_off', $agency->settings['invoice']['invoice_round_off'] ?? '') == '0.5') ? 'selected' : '' }}>Arrondir à 0,50</option>
                                            <option value="1" {{ (old('invoice_round_off', $agency->settings['invoice']['invoice_round_off'] ?? '') == '1') ? 'selected' : '' }}>Arrondir à 1,00</option>
                                            <option value="10" {{ (old('invoice_round_off', $agency->settings['invoice']['invoice_round_off'] ?? '') == '10') ? 'selected' : '' }}>Arrondir à 10,00</option>
                                            <option value="100" {{ (old('invoice_round_off', $agency->settings['invoice']['invoice_round_off'] ?? '') == '100') ? 'selected' : '' }}>Arrondir à 100,00</option>
                                        </select>
                                        <small class="text-muted">Arrondir les montants totaux au plus proche</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Details Section -->
                            <div class="settings-section">
                                <h6>🏢 Informations de l'entreprise</h6>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" name="show_company_details" value="1"
                                                   id="showCompanyDetails"
                                                   {{ (old('show_company_details', $agency->settings['invoice']['show_company_details'] ?? true)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="showCompanyDetails">Afficher les informations de l'entreprise sur la facture</label>
                                        </div>
                                        <small class="text-muted">Afficher le nom, l'adresse, le téléphone et l'e-mail sur la facture</small>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3" id="companyDetailsFields">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nom de l'entreprise</label>
                                                <input type="text" name="company_name" class="form-control" 
                                                       value="{{ old('company_name', $agency->settings['invoice']['company_name'] ?? $agency->name) }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Numéro d'immatriculation</label>
                                                <input type="text" name="company_reg_number" class="form-control" 
                                                       value="{{ old('company_reg_number', $agency->settings['invoice']['company_reg_number'] ?? $agency->rc_number ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Numéro fiscal</label>
                                                <input type="text" name="company_tax_number" class="form-control" 
                                                       value="{{ old('company_tax_number', $agency->settings['invoice']['company_tax_number'] ?? $agency->ice_number ?? '') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Téléphone</label>
                                                <input type="text" name="company_phone" class="form-control" 
                                                       value="{{ old('company_phone', $agency->settings['invoice']['company_phone'] ?? $agency->phone) }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">E-mail</label>
                                                <input type="email" name="company_email" class="form-control" 
                                                       value="{{ old('company_email', $agency->settings['invoice']['company_email'] ?? $agency->email) }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Adresse</label>
                                                <input type="text" name="company_address" class="form-control" 
                                                       value="{{ old('company_address', $agency->settings['invoice']['company_address'] ?? $agency->address) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Terms & Footer -->
                            <div class="settings-section">
                                <h6>📝 Terms & Footer</h6>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Invoice Terms</label>
                                        <textarea name="invoice_terms" class="form-control" rows="4" 
                                                  placeholder="e.g., Payment is due within 15 days. Please include invoice number with your payment.">{{ old('invoice_terms', $agency->settings['invoice']['invoice_terms'] ?? '') }}</textarea>
                                        <small class="text-muted">Terms and conditions that appear on invoices</small>
                                    </div>
                                    
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label required">Footer of Invoice</label>
                                        <textarea name="invoice_footer" class="form-control" rows="3" 
                                                  placeholder="e.g., Thank you for your business!">{{ old('invoice_footer', $agency->settings['invoice']['invoice_footer'] ?? '') }}</textarea>
                                        <small class="text-muted">Text that appears at the bottom of every invoice</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Information -->
                            <div class="settings-section">
                                <h6>💳 Payment Information</h6>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Payment Instructions</label>
                                        <textarea name="payment_instructions" class="form-control" rows="3" 
                                                  placeholder="e.g., Bank transfer to: Account XXXXX, IBAN: XXXXX">{{ old('payment_instructions', $agency->settings['invoice']['payment_instructions'] ?? '') }}</textarea>
                                        <small class="text-muted">Payment instructions shown on invoices</small>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('backoffice.agencies.index') }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewLogo(input) {
    const preview = document.getElementById('logoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Invoice Logo">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const showDetailsCheckbox = document.getElementById('showCompanyDetails');
    const companyFields = document.getElementById('companyDetailsFields');
    
    function toggleCompanyFields() {
        if (showDetailsCheckbox.checked) {
            companyFields.style.display = 'block';
        } else {
            companyFields.style.display = 'none';
        }
    }
    
    if (showDetailsCheckbox && companyFields) {
        toggleCompanyFields();
        showDetailsCheckbox.addEventListener('change', toggleCompanyFields);
    }
});
</script>
@endsection