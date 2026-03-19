<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\AgencySubscription\AgencySubscriptionStoreRequest;
use App\Http\Requests\Backoffice\AgencySubscription\AgencySubscriptionUpdateRequest;
use App\Models\Agency;
use App\Models\AgencySubscription;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AgencySubscriptionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of agency subscriptions.
     */
    public function index(): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('agency-subscriptions.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les abonnements.');
        }

        $query = AgencySubscription::query()->with('agency');

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('plan_name', 'like', '%' . $search . '%')
                  ->orWhereHas('agency', function ($sub) use ($search) {
                      $sub->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */
        if (request()->filled('status')) {
            switch (request('status')) {
                case 'active':
                    $query->where('is_active', true)
                          ->where(function ($q) {
                              $q->whereNull('ends_at')
                                ->orWhere('ends_at', '>=', now());
                          });
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->whereNotNull('ends_at')
                          ->where('ends_at', '<', now());
                    break;
                case 'trial':
                    $query->whereNotNull('trial_ends_at')
                          ->where('trial_ends_at', '>=', now());
                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PROVIDER FILTER
        |--------------------------------------------------------------------------
        */
        if (request()->filled('provider') && request('provider') !== 'all') {
            $query->where('provider', request('provider'));
        }

        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */
        if (request('sort') === 'az') {
            $query->orderBy('plan_name', 'asc');
        } elseif (request('sort') === 'za') {
            $query->orderBy('plan_name', 'desc');
        } else {
            $query->latest();
        }

        $subscriptions = $query->paginate(15)->withQueryString();

        $agencies = Agency::orderBy('name')->get(['id', 'name']);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('agency-subscriptions.general.view'),
            'can_create' => auth()->user()->can('agency-subscriptions.general.create'),
            'can_edit' => auth()->user()->can('agency-subscriptions.general.edit'),
            'can_delete' => auth()->user()->can('agency-subscriptions.general.delete'),
        ];

        return view('backoffice.agency-subscriptions.index', compact('subscriptions', 'agencies', 'permissions'));
    }

    /**
     * Show the form for creating a new subscription.
     */
    public function create(): View
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('agency-subscriptions.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des abonnements.');
        }

        $agencies = Agency::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return redirect()->route('backoffice.agency-subscriptions.index');
    }

    /**
     * Store a newly created subscription in storage.
     */
    public function store(AgencySubscriptionStoreRequest $request): RedirectResponse
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('agency-subscriptions.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des abonnements.');
        }

        $subscription = AgencySubscription::create($request->validated());
        $subscription->load('agency');

        $agencyName = $subscription->agency?->name ?? 'Agence';
        
        $this->createNotification('store', 'agency-subscription', $subscription);

        return redirect()
            ->route('backoffice.agency-subscriptions.index')
            ->with('toast', [
                'title'   => 'Créé',
                'message' => "L'abonnement de « {$agencyName} » a été créé avec succès.",
                'dot'     => '#198754',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Show the current agency's subscription
     */
    public function mySubscription(): View
    {
        $user = auth()->user();
        
        $agency = $user->agency;
        
        if (!$agency) {
            abort(404, 'Agence non trouvée');
        }
        
        $subscription = AgencySubscription::where('agency_id', $agency->id)
            ->with('agency')
            ->latest()
            ->first();
        
        return view('backoffice.profile.my-subscription', [
            'subscription' => $subscription,
            'agency' => $agency
        ]);
    }

    /**
     * Display the specified subscription.
     */
    public function show(AgencySubscription $agencySubscription): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('agency-subscriptions.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les abonnements.');
        }

        $agencySubscription->load('agency');

        $permissions = [
            'can_edit' => auth()->user()->can('agency-subscriptions.general.edit'),
            'can_delete' => auth()->user()->can('agency-subscriptions.general.delete'),
        ];

        return redirect()->route('backoffice.agency-subscriptions.index');
    }

    /**
     * Show the form for editing the specified subscription.
     */
    public function edit(AgencySubscription $agencySubscription): View
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('agency-subscriptions.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les abonnements.');
        }

        $agencySubscription->load('agency');

        $agencies = Agency::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return redirect()->route('backoffice.agency-subscriptions.index');
    }

    /**
     * Update the specified subscription in storage.
     */
    public function update(AgencySubscriptionUpdateRequest $request, AgencySubscription $agencySubscription): RedirectResponse
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('agency-subscriptions.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les abonnements.');
        }

        $agencySubscription->update($request->validated());
        $agencySubscription->load('agency');

        $agencyName = $agencySubscription->agency?->name ?? 'Agence';
        
        $this->createNotification('update', 'agency-subscription', $agencySubscription);

        return redirect()
            ->route('backoffice.agency-subscriptions.index')
            ->with('toast', [
                'title'   => 'Modifié',
                'message' => "L'abonnement de « {$agencyName} » a été modifié avec succès.",
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Remove the specified subscription from storage.
     */
    public function destroy(AgencySubscription $agencySubscription): RedirectResponse
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('agency-subscriptions.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les abonnements.');
        }

        $agencySubscription->load('agency');
        $agencyName = $agencySubscription->agency?->name ?? 'Agence';
        
        $subscriptionData = clone $agencySubscription;
        $agencySubscription->delete();
        
        $this->createNotification('destroy', 'agency-subscription', $subscriptionData);

        return redirect()
            ->route('backoffice.agency-subscriptions.index')
            ->with('toast', [
                'title'   => 'Supprimé',
                'message' => "L'abonnement de « {$agencyName} » a été supprimé avec succès.",
                'dot'     => '#dc3545',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }
}