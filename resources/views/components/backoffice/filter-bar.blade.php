{{--
    Filter Bar Component - Standardized search + sort + filters + create button
    Usage:
    <x-backoffice.filter-bar
        :route="route('backoffice.clients.index')"
        search-placeholder="Rechercher un client..."
        :create-url="route('backoffice.clients.create')"
        create-label="Ajouter un client"
        create-permission="clients.general.create"
        :sort-options="['latest' => 'Plus récents', 'az' => 'A → Z', 'za' => 'Z → A']"
    >
        <x-slot name="filters">
            <!-- Custom filter fields here -->
        </x-slot>
    </x-backoffice.filter-bar>
--}}
@props([
    'route',
    'searchPlaceholder' => 'Rechercher...',
    'createUrl' => null,
    'createLabel' => 'Ajouter',
    'createPermission' => null,
    'sortOptions' => ['latest' => 'Plus récents', 'az' => 'A → Z', 'za' => 'Z → A'],
    'currentSort' => null,
])

@php
    $currentSort = $currentSort ?? request('sort', 'latest');
    $currentSortLabel = $sortOptions[$currentSort] ?? 'Plus récents';
@endphp

<form method="GET" id="filterForm" action="{{ $route }}">
    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">

        {{-- Left: Sort + Filter toggle --}}
        <div class="d-flex align-items-center flex-wrap row-gap-3">
            {{-- Sort Dropdown --}}
            <div class="dropdown me-2">
                <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                   data-bs-toggle="dropdown">
                    <i class="ti ti-arrows-sort me-1"></i>
                    {{ $currentSortLabel }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-2">
                    @foreach($sortOptions as $key => $label)
                        <li>
                            <a class="dropdown-item {{ $currentSort === $key ? 'active' : '' }}"
                               href="{{ $route . '?' . http_build_query(array_merge(request()->except(['sort', 'page']), ['sort' => $key])) }}">
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Filter Toggle --}}
            @if(isset($filters))
            <a href="#filtercollapse"
               class="btn btn-white d-inline-flex align-items-center"
               data-bs-toggle="collapse">
                <i class="ti ti-filter me-1"></i> Filtres
                @if(request()->hasAny(['agency_id', 'status', 'type', 'model_id', 'date_from', 'date_to']))
                    <span class="badge bg-primary ms-1">
                        {{ count(array_filter(request()->only(['agency_id', 'status', 'type', 'model_id', 'date_from', 'date_to']))) }}
                    </span>
                @endif
            </a>
            @endif
        </div>

        {{-- Right: Search + Create --}}
        <div class="d-flex align-items-center flex-wrap row-gap-3">
            {{-- Search --}}
            <div class="me-2">
                <div class="position-relative">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-2">
                        <i class="ti ti-search text-muted"></i>
                    </span>
                    <input type="text"
                           name="search"
                           id="searchInput"
                           value="{{ request('search') }}"
                           class="form-control ps-4"
                           style="min-width: 220px;"
                           placeholder="{{ $searchPlaceholder }}"
                           autocomplete="off">
                    @if(request('search'))
                        <button type="button"
                                class="btn btn-link position-absolute top-50 end-0 translate-middle-y p-0 me-2 text-muted"
                                onclick="document.getElementById('searchInput').value=''; document.getElementById('filterForm').submit();">
                            <i class="ti ti-x"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Create Button --}}
            @if($createUrl)
                @if($createPermission)
                    @can($createPermission)
                    <a href="{{ $createUrl }}" class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i>{{ $createLabel }}
                    </a>
                    @endcan
                @else
                    <a href="{{ $createUrl }}" class="btn btn-primary d-flex align-items-center">
                        <i class="ti ti-plus me-1"></i>{{ $createLabel }}
                    </a>
                @endif
            @endif
        </div>
    </div>
</form>

{{-- Collapsible Filters --}}
@if(isset($filters))
<div class="collapse {{ request()->hasAny(['agency_id', 'status', 'type', 'model_id', 'date_from', 'date_to']) ? 'show' : '' }}"
     id="filtercollapse">
    <div class="card card-body bg-light border-0 mb-3">
        <div class="row align-items-end g-3">
            {{ $filters }}
            <div class="col-auto">
                <a href="{{ $route }}" class="btn btn-outline-danger btn-sm">
                    <i class="ti ti-x me-1"></i>Tout effacer
                </a>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Debounced Search Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const input = document.getElementById('searchInput');
    if (form && input) {
        let timer;
        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => form.submit(), 500);
        });
    }
});
</script>
