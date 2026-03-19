<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\VehicleModel\VehicleModelStoreRequest;
use App\Http\Requests\Backoffice\VehicleModel\VehicleModelUpdateRequest;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of vehicle models.
     */
    public function index(Request $request)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-models.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les modèles.');
        }

        $query = VehicleModel::where('agency_id', Auth::user()->agency_id)
            ->with('brand');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('brand', function($brandQuery) use ($search) {
                      $brandQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $models = $query->latest()->paginate(15);
        
        $brands = VehicleBrand::where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get();

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('vehicle-models.general.view'),
            'can_create' => auth()->user()->can('vehicle-models.general.create'),
            'can_edit' => auth()->user()->can('vehicle-models.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-models.general.delete'),
        ];

        return view('backoffice.vehicle-models.index', compact('models', 'brands', 'permissions'));
    }

    /**
     * Show the form for creating a new model.
     */
    public function create()
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-models.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des modèles.');
        }

        $vehicleModels = VehicleModel::where('agency_id', Auth::user()->agency_id)
            ->with('brand')
            ->orderBy('name')
            ->get();

        return redirect()->route('backoffice.vehicle-models.index');
    }

    /**
     * Store a newly created model.
     */
    public function store(VehicleModelStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-models.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des modèles.');
        }

        $data = $request->validated();
        $data['agency_id'] = Auth::user()->agency_id;

        $model = VehicleModel::create($data);
        
        $this->createNotification('store', 'vehicle-model', $model);

        return redirect()
            ->route('backoffice.vehicle-models.index')
            ->with('toast', [
                'title'   => 'Créé',
                'message' => 'Modèle de véhicule créé avec succès.',
                'dot'     => '#198754',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Display the specified model.
     */
    public function show(VehicleModel $vehicleModel)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-models.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les modèles.');
        }

        $this->authorize('view', $vehicleModel);

        $vehicleModel->load('brand');

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('vehicle-models.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-models.general.delete'),
        ];

        return redirect()->route('backoffice.vehicle-models.index');
    }

    /**
     * Show the form for editing the specified model.
     */
    public function edit(VehicleModel $vehicleModel)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-models.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les modèles.');
        }

        $this->authorize('update', $vehicleModel);

        $brands = VehicleBrand::where('agency_id', Auth::user()->agency_id)
            ->orderBy('name')
            ->get();

        return redirect()->route('backoffice.vehicle-models.index');
    }

    /**
     * Update the specified model.
     */
    public function update(VehicleModelUpdateRequest $request, VehicleModel $vehicleModel)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-models.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les modèles.');
        }

        $this->authorize('update', $vehicleModel);

        $vehicleModel->update([
            'name' => $request->name,
            'vehicle_brand_id' => $request->vehicle_brand_id,
            'is_active' => $request->status === 'active',
        ]);
        
        $this->createNotification('update', 'vehicle-model', $vehicleModel);

        return redirect()
            ->route('backoffice.vehicle-models.index')
            ->with('toast', [
                'title'   => 'Mis à jour',
                'message' => 'Modèle de véhicule mis à jour avec succès.',
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Remove the specified model.
     */
    public function destroy(VehicleModel $vehicleModel)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('vehicle-models.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les modèles.');
        }

        $this->authorize('delete', $vehicleModel);

        $modelData = clone $vehicleModel;
        $vehicleModel->delete();
        
        $this->createNotification('destroy', 'vehicle-model', $modelData);

        return redirect()
            ->route('backoffice.vehicle-models.index')
            ->with('toast', [
                'title'   => 'Supprimé',
                'message' => 'Modèle de véhicule supprimé.',
                'dot'     => '#dc3545',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }
}