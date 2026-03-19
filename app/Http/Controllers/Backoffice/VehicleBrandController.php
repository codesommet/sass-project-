<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\VehicleBrand;
use App\Http\Requests\Backoffice\VehicleBrand\VehicleBrandStoreRequest;
use App\Http\Requests\Backoffice\VehicleBrand\VehicleBrandUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class VehicleBrandController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of vehicle brands.
     */
    public function index(Request $request)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-brands.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les marques.');
        }

        $query = VehicleBrand::where('agency_id', Auth::user()->agency_id)
            ->with('vehicles'); // Eager load vehicles for count

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $brands = $query->latest()->paginate(15);
        
        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('vehicle-brands.general.view'),
            'can_create' => auth()->user()->can('vehicle-brands.general.create'),
            'can_edit' => auth()->user()->can('vehicle-brands.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-brands.general.delete'),
        ];

        return view('backoffice.vehicle-brands.index', compact('brands', 'permissions'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-brands.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des marques.');
        }

        return redirect()->route('backoffice.vehicle-brands.index');
    }

    /**
     * Store a newly created brand.
     */
    public function store(VehicleBrandStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('vehicle-brands.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des marques.');
        }

        $data = $request->validated();
        $data['agency_id'] = Auth::user()->agency_id;

        // Extract logo file from validated data
        $logo = $data['logo'] ?? null;
        unset($data['logo']);

        // Create brand with non-file fields
        $brand = VehicleBrand::create($data);

        // Attach logo to media collection if provided
        if ($logo) {
            $brand->addMediaFromRequest('logo')
                ->toMediaCollection('vehicle_brand_logo');
        }
        
        $this->createNotification('store', 'vehicle-brand', $brand);

        return redirect()
            ->route('backoffice.vehicle-brands.index')
            ->with('toast', [
                'title'   => 'Créée',
                'message' => 'Marque de véhicule créée avec succès.',
                'dot'     => '#198754',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Display the specified brand.
     */
    public function show(VehicleBrand $vehicleBrand)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('vehicle-brands.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les marques.');
        }

        $this->authorize('view', $vehicleBrand);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('vehicle-brands.general.edit'),
            'can_delete' => auth()->user()->can('vehicle-brands.general.delete'),
        ];

        return redirect()->route('backoffice.vehicle-brands.index');
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(VehicleBrand $vehicleBrand)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-brands.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les marques.');
        }

        $this->authorize('update', $vehicleBrand);
        return redirect()->route('backoffice.vehicle-brands.index');
    }

    /**
     * Update the specified brand.
     */
    public function update(VehicleBrandUpdateRequest $request, VehicleBrand $vehicleBrand)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('vehicle-brands.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les marques.');
        }

        $this->authorize('update', $vehicleBrand);

        $data = $request->validated();

        // Extract logo file from validated data
        $logo = $data['logo'] ?? null;
        unset($data['logo']);

        // Update non-file fields
        $vehicleBrand->update($data);

        // Handle logo update
        if ($logo) {
            $vehicleBrand->clearMediaCollection('vehicle_brand_logo');
            $vehicleBrand->addMediaFromRequest('logo')
                ->toMediaCollection('vehicle_brand_logo');
        }
        
        $this->createNotification('update', 'vehicle-brand', $vehicleBrand);

        return redirect()
            ->route('backoffice.vehicle-brands.index')
            ->with('toast', [
                'title'   => 'Mise à jour',
                'message' => 'Marque de véhicule mise à jour avec succès.',
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Remove the specified brand.
     */
    public function destroy(VehicleBrand $vehicleBrand)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('vehicle-brands.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les marques.');
        }

        $this->authorize('delete', $vehicleBrand);

        $brandData = clone $vehicleBrand;
        $vehicleBrand->delete();
        
        $this->createNotification('destroy', 'vehicle-brand', $brandData);

        return redirect()
            ->route('backoffice.vehicle-brands.index')
            ->with('toast', [
                'title'   => 'Supprimée',
                'message' => 'Marque de véhicule supprimée.',
                'dot'     => '#dc3545',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }
}