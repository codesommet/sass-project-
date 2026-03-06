@props(['client'])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- Voir détails - contrôlé par permission VIEW --}}
        @can('clients.general.view')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.clients.show', $client) }}">
                <i class="ti ti-eye me-2"></i>
                Voir détails
            </a>
        </li>
        @endcan

        {{-- Modifier - contrôlé par permission EDIT --}}
        @can('clients.general.edit')
        <li>
            <a class="dropdown-item rounded-1" href="{{ route('backoffice.clients.edit', $client) }}">
                <i class="ti ti-edit me-2"></i>
                Modifier
            </a>
        </li>
        @endcan

        {{-- BLACKLIST OPTIONS --}}
        @can('clients.general.edit')
            @if(!$client->isBlacklisted())
            <li>
                <a class="dropdown-item rounded-1 text-warning" 
                   href="#"
                   onclick="openBlacklistModal({{ $client->id }}, '{{ $client->full_name }}', '{{ $client->phone }}', '{{ $client->cin_number }}')">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Blacklister
                </a>
            </li>
            @else
            <li>
                <a class="dropdown-item rounded-1 text-success" 
                   href="#"
                   onclick="openUnblacklistModal({{ $client->id }}, '{{ $client->full_name }}', '{{ $client->phone }}', '{{ $client->cin_number }}', '{{ $client->currentBlacklistEntry?->created_at->format('d/m/Y H:i') }}', '{{ $client->currentBlacklistEntry?->reason }}')">
                    <i class="ti ti-check me-2"></i>
                    Retirer blacklist
                </a>
            </li>
            <li>
                <a class="dropdown-item rounded-1 text-info" 
                   href="#"
                   onclick="openViewBlacklistModal({{ $client->id }}, '{{ $client->full_name }}', '{{ $client->phone }}', '{{ $client->cin_number }}', '{{ $client->currentBlacklistEntry?->created_at->format('d/m/Y H:i') }}', '{{ $client->currentBlacklistEntry?->blacklistedBy?->name }}', '{{ $client->currentBlacklistEntry?->agency?->name }}', '{{ $client->currentBlacklistEntry?->reason }}', '{{ $client->currentBlacklistEntry?->internal_notes }}')">
                    <i class="ti ti-info-circle me-2"></i>
                    Voir détails blacklist
                </a>
            </li>
            @endif
        @endcan

        {{-- Supprimer - contrôlé par permission DELETE --}}
        @can('clients.general.delete')
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item rounded-1 text-danger" 
               href="#"
               data-bs-toggle="modal" 
               data-bs-target="#delete_client"
               data-delete-action="{{ route('backoffice.clients.destroy', $client->id) }}"
               data-client-name="{{ $client->full_name }}">
                <i class="ti ti-trash me-2"></i>
                Supprimer
            </a>
        </li>
        @endcan
    </ul>
</div>