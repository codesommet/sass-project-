{{--
    Empty State Component
    Usage:
    <x-backoffice.empty-state
        icon="ti-users-off"
        title="Aucun client trouvé"
        message="Commencez par ajouter votre premier client"
        :create-url="route('backoffice.clients.create')"
        create-label="Ajouter un client"
        create-permission="clients.general.create"
    />
--}}
@props([
    'icon' => 'ti-file-off',
    'title' => 'Aucun résultat',
    'message' => '',
    'createUrl' => null,
    'createLabel' => 'Créer',
    'createPermission' => null,
    'colspan' => null,
])

@if($colspan)
<tr>
    <td colspan="{{ $colspan }}" class="text-center py-5">
@endif

<div class="text-center {{ $colspan ? '' : 'py-5' }}">
    <div class="mb-3">
        <i class="ti {{ $icon }} fs-48 text-muted"></i>
    </div>
    <h5 class="text-dark mb-2">{{ $title }}</h5>
    @if($message)
        <p class="text-muted mb-3">{{ $message }}</p>
    @endif
    @if($createUrl)
        @if($createPermission)
            @can($createPermission)
            <a href="{{ $createUrl }}" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>{{ $createLabel }}
            </a>
            @endcan
        @else
            <a href="{{ $createUrl }}" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>{{ $createLabel }}
            </a>
        @endif
    @endif
</div>

@if($colspan)
    </td>
</tr>
@endif
