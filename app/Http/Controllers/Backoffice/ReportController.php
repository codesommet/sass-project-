<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\RentalContract;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Income vs Expense report.
     */
    public function incomeReport(Request $request)
    {
        $user = auth()->user();
        $agencyId = $user->agency_id;

        // Date range (default: current month)
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        $query = FinancialTransaction::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->whereBetween('date', [$startDate, $endDate]);

        // Totals
        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        // Monthly breakdown for chart (last 12 months)
        $monthlyData = FinancialTransaction::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->where('date', '>=', now()->subMonths(11)->startOfMonth())
            ->select(
                DB::raw("strftime('%Y-%m', date) as month"),
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top expense categories
        $topExpenseCategories = FinancialTransaction::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->with('category')
            ->select('transaction_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('transaction_category_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Recent transactions
        $recentTransactions = FinancialTransaction::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['account', 'category'])
            ->latest('date')
            ->limit(10)
            ->get();

        return view('backoffice.reports.income', compact(
            'totalIncome',
            'totalExpense',
            'netProfit',
            'monthlyData',
            'topExpenseCategories',
            'recentTransactions',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Earnings report.
     */
    public function earningsReport(Request $request)
    {
        $user = auth()->user();
        $agencyId = $user->agency_id;

        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        // Payment totals
        $paymentQuery = Payment::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$startDate, $endDate]);

        $totalEarnings = (clone $paymentQuery)->sum('amount');

        // Earnings by payment method
        $earningsByMethod = (clone $paymentQuery)
            ->select('method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->get();

        // Daily earnings for chart
        $dailyEarnings = Payment::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->select(
                DB::raw("strftime('%Y-%m-%d', payment_date) as day"),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Compare with previous period
        $daysDiff = $startDate->diffInDays($endDate);
        $previousStart = (clone $startDate)->subDays($daysDiff + 1);
        $previousEnd = (clone $endDate)->subDays($daysDiff + 1);

        $previousEarnings = Payment::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$previousStart, $previousEnd])
            ->sum('amount');

        $earningsGrowth = $previousEarnings > 0
            ? round((($totalEarnings - $previousEarnings) / $previousEarnings) * 100, 1)
            : 0;

        // Top paying clients
        $topClients = Client::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->select('clients.*')
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query->from('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->whereColumn('invoices.client_id', 'clients.id')
                    ->where('payments.status', 'confirmed')
                    ->whereBetween('payments.payment_date', [$startDate, $endDate])
                    ->selectRaw('COALESCE(SUM(payments.amount), 0)');
            }, 'total_paid')
            ->whereRaw('(select COALESCE(SUM(payments.amount), 0) from payments inner join invoices on payments.invoice_id = invoices.id where invoices.client_id = clients.id and payments.status = ? and payments.payment_date between ? and ?) > 0', ['confirmed', $startDate, $endDate])
            ->orderByDesc('total_paid')
            ->limit(5)
            ->get();

        return view('backoffice.reports.earnings', compact(
            'totalEarnings',
            'earningsByMethod',
            'dailyEarnings',
            'earningsGrowth',
            'previousEarnings',
            'topClients',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Rental/contract report.
     */
    public function rentalReport(Request $request)
    {
        $user = auth()->user();
        $agencyId = $user->agency_id;

        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : now()->startOfMonth();
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : now()->endOfMonth();

        // Contract stats
        $contractQuery = RentalContract::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalContracts = (clone $contractQuery)->count();
        $contractRevenue = (clone $contractQuery)->sum('total_amount');
        $avgContractValue = $totalContracts > 0 ? round($contractRevenue / $totalContracts, 2) : 0;

        // Contracts by status
        $contractsByStatus = (clone $contractQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Booking conversion rate
        $bookingQuery = Booking::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalBookings = (clone $bookingQuery)->count();
        $convertedBookings = (clone $bookingQuery)->where('status', 'converted')->count();
        $conversionRate = $totalBookings > 0 ? round(($convertedBookings / $totalBookings) * 100, 1) : 0;

        // Most rented vehicles
        $topVehicles = Vehicle::query()
            ->when($agencyId, fn($q) => $q->where('vehicles.agency_id', $agencyId))
            ->with('model.brand')
            ->select('vehicles.*')
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query->from('rental_contracts')
                    ->whereColumn('rental_contracts.vehicle_id', 'vehicles.id')
                    ->whereBetween('rental_contracts.created_at', [$startDate, $endDate])
                    ->selectRaw('COUNT(*)');
            }, 'rentals_count')
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query->from('rental_contracts')
                    ->whereColumn('rental_contracts.vehicle_id', 'vehicles.id')
                    ->whereBetween('rental_contracts.created_at', [$startDate, $endDate])
                    ->selectRaw('COALESCE(SUM(total_amount), 0)');
            }, 'total_revenue')
            ->whereRaw('(select COUNT(*) from rental_contracts where rental_contracts.vehicle_id = vehicles.id and rental_contracts.created_at between ? and ?) > 0', [$startDate, $endDate])
            ->orderByDesc('rentals_count')
            ->limit(5)
            ->get();

        // Average rental duration
        $avgDuration = RentalContract::query()
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('planned_days');
        $avgDuration = $avgDuration ? round($avgDuration, 1) : 0;

        return view('backoffice.reports.rentals', compact(
            'totalContracts',
            'contractRevenue',
            'avgContractValue',
            'contractsByStatus',
            'totalBookings',
            'convertedBookings',
            'conversionRate',
            'topVehicles',
            'avgDuration',
            'startDate',
            'endDate'
        ));
    }
}
