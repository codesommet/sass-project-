{{--
    Actions Dropdown Component
    Usage:
    <x-backoffice.actions-dropdown>
        <x-slot name="items">
            <li><a class="dropdown-item rounded-1" href="..."><i class="ti ti-eye me-2"></i>Voir</a></li>
        </x-slot>
    </x-backoffice.actions-dropdown>
--}}
@props([
    'showUrl' => null,
    'showPermission' => null,
    'editUrl' => null,
    'editPermission' => null,
    'deleteAction' => null,
    'deletePermission' => null,
    'deleteTarget' => '#delete_modal',
    'deleteName' => '',
])

<div class="dropdown">
    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="ti ti-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-2">
        {{-- View --}}
        @if($showUrl)
            @if($showPermission)
                @can($showPermission)
                <li>
                    <a class="dropdown-item rounded-1" href="{{ $showUrl }}">
                        <i class="ti ti-eye me-2"></i>Voir détails
                    </a>
                </li>
                @endcan
            @else
                <li>
                    <a class="dropdown-item rounded-1" href="{{ $showUrl }}">
                        <i class="ti ti-eye me-2"></i>Voir détails
                    </a>
                </li>
            @endif
        @endif

        {{-- Edit --}}
        @if($editUrl)
            @if($editPermission)
                @can($editPermission)
                <li>
                    <a class="dropdown-item rounded-1" href="{{ $editUrl }}">
                        <i class="ti ti-edit me-2"></i>Modifier
                    </a>
                </li>
                @endcan
            @else
                <li>
                    <a class="dropdown-item rounded-1" href="{{ $editUrl }}">
                        <i class="ti ti-edit me-2"></i>Modifier
                    </a>
                </li>
            @endif
        @endif

        {{-- Custom items slot --}}
        @if(isset($items))
            {{ $items }}
        @endif

        {{-- Delete --}}
        @if($deleteAction)
            @if($deletePermission)
                @can($deletePermission)
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item rounded-1 text-danger" href="#"
                       data-bs-toggle="modal"
                       data-bs-target="{{ $deleteTarget }}"
                       data-delete-action="{{ $deleteAction }}"
                       data-delete-name="{{ $deleteName }}">
                        <i class="ti ti-trash me-2"></i>Supprimer
                    </a>
                </li>
                @endcan
            @else
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item rounded-1 text-danger" href="#"
                       data-bs-toggle="modal"
                       data-bs-target="{{ $deleteTarget }}"
                       data-delete-action="{{ $deleteAction }}"
                       data-delete-name="{{ $deleteName }}">
                        <i class="ti ti-trash me-2"></i>Supprimer
                    </a>
                </li>
            @endif
        @endif
    </ul>
</div>
