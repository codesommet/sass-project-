{{--
    Stat Card Component
    Usage: <x-backoffice.stat-card label="Total Clients" :value="$totalClients" icon="ti-users" color="primary" />
--}}
@props([
    'label',
    'value',
    'icon' => 'ti-chart-bar',
    'color' => 'primary',
])

<div class="col-xl-3 col-sm-6">
    <div class="card bg-{{ $color }} text-white">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-2">{{ $label }}</h6>
                    <h3 class="text-white mb-0">{{ $value }}</h3>
                </div>
                <i class="ti {{ $icon }} fs-40 opacity-50"></i>
            </div>
        </div>
    </div>
</div>
