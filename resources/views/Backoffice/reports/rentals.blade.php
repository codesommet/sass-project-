<?php $page = 'reports-rentals'; ?>
@extends('layout.mainlayout_admin')
@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">Rental Report</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('backoffice.dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">Reports</li>
                            <li class="breadcrumb-item active" aria-current="page">Rentals</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <form method="GET" action="{{ route('backoffice.reports.rentals') }}" class="d-flex gap-2 mb-2">
                        <input type="date" name="start_date" class="form-control form-control-sm"
                            value="{{ $startDate->format('Y-m-d') }}">
                        <input type="date" name="end_date" class="form-control form-control-sm"
                            value="{{ $endDate->format('Y-m-d') }}">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Contracts</p>
                            <h4>{{ $totalContracts }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Contract Revenue</p>
                            <h4 class="text-success">{{ number_format($contractRevenue, 2) }} MAD</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Avg. Contract Value</p>
                            <h4>{{ number_format($avgContractValue, 2) }} MAD</h4>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-1">Avg. Rental Duration</p>
                            <h4>{{ $avgDuration }} days</h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Summary Cards -->

            <div class="row">
                <!-- Contracts by Status -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Contracts by Status</h5>
                        </div>
                        <div class="card-body">
                            @forelse ($contractsByStatus as $status)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>{{ ucfirst(str_replace('_', ' ', $status->status)) }}</span>
                                    <span class="badge bg-primary">{{ $status->count }}</span>
                                </div>
                            @empty
                                <p class="text-muted text-center">No contracts in this period</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Booking Conversion -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Booking Conversion</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h2 class="text-primary">{{ $conversionRate }}%</h2>
                                <p class="text-muted">Conversion Rate</p>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div>
                                    <h5>{{ $totalBookings }}</h5>
                                    <small class="text-muted">Total Bookings</small>
                                </div>
                                <div>
                                    <h5 class="text-success">{{ $convertedBookings }}</h5>
                                    <small class="text-muted">Converted</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Period Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Period</span>
                                <span>{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Total Revenue</span>
                                <span class="text-success fw-medium">{{ number_format($contractRevenue, 2) }} MAD</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Contracts Created</span>
                                <span>{{ $totalContracts }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Avg. Duration</span>
                                <span>{{ $avgDuration }} days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Rented Vehicles -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Most Rented Vehicles</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Registration</th>
                                    <th>Daily Rate</th>
                                    <th class="text-center">Rentals</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topVehicles as $vehicle)
                                    <tr>
                                        <td>
                                            <a href="{{ route('backoffice.vehicles.show', $vehicle) }}">
                                                {{ $vehicle->model->brand->name ?? '' }} {{ $vehicle->model->name ?? '' }}
                                            </a>
                                        </td>
                                        <td>{{ $vehicle->registration_number }}</td>
                                        <td>{{ number_format($vehicle->daily_rate, 2) }} MAD</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $vehicle->rentals_count }}</span>
                                        </td>
                                        <td class="text-end text-success fw-medium">
                                            {{ number_format($vehicle->total_revenue, 2) }} MAD
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No rental data for this period</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /Page Wrapper -->
@endsection
