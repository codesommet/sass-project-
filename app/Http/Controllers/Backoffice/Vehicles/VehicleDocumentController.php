<?php

namespace App\Http\Controllers\Backoffice\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\VehicleVignette;
use App\Models\VehicleInsurance;
use App\Models\VehicleTechnicalCheck;
use App\Models\VehicleOilChange;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'vignettes');
        $validTabs = ['vignettes', 'insurances', 'technical-checks', 'oil-changes'];
        if (!in_array($activeTab, $validTabs)) {
            $activeTab = 'vignettes';
        }

        // Counts for all tabs
        $counts = [
            'vignettes' => VehicleVignette::count(),
            'insurances' => VehicleInsurance::count(),
            'technical-checks' => VehicleTechnicalCheck::count(),
            'oil-changes' => VehicleOilChange::count(),
        ];

        $data = compact('activeTab', 'counts');

        // Load ALL tabs' data — search/filters only apply to active tab
        $data = array_merge($data, $this->getVignettesData($request, $activeTab === 'vignettes'));
        $data = array_merge($data, $this->getInsurancesData($request, $activeTab === 'insurances'));
        $data = array_merge($data, $this->getTechnicalChecksData($request, $activeTab === 'technical-checks'));
        $data = array_merge($data, $this->getOilChangesData($request, $activeTab === 'oil-changes'));

        return view('backoffice.vehicle-documents.index', $data);
    }

    private function getVignettesData(Request $request, bool $applyFilters): array
    {
        $query = VehicleVignette::with('vehicle');

        if ($applyFilters) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('year', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhere('date', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function ($sub) use ($search) {
                          $sub->where('registration_number', 'like', "%{$search}%")
                              ->orWhere('registration_city', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('year')) {
                $query->where('year', $request->year);
            }
            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }
            if ($request->filled('amount_min')) {
                $query->where('amount', '>=', $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $query->where('amount', '<=', $request->amount_max);
            }

            $sort = $request->get('sort', 'latest');
            match ($sort) {
                'oldest' => $query->orderBy('date', 'asc'),
                'amount_asc' => $query->orderBy('amount', 'asc'),
                'amount_desc' => $query->orderBy('amount', 'desc'),
                'year_asc' => $query->orderBy('year', 'asc'),
                'year_desc' => $query->orderBy('year', 'desc'),
                default => $query->orderBy('date', 'desc'),
            };
        } else {
            $query->orderBy('date', 'desc');
        }

        $availableYears = VehicleVignette::distinct()->orderBy('year', 'desc')->pluck('year');

        return [
            'vignettes' => $query->paginate(15, ['*'], 'vignettes_page')->withQueryString(),
            'availableYears' => $availableYears,
        ];
    }

    private function getInsurancesData(Request $request, bool $applyFilters): array
    {
        $query = VehicleInsurance::with('vehicle');

        if ($applyFilters) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                      ->orWhere('policy_number', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function ($sub) use ($search) {
                          $sub->where('registration_number', 'like', "%{$search}%")
                              ->orWhere('registration_city', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('company')) {
                $query->where('company_name', 'like', "%{$request->company}%");
            }
            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }
            if ($request->filled('next_date_from')) {
                $query->whereDate('next_insurance_date', '>=', $request->next_date_from);
            }
            if ($request->filled('next_date_to')) {
                $query->whereDate('next_insurance_date', '<=', $request->next_date_to);
            }
            if ($request->filled('amount_min')) {
                $query->where('amount', '>=', $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $query->where('amount', '<=', $request->amount_max);
            }

            $sort = $request->get('sort', 'latest');
            match ($sort) {
                'oldest' => $query->orderBy('date', 'asc'),
                'amount_asc' => $query->orderBy('amount', 'asc'),
                'amount_desc' => $query->orderBy('amount', 'desc'),
                'next_date_asc' => $query->orderBy('next_insurance_date', 'asc'),
                'next_date_desc' => $query->orderBy('next_insurance_date', 'desc'),
                default => $query->orderBy('date', 'desc'),
            };
        } else {
            $query->orderBy('date', 'desc');
        }

        $availableCompanies = VehicleInsurance::distinct()->whereNotNull('company_name')->orderBy('company_name')->pluck('company_name');

        return [
            'insurances' => $query->paginate(15, ['*'], 'insurances_page')->withQueryString(),
            'availableCompanies' => $availableCompanies,
        ];
    }

    private function getTechnicalChecksData(Request $request, bool $applyFilters): array
    {
        $query = VehicleTechnicalCheck::with('vehicle');

        if ($applyFilters) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('amount', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhere('date', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function ($sub) use ($search) {
                          $sub->where('registration_number', 'like', "%{$search}%")
                              ->orWhere('registration_city', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }
            if ($request->filled('next_date_from')) {
                $query->whereDate('next_check_date', '>=', $request->next_date_from);
            }
            if ($request->filled('next_date_to')) {
                $query->whereDate('next_check_date', '<=', $request->next_date_to);
            }
            if ($request->filled('amount_min')) {
                $query->where('amount', '>=', $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $query->where('amount', '<=', $request->amount_max);
            }

            $sort = $request->get('sort', 'latest');
            match ($sort) {
                'oldest' => $query->orderBy('date', 'asc'),
                'amount_asc' => $query->orderBy('amount', 'asc'),
                'amount_desc' => $query->orderBy('amount', 'desc'),
                'next_date_asc' => $query->orderBy('next_check_date', 'asc'),
                'next_date_desc' => $query->orderBy('next_check_date', 'desc'),
                default => $query->orderBy('date', 'desc'),
            };
        } else {
            $query->orderBy('date', 'desc');
        }

        return [
            'technicalChecks' => $query->paginate(15, ['*'], 'checks_page')->withQueryString(),
        ];
    }

    private function getOilChangesData(Request $request, bool $applyFilters): array
    {
        $query = VehicleOilChange::with('vehicle');

        if ($applyFilters) {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('mechanic_name', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhere('mileage', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function ($sub) use ($search) {
                          $sub->where('registration_number', 'like', "%{$search}%")
                              ->orWhere('registration_city', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }
            if ($request->filled('mechanic')) {
                $query->where('mechanic_name', 'like', "%{$request->mechanic}%");
            }
            if ($request->filled('mileage_min')) {
                $query->where('mileage', '>=', $request->mileage_min);
            }
            if ($request->filled('mileage_max')) {
                $query->where('mileage', '<=', $request->mileage_max);
            }
            if ($request->filled('amount_min')) {
                $query->where('amount', '>=', $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $query->where('amount', '<=', $request->amount_max);
            }

            $sort = $request->get('sort', 'latest');
            match ($sort) {
                'oldest' => $query->orderBy('date', 'asc'),
                'mileage_asc' => $query->orderBy('mileage', 'asc'),
                'mileage_desc' => $query->orderBy('mileage', 'desc'),
                'amount_asc' => $query->orderBy('amount', 'asc'),
                'amount_desc' => $query->orderBy('amount', 'desc'),
                default => $query->orderBy('date', 'desc'),
            };
        } else {
            $query->orderBy('date', 'desc');
        }

        $availableMechanics = VehicleOilChange::distinct()->whereNotNull('mechanic_name')->orderBy('mechanic_name')->pluck('mechanic_name');

        return [
            'oilChanges' => $query->paginate(15, ['*'], 'oils_page')->withQueryString(),
            'availableMechanics' => $availableMechanics,
        ];
    }
}
