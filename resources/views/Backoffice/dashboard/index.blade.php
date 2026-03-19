<?php $page = 'dashboard'; ?>
@extends('layout.mainlayout_admin')

@section('content')
<style>
    /* Equal height cards for Newly Added Cars */
    .equal-height-card {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .equal-height-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .equal-height-card .card-body .mt-auto {
        margin-top: auto;
    }
    
    /* Fix car image size - using placeholder */
    .car-image-fixed {
        width: 100%;
        height: 160px;
        object-fit: cover;
        object-position: center;
    }
    
    /* Make all stat cards same height */
    .stat-card {
        height: 130px;
        display: flex;
        align-items: center;
    }
    
    .stat-card .card-body {
        width: 100%;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <!-- Page Header with Working Date Filter -->
        <div class="d-md-flex d-block align-items-center justify-content-between mb-3">
            <div class="my-auto mb-2">
                <h3 class="page-title mb-1">Tableau de bord</h3>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('backoffice.dashboard') }}">Accueil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tableau de bord admin</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <button class="btn btn-outline-light bg-white dropdown-toggle" type="button" id="dateFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-calendar me-2"></i>
                        <span id="dateRangeText">{{ $startDate->format('m/d/Y') }} - {{ $endDate->format('m/d/Y') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-3" style="min-width: 300px;" aria-labelledby="dateFilterDropdown">
                        <li>
                            <div class="mb-3">
                                <label class="form-label">Sélection rapide</label>
                                <select class="form-select" id="quickPeriodSelect">
                                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                                    <option value="yesterday" {{ $period == 'yesterday' ? 'selected' : '' }}>Hier</option>
                                    <option value="last7" {{ $period == 'last7' ? 'selected' : '' }}>7 derniers jours</option>
                                    <option value="last30" {{ $period == 'last30' ? 'selected' : '' }}>30 derniers jours</option>
                                    <option value="thisMonth" {{ $period == 'thisMonth' ? 'selected' : '' }}>Ce mois</option>
                                    <option value="lastMonth" {{ $period == 'lastMonth' ? 'selected' : '' }}>Mois dernier</option>
                                    <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Période personnalisée</option>
                                </select>
                            </div>
                        </li>
                        <li>
                            <div class="mb-3" id="customDateRange" style="{{ $period != 'custom' ? 'display: none;' : '' }}">
                                <label class="form-label">Date de début</label>
                                <input type="date" class="form-control mb-2" id="startDate" value="{{ $startDate->format('Y-m-d') }}">
                                <label class="form-label">Date de fin</label>
                                <input type="date" class="form-control" id="endDate" value="{{ $endDate->format('Y-m-d') }}">
                            </div>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary" id="cancelFilter">Annuler</button>
                                <button type="button" class="btn btn-primary" id="applyFilter">Appliquer</button>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="mb-0">
                    <a href="javascript:void(0);" class="btn btn-primary" onclick="location.reload()">
                        <i class="ti ti-refresh me-2"></i>Actualiser
                    </a>
                </div>
            </div>
        </div>

        <!-- Welcome Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="text-white mb-2">Bienvenue, {{ auth()->user()->name ?? 'Admin' }}</h4>
                            <p class="text-white-50 mb-0">{{ $availableVehicles }} véhicules disponibles à la location</p>
                        </div>
                        <div>
                            <i class="ti ti-car fs-48 text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards - Equal height -->
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted mb-2 d-block">Nombre total de voitures</span>
                                <h3 class="mb-0">{{ $totalVehicles }}</h3>
                                <small class="text-muted">{{ $rentedVehicles }} en location</small>
                            </div>
                            <div class="bg-primary-transparent rounded-circle p-3">
                                <i class="ti ti-car text-primary fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted mb-2 d-block">Total réservations</span>
                                <h3 class="mb-0">{{ $totalBookings }}</h3>
                                @if($bookingsGrowth > 0)
                                <small class="text-success">+{{ $bookingsGrowth }}%</small>
                                @elseif($bookingsGrowth < 0)
                                <small class="text-danger">{{ $bookingsGrowth }}%</small>
                                @endif
                            </div>
                            <div class="bg-success-transparent rounded-circle p-3">
                                <i class="ti ti-calendar-stats text-success fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted mb-2 d-block">Total bénéfices</span>
                                <h3 class="mb-0">{{ number_format($totalRevenue, 0, ',', ' ') }} MAD</h3>
                                @if($revenueGrowth > 0)
                                <small class="text-success">+{{ $revenueGrowth }}%</small>
                                @elseif($revenueGrowth < 0)
                                <small class="text-danger">{{ $revenueGrowth }}%</small>
                                @endif
                            </div>
                            <div class="bg-warning-transparent rounded-circle p-3">
                                <i class="ti ti-currency-dollar text-warning fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted mb-2 d-block">Total voitures</span>
                                <h3 class="mb-0">{{ $totalVehicles }}</h3>
                                @if($vehicleChange != 0)
                                <small class="{{ $vehicleChange < 0 ? 'text-danger' : 'text-success' }}">{{ $vehicleChange }}%</small>
                                @endif
                            </div>
                            <div class="bg-info-transparent rounded-circle p-3">
                                <i class="ti ti-car text-info fs-24"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Newly Added Cars - WITH place-holder.webp for cars -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Voitures récemment ajoutées</h5>
                        <a href="{{ route('backoffice.vehicles.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($newVehicles as $vehicle)
                            <div class="col-xl-3 col-lg-6 mb-3">
                                <div class="card equal-height-card">
                                    <img src="{{ $vehicle->getMainPhotoUrlAttribute() ?: URL::asset('assets/place-holder.webp') }}" class="card-img-top car-image-fixed" alt="car">
                                    <div class="card-body">
                                        <h6>{{ $vehicle->brand->name ?? 'Inconnu' }} {{ $vehicle->model->name ?? '' }}</h6>
                                        <p class="text-muted mb-2 small">{{ $vehicle->registration_number }}</p>
                                        <h5 class="text-primary mb-3">{{ number_format($vehicle->daily_rate ?? 0, 0, ',', ' ') }} MAD/jour</h5>
                                        <div class="d-flex justify-content-between mt-auto small">
                                            <span><i class="ti ti-gas-station me-1"></i> {{ $vehicle->fuel_type ?? 'Essence' }}</span>
                                            <span><i class="ti ti-users me-1"></i> {{ str_pad($vehicle->seats ?? 4, 2, '0', STR_PAD_LEFT) }}</span>
                                            <span><i class="ti ti-steering-wheel me-1"></i> {{ $vehicle->transmission ?? 'Manuelle' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-center py-4 text-muted">
                                <p>Aucune voiture récemment ajoutée</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Reservations and Customers Row -->
        <div class="row mt-4">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Réservations récentes</h5>
                        <a href="{{ route('backoffice.bookings.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                    </div>
                    <div class="card-body">
                        @forelse($recentBookings as $booking)
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <!-- Use placeholder for reservation car images too -->
                                <img src="{{ URL::asset('assets/place-holder.webp') }}" class="rounded me-3" width="60" height="40" style="object-fit: cover;" alt="car">
                                <div>
                                    <h6 class="mb-1">{{ $booking->vehicle?->brand?->name ?? 'Voiture' }} {{ $booking->vehicle?->model?->name ?? '' }}</h6>
                                    <p class="text-muted mb-0 small">
                                        <i class="ti ti-map-pin me-1"></i> {{ $booking->pickup_location ?? 'N/A' }} 
                                        <i class="ti ti-arrow-right mx-2"></i> 
                                        <i class="ti ti-map-pin me-1"></i> {{ $booking->dropoff_location ?? 'N/A' }}
                                    </p>
                                    <p class="text-muted mb-0 small">
                                        <i class="ti ti-clock me-1"></i> {{ $booking->start_date ? date('d M Y, h:i A', strtotime($booking->start_date)) : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'secondary') }} mb-2">
                                    {{ $booking->duration }}
                                </span>
                                <h6 class="mb-0">{{ number_format($booking->total_amount ?? 0, 0, ',', ' ') }} MAD/jour</h6>
                                <small class="text-muted">{{ $booking->driver_type ?? 'Autonome' }}</small>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <p>Aucune réservation récente</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <!-- Customers - Use placeholder for avatars -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Clients</h5>
                        <a href="{{ route('backoffice.clients.index') }}" class="btn btn-sm btn-primary">Voir tout</a>
                    </div>
                    <div class="card-body">
                        @forelse($topCustomers as $customer)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <!-- Use placeholder for customer avatars too -->
                                <img src="{{ URL::asset('assets/place-holder.webp') }}" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;" alt="customer">
                                <div>
                                    <h6 class="mb-0">{{ $customer->full_name }}</h6>
                                    <small class="text-muted">Client</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">Réservations</span>
                                <h6 class="mb-0 mt-1">{{ $customer->bookings_count ?? 0 }}</h6>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <p>Aucune donnée client</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Income & Expenses -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Revenus et dépenses</h5>
                    </div>
                    <div class="card-body">
                        @if($monthRevenue > 0 || $monthExpenses > 0)
                            <div class="d-flex justify-content-between mb-3">
                                @if($monthRevenue > 0)
                                <div>
                                    <span class="badge bg-success mb-2">Revenus</span>
                                    <h4>{{ number_format($monthRevenue, 0, ',', ' ') }} MAD</h4>
                                    @if($revenueGrowth > 0)
                                    <small class="text-success">+{{ $revenueGrowth }}%</small>
                                    @elseif($revenueGrowth < 0)
                                    <small class="text-danger">{{ $revenueGrowth }}%</small>
                                    @endif
                                </div>
                                @endif
                                @if($monthExpenses > 0)
                                <div>
                                    <span class="badge bg-danger mb-2">Dépenses</span>
                                    <h4>{{ number_format($monthExpenses, 0, ',', ' ') }} MAD</h4>
                                    @if($expenseGrowth > 0)
                                    <small class="text-danger">+{{ $expenseGrowth }}%</small>
                                    @elseif($expenseGrowth < 0)
                                    <small class="text-success">{{ $expenseGrowth }}%</small>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @if($monthRevenue > 0 && $monthExpenses > 0)
                            <div class="progress mt-3" style="height: 10px;">
                                <div class="progress-bar bg-success" style="width: {{ ($monthRevenue / ($monthRevenue + $monthExpenses)) * 100 }}%"></div>
                                <div class="progress-bar bg-danger" style="width: {{ ($monthExpenses / ($monthRevenue + $monthExpenses)) * 100 }}%"></div>
                            </div>
                            @endif
                        @else
                            <div class="text-center py-4 text-muted">
                                <p>Aucune donnée de revenus ou dépenses</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date Filter Functionality
    const quickPeriodSelect = document.getElementById('quickPeriodSelect');
    const customDateRange = document.getElementById('customDateRange');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const applyButton = document.getElementById('applyFilter');
    const cancelButton = document.getElementById('cancelFilter');
    const dateRangeText = document.getElementById('dateRangeText');
    
    // Show/hide custom date range based on selection
    quickPeriodSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.style.display = 'block';
        } else {
            customDateRange.style.display = 'none';
            // Set dates based on quick selection
            setDatesFromQuickSelect(this.value);
        }
    });
    
    function setDatesFromQuickSelect(period) {
        const today = new Date();
        let startDate = new Date();
        let endDate = new Date();
        
        switch(period) {
            case 'today':
                startDate = today;
                endDate = today;
                break;
            case 'yesterday':
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 1);
                endDate = new Date(today);
                endDate.setDate(today.getDate() - 1);
                break;
            case 'last7':
                endDate = new Date(today);
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 7);
                break;
            case 'last30':
                endDate = new Date(today);
                startDate = new Date(today);
                startDate.setDate(today.getDate() - 30);
                break;
            case 'thisMonth':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'lastMonth':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
        }
        
        startDateInput.value = formatDate(startDate);
        endDateInput.value = formatDate(endDate);
    }
    
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    function formatDisplayDate(dateStr) {
        const date = new Date(dateStr);
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const year = date.getFullYear();
        return `${month}/${day}/${year}`;
    }
    
    // Apply filter
    applyButton.addEventListener('click', function() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const period = quickPeriodSelect.value;
        
        if (!startDate || !endDate) {
            alert('Veuillez sélectionner les dates de début et de fin');
            return;
        }
        
        // Update display text
        dateRangeText.textContent = `${formatDisplayDate(startDate)} - ${formatDisplayDate(endDate)}`;
        
        // Reload page with filter parameters
        window.location.href = `{{ route('backoffice.dashboard') }}?start_date=${startDate}&end_date=${endDate}&period=${period}`;
    });
    
    // Cancel filter
    cancelButton.addEventListener('click', function() {
        // Just close dropdown without doing anything
        const dropdown = bootstrap.Dropdown.getInstance(document.getElementById('dateFilterDropdown'));
        if (dropdown) {
            dropdown.hide();
        }
    });
});
</script>
@endpush
@endsection