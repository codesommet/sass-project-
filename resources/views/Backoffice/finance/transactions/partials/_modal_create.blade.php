<?php $page = 'finance-transactions'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .wizard-nav {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .wizard-nav .nav-item {
        flex: 1;
        min-width: 150px;
    }
    .wizard-nav .nav-link {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        color: #6c757d;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s;
        border: 1px solid #dee2e6;
        cursor: pointer;
    }
    .wizard-nav .nav-link i {
        margin-right: 8px;
        font-size: 1.2rem;
    }
    .wizard-nav .nav-link.active {
        background: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    .fieldset {
        display: none;
    }
    .fieldset.active {
        display: block;
    }
    
    /* Auto revenue section */
    .auto-revenue-section {
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        display: none;
        border: 1px solid #dee2e6;
    }
    .auto-revenue-section.show {
        display: block;
    }
    .auto-revenue-section h5 {
        margin-bottom: 15px;
        color: #0d6efd;
    }
    
    /* Source info */
    .source-info {
        background: #e7f1ff;
        border-left: 4px solid #0d6efd;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .source-info i {
        color: #0d6efd;
    }
</style>

<div class="page-wrapper">
    <div class="content me-0">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-3">
                    <a href="{{ route('backoffice.finance.transactions.index') }}" class="d-inline-flex align-items-center fw-medium">
                        <i class="ti ti-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="ti ti-transfer me-2"></i>
                            Nouvelle transaction
                        </h4>
                        @if(isset($prefillData) && !empty($prefillData))
                        <p class="text-muted mb-0 mt-1">
                            Transaction pré-remplie à partir d'une source
                        </p>
                        @endif
                    </div>

                    <div class="card-body">
                        <!-- Source Info (if prefilled) -->
                        @if(isset($prefillData) && !empty($prefillData))
                        <div class="source-info">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-info-circle fs-20 me-2"></i>
                                <div>
                                    <strong>Transaction automatique</strong>
                                    <p class="mb-0">Cette transaction est liée à un contrat de location. Le montant et la description ont été pré-remplis automatiquement.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Wizard Navigation -->
                        <div class="wizard-nav">
                            <div class="nav-item">
                                <a class="nav-link active" data-tab="1">
                                    <i class="ti ti-info-circle"></i>
                                    Informations
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="2">
                                    <i class="ti ti-currency-dollar"></i>
                                    Montant
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" data-tab="3">
                                    <i class="ti ti-details"></i>
                                    Détails
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('backoffice.finance.transactions.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            
                            @if(isset($prefillData) && !empty($prefillData))
                                <input type="hidden" name="source_type" value="{{ $prefillData['source_type'] ?? '' }}">
                                <input type="hidden" name="source_id" value="{{ $prefillData['source_id'] ?? '' }}">
                            @endif

                            <!-- Tab 1: Informations -->
                            <fieldset class="fieldset active" id="tab1">
                                <div class="row">
                                    <!-- Date -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Date <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="date" 
                                                   value="{{ old('date', $prefillData['date'] ?? date('Y-m-d')) }}" 
                                                   class="form-control @error('date') is-invalid @enderror" 
                                                   {{ isset($prefillData['date']) ? 'readonly' : '' }}
                                                   required>
                                            @error('date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if(isset($prefillData['date']))
                                                <small class="text-muted">Date provenant du contrat</small>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Type (Revenu/Dépense) -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Type <span class="text-danger">*</span>
                                            </label>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="type" id="type_income" 
                                                           value="income" {{ old('type', $prefillData['type'] ?? 'income') == 'income' ? 'checked' : '' }} 
                                                           {{ isset($prefillData['type']) ? 'disabled' : '' }}
                                                           required>
                                                    <label class="form-check-label text-success" for="type_income">
                                                        <i class="ti ti-trending-up me-1"></i>Revenu
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="type" id="type_expense" 
                                                           value="expense" {{ old('type', $prefillData['type'] ?? '') == 'expense' ? 'checked' : '' }} 
                                                           {{ isset($prefillData['type']) ? 'disabled' : '' }}
                                                           required>
                                                    <label class="form-check-label text-danger" for="type_expense">
                                                        <i class="ti ti-trending-down me-1"></i>Dépense
                                                    </label>
                                                </div>
                                            </div>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if(isset($prefillData['type']))
                                                <input type="hidden" name="type" value="{{ $prefillData['type'] }}">
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Revenue Type Dropdown (appears only when Revenu is selected and not prefilled) -->
                                    @if(!isset($prefillData['type']) || $prefillData['type'] != 'income')
                                    <div class="col-md-12" id="revenue-type-container" style="{{ old('type', $prefillData['type'] ?? 'income') == 'income' ? '' : 'display: none;' }}">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Type de revenu <span class="text-danger">*</span>
                                            </label>
                                            <select name="revenue_type" id="revenue_type" class="form-select @error('revenue_type') is-invalid @enderror">
                                                <option value="manuelle" {{ old('revenue_type', 'manuelle') == 'manuelle' ? 'selected' : '' }}>Manuelle</option>
                                                <option value="auto" {{ old('revenue_type') == 'auto' ? 'selected' : '' }}>Automatique (Récurrent)</option>
                                            </select>
                                            @error('revenue_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Auto Revenue Section (appears when "Auto" is selected) -->
                                    <div class="col-md-12 auto-revenue-section" id="auto-revenue-section">
                                        <h5><i class="ti ti-repeat me-2"></i>Paramètres de récurrence</h5>
                                        
                                        <div class="row">
                                            <!-- Valeur du revenu -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Montant du revenu <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" name="recurring_amount" value="{{ old('recurring_amount') }}" 
                                                           class="form-control @error('recurring_amount') is-invalid @enderror" 
                                                           step="0.01" min="0.01" placeholder="Montant récurrent">
                                                    <span class="input-group-text">MAD</span>
                                                </div>
                                                <small class="text-muted">Le montant qui sera répété automatiquement</small>
                                                @error('recurring_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Fréquence -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Fréquence</label>
                                                <select name="recurrence_frequency" class="form-select">
                                                    <option value="daily">Quotidienne</option>
                                                    <option value="weekly">Hebdomadaire</option>
                                                    <option value="monthly" selected>Mensuelle</option>
                                                    <option value="yearly">Annuelle</option>
                                                </select>
                                            </div>

                                            <!-- Intervalle -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Intervalle</label>
                                                <div class="input-group">
                                                    <input type="number" name="recurrence_interval" class="form-control" value="1" min="1" max="12">
                                                    <span class="input-group-text">mois</span>
                                                </div>
                                                <small class="text-muted">ex: tous les 2 mois</small>
                                            </div>

                                            <!-- Date de début -->
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Date de début</label>
                                                <input type="date" name="recurrence_start_date" class="form-control" value="{{ date('Y-m-d') }}">
                                            </div>

                                            <!-- Type de fin -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Fin de récurrence</label>
                                                <select name="recurrence_end_type" class="form-select" onchange="toggleEndDate()">
                                                    <option value="never">Jamais</option>
                                                    <option value="date">Date spécifique</option>
                                                    <option value="occurrences">Après X occurrences</option>
                                                </select>
                                            </div>

                                            <!-- Date de fin -->
                                            <div class="col-md-6 mb-3" id="end-date-field" style="display: none;">
                                                <label class="form-label">Date de fin</label>
                                                <input type="date" name="recurrence_end_date" class="form-control">
                                            </div>

                                            <!-- Nombre d'occurrences -->
                                            <div class="col-md-6 mb-3" id="occurrences-field" style="display: none;">
                                                <label class="form-label">Nombre d'occurrences</label>
                                                <input type="number" name="recurrence_occurrences" class="form-control" min="1" value="12">
                                            </div>

                                            <!-- Description pour le revenu automatique -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Description du revenu automatique</label>
                                                <textarea name="recurring_description" class="form-control" rows="2" placeholder="ex: Loyer mensuel, Abonnement, Salaire...">{{ old('recurring_description') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Account -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Compte <span class="text-danger">*</span>
                                            </label>
                                            <select name="financial_account_id" class="form-select @error('financial_account_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un compte</option>
                                                @foreach($accounts as $account)
                                                    <option value="{{ $account->id }}" {{ old('financial_account_id') == $account->id ? 'selected' : '' }}>
                                                        {{ $account->name }} ({{ $account->formatted_current_balance }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('financial_account_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-primary next-tab" data-next="2">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- Tab 2: Montant -->
                            <fieldset class="fieldset" id="tab2">
                                <div class="row">
                                    <!-- Amount (for manual transactions) -->
                                    <div class="col-md-12" id="manual-amount-container">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Montant <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number" name="amount" 
                                                       value="{{ old('amount', $prefillData['amount'] ?? '') }}" 
                                                       class="form-control @error('amount') is-invalid @enderror" 
                                                       step="0.01" min="0.01" 
                                                       placeholder="Montant de la transaction"
                                                       {{ isset($prefillData['amount']) ? 'readonly' : '' }}>
                                                <span class="input-group-text">MAD</span>
                                            </div>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if(isset($prefillData['amount']))
                                                <small class="text-muted">Montant calculé automatiquement à partir du contrat</small>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Category -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Catégorie</label>
                                            <select name="transaction_category_id" class="form-select @error('transaction_category_id') is-invalid @enderror">
                                                <option value="">Sélectionner une catégorie (optionnel)</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('transaction_category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }} ({{ $category->type_text }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('transaction_category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="1">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="button" class="btn btn-primary next-tab" data-next="3">
                                        Suivant <i class="ti ti-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </fieldset>

                            <!-- Tab 3: Détails -->
                            <fieldset class="fieldset" id="tab3">
                                <div class="row">
                                    <!-- Description -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" 
                                                      class="form-control @error('description') is-invalid @enderror" 
                                                      rows="3" 
                                                      placeholder="Description de la transaction..."
                                                      {{ isset($prefillData['description']) ? 'readonly' : '' }}>{{ old('description', $prefillData['description'] ?? '') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if(isset($prefillData['description']))
                                                <small class="text-muted">Description générée automatiquement</small>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Reference -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Référence</label>
                                            <input type="text" name="reference" 
                                                   value="{{ old('reference', $prefillData['reference'] ?? '') }}" 
                                                   class="form-control @error('reference') is-invalid @enderror" 
                                                   maxlength="100" 
                                                   placeholder="Numéro de facture, reçu..."
                                                   {{ isset($prefillData['reference']) ? 'readonly' : '' }}>
                                            @error('reference')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if(isset($prefillData['reference']))
                                                <small class="text-muted">Référence du contrat</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-light prev-tab" data-prev="2">
                                        <i class="ti ti-chevron-left me-1"></i> Précédent
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="ti ti-device-floppy me-1"></i> Créer la transaction
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab Navigation
    const tabs = document.querySelectorAll('.nav-link[data-tab]');
    const fieldsets = document.querySelectorAll('.fieldset');
    
    function showTab(tabNumber) {
        fieldsets.forEach(f => f.classList.remove('active'));
        document.getElementById(`tab${tabNumber}`).classList.add('active');
        
        tabs.forEach(t => t.classList.remove('active'));
        document.querySelector(`.nav-link[data-tab="${tabNumber}"]`).classList.add('active');
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            showTab(this.getAttribute('data-tab'));
        });
    });

    document.querySelectorAll('.next-tab').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            showTab(this.getAttribute('data-next'));
        });
    });

    document.querySelectorAll('.prev-tab').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            showTab(this.getAttribute('data-prev'));
        });
    });

    // Type radio change handler (Revenu/Dépense) - only if not prefilled
    @if(!isset($prefillData['type']) || $prefillData['type'] != 'income')
    const typeIncome = document.getElementById('type_income');
    const typeExpense = document.getElementById('type_expense');
    const revenueTypeContainer = document.getElementById('revenue-type-container');
    
    function toggleRevenueType() {
        if (typeIncome.checked) {
            revenueTypeContainer.style.display = 'block';
            // Trigger change on revenue type to update auto section
            document.getElementById('revenue_type').dispatchEvent(new Event('change'));
        } else {
            revenueTypeContainer.style.display = 'none';
            document.getElementById('auto-revenue-section').classList.remove('show');
        }
    }
    
    if (typeIncome && typeExpense) {
        typeIncome.addEventListener('change', toggleRevenueType);
        typeExpense.addEventListener('change', toggleRevenueType);
    }

    // Revenue type dropdown change handler
    const revenueType = document.getElementById('revenue_type');
    const autoSection = document.getElementById('auto-revenue-section');
    const manualAmountContainer = document.getElementById('manual-amount-container');
    
    if (revenueType) {
        revenueType.addEventListener('change', function() {
            if (this.value === 'auto' && typeIncome.checked) {
                autoSection.classList.add('show');
                // Disable manual amount field when auto is selected
                manualAmountContainer.style.opacity = '0.5';
                document.querySelector('[name="amount"]').disabled = true;
                document.querySelector('[name="amount"]').required = false;
            } else {
                autoSection.classList.remove('show');
                manualAmountContainer.style.opacity = '1';
                document.querySelector('[name="amount"]').disabled = false;
                document.querySelector('[name="amount"]').required = true;
            }
        });
    }

    // Initial state
    toggleRevenueType();
    @endif

    // Bootstrap validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});

// Toggle end date field based on selection
function toggleEndDate() {
    const endType = document.querySelector('[name="recurrence_end_type"]').value;
    const endDateField = document.getElementById('end-date-field');
    const occurrencesField = document.getElementById('occurrences-field');
    
    endDateField.style.display = endType === 'date' ? 'block' : 'none';
    occurrencesField.style.display = endType === 'occurrences' ? 'block' : 'none';
}
</script>
@endsection