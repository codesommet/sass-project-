<?php

namespace App\Http\Controllers\Backoffice\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleVignette;
use App\Http\Requests\Backoffice\VehicleVignette\VehicleVignetteStoreRequest;
use App\Http\Requests\Backoffice\VehicleVignette\VehicleVignetteUpdateRequest;
use App\Services\Finance\AutoTransactionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class VignetteController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the vignettes.
     * Route: /backoffice/vehicles/{vehicle}/vignettes
     */
    public function index(Request $request, $vehicleId): View
    {
        // ============ GLOBAL VIEW - ALL VEHICLES ============
        if ($vehicleId === 'all') {
            // ✅ Vérifier la permission VIEW
            if (!auth()->user()->can('vehicle-vignettes.general.view')) {
                abort(403, 'Vous n\'avez pas la permission de voir les vignettes.');
            }

            $query = VehicleVignette::with('vehicle');
            
            // 🔎 SEARCH
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

            // 📅 FILTER BY YEAR
            if ($request->filled('year')) {
                $query->where('year', $request->year);
            }

            // 📅 FILTER BY DATE RANGE
            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
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
            } elseif ($sort === 'year_asc') {
                $query->orderBy('year', 'asc');
            } elseif ($sort === 'year_desc') {
                $query->orderBy('year', 'desc');
            } else {
                $query->orderBy('date', 'desc');
            }

            $vignettes = $query->paginate(15)->withQueryString();

            // Get available years for filter
            $availableYears = VehicleVignette::select('year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');

            // ✅ Passer les permissions à la vue
            $permissions = [
                'can_view' => auth()->user()->can('vehicle-vignettes.general.view'),
                'can_create' => auth()->user()->can('vehicle-vignettes.general.create'),
                'can_edit' => auth()->user()->can('vehicle-vignettes.general.edit'),
                'can_delete' => auth()->user()->can('vehicle-vignettes.general.delete'),
            ];

            return view('Backoffice.vignettes.index', [
                'vehicle' => null,
                'vignettes' => $vignettes,
                'availableYears' => $availableYears,
                'isGlobalView' => true,
                'permissions' => $permissions
            ]);
        }
        
        // ============ SINGLE VEHICLE VIEW ============
        $vehicle = Vehicle::find($vehicleId);
        
        if (!$vehicle) {
            return view('Backoffice.vignettes.index', [
                'vehicle' => null,
                'vignettes' => new LengthAwarePaginator([], 0, 15),
                'availableYears' => collect([]),
                'isGlobalView' => false,
                'permissions' => []
            ]);
        }
        
        $this->authorize('view', $vehicle);
        
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-vignettes.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les vignettes.');
        }

        $query = $vehicle->vignettes();

        // 🔎 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('year', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('date', 'like', "%{$search}%");
            });
        }

        // 📅 FILTER BY YEAR
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // 📅 FILTER BY DATE RANGE
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
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
        } elseif ($sort === 'year_asc') {
            $query->orderBy('year', 'asc');
        } elseif ($sort === 'year_desc') {
            $query->orderBy('year', 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $vignettes = $query->paginate(15)->withQueryString();

        // Get available years for filter
        $availableYears = VehicleVignette::where('vehicle_id', $vehicle->id)
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('vehicle-vignettes.general.view'),
            'can_create' => auth()->user()->can('vehicle-vignettes.general.create'),
            'can_edit' => auth()->user()->can('vehicle-vignettes.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-vignettes.general.delete'),
        ];

        return view('Backoffice.vignettes.index', compact('vehicle', 'vignettes', 'availableYears', 'permissions'));
    }

    /**
     * Show the form for creating a new vignette.
     */
    public function create(Vehicle $vehicle = null)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-vignettes.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des vignettes.');
        }

        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('Backoffice.vignettes.partials._modal_create', compact('vehicle', 'vehicles'));
    }

    /**
     * Store a newly created vignette in storage.
     */
    public function store(VehicleVignetteStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-vignettes.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des vignettes.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            $vehicle = Vehicle::findOrFail($data['vehicle_id']);
            
            $vignette = VehicleVignette::create([
                'vehicle_id' => $vehicle->id,
                'date' => $data['date'],
                'amount' => $data['amount'],
                'year' => $data['year'],
                'notes' => $data['notes'] ?? null,
            ]);
            
            // CRÉER AUTOMATIQUEMENT LA TRANSACTION DE DÉPENSE
            if ($vignette->amount > 0) {
                app(AutoTransactionService::class)
                    ->createExpenseFromVignette($vignette);
            }
            
            $this->createNotification('store', 'vignette', $vignette);
            
            DB::commit();

            return redirect()
                ->route('backoffice.vehicles.vignettes.index', $vehicle->id)
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Vignette créée avec succès.',
                    'dot' => '#198754',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la création: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Display the specified vignette.
     */
    public function show(Vehicle $vehicle, VehicleVignette $vignette)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-vignettes.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les vignettes.');
        }

        $this->authorize('view', $vehicle);
        $this->verifyResource($vehicle, $vignette);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('vehicle-vignettes.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-vignettes.general.delete'),
        ];

        return view('Backoffice.vignettes.show', compact('vehicle', 'vignette', 'permissions'));
    }

    /**
     * Show the form for editing the specified vignette.
     */
    public function edit(Vehicle $vehicle, VehicleVignette $vignette)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-vignettes.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les vignettes.');
        }

        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $vignette);
        $vehicles = Vehicle::orderBy('registration_number')->get();
        return view('Backoffice.vignettes.partials._modal_edit', compact('vehicle', 'vignette', 'vehicles'));
    }

    /**
     * Update the specified vignette in storage.
     */
    public function update(VehicleVignetteUpdateRequest $request, Vehicle $vehicle, VehicleVignette $vignette)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-vignettes.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les vignettes.');
        }

        $this->authorize('update', $vehicle);
        $this->verifyResource($vehicle, $vignette);

        try {
            DB::beginTransaction();
            $data = $request->validated();
            
            $vignette->update([
                'vehicle_id' => $data['vehicle_id'] ?? $vignette->vehicle_id,
                'date' => $data['date'],
                'amount' => $data['amount'],
                'year' => $data['year'],
                'notes' => $data['notes'] ?? null,
            ]);
            
            $this->createNotification('update', 'vignette', $vignette);

            DB::commit();
            
            return redirect()
                ->route('backoffice.vehicles.vignettes.index', $vignette->vehicle_id)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Vignette mise à jour avec succès.',
                    'dot' => '#0d6efd',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Remove the specified vignette from storage.
     */
    public function destroy(Request $request, $vehicleId, VehicleVignette $vignette)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('vehicle-vignettes.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les vignettes.');
        }

        if ($vignette->vehicle_id != $vehicleId) {
            abort(404);
        }
        
        try {
            DB::beginTransaction();
            
            // Supprimer la transaction associée
            app(AutoTransactionService::class)
                ->deleteTransactionForSource('vignette', $vignette->id);
            
            // Store vignette data for notification before delete
            $vignetteData = clone $vignette;
            $vignette->delete();
            
            $this->createNotification('destroy', 'vignette', $vignetteData);
            
            DB::commit();
            
            $referer = $request->header('referer');
            $isGlobalView = str_contains($referer, '/vehicles/all/');
            
            if ($isGlobalView) {
                return redirect()
                    ->route('backoffice.vehicles.vignettes.index', ['vehicle' => 'all'])
                    ->with('toast', [
                        'title' => 'Supprimé', 
                        'message' => 'Vignette supprimée avec succès.', 
                        'dot' => '#dc3545'
                    ]);
            } else {
                return redirect()
                    ->route('backoffice.vehicles.vignettes.index', ['vehicle' => $vehicleId])
                    ->with('toast', [
                        'title' => 'Supprimé', 
                        'message' => 'Vignette supprimée avec succès.', 
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

    /**
     * Verify that the vignette belongs to the vehicle
     */
    private function verifyResource(Vehicle $vehicle, VehicleVignette $vignette): void
    {
        if ($vignette->vehicle_id !== $vehicle->id) {
            abort(404);
        }
    }
}