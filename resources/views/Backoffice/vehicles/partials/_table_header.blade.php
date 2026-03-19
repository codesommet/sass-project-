<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
    <div class="d-flex align-items-center flex-wrap row-gap-3">
        <div class="dropdown me-2">
            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                data-bs-toggle="dropdown">
                <i class="ti ti-filter me-1"></i>
                Trier par : {{ request('sort', 'latest') === 'latest' ? 'Plus récents' : ucfirst(request('sort')) }}
            </a>

            <ul class="dropdown-menu dropdown-menu-end p-2">
                <li>
                    <a href="{{ route('backoffice.vehicles.index', array_merge(request()->query(), ['sort' => 'latest'])) }}"
                        class="dropdown-item rounded-1">
                        Plus récents
                    </a>
                </li>
                <li>
                    <a href="{{ route('backoffice.vehicles.index', array_merge(request()->query(), ['sort' => 'asc'])) }}"
                        class="dropdown-item rounded-1">
                        Croissant
                    </a>
                </li>
                <li>
                    <a href="{{ route('backoffice.vehicles.index', array_merge(request()->query(), ['sort' => 'desc'])) }}"
                        class="dropdown-item rounded-1">
                        Décroissant
                    </a>
                </li>
                <li>
                    <a href="{{ route('backoffice.vehicles.index', array_merge(request()->query(), ['sort' => 'last_month'])) }}"
                        class="dropdown-item rounded-1">
                        Mois dernier
                    </a>
                </li>
                <li>
                    <a href="{{ route('backoffice.vehicles.index', array_merge(request()->query(), ['sort' => 'last_7_days'])) }}"
                        class="dropdown-item rounded-1">
                        7 derniers jours
                    </a>
                </li>
            </ul>
        </div>

        <div class="me-2">
            <div class="input-icon-start position-relative topdatepicker">
                <span class="input-icon-addon">
                    <i class="ti ti-calendar"></i>
                </span>

                {{-- On garde ton input date range, mais on lui met un name pour passer en GET --}}
                <input type="text" class="form-control date-range bookingrange" name="date"
                    value="{{ request('date') }}" placeholder="dd/mm/yyyy - dd/mm/yyyy">
            </div>
        </div>

        <div class="dropdown">
            <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center"
                data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                <i class="ti ti-filter me-1"></i> Filtrer
                <span
                    class="badge badge-xs rounded-pill bg-danger ms-2">{{ (int) request('filters_count', 0) }}</span>
            </a>
        </div>
    </div>

    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
        <div class="dropdown me-2">
            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                data-bs-toggle="dropdown">
                <i class="ti ti-edit-circle me-1"></i> Actions groupées
            </a>
            <ul class="dropdown-menu dropdown-menu-end p-2">
                <li>
                    {{-- Si tu crées une route bulkDestroy: POST backoffice.vehicles.bulkDestroy --}}
                    <a href="javascript:void(0);" class="dropdown-item rounded-1"
                        onclick="document.getElementById('vehicles-bulk-delete-form')?.submit();">
                        Supprimer
                    </a>
                </li>
            </ul>
        </div>

        {{-- Search en GET vers index --}}
        <form action="{{ route('backoffice.vehicles.index') }}" method="GET" class="top-search me-2">
            {{-- on garde les autres params (sort/date/...) --}}
            @foreach (request()->except('q', 'page') as $k => $v)
                @if (is_array($v))
                    @foreach ($v as $vv)
                        <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endif
            @endforeach

            <div class="top-search-group">
                <span class="input-icon"><i class="ti ti-search"></i></span>
                <input type="text" class="form-control" name="q" value="{{ request('q') }}"
                    placeholder="Rechercher">
            </div>
        </form>

        <div class="dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle coloumn btn btn-white d-inline-flex align-items-center"
                data-bs-toggle="dropdown">
                <i class="ti ti-layout-board me-1"></i> Colonnes
            </a>
            <div class="dropdown-menu dropdown-menu-lg p-2">
                <ul>
                    <li>
                        <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                            <span class="d-inline-flex align-items-center"><i class="ti ti-grip-vertical me-1"></i>
                                VÉHICULE</span>
                            <div class="form-check form-check-sm form-switch mb-0">
                                <input class="form-check-input form-label" type="checkbox" role="switch"
                                    checked="">
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                            <span><i class="ti ti-grip-vertical me-1"></i>EMPLACEMENT</span>
                            <div class="form-check form-check-sm form-switch mb-0">
                                <input class="form-check-input form-label" type="checkbox" role="switch"
                                    checked="">
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                            <span><i class="ti ti-grip-vertical me-1"></i>PRIX (PAR JOUR)</span>
                            <div class="form-check form-check-sm form-switch mb-0">
                                <input class="form-check-input form-label" type="checkbox" role="switch"
                                    checked="">
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                            <span><i class="ti ti-grip-vertical me-1"></i>DOMMAGES</span>
                            <div class="form-check form-check-sm form-switch mb-0">
                                <input class="form-check-input form-label" type="checkbox" role="switch"
                                    checked="">
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                            <span><i class="ti ti-grip-vertical me-1"></i>EN VEDETTE</span>
                            <div class="form-check form-check-sm form-switch mb-0">
                                <input class="form-check-input form-label" type="checkbox" role="switch"
                                    checked="">
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                            <span><i class="ti ti-grip-vertical me-1"></i>DATE DE CRÉATION</span>
                            <div class="form-check form-check-sm form-switch mb-0">
                                <input class="form-check-input form-label" type="checkbox" role="switch"
                                    checked="">
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                            <span><i class="ti ti-grip-vertical me-1"></i>STATUT</span>
                            <div class="form-check form-check-sm form-switch mb-0">
                                <input class="form-check-input form-label" type="checkbox" role="switch"
                                    checked="">
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

{{-- Bulk delete form (sélection via checkbox name="ids[]") --}}
<form id="vehicles-bulk-delete-form" action="{{ route('backoffice.vehicles.bulkDestroy') }}" method="POST"
    class="d-none" onsubmit="return confirm('Confirmer la suppression des véhicules sélectionnés ?');">
    @csrf
    @method('DELETE')
    {{-- Les ids[] seront clonés en JS depuis le tableau (si tu veux je te donne le JS après) --}}
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('select-all-vehicles');
    const checkboxes = document.querySelectorAll('.vehicle-checkbox');
    const bulkForm = document.getElementById('vehicles-bulk-delete-form');

    // Select / unselect all
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }

    // Before submitting bulk delete
    bulkForm?.addEventListener('submit', function (e) {

        // Remove old inputs
        bulkForm.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

        let checked = false;

        checkboxes.forEach(cb => {
            if (cb.checked) {
                checked = true;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                bulkForm.appendChild(input);
            }
        });

        if (!checked) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un véhicule.');
        }
    });
});
</script>

