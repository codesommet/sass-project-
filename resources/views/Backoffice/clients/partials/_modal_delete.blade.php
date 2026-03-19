{{-- Delete modal is now included via <x-backoffice.delete-modal /> in index.blade.php --}}
{{-- This file kept for backwards compatibility with other includes --}}
<x-backoffice.delete-modal
    id="delete_client"
    title="Supprimer le client"
    form-id="deleteClientForm"
    name-id="deleteClientName"
    warning="Le client et toutes ses données seront définitivement supprimés."
/>
