<?php

namespace App\Http\Controllers\Backoffice\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleInsurance;
use App\Http\Requests\Backoffice\VehicleInsurance\VehicleInsuranceStoreRequest;
use App\Http\Requests\Backoffice\VehicleInsurance\VehicleInsuranceUpdateRequest;
use App\Services\Finance\AutoTransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class InsuranceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the insurances.
     * Route: /backoffice/vehicles/{vehicle}/insurances
     */
    public function index(Request $request, $vehicleId)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-insurances.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les assurances.');
        }

        // ============ GLOBAL VIEW - ALL VEHICLES ============
        if ($vehicleId === 'all') {
            $query = VehicleInsurance::with('vehicle');
            
            // 🔎 SEARCH
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

            // 🏢 FILTER BY COMPANY
            if ($request->filled('company')) {
                $query->where('company_name', 'like', "%{$request->company}%");
            }

            // 📅 FILTER BY DATE RANGE
            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            // 📅 FILTER BY NEXT INSURANCE DATE RANGE
            if ($request->filled('next_date_from')) {
                $query->whereDate('next_insurance_date', '>=', $request->next_date_from);
            }
            if ($request->filled('next_date_to')) {
                $query->whereDate('next_insurance_date', '<=', $request->next_date_to);
            }

            // 💰 FILTER BY AMOUNT RANGE
            if ($request->filled('amount_min')) {
                $query->where('amount', '>=', $request->amount_min);
            }
            if ($request->filled('amount_max')) {
                $query->where('amount', '<=', $request->amount_max);
            }

            // 🔤 SORT
            $sort = $request->get('sort', 'latest');
            if ($sort === 'oldest') {
                $query->orderBy('date', 'asc');
            } elseif ($sort === 'amount_asc') {
                $query->orderBy('amount', 'asc');
            } elseif ($sort === 'amount_desc') {
                $query->orderBy('amount', 'desc');
            } elseif ($sort === 'next_date_asc') {
                $query->orderBy('next_insurance_date', 'asc');
            } elseif ($sort === 'next_date_desc') {
                $query->orderBy('next_insurance_date', 'desc');
            } else {
                $query->orderBy('date', 'desc');
            }

            $insurances = $query->paginate(15)->withQueryString();

            // Get available companies for filter
            $availableCompanies = VehicleInsurance::whereNotNull('company_name')
                ->select('company_name')
                ->distinct()
                ->orderBy('company_name')
                ->pluck('company_name');

            // ✅ Passer les permissions à la vue
            $permissions = [
                'can_view' => auth()->user()->can('vehicle-insurances.general.view'),
                'can_create' => auth()->user()->can('vehicle-insurances.general.create'),
                'can_edit' => auth()->user()->can('vehicle-insurances.general.edit'),
                'can_delete' => auth()->user()->can('vehicle-insurances.general.delete'),
            ];

            return view('backoffice.insurances.index', [
                'vehicle' => null,
                'insurances' => $insurances,
                'availableCompanies' => $availableCompanies,
                'isGlobalView' => true,
                'permissions' => $permissions
            ]);
        }
        
        // ============ SINGLE VEHICLE VIEW ============
        $vehicle = Vehicle::find($vehicleId);
        
        if (!$vehicle) {
            return view('backoffice.insurances.index', [
                'vehicle' => null,
                'insurances' => new LengthAwarePaginator([], 0, 15),
                'availableCompanies' => collect([]),
                'isGlobalView' => false,
                'permissions' => [
                    'can_view' => auth()->user()->can('vehicle-insurances.general.view'),
                    'can_create' => auth()->user()->can('vehicle-insurances.general.create'),
                    'can_edit' => auth()->user()->can('vehicle-insurances.general.edit'),
                    'can_delete' => auth()->user()->can('vehicle-insurances.general.delete'),
                ],
            ]);
        }
        
        $this->authorize('view', $vehicle);

        $query = $vehicle->insurances();

        // 🔎 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('policy_number', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // 🏢 FILTER BY COMPANY
        if ($request->filled('company')) {
            $query->where('company_name', 'like', "%{$request->company}%");
        }

        // 📅 FILTER BY DATE RANGE
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // 📅 FILTER BY NEXT INSURANCE DATE RANGE
        if ($request->filled('next_date_from')) {
            $query->whereDate('next_insurance_date', '>=', $request->next_date_from);
        }
        if ($request->filled('next_date_to')) {
            $query->whereDate('next_insurance_date', '<=', $request->next_date_to);
        }

        // 💰 FILTER BY AMOUNT RANGE
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->amount_min);
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->amount_max);
        }

        // 🔤 SORT
        $sort = $request->get('sort', 'latest');
        
        if ($sort === 'oldest') {
            $query->orderBy('date', 'asc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('amount', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } elseif ($sort === 'next_date_asc') {
            $query->orderBy('next_insurance_date', 'asc');
        } elseif ($sort === 'next_date_desc') {
            $query->orderBy('next_insurance_date', 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $insurances = $query->paginate(15)->withQueryString();

        // Get available companies for filter
        $availableCompanies = VehicleInsurance::where('vehicle_id', $vehicle->id)
            ->whereNotNull('company_name')
            ->select('company_name')
            ->distinct()
            ->orderBy('company_name')
            ->pluck('company_name');

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('vehicle-insurances.general.view'),
            'can_create' => auth()->user()->can('vehicle-insurances.general.create'),
            'can_edit' => auth()->user()->can('vehicle-insurances.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-insurances.general.delete'),
        ];

        return view('backoffice.insurances.index', compact('vehicle', 'insurances', 'availableCompanies', 'permissions'));
    }

    public function create(Vehicle $vehicle = null)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-insurances.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des assurances.');
        }

        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('backoffice.insurances.partials._modal_create', compact('vehicle', 'vehicles'));
    }

    public function store(VehicleInsuranceStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-insurances.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des assurances.');
        }

        try {
            DB::beginTransaction();
            $data = $request->validated();
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);
            
            $insurance = VehicleInsurance::create([
                'vehicle_id' => $vehicle->id,
                'company_name' => $data['company_name'] ?? null,
                'policy_number' => $data['policy_number'] ?? null,
                'date' => $data['date'],
                'amount' => $data['amount'],
                'next_insurance_date' => $data['next_insurance_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            
            // CRÉER AUTOMATIQUEMENT LA TRANSACTION DE DÉPENSE
            if ($insurance->amount > 0) {
                app(AutoTransactionService::class)
                    ->createExpenseFromInsurance($insurance);
            }
            
            $this->createNotification('store', 'insurance', $insurance);
            
            DB::commit();
            
            return redirect()->route('backoffice.vehicles.insurances.index', $vehicle->id)
                ->with('toast', [
                    'title' => 'Créé', 
                    'message' => 'Assurance créée avec succès.', 
                    'dot' => '#198754', 
                    'delay' => 3500, 
                    'time' => 'now'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur', 
                'message' => 'Erreur lors de la création: ' . $e->getMessage(),
                'dot' => '#dc3545', 
                'delay' => 3500, 
                'time' => 'now'
            ]);
        }
    }

    public function show(Vehicle $vehicle, VehicleInsurance $insurance)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-insurances.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les assurances.');
        }

        $this->authorize('view', $vehicle);
        $this->verifyResource($vehicle, $insurance);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('vehicle-insurances.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-insurances.general.delete'),
        ];

        return view('backoffice.insurances.show', compact('vehicle', 'insurance', 'permissions'));
    }

    public function edit(Vehicle $vehicle, VehicleInsurance $insurance)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-insurances.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les assurances.');
        }

        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $insurance);
        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('backoffice.insurances.partials._modal_edit', compact('vehicle', 'insurance', 'vehicles'));
    }

    public function update(VehicleInsuranceUpdateRequest $request, Vehicle $vehicle, VehicleInsurance $insurance)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-insurances.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les assurances.');
        }

        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $insurance);

        try {
            DB::beginTransaction();
            $data = $request->validated();
            
            $insurance->update([
                'vehicle_id' => $data['vehicle_id'],
                'company_name' => $data['company_name'] ?? null,
                'policy_number' => $data['policy_number'] ?? null,
                'date' => $data['date'],
                'amount' => $data['amount'],
                'next_insurance_date' => $data['next_insurance_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            
            $this->createNotification('update', 'insurance', $insurance);

            DB::commit();
            
            return redirect()->route('backoffice.vehicles.insurances.index', $insurance->vehicle_id)
                ->with('toast', [
                    'title' => 'Mis à jour', 
                    'message' => 'Assurance mise à jour avec succès.', 
                    'dot' => '#0d6efd', 
                    'delay' => 3500, 
                    'time' => 'now'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur', 
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
                'dot' => '#dc3545', 
                'delay' => 3500, 
                'time' => 'now'
            ]);
        }
    }

    public function destroy(Request $request, $vehicleId, VehicleInsurance $insurance)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('vehicle-insurances.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les assurances.');
        }

        if ($insurance->vehicle_id != $vehicleId) {
            abort(404);
        }
        
        try {
            DB::beginTransaction();
            
            // Supprimer la transaction associée
            app(AutoTransactionService::class)
                ->deleteTransactionForSource('insurance', $insurance->id);
            
            $insuranceData = clone $insurance;
            $insurance->delete();
            
            $this->createNotification('destroy', 'insurance', $insuranceData);
            
            DB::commit();
            
            $referer = $request->header('referer');
            $isGlobalView = str_contains($referer, '/vehicles/all/');
            
            if ($isGlobalView) {
                return redirect()
                    ->route('backoffice.vehicles.insurances.index', ['vehicle' => 'all'])
                    ->with('toast', [
                        'title' => 'Supprimé', 
                        'message' => 'Assurance supprimée avec succès.', 
                        'dot' => '#dc3545'
                    ]);
            } else {
                return redirect()
                    ->route('backoffice.vehicles.insurances.index', ['vehicle' => $vehicleId])
                    ->with('toast', [
                        'title' => 'Supprimé', 
                        'message' => 'Assurance supprimée avec succès.', 
                        'dot' => '#dc3545'
                    ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('toast', [
                'title' => 'Erreur', 
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage(), 
                'dot' => '#dc3545'
            ]);
        }
    }

    private function verifyResource(Vehicle $vehicle, VehicleInsurance $insurance): void
    {
        if ($insurance->vehicle_id !== $vehicle->id) abort(404);
    }
}