<?php $page = 'reports-earnings'; ?>
@extends('layout.mainlayout_admin')
@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">Earnings Report</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('backoffice.dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">Reports</li>
                            <li class="breadcrumb-item active" aria-current="page">Earnings</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <form method="GET" action="{{ route('backoffice.reports.earnings') }}" class="d-flex gap-2 mb-2">
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
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Total Earnings</p>
                                    <h4 class="text-success">{{ number_format($totalEarnings, 2) }} MAD</h4>
                                </div>
                                <div class="avatar avatar-lg bg-success-light rounded">
                                    <i class="ti ti-currency-dollar fs-4 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Previous Period</p>
                                    <h4>{{ number_format($previousEarnings, 2) }} MAD</h4>
                                </div>
                                <div class="avatar avatar-lg bg-info-light rounded">
                                    <i class="ti ti-history fs-4 text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1">Growth</p>
                                    <h4 class="{{ $earningsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $earningsGrowth >= 0 ? '+' : '' }}{{ $earningsGrowth }}%
                                    </h4>
                                </div>
                                <div class="avatar avatar-lg {{ $earningsGrowth >= 0 ? 'bg-success-light' : 'bg-danger-light' }} rounded">
                                    <i class="ti {{ $earningsGrowth >= 0 ? 'ti-arrow-up' : 'ti-arrow-down' }} fs-4 {{ $earningsGrowth >= 0 ? 'text-success' : 'text-danger' }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Summary Cards -->

            <div class="row">
                <!-- Daily Earnings Chart -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Daily Earnings</h5>
                        </div>
                        <div class="card-body">
                            <div id="daily-earnings-chart"></div>
                        </div>
                    </div>
                </div>

                <!-- Earnings by Payment Method -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">By Payment Method</h5>
                        </div>
                        <div class="card-body">
                            @forelse ($earningsByMethod as $method)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $method->method)) }}</span>
                                        <br><small class="text-muted">{{ $method->count }} payments</small>
                                    </div>
                                    <span class="badge bg-primary-light text-primary">
                                        {{ number_format($method->total, 2) }} MAD
                                    </span>
                                </div>
                            @empty
                                <p class="text-muted text-center">No earnings in this period</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Paying Clients -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top Paying Clients</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th class="text-end">Total Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topClients as $client)
                                    <tr>
                                        <td>
                                            <a href="{{ route('backoffice.clients.show', $client) }}">
                                                {{ $client->full_name }}
                                            </a>
                                        </td>
                                        <td>{{ $client->phone ?? '-' }}</td>
                                        <td>{{ $client->email ?? '-' }}</td>
                                        <td class="text-end text-success fw-medium">
                                            {{ number_format($client->total_paid, 2) }} MAD
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No client data for this period</td>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dailyData = @json($dailyEarnings);
        if (typeof ApexCharts !== 'undefined' && dailyData.length > 0) {
            var options = {
                chart: { type: 'area', height: 350 },
                series: [{ name: 'Earnings', data: dailyData.map(d => parseFloat(d.total)) }],
                xaxis: { categories: dailyData.map(d => d.day), labels: { rotate: -45 } },
                colors: ['#198754'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
                stroke: { curve: 'smooth', width: 2 }
            };
            new ApexCharts(document.querySelector("#daily-earnings-chart"), options).render();
        }
    });
</script>
@endsection
