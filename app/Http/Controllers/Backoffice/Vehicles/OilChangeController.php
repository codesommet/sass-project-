<?php

namespace App\Http\Controllers\Backoffice\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleOilChange;
use App\Http\Requests\Backoffice\VehicleOilChange\VehicleOilChangeStoreRequest;
use App\Http\Requests\Backoffice\VehicleOilChange\VehicleOilChangeUpdateRequest;
use App\Services\Finance\AutoTransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class OilChangeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the oil changes.
     * Route: /backoffice/vehicles/{vehicle}/oil-changes
     */
    public function index(Request $request, $vehicleId)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-oil-changes.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les vidanges.');
        }

        // ============ GLOBAL VIEW - ALL VEHICLES ============
        if ($vehicleId === 'all') {
            $query = VehicleOilChange::with('vehicle');
            
            // 🔎 SEARCH
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('mechanic_name', 'like', "%{$search}%")
                      ->orWhere('mileage', 'like', "%{$search}%")
                      ->orWhere('next_mileage', 'like', "%{$search}%")
                      ->orWhere('amount', 'like', "%{$search}%")
                      ->orWhere('observations', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function ($sub) use ($search) {
                          $sub->where('registration_number', 'like', "%{$search}%")
                              ->orWhere('registration_city', 'like', "%{$search}%");
                      });
                });
            }

            // 📅 FILTER BY DATE RANGE
            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }

            // 👨‍🔧 FILTER BY MECHANIC
            if ($request->filled('mechanic')) {
                $query->where('mechanic_name', 'like', "%{$request->mechanic}%");
            }

            // 🔢 FILTER BY MILEAGE RANGE
            if ($request->filled('mileage_min')) {
                $query->where('mileage', '>=', $request->mileage_min);
            }
            if ($request->filled('mileage_max')) {
                $query->where('mileage', '<=', $request->mileage_max);
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
            } elseif ($sort === 'mileage_asc') {
                $query->orderBy('mileage', 'asc');
            } elseif ($sort === 'mileage_desc') {
                $query->orderBy('mileage', 'desc');
            } elseif ($sort === 'amount_asc') {
                $query->orderBy('amount', 'asc');
            } elseif ($sort === 'amount_desc') {
                $query->orderBy('amount', 'desc');
            } else {
                $query->orderBy('date', 'desc');
            }

            $oilChanges = $query->paginate(15)->withQueryString();

            // Get available mechanics for filter
            $availableMechanics = VehicleOilChange::whereNotNull('mechanic_name')
                ->select('mechanic_name')
                ->distinct()
                ->orderBy('mechanic_name')
                ->pluck('mechanic_name');

            // ✅ Passer les permissions à la vue
            $permissions = [
                'can_view' => auth()->user()->can('vehicle-oil-changes.general.view'),
                'can_create' => auth()->user()->can('vehicle-oil-changes.general.create'),
                'can_edit' => auth()->user()->can('vehicle-oil-changes.general.edit'),
                'can_delete' => auth()->user()->can('vehicle-oil-changes.general.delete'),
            ];

            return view('backoffice.oil-changes.index', [
                'vehicle' => null,
                'oilChanges' => $oilChanges,
                'availableMechanics' => $availableMechanics,
                'isGlobalView' => true,
                'permissions' => $permissions
            ]);
        }
        
        // ============ SINGLE VEHICLE VIEW ============
        $vehicle = Vehicle::find($vehicleId);
        
        if (!$vehicle) {
            return view('backoffice.oil-changes.index', [
                'vehicle' => null,
                'oilChanges' => new LengthAwarePaginator([], 0, 15),
                'availableMechanics' => collect([]),
                'isGlobalView' => false,
                'permissions' => [
                    'can_view' => auth()->user()->can('vehicle-oil-changes.general.view'),
                    'can_create' => auth()->user()->can('vehicle-oil-changes.general.create'),
                    'can_edit' => auth()->user()->can('vehicle-oil-changes.general.edit'),
                    'can_delete' => auth()->user()->can('vehicle-oil-changes.general.delete'),
                ],
            ]);
        }
        
        $this->authorize('view', $vehicle);

        $query = $vehicle->oilChanges();

        // 🔎 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mechanic_name', 'like', "%{$search}%")
                  ->orWhere('mileage', 'like', "%{$search}%")
                  ->orWhere('next_mileage', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('observations', 'like', "%{$search}%");
            });
        }

        // 📅 FILTER BY DATE RANGE
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        // 👨‍🔧 FILTER BY MECHANIC
        if ($request->filled('mechanic')) {
            $query->where('mechanic_name', 'like', "%{$request->mechanic}%");
        }

        // 🔢 FILTER BY MILEAGE RANGE
        if ($request->filled('mileage_min')) {
            $query->where('mileage', '>=', $request->mileage_min);
        }
        if ($request->filled('mileage_max')) {
            $query->where('mileage', '<=', $request->mileage_max);
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
        } elseif ($sort === 'mileage_asc') {
            $query->orderBy('mileage', 'asc');
        } elseif ($sort === 'mileage_desc') {
            $query->orderBy('mileage', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('amount', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $oilChanges = $query->paginate(15)->withQueryString();

        // Get available mechanics for filter
        $availableMechanics = VehicleOilChange::where('vehicle_id', $vehicle->id)
            ->whereNotNull('mechanic_name')
            ->select('mechanic_name')
            ->distinct()
            ->orderBy('mechanic_name')
            ->pluck('mechanic_name');

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('vehicle-oil-changes.general.view'),
            'can_create' => auth()->user()->can('vehicle-oil-changes.general.create'),
            'can_edit' => auth()->user()->can('vehicle-oil-changes.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-oil-changes.general.delete'),
        ];

        return view('backoffice.oil-changes.index', compact('vehicle', 'oilChanges', 'availableMechanics', 'permissions'));
    }

    public function create(Vehicle $vehicle = null)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-oil-changes.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des vidanges.');
        }

        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('backoffice.oil-changes.partials._modal_create', compact('vehicle', 'vehicles'));
    }

    public function store(VehicleOilChangeStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-oil-changes.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des vidanges.');
        }

        try {
            DB::beginTransaction();
            $data = $request->validated();
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);
            
            $oilChange = VehicleOilChange::create([
                'vehicle_id' => $vehicle->id,
                'date' => $data['date'],
                'amount' => $data['amount'],
                'mileage' => $data['mileage'],
                'next_mileage' => $data['next_mileage'],
                'mechanic_name' => $data['mechanic_name'] ?? null,
                'observations' => $data['observations'] ?? null,
            ]);
            
            // CRÉER AUTOMATIQUEMENT LA TRANSACTION DE DÉPENSE
            if ($oilChange->amount > 0) {
                app(AutoTransactionService::class)
                    ->createExpenseFromOilChange($oilChange);
            }
            
            $this->createNotification('store', 'oil-change', $oilChange);
            
            DB::commit();
            
            return redirect()->route('backoffice.vehicles.oil-changes.index', $vehicle->id)
                ->with('toast', [
                    'title' => 'Créé', 
                    'message' => 'Vidange créée avec succès.', 
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

    public function show(Vehicle $vehicle, VehicleOilChange $oilChange)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-oil-changes.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les vidanges.');
        }

        $this->authorize('view', $vehicle);
        $this->verifyResource($vehicle, $oilChange);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('vehicle-oil-changes.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-oil-changes.general.delete'),
        ];

        return view('backoffice.oil-changes.show', compact('vehicle', 'oilChange', 'permissions'));
    }

    public function edit(Vehicle $vehicle, VehicleOilChange $oilChange)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-oil-changes.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les vidanges.');
        }

        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $oilChange);
        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('backoffice.oil-changes.partials._modal_edit', compact('vehicle', 'oilChange', 'vehicles'));
    }

    public function update(VehicleOilChangeUpdateRequest $request, Vehicle $vehicle, VehicleOilChange $oilChange)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-oil-changes.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les vidanges.');
        }

        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $oilChange);

        try {
            DB::beginTransaction();
            $data = $request->validated();
            
            $oilChange->update([
                'vehicle_id' => $data['vehicle_id'],
                'date' => $data['date'],
                'amount' => $data['amount'],
                'mileage' => $data['mileage'],
                'next_mileage' => $data['next_mileage'],
                'mechanic_name' => $data['mechanic_name'] ?? null,
                'observations' => $data['observations'] ?? null,
            ]);
            
            $this->createNotification('update', 'oil-change', $oilChange);

            DB::commit();
            
            return redirect()->route('backoffice.vehicles.oil-changes.index', $oilChange->vehicle_id)
                ->with('toast', [
                    'title' => 'Mis à jour', 
                    'message' => 'Vidange mise à jour avec succès.', 
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

    /**
     * Remove the specified oil change from storage.
     * Route: /backoffice/vehicles/{vehicle}/oil-changes/{oilChange} (DELETE)
     */
    public function destroy(Request $request, $vehicleId, VehicleOilChange $oilChange)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('vehicle-oil-changes.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les vidanges.');
        }

        // Verify the oil change belongs to the vehicle
        if ($oilChange->vehicle_id != $vehicleId) {
            abort(404);
        }
        
        try {
            DB::beginTransaction();
            
            // Supprimer la transaction associée
            app(AutoTransactionService::class)
                ->deleteTransactionForSource('oil_change', $oilChange->id);
            
            $oilChangeData = clone $oilChange;
            $vehicleId = $oilChange->vehicle_id;
            $oilChange->delete();
            
            $this->createNotification('destroy', 'oil-change', $oilChangeData);
            
            DB::commit();
            
            // Check if we came from a global view (all vehicles)
            $referer = $request->header('referer');
            $isGlobalView = str_contains($referer, '/vehicles/all/');
            
            // Smart redirect - go back to same view
            if ($isGlobalView) {
                return redirect()
                    ->route('backoffice.vehicles.oil-changes.index', ['vehicle' => 'all'])
                    ->with('toast', [
                        'title' => 'Supprimé',
                        'message' => 'Vidange supprimée avec succès.',
                        'dot' => '#dc3545',
                        'delay' => 3500,
                        'time' => 'now',
                    ]);
            } else {
                return redirect()
                    ->route('backoffice.vehicles.oil-changes.index', ['vehicle' => $vehicleId])
                    ->with('toast', [
                        'title' => 'Supprimé',
                        'message' => 'Vidange supprimée avec succès.',
                        'dot' => '#dc3545',
                        'delay' => 3500,
                        'time' => 'now',
                    ]);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('toast', [
                    'title' => 'Erreur',
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage(),
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        }
    }

    private function verifyResource(Vehicle $vehicle, VehicleOilChange $oilChange): void
    {
        if ($oilChange->vehicle_id !== $vehicle->id) abort(404);
    }
}