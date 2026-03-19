<?php $page = 'reports-income'; ?>
@extends('layout.mainlayout_admin')
@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">Income vs Expenses</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('backoffice.dashboard') }}">Home</a>
                            </li>
                            <li class="breadcrumb-item">Reports</li>
                            <li class="breadcrumb-item active" aria-current="page">Income vs Expenses</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <form method="GET" action="{{ route('backoffice.reports.income') }}" class="d-flex gap-2 mb-2">
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
                                    <p class="text-muted mb-1">Total Income</p>
                                    <h4 class="text-success">{{ number_format($totalIncome, 2) }} MAD</h4>
                                </div>
                                <div class="avatar avatar-lg bg-success-light rounded">
                                    <i class="ti ti-trending-up fs-4 text-success"></i>
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
                                    <p class="text-muted mb-1">Total Expenses</p>
                                    <h4 class="text-danger">{{ number_format($totalExpense, 2) }} MAD</h4>
                                </div>
                                <div class="avatar avatar-lg bg-danger-light rounded">
                                    <i class="ti ti-trending-down fs-4 text-danger"></i>
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
                                    <p class="text-muted mb-1">Net Profit</p>
                                    <h4 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($netProfit, 2) }} MAD
                                    </h4>
                                </div>
                                <div class="avatar avatar-lg bg-primary-light rounded">
                                    <i class="ti ti-wallet fs-4 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Summary Cards -->

            <div class="row">
                <!-- Monthly Chart Data -->
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Monthly Income vs Expenses</h5>
                        </div>
                        <div class="card-body">
                            <div id="income-expense-chart"></div>
                        </div>
                    </div>
                </div>

                <!-- Top Expense Categories -->
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Top Expense Categories</h5>
                        </div>
                        <div class="card-body">
                            @forelse ($topExpenseCategories as $cat)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span>{{ $cat->category->name ?? 'Uncategorized' }}</span>
                                    <span class="badge bg-danger-light text-danger">
                                        {{ number_format($cat->total, 2) }} MAD
                                    </span>
                                </div>
                            @empty
                                <p class="text-muted text-center">No expenses in this period</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Transactions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentTransactions as $tx)
                                    <tr>
                                        <td>{{ $tx->date->format('d/m/Y') }}</td>
                                        <td>{{ Str::limit($tx->description, 40) }}</td>
                                        <td>{{ $tx->category->name ?? '-' }}</td>
                                        <td>{{ $tx->account->name ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $tx->type === 'income' ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                                {{ ucfirst($tx->type) }}
                                            </span>
                                        </td>
                                        <td class="text-end {{ $tx->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->type === 'income' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} MAD
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No transactions found</td>
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
        var monthlyData = @json($monthlyData);
        if (typeof ApexCharts !== 'undefined' && monthlyData.length > 0) {
            var options = {
                chart: { type: 'bar', height: 350 },
                series: [
                    { name: 'Income', data: monthlyData.map(m => parseFloat(m.income)) },
                    { name: 'Expense', data: monthlyData.map(m => parseFloat(m.expense)) }
                ],
                xaxis: { categories: monthlyData.map(m => m.month) },
                colors: ['#198754', '#dc3545'],
                plotOptions: { bar: { columnWidth: '50%', borderRadius: 4 } }
            };
            new ApexCharts(document.querySelector("#income-expense-chart"), options).render();
        }
    });
</script>
@endsection
