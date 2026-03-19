<head>
    <style>
        .table-responsive,
        .dataTables_wrapper {
            overflow: visible !important;
        }
        .brand-avatar-img {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            background-color: #fff;
            object-fit: contain;
            padding: 6px;
            display: inline-block;
            vertical-align: middle;
        }
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #6c757d;
            background: transparent;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-icon:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #0d6efd;
        }
        .form-check {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<table class="table datatable align-middle">
    <thead class="thead-light">
        <tr>
            {{-- Case à cocher - visible seulement si permission DELETE --}}
            @can('vehicle-brands.general.delete')
            <th class="no-sort" width="50">
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            @endcan
            <th>NAME</th>
            <th>TOTAL CARS</th>
            <th>STATUS</th>
            {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
            @canany(['vehicle-brands.general.view', 'vehicle-brands.general.edit', 'vehicle-brands.general.delete'])
            <th width="80"></th>
            @endcanany
        </tr>
    </thead>

    <tbody>
        @forelse($brands as $brand)
            @php
                $logo = $brand->logo_url ?: asset('admin_assets/img/brands/toyota.svg');
                $carsCount = $brand->vehicles()->count();
                
                $canView = auth()->user()->can('vehicle-brands.general.view');
            @endphp

            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('vehicle-brands.general.delete')
                <td>
                    <div class="form-check form-check-md">
                        <input class="form-check-input brand-checkbox" type="checkbox" value="{{ $brand->id }}">
                    </div>
                </td>
                @endcan

                {{-- Brand with Logo --}}
                <td>
                    <div class="d-flex align-items-center file-name-icon">
                        <a href="javascript:void(0);" class="avatar avatar-lg border">
                            <img src="{{ $logo }}" class="img-fluid brand-avatar-img" alt="{{ $brand->name }}">
                        </a>
                        <div class="ms-2">
                            <h6 class="fw-medium mb-0">
                                {{-- Lien vers show - contrôlé par permission VIEW --}}
                                @if($canView)
                                    <a href="{{ route('backoffice.vehicle-brands.show', $brand) }}">
                                        {{ $brand->name }}
                                    </a>
                                @else
                                    <span>{{ $brand->name }}</span>
                                @endif
                            </h6>
                        </div>
                    </div>
                </td>

                {{-- Total Cars --}}
                <td>{{ $carsCount }}</td>

                {{-- Status (static UI) --}}
                <td>
                    <span class="badge badge-success-transparent d-inline-flex align-items-center badge-sm">
                        <i class="ti ti-point-filled me-1"></i>Active
                    </span>
                </td>

                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['vehicle-brands.general.view', 'vehicle-brands.general.edit', 'vehicle-brands.general.delete'])
                <td>
                    @include('backoffice.vehicle-brands.partials._actions', [
                        'brand' => $brand,
                        'logo' => $logo,
                    ])
                </td>
                @endcanany
            </tr>
        @empty
            @php
                $emptyColspan = 3
                    + (auth()->user()->can('vehicle-brands.general.delete') ? 1 : 0)
                    + (auth()->user()->canAny(['vehicle-brands.general.view', 'vehicle-brands.general.edit', 'vehicle-brands.general.delete']) ? 1 : 0);
            @endphp
            <tr>
                <td colspan="{{ $emptyColspan }}" class="text-center py-4">
                    <div class="text-muted">
                        <i class="ti ti-car-off fs-4 mb-2"></i>
                        <p class="mb-0">Aucune marque trouvée.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('vehicle-brands.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.brand-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan