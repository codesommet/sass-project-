{{--
    Reusable Delete Confirmation Modal
    Usage:
    <x-backoffice.delete-modal
        id="delete_client"
        title="Supprimer le client"
        form-id="deleteClientForm"
        name-id="deleteItemName"
    />
--}}
@props([
    'id' => 'delete_modal',
    'title' => 'Confirmer la suppression',
    'formId' => 'deleteForm',
    'nameId' => 'deleteItemName',
    'warning' => null,
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-4">
            <div class="mb-3">
                <span class="avatar avatar-lg bg-danger-transparent rounded-circle text-danger mx-auto">
                    <i class="ti ti-trash-x fs-26"></i>
                </span>
            </div>
            <h5 class="mb-2">{{ $title }}</h5>
            <p class="text-muted mb-1">
                Êtes-vous sûr de vouloir supprimer
                <strong id="{{ $nameId }}" class="text-dark">—</strong> ?
            </p>
            @if($warning)
                <p class="text-danger small mb-3">
                    <i class="ti ti-alert-triangle me-1"></i>{{ $warning }}
                </p>
            @else
                <p class="text-danger small mb-3">
                    <i class="ti ti-alert-triangle me-1"></i>Cette action est irréversible.
                </p>
            @endif
            <form id="{{ $formId }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ti ti-trash me-1"></i>Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
