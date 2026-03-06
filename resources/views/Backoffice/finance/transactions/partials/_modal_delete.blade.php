<div class="modal fade" id="delete_transaction" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                    <i class="ti ti-trash-x fs-26"></i>
                </span>
                <h4 class="mb-1">Supprimer la transaction</h4>
                <p class="mb-3">Êtes-vous sûr de vouloir supprimer la transaction <strong id="deleteTransactionRef"></strong> ?</p>
                
                <form method="POST" action="" id="deleteTransactionForm">
                    @csrf
                    @method('DELETE')
                    
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('delete_transaction');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            if (button) {
                const action = button.getAttribute('data-delete-action');
                const details = button.getAttribute('data-delete-details') || 'cette transaction';
                
                const form = document.getElementById('deleteTransactionForm');
                const refElement = document.getElementById('deleteTransactionRef');
                
                if (action && form) {
                    form.action = action;
                }
                
                if (refElement && details) {
                    refElement.innerText = details;
                }
            }
        });
    }
});
</script>