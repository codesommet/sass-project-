<?php $page = 'invoice-template'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .template-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        background: #fff;
    }
    .template-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }
    .template-card.selected {
        border-color: #0d6efd;
        box-shadow: 0 8px 25px rgba(13,110,253,0.2);
    }

    /* Check icon */
    .template-check {
        position: absolute;
        top: 12px;
        left: 12px;
        width: 28px;
        height: 28px;
        background: #0d6efd;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        z-index: 10;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.3s ease;
    }
    .template-card.selected .template-check {
        opacity: 1;
        transform: scale(1);
    }

    /* Default badge */
    .default-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #0d6efd;
        color: #fff;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 10;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .template-card.selected .default-badge {
        opacity: 1;
    }

    /* Template header */
    .template-header {
        padding: 16px 20px;
        color: #fff;
        font-weight: 700;
        font-size: 15px;
        text-align: center;
        letter-spacing: 0.5px;
    }
    .t1 .template-header { background: linear-gradient(135deg, #1a56db 0%, #1e40af 100%); }
    .t2 .template-header { background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); }
    .t3 .template-header { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }

    /* Mini preview */
    .template-preview {
        padding: 18px;
        flex: 1;
        font-size: 10px;
        color: #475569;
        background: #fafbfc;
    }
    .prev-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
    .prev-logo { width: 32px; height: 32px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 11px; }
    .t1 .prev-logo { background: #1a56db; }
    .t2 .prev-logo { background: #0f766e; }
    .t3 .prev-logo { background: #2c3e50; }
    .prev-inv-label { font-size: 14px; font-weight: 700; letter-spacing: 1px; }
    .t1 .prev-inv-label { color: #1a56db; }
    .t2 .prev-inv-label { color: #0f766e; }
    .t3 .prev-inv-label { color: #2c3e50; }
    .prev-inv-no { font-size: 9px; color: #94a3b8; }

    .prev-boxes { display: flex; gap: 8px; margin-bottom: 12px; }
    .prev-box {
        flex: 1;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        padding: 6px 8px;
        background: #fff;
    }
    .prev-box-title { font-size: 7px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 2px; }
    .t1 .prev-box-title { color: #1a56db; }
    .t2 .prev-box-title { color: #0f766e; }
    .t3 .prev-box-title { color: #2c3e50; }
    .prev-box-text { font-size: 9px; color: #1e293b; font-weight: 600; }

    /* Mini table */
    .prev-table { width: 100%; margin-bottom: 10px; }
    .prev-table-head {
        padding: 5px 8px;
        color: #fff;
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
    }
    .t1 .prev-table-head { background: #1a56db; }
    .t2 .prev-table-head { background: #0f766e; }
    .t3 .prev-table-head { background: #2c3e50; }
    .prev-table-row {
        display: flex;
        justify-content: space-between;
        padding: 4px 8px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 9px;
    }
    .prev-table-row:nth-child(even) { background: #f8fafc; }

    .prev-total {
        display: flex;
        justify-content: space-between;
        padding: 6px 8px;
        font-weight: 700;
        font-size: 11px;
        color: #fff;
        border-radius: 4px;
        margin-top: 6px;
    }
    .t1 .prev-total { background: #1a56db; }
    .t2 .prev-total { background: #0f766e; }
    .t3 .prev-total { background: #2c3e50; }

    .prev-footer {
        margin-top: 10px;
        padding-top: 6px;
        border-top: 1px solid #e2e8f0;
        text-align: center;
        font-size: 8px;
        color: #94a3b8;
    }

    /* Template name label */
    .template-label {
        padding: 12px 20px;
        background: #fff;
        border-top: 1px solid #f1f5f9;
        text-align: center;
    }
    .template-name { font-weight: 700; font-size: 13px; color: #1e293b; }
    .template-desc { font-size: 11px; color: #64748b; margin-top: 2px; }

    /* Section divider */
    .section-divider {
        border: none;
        border-top: 2px solid #e2e8f0;
        margin: 30px 0 20px;
    }
</style>

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0 pb-0 me-lg-4">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Modèles de Documents</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Accueil</a></li>
                        <li class="breadcrumb-item">Paramètres</li>
                        <li class="breadcrumb-item active" aria-current="page">Modèles de Documents</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                @include('Backoffice.profile.partials._agency_settings_sidebar', [
                    'agency' => $agency,
                    'active' => 'invoice-template',
                ])
            </div>
            <div class="col-lg-9">
                <form action="{{ route('backoffice.agencies.settings.update', $agency) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="app[invoice_template]" id="selected_invoice_template" value="{{ $agency->settings['app']['invoice_template'] ?? 'template1' }}">
                    <input type="hidden" name="app[contract_template]" id="selected_contract_template" value="{{ $agency->settings['app']['contract_template'] ?? 'template1' }}">

                    @if(session('toast'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('toast')['message'] ?? 'Modèles mis à jour avec succès.' }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- ══════════════════════════════════════════════════════ --}}
                    {{-- ── INVOICE TEMPLATES ── --}}
                    {{-- ══════════════════════════════════════════════════════ --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold mb-1"><i class="ti ti-file-invoice me-2"></i>Modèle de Facture</h5>
                            <p class="text-muted mb-0">Choisissez le modèle utilisé automatiquement lors de l'export PDF de vos factures</p>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                {{-- Template 1: Classic Blue --}}
                                <div class="col-md-6">
                                    <div class="template-card t1 {{ ($agency->settings['app']['invoice_template'] ?? 'template1') === 'template1' ? 'selected' : '' }}" data-template="template1" data-group="invoice">
                                        <div class="template-check"><i class="ti ti-check"></i></div>
                                        <div class="default-badge">Par défaut</div>
                                        <div class="template-header">Classic Blue</div>
                                        <div class="template-preview">
                                            <div class="prev-top">
                                                <div class="prev-logo">AG</div>
                                                <div style="text-align:right;">
                                                    <div class="prev-inv-label">FACTURE</div>
                                                    <div class="prev-inv-no">N° INV-202503-0001</div>
                                                </div>
                                            </div>
                                            <div class="prev-boxes">
                                                <div class="prev-box">
                                                    <div class="prev-box-title">De</div>
                                                    <div class="prev-box-text">{{ Str::limit($agency->name, 18) }}</div>
                                                </div>
                                                <div class="prev-box">
                                                    <div class="prev-box-title">Facturer à</div>
                                                    <div class="prev-box-text">Client exemple</div>
                                                </div>
                                            </div>
                                            <div class="prev-table">
                                                <div class="prev-table-head" style="display:flex; justify-content:space-between;">
                                                    <span>Description</span><span>Total</span>
                                                </div>
                                                <div class="prev-table-row"><span>Location véhicule (5j)</span><span>1 500,00</span></div>
                                                <div class="prev-table-row"><span>Assurance complète</span><span>350,00</span></div>
                                                <div class="prev-table-row"><span>GPS</span><span>150,00</span></div>
                                            </div>
                                            <div class="prev-total"><span>TOTAL TTC</span><span>2 000,00 MAD</span></div>
                                            <div class="prev-footer">Document généré automatiquement</div>
                                        </div>
                                        <div class="template-label">
                                            <div class="template-name">Classic Blue</div>
                                            <div class="template-desc">En-tête bleu professionnel avec mise en page structurée</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Template 2: Modern Teal --}}
                                <div class="col-md-6">
                                    <div class="template-card t2 {{ ($agency->settings['app']['invoice_template'] ?? 'template1') === 'template2' ? 'selected' : '' }}" data-template="template2" data-group="invoice">
                                        <div class="template-check"><i class="ti ti-check"></i></div>
                                        <div class="default-badge">Par défaut</div>
                                        <div class="template-header">Modern Teal</div>
                                        <div class="template-preview">
                                            <div class="prev-top">
                                                <div class="prev-logo">AG</div>
                                                <div style="text-align:right;">
                                                    <div class="prev-inv-label">FACTURE</div>
                                                    <div class="prev-inv-no">INV-202503-0001</div>
                                                </div>
                                            </div>
                                            <div style="height:1px; background:#e2e8f0; margin-bottom:10px;"></div>
                                            <div class="prev-boxes">
                                                <div class="prev-box" style="background:#f0fdfa;">
                                                    <div class="prev-box-title">Émetteur</div>
                                                    <div class="prev-box-text">{{ Str::limit($agency->name, 18) }}</div>
                                                </div>
                                                <div class="prev-box" style="background:#f0fdfa;">
                                                    <div class="prev-box-title">Client</div>
                                                    <div class="prev-box-text">Client exemple</div>
                                                </div>
                                            </div>
                                            <div class="prev-table">
                                                <div class="prev-table-head" style="display:flex; justify-content:space-between;">
                                                    <span>Description</span><span>Total</span>
                                                </div>
                                                <div class="prev-table-row"><span>Location véhicule (5j)</span><span>1 500,00</span></div>
                                                <div class="prev-table-row"><span>Assurance complète</span><span>350,00</span></div>
                                                <div class="prev-table-row"><span>GPS</span><span>150,00</span></div>
                                            </div>
                                            <div class="prev-total"><span>TOTAL TTC</span><span>2 000,00 MAD</span></div>
                                            <div class="prev-footer">
                                                <div style="width:30px; height:3px; background:#0f766e; border-radius:2px; margin:0 auto 4px;"></div>
                                                Document généré automatiquement
                                            </div>
                                        </div>
                                        <div class="template-label">
                                            <div class="template-name">Modern Teal</div>
                                            <div class="template-desc">Design moderne avec barre d'accent et détails élégants</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('app.invoice_template')
                                <small class="text-danger d-block mt-3">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════════════════ --}}
                    {{-- ── CONTRACT TEMPLATES ── --}}
                    {{-- ══════════════════════════════════════════════════════ --}}
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="fw-bold mb-1"><i class="ti ti-file-text me-2"></i>Modèle de Contrat</h5>
                            <p class="text-muted mb-0">Choisissez le modèle utilisé automatiquement lors de l'export PDF de vos contrats de location</p>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                {{-- Contract Template 1: Classic Blue --}}
                                <div class="col-md-4">
                                    <div class="template-card t1 {{ ($agency->settings['app']['contract_template'] ?? 'template1') === 'template1' ? 'selected' : '' }}" data-template="template1" data-group="contract">
                                        <div class="template-check"><i class="ti ti-check"></i></div>
                                        <div class="default-badge">Par défaut</div>
                                        <div class="template-header">Classic Blue</div>
                                        <div class="template-preview">
                                            <div class="prev-top">
                                                <div class="prev-logo">AG</div>
                                                <div style="text-align:right;">
                                                    <div class="prev-inv-label">CONTRAT</div>
                                                    <div class="prev-inv-no">N° CTR-202503-0001</div>
                                                </div>
                                            </div>
                                            <div class="prev-boxes">
                                                <div class="prev-box">
                                                    <div class="prev-box-title">Agence</div>
                                                    <div class="prev-box-text">{{ Str::limit($agency->name, 18) }}</div>
                                                </div>
                                                <div class="prev-box">
                                                    <div class="prev-box-title">Client</div>
                                                    <div class="prev-box-text">Client exemple</div>
                                                </div>
                                            </div>
                                            <div class="prev-boxes">
                                                <div class="prev-box">
                                                    <div class="prev-box-title">Véhicule</div>
                                                    <div class="prev-box-text">Dacia Logan</div>
                                                </div>
                                                <div class="prev-box">
                                                    <div class="prev-box-title">Durée</div>
                                                    <div class="prev-box-text">5 jours</div>
                                                </div>
                                            </div>
                                            <div class="prev-total"><span>TOTAL TTC</span><span>2 000,00 MAD</span></div>
                                            <div class="prev-footer">Document généré automatiquement</div>
                                        </div>
                                        <div class="template-label">
                                            <div class="template-name">Classic Blue</div>
                                            <div class="template-desc">En-tête bleu avec sections structurées pour contrat</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Contract Template 2: Modern Teal --}}
                                <div class="col-md-4">
                                    <div class="template-card t2 {{ ($agency->settings['app']['contract_template'] ?? 'template1') === 'template2' ? 'selected' : '' }}" data-template="template2" data-group="contract">
                                        <div class="template-check"><i class="ti ti-check"></i></div>
                                        <div class="default-badge">Par défaut</div>
                                        <div class="template-header">Modern Teal</div>
                                        <div class="template-preview">
                                            <div class="prev-top">
                                                <div class="prev-logo">AG</div>
                                                <div style="text-align:right;">
                                                    <div class="prev-inv-label">CONTRAT</div>
                                                    <div class="prev-inv-no">CTR-202503-0001</div>
                                                </div>
                                            </div>
                                            <div style="height:1px; background:#e2e8f0; margin-bottom:10px;"></div>
                                            <div class="prev-boxes">
                                                <div class="prev-box" style="background:#f0fdfa;">
                                                    <div class="prev-box-title">Agence</div>
                                                    <div class="prev-box-text">{{ Str::limit($agency->name, 18) }}</div>
                                                </div>
                                                <div class="prev-box" style="background:#f0fdfa;">
                                                    <div class="prev-box-title">Client</div>
                                                    <div class="prev-box-text">Client exemple</div>
                                                </div>
                                            </div>
                                            <div class="prev-boxes">
                                                <div class="prev-box" style="background:#f0fdfa;">
                                                    <div class="prev-box-title">Véhicule</div>
                                                    <div class="prev-box-text">Dacia Logan</div>
                                                </div>
                                                <div class="prev-box" style="background:#f0fdfa;">
                                                    <div class="prev-box-title">Durée</div>
                                                    <div class="prev-box-text">5 jours</div>
                                                </div>
                                            </div>
                                            <div class="prev-total"><span>TOTAL TTC</span><span>2 000,00 MAD</span></div>
                                            <div class="prev-footer">
                                                <div style="width:30px; height:3px; background:#0f766e; border-radius:2px; margin:0 auto 4px;"></div>
                                                Document généré automatiquement
                                            </div>
                                        </div>
                                        <div class="template-label">
                                            <div class="template-name">Modern Teal</div>
                                            <div class="template-desc">Design moderne avec barre d'accent pour contrat</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Contract Template 3: Formulaire Classique --}}
                                <div class="col-md-4">
                                    <div class="template-card t3 {{ ($agency->settings['app']['contract_template'] ?? 'template1') === 'template3' ? 'selected' : '' }}" data-template="template3" data-group="contract">
                                        <div class="template-check"><i class="ti ti-check"></i></div>
                                        <div class="default-badge">Par défaut</div>
                                        <div class="template-header">Formulaire Classique</div>
                                        <div class="template-preview">
                                            <div style="text-align:center; font-weight:700; font-size:9px; padding:6px 0; border-bottom:1px solid #ccc; margin-bottom:8px; letter-spacing:0.5px;">
                                                CONTRAT DE LOCATION DE VEHICULE
                                            </div>
                                            <div style="display:flex; gap:6px; margin-bottom:8px; align-items:center;">
                                                <div class="prev-logo" style="width:24px; height:24px; font-size:8px; border-radius:4px;">AG</div>
                                                <div style="font-size:8px; color:#555; text-align:center; flex:1;">{{ Str::limit($agency->name, 20) }}</div>
                                            </div>
                                            <div style="background:#ecf0f1; padding:3px 6px; font-size:7px; font-weight:700; margin-bottom:4px;">VEHICULE</div>
                                            <div style="font-size:8px; color:#555; padding:2px 6px; border-bottom:1px solid #eee;">Marque: Dacia - Logan</div>
                                            <div style="font-size:8px; color:#555; padding:2px 6px; border-bottom:1px solid #eee;">Durée: 5 jours</div>
                                            <div style="background:#ecf0f1; padding:3px 6px; font-size:7px; font-weight:700; margin: 4px 0;">LOCATAIRE</div>
                                            <div style="font-size:8px; color:#555; padding:2px 6px; border-bottom:1px solid #eee;">Nom: Client | CIN: XX123</div>
                                            <div style="background:#ecf0f1; padding:3px 6px; font-size:7px; font-weight:700; margin: 4px 0;">2ème CONDUCTEUR</div>
                                            <div style="font-size:8px; color:#aaa; padding:2px 6px;">---</div>
                                            <div class="prev-total" style="margin-top:6px;"><span>TOTAL</span><span>2 000 Dhs</span></div>
                                        </div>
                                        <div class="template-label">
                                            <div class="template-name">Formulaire Classique</div>
                                            <div class="template-desc">Style formulaire officiel avec bordures et sections détaillées</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('app.contract_template')
                                <small class="text-danger d-block mt-3">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    {{-- ── Save Button ── --}}
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>Enregistrer les modèles
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceInput = document.getElementById('selected_invoice_template');
    const contractInput = document.getElementById('selected_contract_template');

    document.querySelectorAll('.template-card').forEach(card => {
        card.addEventListener('click', function() {
            const group = this.dataset.group;
            // Deselect only cards in the same group
            document.querySelectorAll(`.template-card[data-group="${group}"]`).forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');

            if (group === 'invoice') {
                invoiceInput.value = this.dataset.template;
            } else if (group === 'contract') {
                contractInput.value = this.dataset.template;
            }
        });
    });
});
</script>
@endsection
