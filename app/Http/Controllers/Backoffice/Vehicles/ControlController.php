<?php

namespace App\Http\Controllers\Backoffice\Vehicles;

use App\Http\Controllers\Controller;
use App\Models\VehicleControl;
use App\Models\Vehicle;
use App\Models\RentalContract;
use App\Models\User;
use App\Http\Requests\Backoffice\VehicleControl\VehicleControlStoreRequest;
use App\Http\Requests\Backoffice\VehicleControl\VehicleControlUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ControlController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of vehicle controls.
     */
    public function index(Request $request)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-controls.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les contrôles.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = VehicleControl::with(['vehicle.model.brand', 'rentalContract', 'performer'])
            ->where('agency_id', $agencyId);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('control_number', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                      $vehicleQuery->where('registration_number', 'like', "%{$search}%")
                                  ->orWhereHas('model', function ($modelQuery) use ($search) {
                                      $modelQuery->where('name', 'like', "%{$search}%")
                                                ->orWhereHas('brand', function ($brandQuery) use ($search) {
                                                    $brandQuery->where('name', 'like', "%{$search}%");
                                                });
                                  });
                  });
            });
        }

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('end_mileage');
            } elseif ($request->status === 'pending') {
                $query->whereNull('end_mileage');
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'mileage_asc') {
            $query->orderBy('start_mileage', 'asc');
        } elseif ($sort === 'mileage_desc') {
            $query->orderBy('start_mileage', 'desc');
        } elseif ($sort === 'control_number_asc') {
            $query->orderBy('control_number', 'asc');
        } elseif ($sort === 'control_number_desc') {
            $query->orderBy('control_number', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $controls = $query->paginate(15)->withQueryString();

        // Get vehicles for filter dropdown
        $vehicles = Vehicle::with('model.brand')
            ->where('agency_id', $agencyId)
            ->orderBy('registration_number')
            ->get();
        
        // Get data for modals
        $rentalContracts = RentalContract::where('agency_id', $agencyId)
            ->orderBy('created_at', 'desc')
            ->get();

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('vehicle-controls.general.view'),
            'can_create' => auth()->user()->can('vehicle-controls.general.create'),
            'can_edit' => auth()->user()->can('vehicle-controls.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-controls.general.delete'),
        ];

        return view('backoffice.controls.index', compact('controls', 'vehicles', 'rentalContracts', 'permissions'));
    }

    /**
     * Show the form for creating a new vehicle control.
     */
    public function create()
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-controls.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des contrôles.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        
        $vehicles = Vehicle::with(['model.brand'])
            ->where('agency_id', $agencyId)
            ->orderBy('registration_number')
            ->get();
        
        $rentalContracts = RentalContract::where('agency_id', $agencyId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backoffice.controls.partials._modal_create', compact('vehicles', 'rentalContracts'));
    }

    /**
     * Store a newly created vehicle control.
     */
    public function store(VehicleControlStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-controls.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des contrôles.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;
            $data['performed_by'] = Auth::guard('backoffice')->id();

            // Generate control number if not provided
            if (empty($data['control_number'])) {
                $data['control_number'] = $this->generateControlNumber();
            }

            $control = VehicleControl::create($data);
            
            $this->createNotification('store', 'control', $control);
            
            DB::commit();

            return redirect()
                ->route('backoffice.controls.index')
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Contrôle véhicule créé avec succès.',
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
     * Display the specified vehicle control.
     */
    public function show(VehicleControl $control)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-controls.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les contrôles.');
        }

        // Check if control belongs to user's agency
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($control->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé');
        }

        $control->load(['vehicle', 'rentalContract', 'performer', 'agency']);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('vehicle-controls.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-controls.general.delete'),
        ];

        return view('backoffice.controls.show', compact('control', 'permissions'));
    }

    /**
     * Show the form for editing the specified vehicle control.
     */
    public function edit(VehicleControl $control)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-controls.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les contrôles.');
        }

        // Check if control belongs to user's agency
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($control->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé');
        }

        $vehicles = Vehicle::with(['model.brand'])
            ->where('agency_id', $agencyId)
            ->orderBy('registration_number')
            ->get();
        
        $rentalContracts = RentalContract::where('agency_id', $agencyId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backoffice.controls.partials._modal_edit', compact('control', 'vehicles', 'rentalContracts'));
    }

    /**
     * Update the specified vehicle control.
     */
    public function update(VehicleControlUpdateRequest $request, VehicleControl $control) 
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-controls.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les contrôles.');
        }

        // Check if control belongs to user's agency
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($control->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            $control->update($data);
            
            $this->createNotification('update', 'control', $control);

            DB::commit();

            return redirect()
                ->route('backoffice.controls.show', $control)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Contrôle véhicule mis à jour avec succès.',
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
     * Remove the specified vehicle control.
     */
    public function destroy(VehicleControl $control)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('vehicle-controls.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les contrôles.');
        }

        // Check if control belongs to user's agency
        $agencyId = Auth::guard('backoffice')->user()->agency_id;
        if ($control->agency_id !== $agencyId) {
            abort(403, 'Accès non autorisé');
        }

        try {
            DB::beginTransaction();

            $controlData = clone $control;
            $control->delete();
            
            $this->createNotification('destroy', 'control', $controlData);
            
            DB::commit();

            return redirect()
                ->route('backoffice.controls.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Contrôle véhicule supprimé avec succès.',
                    'dot' => '#dc3545',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Generate a unique control number.
     */
    private function generateControlNumber(): string
    {
        $prefix = 'CTRL';
        $year = date('Y');
        $month = date('m');
        $random = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        
        $controlNumber = $prefix . '-' . $year . $month . '-' . $random;
        
        // Ensure uniqueness
        while (VehicleControl::where('control_number', $controlNumber)->exists()) {
            $random = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $controlNumber = $prefix . '-' . $year . $month . '-' . $random;
        }
        
        return $controlNumber;
    }
}