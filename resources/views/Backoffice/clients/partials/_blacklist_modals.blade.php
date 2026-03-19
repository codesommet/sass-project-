<!-- Blacklist Client Modal -->
<div class="modal fade" id="blacklistClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Blacklister le client
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="blacklistForm">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="ti ti-alert-triangle fs-48 text-warning mb-3"></i>
                        <h5 class="mb-2">Confirmation de blacklistage</h5>
                        <p class="mb-3" id="blacklistClientInfo"></p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Attention !</strong> Cette action aura les conséquences suivantes :
                        <ul class="mt-2 mb-0">
                            <li>Le client ne pourra plus effectuer de réservations</li>
                            <li>Un avertissement apparaîtra dans toutes les agences</li>
                            <li>Cette action est réversible</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Raison du blacklistage <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required 
                                  placeholder="Expliquez la raison du blacklistage..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Notes internes (optionnel)</label>
                        <textarea name="internal_notes" class="form-control" rows="2" 
                                  placeholder="Notes internes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ti ti-alert-triangle me-1"></i>
                        Confirmer le blacklistage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unblacklist Client Modal -->
<div class="modal fade" id="unblacklistClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">
                    <i class="ti ti-check me-2"></i>
                    Retirer de la blacklist
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="unblacklistForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="ti ti-check-circle fs-48 text-success mb-3"></i>
                        <h5 class="mb-2">Confirmation de retrait</h5>
                        <p class="mb-3" id="unblacklistClientInfo"></p>
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="ti ti-info-circle me-2"></i>
                        Le client pourra à nouveau effectuer des réservations normalement.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Raison du retrait (optionnel)</label>
                        <textarea name="unblacklist_reason" class="form-control" rows="3" 
                                  placeholder="Expliquez pourquoi ce client est retiré de la blacklist..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-check me-1"></i>
                        Confirmer le retrait
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Blacklist Details Modal -->
<div class="modal fade" id="viewBlacklistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Détails du blacklistage
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewBlacklistContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
// Blacklist Modal Functions
function openBlacklistModal(id, name, phone, cin) {
    const modal = new bootstrap.Modal(document.getElementById('blacklistClientModal'));
    document.getElementById('blacklistClientInfo').innerHTML = `
        <strong>${name}</strong><br>
        <small class="text-muted">Tél: ${phone || 'N/A'} | CIN: ${cin || 'N/A'}</small>
    `;
    document.getElementById('blacklistForm').action = `/backoffice/clients/${id}/blacklist`;
    modal.show();
}

function openUnblacklistModal(id, name, phone, cin, date, reason) {
    const modal = new bootstrap.Modal(document.getElementById('unblacklistClientModal'));
    document.getElementById('unblacklistClientInfo').innerHTML = `
        <strong>${name}</strong><br>
        <small class="text-muted">Tél: ${phone || 'N/A'} | CIN: ${cin || 'N/A'}</small>
        <hr>
        <small class="text-danger">Blacklisté le : ${date || 'N/A'}</small><br>
        <small class="text-danger">Motif : ${reason || 'N/A'}</small>
    `;
    document.getElementById('unblacklistForm').action = `/backoffice/clients/${id}/unblacklist`;
    modal.show();
}

function openViewBlacklistModal(name, phone, cin, date, by, agency, reason, notes) {
    const modal = new bootstrap.Modal(document.getElementById('viewBlacklistModal'));
    const content = document.getElementById('viewBlacklistContent');
    content.innerHTML = `
        <div class="text-center mb-3">
            <i class="ti ti-alert-triangle fs-48 text-danger mb-3"></i>
            <h5 class="text-danger">Client sur liste noire</h5>
        </div>
        <div class="alert alert-danger">
            <p><strong>Nom :</strong> ${name}</p>
            <p><strong>Téléphone :</strong> ${phone || 'N/A'}</p>
            <p><strong>CIN :</strong> ${cin || 'N/A'}</p>
            <p><strong>Blacklisté le :</strong> ${date}</p>
            <p><strong>Par :</strong> ${by}</p>
            <p><strong>Agence :</strong> ${agency}</p>
            <p><strong>Motif :</strong> ${reason}</p>
            ${notes ? `<p><strong>Notes :</strong> ${notes}</p>` : ''}
        </div>
    `;
    modal.show();
}
</script>