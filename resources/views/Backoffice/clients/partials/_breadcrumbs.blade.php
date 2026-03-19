@php
    $agencyId = auth()->guard('backoffice')->user()->agency_id;
    $totalClients = App\Models\Client::where('agency_id', $agencyId)->count();
    $activeClients = App\Models\Client::where('agency_id', $agencyId)->where('status', 'active')->count();
    $inactiveClients = App\Models\Client::where('agency_id', $agencyId)->where('status', 'inactive')->count();
    $newThisMonth = App\Models\Client::where('agency_id', $agencyId)
        ->whereMonth('created_at', now()->month)
        ->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Total clients</h6>
                        <h3 class="text-white mb-0">{{ $totalClients }}</h3>
                    </div>
                    <i class="ti ti-users fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Actifs</h6>
                        <h3 class="text-white mb-0">{{ $activeClients }}</h3>
                    </div>
                    <i class="ti ti-user-check fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Inactifs</h6>
                        <h3 class="text-white mb-0">{{ $inactiveClients }}</h3>
                    </div>
                    <i class="ti ti-user-x fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-white-50 mb-2">Nouveaux (mois)</h6>
                        <h3 class="text-white mb-0">{{ $newThisMonth }}</h3>
                    </div>
                    <i class="ti ti-user-plus fs-40 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>