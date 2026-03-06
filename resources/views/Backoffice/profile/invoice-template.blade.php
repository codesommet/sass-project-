<?php $page = 'invoice-template'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .invoice-template-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    
    .invoice-template-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .invoice-template-card.selected {
        border-color: #0d6efd;
        box-shadow: 0 5px 15px rgba(13,110,253,0.2);
    }
    
    .invoice-template-card .card-header {
        color: white;
        font-weight: 600;
        text-align: center;
        padding: 12px;
        font-size: 1.1rem;
        position: relative;
    }
    
    /* Free Badge */
    .free-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #28a745;
        color: white;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        z-index: 10;
    }
    
    /* Premium Badge */
    .premium-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ffc107;
        color: #333;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        z-index: 10;
    }
    
    /* Preview Button */
    .preview-btn {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255,255,255,0.9);
        border: 1px solid #0d6efd;
        color: #0d6efd;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 30;
        text-decoration: none;
    }
    
    .invoice-template-card:hover .preview-btn {
        opacity: 1;
    }
    
    .preview-btn:hover {
        background: #0d6efd;
        color: white;
    }
    
    .invoice-preview {
        background: white;
        padding: 20px;
        min-height: 320px;
        position: relative;
        flex: 1;
    }
    
    /* Premium Blur Effect */
    .premium-blur {
        filter: blur(4px);
        pointer-events: none;
        user-select: none;
    }
    
    .premium-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        z-index: 25;
        border-radius: 8px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .invoice-template-card:hover .premium-overlay {
        opacity: 1;
    }
    
    .premium-message {
        background: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        max-width: 200px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    
    .premium-message i {
        font-size: 40px;
        color: #ffc107;
        margin-bottom: 10px;
    }
    
    .premium-message h6 {
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .premium-message p {
        font-size: 0.8rem;
        color: #666;
        margin-bottom: 15px;
    }
    
    .premium-message .btn-contact {
        background: #ffc107;
        color: #333;
        border: none;
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .premium-message .btn-contact:hover {
        background: #e0a800;
        transform: scale(1.05);
    }
    
    /* Template 1 - Simple Blue (Free) */
    .template-1 .card-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    .template-1 .invoice-preview {
        border-left: 4px solid #4e73df;
    }
    
    /* Template 2 - Clean Green (Free) */
    .template-2 .card-header {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }
    .template-2 .invoice-preview {
        background: #f8f9fc;
        border-radius: 8px;
    }
    
    /* Template 3 - Premium Gold */
    .template-3 .card-header {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
    }
    .template-3 .invoice-preview {
        background: linear-gradient(135deg, #fff9e6 0%, #fff 100%);
        border: 2px solid #f6c23e;
        box-shadow: 0 5px 15px rgba(246,194,62,0.2);
        position: relative;
    }
    
    .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .preview-company {
        font-weight: bold;
        font-size: 1rem;
    }
    
    .preview-invoice-no {
        font-size: 0.8rem;
        color: #858796;
    }
    
    .preview-title {
        text-align: center;
        font-weight: bold;
        margin: 10px 0;
        font-size: 1.1rem;
    }
    
    .client-info {
        background: #f8f9fc;
        padding: 10px;
        border-radius: 6px;
        margin: 15px 0;
        font-size: 0.9rem;
    }
    
    .client-info.premium {
        background: linear-gradient(135deg, #fff9e6 0%, #fff2cc 100%);
        border: 1px solid #f6c23e;
    }
    
    .items-table {
        margin: 15px 0;
    }
    
    .item-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px dashed #e3e6f0;
        font-size: 0.85rem;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        padding-top: 10px;
        border-top: 2px solid #4e73df;
        font-weight: bold;
    }
    
    .total-row.premium {
        border-top-color: #f6c23e;
    }
    
    .footer-note {
        margin-top: 15px;
        font-size: 0.7rem;
        color: #858796;
        text-align: center;
        font-style: italic;
    }
    
    .check-icon {
        position: absolute;
        top: 10px;
        left: 10px;
        color: #0d6efd;
        font-size: 24px;
        opacity: 0;
        transition: opacity 0.3s ease;
        background: white;
        border-radius: 50%;
        z-index: 20;
    }
    
    .invoice-template-card.selected .check-icon {
        opacity: 1;
    }
    
    .template-badge-main {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        background: #fff;
        color: #333;
        padding: 4px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 15;
        border: 1px solid #e3e6f0;
    }
    
    .template-1 .template-badge-main { border-top: 3px solid #4e73df; }
    .template-2 .template-badge-main { border-top: 3px solid #1cc88a; }
    .template-3 .template-badge-main { border-top: 3px solid #f6c23e; }

    /* Modal Styles */
    .preview-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.8);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    
    .preview-modal.active {
        display: flex;
    }
    
    .preview-modal-content {
        background: white;
        padding: 30px;
        border-radius: 12px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
    }
    
    .preview-modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #f8f9fa;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 20px;
        color: #666;
        transition: all 0.3s ease;
    }
    
    .preview-modal-close:hover {
        background: #e9ecef;
        color: #dc3545;
    }
    
    .preview-modal .invoice-preview {
        min-height: auto;
        padding: 0;
    }
</style>

<!-- Preview Modal -->
<div class="preview-modal" id="previewModal">
    <div class="preview-modal-content">
        <button class="preview-modal-close" onclick="closePreview()">
            <i class="ti ti-x"></i>
        </button>
        <div id="previewContent"></div>
    </div>
</div>

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0 pb-0 me-lg-4">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Agency Settings</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.dashboard') }}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.agencies.index') }}">Agencies</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Invoice Template</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <!-- Settings Prefix -->
        <div class="row">
            <div class="col-lg-3">
                <!-- inner sidebar -->
                @include('Backoffice.profile.partials._agency_settings_sidebar', [
                    'agency' => $agency,
                    'active' => 'invoice-template',
                ])
                <!-- /inner sidebar -->
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold">Invoice Template Selection</h5>
                        <p class="text-muted mb-0">Choose a template for your agency's invoices</p>
                    </div>
                    <form action="{{ route('backoffice.agencies.settings.update', $agency) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Hidden input for selected template -->
                        <input type="hidden" name="app[invoice_template]" id="selected_template" value="{{ $agency->settings['invoice_template'] ?? 'template1' }}">
                        
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <div class="row g-4">
                                <!-- Template 1 - Simple Blue (FREE) -->
                                <div class="col-md-4">
                                    <div class="invoice-template-card template-1 {{ ($agency->settings['invoice_template'] ?? 'template1') === 'template1' ? 'selected' : '' }}" data-template="template1">
                                        <span class="free-badge">FREE</span>
                                        <div class="card-header">Simple Blue</div>
                                        <div class="invoice-preview">
                                            <i class="ti ti-check check-icon"></i>
                                            <div class="template-badge-main">General Invoice</div>
                                            
                                            <div class="preview-header">
                                                <span class="preview-company">{{ $agency->name }}</span>
                                                <span class="preview-invoice-no">INV-001</span>
                                            </div>
                                            
                                            <div class="client-info">
                                                <strong>Client:</strong> Jean Dupont<br>
                                                <small>Vignette client 1</small>
                                            </div>
                                            
                                            <div class="items-table">
                                                <div class="item-row">
                                                    <span>Renault Clio (7 jours)</span>
                                                    <span>350,00 €</span>
                                                </div>
                                                <div class="item-row">
                                                    <span>Assurance complète</span>
                                                    <span>120,00 €</span>
                                                </div>
                                                <div class="item-row">
                                                    <span>GPS</span>
                                                    <span>35,00 €</span>
                                                </div>
                                                <div class="item-row">
                                                    <span>Siège bébé</span>
                                                    <span>25,00 €</span>
                                                </div>
                                            </div>
                                            
                                            <div class="total-row">
                                                <span>TOTAL</span>
                                                <span>530,00 €</span>
                                            </div>
                                            
                                            <div class="footer-note">
                                                Paiement dû sous 15 jours
                                            </div>
                                        </div>
                                        <button class="preview-btn" onclick="previewTemplate('template1', event)">
                                            <i class="ti ti-eye me-1"></i>Aperçu
                                        </button>
                                    </div>
                                </div>

                                <!-- Template 2 - Clean Green (FREE) -->
                                <div class="col-md-4">
                                    <div class="invoice-template-card template-2 {{ ($agency->settings['invoice_template'] ?? 'template1') === 'template2' ? 'selected' : '' }}" data-template="template2">
                                        <span class="free-badge">FREE</span>
                                        <div class="card-header">Clean Green</div>
                                        <div class="invoice-preview">
                                            <i class="ti ti-check check-icon"></i>
                                            <div class="template-badge-main">General Invoice</div>
                                            
                                            <div class="preview-header">
                                                <span class="preview-company">{{ $agency->name }}</span>
                                                <span class="preview-invoice-no">INV-001</span>
                                            </div>
                                            
                                            <div class="client-info">
                                                <strong>Client:</strong> Jean Dupont<br>
                                                <small>Vignette client 1</small>
                                            </div>
                                            
                                            <div class="items-table">
                                                <div class="item-row">
                                                    <span>Peugeot 308 (5 jours)</span>
                                                    <span>275,00 €</span>
                                                </div>
                                                <div class="item-row">
                                                    <span>Assurance de base</span>
                                                    <span>75,00 €</span>
                                                </div>
                                                <div class="item-row">
                                                    <span>Kilométrage illimité</span>
                                                    <span>50,00 €</span>
                                                </div>
                                            </div>
                                            
                                            <div class="total-row">
                                                <span>TOTAL</span>
                                                <span>400,00 €</span>
                                            </div>
                                            
                                            <div class="footer-note">
                                                Merci de votre confiance
                                            </div>
                                        </div>
                                        <button class="preview-btn" onclick="previewTemplate('template2', event)">
                                            <i class="ti ti-eye me-1"></i>Aperçu
                                        </button>
                                    </div>
                                </div>

                                <!-- Template 3 - Premium Gold -->
                                <div class="col-md-4">
                                    <div class="invoice-template-card template-3 {{ ($agency->settings['invoice_template'] ?? 'template1') === 'template3' ? 'selected' : '' }}" data-template="template3">
                                        <span class="premium-badge">PREMIUM</span>
                                        <div class="card-header">Premium Gold</div>
                                        <div class="invoice-preview">
                                            <i class="ti ti-check check-icon"></i>
                                            <div class="template-badge-main">General Invoice</div>
                                            
                                            <div class="preview-header">
                                                <span class="preview-company">{{ $agency->name }}</span>
                                                <span class="preview-invoice-no">INV-001</span>
                                            </div>
                                            
                                            <div class="client-info premium">
                                                <strong>Client:</strong> Jean Dupont<br>
                                                <small>Vignette client 1 • VIP</small>
                                            </div>
                                            
                                            <div class="items-table">
                                                <div class="item-row">
                                                    <span>Mercedes Classe C (7 jours)</span>
                                                    <span>840,00 €</span>
                                                </div>
                                                <div class="item-row">
                                                    <span>Assurance premium</span>
                                                    <span>210,00 €</span>
                                                </div>
                                                <div class="item-row">
                                                    <span>GPS + Conciergerie</span>
                                                    <span>95,00 €</span>
                                                </div>
                                                <div class="item-row">
                                                    <span>Livraison aéroport</span>
                                                    <span>45,00 €</span>
                                                </div>
                                                <div class="item-row">
                                                    <span>Réduction fidélité</span>
                                                    <span>-50,00 €</span>
                                                </div>
                                            </div>
                                            
                                            <div class="total-row premium">
                                                <span>TOTAL</span>
                                                <span>1.140,00 €</span>
                                            </div>
                                            
                                            <div class="footer-note">
                                                Paiement en ligne • Facture détaillée envoyée par email
                                            </div>
                                            
                                            <!-- Premium Overlay -->
                                            <div class="premium-overlay">
                                                <div class="premium-message">
                                                    <i class="ti ti-crown"></i>
                                                    <h6>Template Premium</h6>
                                                    <p>Ce template est disponible uniquement pour les abonnements Premium</p>
                                                    <button class="btn-contact" onclick="contactOwner(event)">
                                                        <i class="ti ti-mail me-1"></i>Contacter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('app.invoice_template')
                                <small class="text-danger d-block mt-3">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-end">
                                <a href="{{ route('backoffice.agencies.index') }}" class="btn btn-light me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Settings Prefix -->
    </div>
    
    <!-- Footer-->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Privacy Policy</a>
            <a href="javascript:void(0);" class="ms-4">Terms of Use</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Made with <span class="text-danger">❤</span> by <a href="javascript:void(0);"
                class="text-secondary">Dreams</a></p>
    </div>
    <!-- /Footer-->
</div>
<!-- /Page Wrapper -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const templateCards = document.querySelectorAll('.invoice-template-card');
    const hiddenInput = document.getElementById('selected_template');
    
    templateCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't select if clicking on preview button or contact button
            if (e.target.closest('.preview-btn') || e.target.closest('.btn-contact')) {
                return;
            }
            templateCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const template = this.dataset.template;
            hiddenInput.value = template;
        });
    });
});

