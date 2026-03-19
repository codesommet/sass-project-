<?php $page = 'statistics'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    .stat-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }
    .trend-up { color: #198754; }
    .trend-down { color: #dc3545; }
    .period-selector {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 5px;
    }
    .period-selector .btn {
        border-radius: 6px;
        padding: 5px 15px;
    }
    .period-selector .btn.active {
        background: #0d6efd;
        color: white;
    }
</style>

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">Statistiques détaillées</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.dashboard') }}">Accueil</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('backoffice.dashboard') }}">Tableau de bord</a>
                        </li>
                        <li class="breadcrumb-item active">Statistiques</li>
                    </ol>
                </nav>
            </div>
            <div class="mt-2 mt-md-0">
                <div class="period-selector d-inline-flex">
                    <button class="btn btn-sm period-btn active" data-period="today">Aujourd'hui</button>
                    <button class="btn btn-sm period-btn" data-period="week">Cette semaine</button>
                    <button class="btn btn-sm period-btn" data-period="month">Ce mois</button>
                    <button class="btn btn-sm period-btn" data-period="year">Cette année</button>
                </div>
                <button class="btn btn-primary ms-2" onclick="exportStatistics()">
                    <i class="ti ti-download me-1"></i>Exporter
                </button>
            </div>
        </div>

        <!-- Summary Cards Row -->
        @include('Backoffice.dashboard.partials._summary-cards')

        <!-- Charts Row -->
        <div class="row">
            <div class="col-xl-8">
                @include('Backoffice.dashboard.charts.revenue-chart')
            </div>
            <div class="col-xl-4">
                @include('Backoffice.dashboard.charts.booking-chart')
            </div>
        </div>

        <!-- Statistics Tabs -->
        <div class="card mt-3">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#stats-vehicles">
                            <i class="ti ti-car me-1"></i>Véhicules
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#stats-bookings">
                            <i class="ti ti-calendar-stats me-1"></i>Réservations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#stats-clients">
                            <i class="ti ti-users me-1"></i>Clients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#stats-finance">
                            <i class="ti ti-currency-dollar me-1"></i>Finances
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#stats-payments">
                            <i class="ti ti-credit-card me-1"></i>Paiements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#stats-agents">
                            <i class="ti ti-user-circle me-1"></i>Agents
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Vehicles Statistics -->
                    <div class="tab-pane active" id="stats-vehicles">
                        @include('Backoffice.dashboard.statistics.vehicles')
                    </div>

                    <!-- Bookings Statistics -->
                    <div class="tab-pane" id="stats-bookings">
                        @include('Backoffice.dashboard.statistics.bookings')
                    </div>

                    <!-- Clients Statistics -->
                    <div class="tab-pane" id="stats-clients">
                        @include('Backoffice.dashboard.statistics.customers')
                    </div>

                    <!-- Finance Statistics -->
                    <div class="tab-pane" id="stats-finance">
                        @include('Backoffice.dashboard.statistics.revenue')
                    </div>

                    <!-- Payments Statistics -->
                    <div class="tab-pane" id="stats-payments">
                        @php
                            $agencyId = auth()->guard('backoffice')->user()->agency_id;
                            $startDate = request('start_date', now()->startOfMonth());
                            $endDate = request('end_date', now());
                            
                            $paymentMethods = [
                                'cash' => ['label' => 'Espèces', 'color' => 'success', 'icon' => 'ti ti-cash'],
                                'card' => ['label' => 'Carte bancaire', 'color' => 'primary', 'icon' => 'ti ti-credit-card'],
                                'bank_transfer' => ['label' => 'Virement', 'color' => 'info', 'icon' => 'ti ti-transfer'],
                                'cheque' => ['label' => 'Chèque', 'color' => 'warning', 'icon' => 'ti ti-file-check']
                            ];
                            
                            $totalPayments = App\Models\Payment::where('agency_id', $agencyId)
                                ->whereBetween('payment_date', [$startDate, $endDate])
                                ->count();
                            
                            $totalAmount = App\Models\Payment::where('agency_id', $agencyId)
                                ->whereBetween('payment_date', [$startDate, $endDate])
                                ->sum('amount');
                            
                            $avgPayment = $totalPayments > 0 ? $totalAmount / $totalPayments : 0;
                        @endphp

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <div class="stat-icon mx-auto mb-3 bg-primary bg-opacity-10">
                                            <i class="ti ti-credit-card text-primary"></i>
                                        </div>
                                        <h5 class="mb-1">{{ number_format($totalPayments) }}</h5>
                                        <p class="text-muted mb-0">Total paiements</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <div class="stat-icon mx-auto mb-3 bg-success bg-opacity-10">
                                            <i class="ti ti-currency-dollar text-success"></i>
                                        </div>
                                        <h5 class="mb-1">{{ number_format($totalAmount, 2, ',', ' ') }} MAD</h5>
                                        <p class="text-muted mb-0">Montant total</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card stat-card">
                                    <div class="card-body text-center">
                                        <div class="stat-icon mx-auto mb-3 bg-info bg-opacity-10">
                                            <i class="ti ti-chart-bar text-info"></i>
                                        </div>
                                        <h5 class="mb-1">{{ number_format($avgPayment, 2, ',', ' ') }} MAD</h5>
                                        <p class="text-muted mb-0">Paiement moyen</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6 class="mb-3">Répartition par méthode</h6>
                            @foreach($paymentMethods as $method => $details)
                                @php
                                    $amount = App\Models\Payment::where('agency_id', $agencyId)
                                        ->where('method', $method)
                                        ->whereBetween('payment_date', [$startDate, $endDate])
                                        ->sum('amount');
                                    $percentage = $totalAmount > 0 ? round(($amount / $totalAmount) * 100) : 0;
                                @endphp
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-3" style="width: 30px;">
                                        <i class="{{ $details['icon'] }} text-{{ $details['color'] }}"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>{{ $details['label'] }}</span>
                                            <span class="fw-bold">{{ number_format($amount, 2, ',', ' ') }} MAD</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $details['color'] }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Agents Statistics -->
                    <div class="tab-pane" id="stats-agents">
                        @php
                            $agencyId = auth()->guard('backoffice')->user()->agency_id;
                            
                            $totalAgents = App\Models\Agent::where('agency_id', $agencyId)->count();
                            
                            $topAgents = App\Models\RentalContract::where('agency_id', $agencyId)
                                ->with('agent')
                                ->selectRaw('agent_id, count(*) as contracts, sum(total_amount) as revenue')
                                ->whereNotNull('agent_id')
                                ->groupBy('agent_id')
                                ->orderBy('revenue', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <h6 class="mb-3">Top agents par revenu</h6>
                                        @forelse($topAgents as $agent)
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-shrink-0 me-3">
                                                    <span class="avatar avatar-sm bg-primary bg-opacity-10">
                                                        {{ strtoupper(substr($agent->agent->full_name ?? 'NA', 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between">
                                                        <span>{{ $agent->agent->full_name ?? 'N/A' }}</span>
                                                        <span class="fw-bold">{{ number_format($agent->revenue, 2, ',', ' ') }} MAD</span>
                                                    </div>
                                                    <small class="text-muted">{{ $agent->contracts }} contrats</small>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted text-center">Aucune donnée disponible</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <h6 class="mb-3">Statistiques générales</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total agents:</span>
                                            <span class="fw-bold">{{ $totalAgents }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Agents actifs:</span>
                                            <span class="fw-bold">{{ $totalAgents }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Moyenne contrats/agent:</span>
                                            <span class="fw-bold">
                                                {{ $totalAgents > 0 ? round($topAgents->sum('contracts') / $totalAgents, 1) : 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Charts -->
        <div class="row mt-3">
            <div class="col-xl-6">
                @include('Backoffice.dashboard.charts.income-expenses')
            </div>
            <div class="col-xl-6">
                @include('Backoffice.dashboard.charts.occupancy-chart')
            </div>
        </div>

        <!-- Comparison Section -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Comparaison périodes</h5>
            </div>
            <div class="card-body">
                @php
                    $currentPeriod = [
                        'revenue' => App\Models\Payment::where('agency_id', $agencyId)
                            ->whereBetween('payment_date', [now()->startOfMonth(), now()])
                            ->sum('amount'),
                        'bookings' => App\Models\Booking::where('agency_id', $agencyId)
                            ->whereBetween('created_at', [now()->startOfMonth(), now()])
                            ->count()
                    ];
                    
                    $previousPeriod = [
                        'revenue' => App\Models\Payment::where('agency_id', $agencyId)
                            ->whereBetween('payment_date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                            ->sum('amount'),
                        'bookings' => App\Models\Booking::where('agency_id', $agencyId)
                            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                            ->count()
                    ];
                    
                    $revenueGrowth = $previousPeriod['revenue'] > 0 
                        ? round((($currentPeriod['revenue'] - $previousPeriod['revenue']) / $previousPeriod['revenue']) * 100, 1)
                        : 0;
                    
                    $bookingsGrowth = $previousPeriod['bookings'] > 0
                        ? round((($currentPeriod['bookings'] - $previousPeriod['bookings']) / $previousPeriod['bookings']) * 100, 1)
                        : 0;
                @endphp

                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="mb-3">Revenus</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Ce mois:</span>
                                    <span class="fw-bold">{{ number_format($currentPeriod['revenue'], 2, ',', ' ') }} MAD</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Mois dernier:</span>
                                    <span>{{ number_format($previousPeriod['revenue'], 2, ',', ' ') }} MAD</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Croissance:</span>
                                    <span class="fw-bold {{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="mb-3">Réservations</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Ce mois:</span>
                                    <span class="fw-bold">{{ $currentPeriod['bookings'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Mois dernier:</span>
                                    <span>{{ $previousPeriod['bookings'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Croissance:</span>
                                    <span class="fw-bold {{ $bookingsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $bookingsGrowth >= 0 ? '+' : '' }}{{ $bookingsGrowth }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
        <p class="mb-0">
            <a href="javascript:void(0);">Politique de confidentialité</a>
            <a href="javascript:void(0);" class="ms-4">Conditions d'utilisation</a>
        </p>
        <p>&copy; 2025 Dreamsrent, Fait avec <span class="text-danger">❤</span> par <a href="javascript:void(0);" class="text-secondary">Dreams</a></p>
    </div>
</div>

<script>
// Period selector functionality
document.querySelectorAll('.period-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const period = this.dataset.period;
        const today = new Date();
        let startDate, endDate = today.toISOString().split('T')[0];
        
        switch(period) {
            case 'today':
                startDate = endDate;
                break;
            case 'week':
                const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
                startDate = firstDay.toISOString().split('T')[0];
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                break;
            case 'year':
                startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                break;
        }
        
        // Reload with new date range
        window.location.href = updateQueryString(window.location.href, {
            start_date: startDate,
            end_date: endDate
        });
    });
});

function updateQueryString(uri, params) {
    const url = new URL(uri);
    Object.keys(params).forEach(key => {
        url.searchParams.set(key, params[key]);
    });
    return url.toString();
}

function exportStatistics() {
    const format = confirm('Exporter en PDF?') ? 'pdf' : 'excel';
    const url = format === 'pdf' 
        ? '{{ route("backoffice.dashboard.export.pdf") }}' 
        : '{{ route("backoffice.dashboard.export.excel") }}';
    
    window.location.href = url + window.location.search;
}

// Auto-refresh data every 5 minutes
setTimeout(() => {
    location.reload();
}, 300000);
</script>
@endsection