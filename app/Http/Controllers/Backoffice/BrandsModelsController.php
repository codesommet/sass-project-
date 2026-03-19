<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BrandsModelsController extends Controller
{
    public function index(Request $request): View
    {
        $activeTab = $request->get('tab', 'brands');
        if (!in_array($activeTab, ['brands', 'models'])) {
            $activeTab = 'brands';
        }

        $agencyId = Auth::user()->agency_id;

        $counts = [
            'brands' => VehicleBrand::where('agency_id', $agencyId)->count(),
            'models' => VehicleModel::where('agency_id', $agencyId)->count(),
        ];

        // Load both tabs' data — search/filters only apply to active tab
        $data = compact('activeTab', 'counts');
        $data = array_merge($data, $this->getBrandsData($request, $agencyId, $activeTab === 'brands'));
        $data = array_merge($data, $this->getModelsData($request, $agencyId, $activeTab === 'models'));

        return view('backoffice.brands-models.index', $data);
    }

    private function getBrandsData(Request $request, $agencyId, bool $applyFilters): array
    {
        $query = VehicleBrand::where('agency_id', $agencyId)->with('vehicles');

        if ($applyFilters && $request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        return [
            'brands' => $query->latest()->paginate(15, ['*'], 'brands_page')->withQueryString(),
            'brandsPermissions' => [
                'can_view' => auth()->user()->can('vehicle-brands.general.view'),
                'can_create' => auth()->user()->can('vehicle-brands.general.create'),
                'can_edit' => auth()->user()->can('vehicle-brands.general.edit'),
                'can_delete' => auth()->user()->can('vehicle-brands.general.delete'),
            ],
        ];
    }

    private function getModelsData(Request $request, $agencyId, bool $applyFilters): array
    {
        $query = VehicleModel::where('agency_id', $agencyId)->with('brand');

        if ($applyFilters && $request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('brand', function ($brandQuery) use ($search) {
                      $brandQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $allBrands = VehicleBrand::where('agency_id', $agencyId)->orderBy('name')->get();

        return [
            'models' => $query->latest()->paginate(15, ['*'], 'models_page')->withQueryString(),
            'allBrands' => $allBrands,
            'modelsPermissions' => [
                'can_view' => auth()->user()->can('vehicle-models.general.view'),
                'can_create' => auth()->user()->can('vehicle-models.general.create'),
                'can_edit' => auth()->user()->can('vehicle-models.general.edit'),
                'can_delete' => auth()->user()->can('vehicle-models.general.delete'),
            ],
        ];
    }
}