function previewTemplate(template, event) {
    event.stopPropagation();
    
    let previewHtml = '';
    
    if (template === 'template1') {
        previewHtml = `
            <div class="invoice-preview template-1" style="min-height: auto; padding: 20px;">
                <div class="template-badge-main">General Invoice</div>
                <div class="preview-header">
                    <span class="preview-company">{{ $agency->name }}</span>
                    <span class="preview-invoice-no">INV-001</span>
                </div>
                <div class="client-info">
                    <strong>Client:</strong> Jean Dupont<br>
                    <small>Vignette client 1</small>
                </div>
                <div class="items-table">
                    <div class="item-row"><span>Renault Clio (7 jours)</span><span>350,00 €</span></div>
                    <div class="item-row"><span>Assurance complète</span><span>120,00 €</span></div>
                    <div class="item-row"><span>GPS</span><span>35,00 €</span></div>
                    <div class="item-row"><span>Siège bébé</span><span>25,00 €</span></div>
                </div>
                <div class="total-row"><span>TOTAL</span><span>530,00 €</span></div>
                <div class="footer-note">Paiement dû sous 15 jours</div>
            </div>
        `;
    } else if (template === 'template2') {
        previewHtml = `
            <div class="invoice-preview template-2" style="min-height: auto; padding: 20px;">
                <div class="template-badge-main">General Invoice</div>
                <div class="preview-header">
                    <span class="preview-company">{{ $agency->name }}</span>
                    <span class="preview-invoice-no">INV-001</span>
                </div>
                <div class="client-info">
                    <strong>Client:</strong> Jean Dupont<br>
                    <small>Vignette client 1</small>
                </div>
                <div class="items-table">
                    <div class="item-row"><span>Peugeot 308 (5 jours)</span><span>275,00 €</span></div>
                    <div class="item-row"><span>Assurance de base</span><span>75,00 €</span></div>
                    <div class="item-row"><span>Kilométrage illimité</span><span>50,00 €</span></div>
                </div>
                <div class="total-row"><span>TOTAL</span><span>400,00 €</span></div>
                <div class="footer-note">Merci de votre confiance</div>
            </div>
        `;
    }
    
    document.getElementById('previewContent').innerHTML = previewHtml;
    document.getElementById('previewModal').classList.add('active');
}

function closePreview() {
    document.getElementById('previewModal').classList.remove('active');
}

function contactOwner(event) {
    event.stopPropagation();
    
    // You can replace this with actual contact logic
    const email = '{{ $agency->email ?? "contact@dreamsrent.com" }}';
    const subject = encodeURIComponent('Demande d\'accès au template Premium');
    const body = encodeURIComponent('Bonjour,\n\nJe souhaiterais obtenir plus d\'informations sur le template Premium pour mes factures.\n\nCordialement');
    
    window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('previewModal');
    if (e.target === modal) {
        closePreview();
    }
});
</script>
@endsection