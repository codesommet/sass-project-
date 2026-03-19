<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Agency\AgencyStoreRequest;
use App\Http\Requests\Backoffice\Agency\AgencyUpdateRequest;
use App\Http\Requests\Backoffice\Agency\UpdateAgencyProfileRequest;
use App\Models\Agency;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of agencies.
     */
    public function index(): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('agencies.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les agences.');
        }

        $query = Agency::query();

        // SEARCH
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        // FAKE STATUS LOGIC
        if (request()->has('status')) {
            if (request('status') == 0) {
                $query->whereRaw('1 = 0');
            }
        }

        // SORT
        if (request('sort') === 'az') {
            $query->orderBy('name', 'asc');
        } elseif (request('sort') === 'za') {
            $query->orderBy('name', 'desc');
        } else {
            $query->latest();
        }

        $agencies = $query->paginate(15)->withQueryString();

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('agencies.general.view'),
            'can_create' => auth()->user()->can('agencies.general.create'),
            'can_edit' => auth()->user()->can('agencies.general.edit'),
            'can_delete' => auth()->user()->can('agencies.general.delete'),
        ];

        return view('backoffice.agencies.index', compact('agencies', 'permissions'));
    }

    /**
     * Show the form for creating a new agency.
     */
    public function create(): View
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('agencies.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des agences.');
        }

        return redirect()->route('backoffice.agencies.index');
    }

    /**
     * Store a newly created agency in storage.
     */
    public function store(AgencyStoreRequest $request): RedirectResponse
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('agencies.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des agences.');
        }
        
        $agency = Agency::create($request->validated());
        
        $this->createNotification('store', 'agency', $agency);

        return redirect()
            ->route('backoffice.agencies.index')
            ->with('toast', [
                'title'   => 'Créé',
                'message' => "L'agence « {$agency->name} » a été créée avec succès.",
                'dot'     => '#198754',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Display the specified agency.
     */
    public function show(Agency $agency): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('agencies.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les agences.');
        }

        // Charger les relations
        $agency->load(['users', 'subscription']);

        // Statistiques
        $stats = [
            'total_users' => $agency->users()->count(),
            'total_vehicles' => $agency->vehicles()->count(),
            'total_clients' => $agency->clients()->count(),
            'total_contracts' => $agency->contracts()->count(),
            'total_bookings' => $agency->bookings()->count(),
        ];

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('agencies.general.edit'),
            'can_delete' => auth()->user()->can('agencies.general.delete'),
        ];

        return redirect()->route('backoffice.agencies.index');
    }

    /**
     * Show the form for editing the specified agency.
     */
    public function edit(Agency $agency): View
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('agencies.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les agences.');
        }

        return redirect()->route('backoffice.agencies.index');
    }

    /**
     * Update the specified agency in storage.
     */
    public function update(AgencyUpdateRequest $request, Agency $agency): RedirectResponse
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('agencies.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les agences.');
        }

        $agency->update($request->validated());
        
        $this->createNotification('update', 'agency', $agency);

        return redirect()
            ->route('backoffice.agencies.index')
            ->with('toast', [
                'title'   => 'Modifié',
                'message' => "L'agence « {$agency->name} » a été modifiée avec succès.",
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Remove the specified agency from storage.
     */
    public function destroy(Agency $agency): RedirectResponse
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('agencies.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les agences.');
        }

        $name = $agency->name;
        
        $agencyData = clone $agency;
        $agency->delete();
        
        $this->createNotification('destroy', 'agency', $agencyData);

        return redirect()
            ->route('backoffice.agencies.index')
            ->with('toast', [
                'title'   => 'Supprimé',
                'message' => "L'agence « {$name} » a été supprimée avec succès.",
                'dot'     => '#dc3545',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Show the agency profile settings page
     */
    public function profile(Agency $agency): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('agency-settings.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les paramètres de l\'agence.');
        }
        
        $user = auth()->user();

        return view('backoffice.profile.profile-setting', [
            'agency' => $agency,
            'user' => $user,
        ]);
    }

    /**
     * Update agency profile
     */
    public function updateProfile(UpdateAgencyProfileRequest $request, Agency $agency): RedirectResponse
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('agency-settings.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les paramètres de l\'agence.');
        }

        $agency->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
        ]);

        // Handle logo upload
        if ($request->hasFile('profile_photo')) {
            $agency->clearMediaCollection('logo');
            $agency->addMediaFromRequest('profile_photo')
                ->toMediaCollection('logo');
        }

        $this->createNotification('update', 'agency_profile', $agency);

        return redirect()
            ->route('backoffice.agencies.settings.profile', $agency)
            ->with('toast', [
                'title'   => 'Modifié',
                'message' => "Le profil de l'agence « {$agency->name} » a été mis à jour avec succès.",
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }
}