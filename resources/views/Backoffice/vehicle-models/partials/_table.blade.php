<head>
    <style>
        /* FIX: allow dropdowns inside tables */
        .table-responsive,
        .custom-datatable-filter {
            overflow: visible !important;
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
            @can('vehicle-models.general.delete')
            <th class="no-sort" width="50">
                <div class="form-check form-check-md">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            @endcan
            <th>MODÈLE</th>
            <th>MARQUE</th>
            <th>STATUT</th>
            {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
            @canany(['vehicle-models.general.view', 'vehicle-models.general.edit', 'vehicle-models.general.delete'])
            <th class="text-end no-sort">ACTIONS</th>
            @endcanany
        </tr>
    </thead>

    <tbody>
        @forelse($models as $model)
        @php
        $brandName = $model->brand->name ?? '—';
        $brandLogo = $model->brand->logo_url ?? asset('admin_assets/img/brands/toyota.svg');
        
        $canView = auth()->user()->can('vehicle-models.general.view');
        @endphp

        <tr>
            {{-- Case à cocher - visible seulement si permission DELETE --}}
            @can('vehicle-models.general.delete')
            <td>
                <div class="form-check form-check-md">
                    <input class="form-check-input model-checkbox" type="checkbox" value="{{ $model->id }}">
                </div>
            </td>
            @endcan

            {{-- Model --}}
            <td>
                <div class="d-flex align-items-center">
                    <div>
                        <h6 class="fw-medium mb-0">
                            {{-- Lien vers show - contrôlé par permission VIEW --}}
                            @if($canView)
                                <a href="{{ route('backoffice.vehicle-models.show', $model) }}">
                                    {{ $model->name }}
                                </a>
                            @else
                                <span>{{ $model->name }}</span>
                            @endif
                        </h6>
                    </div>
                </div>
            </td>

            {{-- Brand --}}
            <td>
                <div class="d-flex align-items-center">
                    <a href="javascript:void(0);" class="avatar avatar-lg border me-2">
                        <img src="{{ $brandLogo }}" class="img-fluid" alt="brand" style="object-fit: contain; width: 40px; height: 40px;">
                    </a>
                    <div>
                        <h6 class="fw-medium mb-0">
                            {{-- Lien vers marque - contrôlé par permission VIEW sur marques --}}
                            @can('vehicle-brands.general.view')
                                @if($model->brand_id)
                                    <a href="{{ route('backoffice.vehicle-brands.show', $model->brand_id) }}">
                                        {{ $brandName }}
                                    </a>
                                @else
                                    <span>{{ $brandName }}</span>
                                @endif
                            @else
                                <span>{{ $brandName }}</span>
                            @endcan
                        </h6>
                    </div>
                </div>
            </td>

            {{-- Status --}}
            <td>
                @if($model->is_active ?? true)
                    <span class="badge badge-success-transparent d-inline-flex align-items-center badge-sm">
                        <i class="ti ti-point-filled me-1"></i>Actif
                    </span>
                @else
                    <span class="badge badge-danger-transparent d-inline-flex align-items-center badge-sm">
                        <i class="ti ti-point-filled me-1"></i>Inactif
                    </span>
                @endif
            </td>

            {{-- Actions - visible seulement si au moins une permission d'action --}}
            @canany(['vehicle-models.general.view', 'vehicle-models.general.edit', 'vehicle-models.general.delete'])
            <td class="text-end position-static">
                @include('backoffice.vehicle-models.partials._actions', ['model' => $model])
            </td>
            @endcanany
        </tr>
        @empty
        @php
            $emptyColspan = 3
                + (auth()->user()->can('vehicle-models.general.delete') ? 1 : 0)
                + (auth()->user()->canAny(['vehicle-models.general.view', 'vehicle-models.general.edit', 'vehicle-models.general.delete']) ? 1 : 0);
        @endphp
        <tr>
            <td colspan="{{ $emptyColspan }}" class="text-center py-4">
                <div class="text-muted">
                    <i class="ti ti-car-off fs-4 mb-2"></i>
                    <p class="mb-0">Aucun modèle trouvé.</p>
                    @can('vehicle-models.general.create')
                        <a href="javascript:void(0);" class="btn btn-sm btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#add_model">
                            <i class="ti ti-plus me-1"></i>Ajouter un modèle
                        </a>
                    @endcan
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('vehicle-models.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.model-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan