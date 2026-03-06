<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Agent\AgentStoreRequest;
use App\Http\Requests\Backoffice\Agent\AgentUpdateRequest;
use App\Models\Agent;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentController extends Controller
{
    /**
     * Display a listing of the agents.
     */
    public function index(Request $request): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('agents.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les agents.');
        }

        $user = auth()->user();
        $userAgencyId = $user->agency_id; // Get the current user's agency ID

        $query = Agent::with(['agency', 'user']);

        // 👥 FILTER BY CURRENT USER'S AGENCY
        // If user is not super admin, only show agents from their agency
        if (!$user->hasRole('super-admin')) {
            $query->where('agency_id', $userAgencyId);
        }

        // 🔎 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('agency', function ($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 🏢 FILTER BY AGENCY (only if user is super-admin)
        if ($user->hasRole('super-admin') && $request->filled('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        // 🔤 SORT
        if ($request->get('sort') === 'az') {
            $query->orderBy('full_name', 'asc');
        } elseif ($request->get('sort') === 'za') {
            $query->orderBy('full_name', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $agents = $query->paginate(15)->withQueryString();

        // Get agencies for filter dropdown - only show relevant agencies
        if ($user->hasRole('super-admin')) {
            // Super-admin can see all agencies
            $agencies = Agency::all();
        } else {
            // Regular users only see their own agency
            $agencies = Agency::where('id', $userAgencyId)->get();
        }

        // Get users for filter dropdown - only show users from the same agency
        $usersQuery = User::query();
        if (!$user->hasRole('super-admin')) {
            $usersQuery->where('agency_id', $userAgencyId);
        }
        $users = $usersQuery->get();

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('agents.general.view'),
            'can_create' => auth()->user()->can('agents.general.create'),
            'can_edit' => auth()->user()->can('agents.general.edit'),
            'can_delete' => auth()->user()->can('agents.general.delete'),
        ];

        return view('backoffice.agents.index', compact('agents', 'agencies', 'users', 'permissions'));
    }

    /**
     * Show the form for creating a new agent.
     */
    public function create()
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('agents.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des agents.');
        }

        $user = auth()->user();
        $userAgencyId = $user->agency_id;

        // Only show agencies the user has access to
        if ($user->hasRole('super-admin')) {
            $agencies = Agency::all();
        } else {
            $agencies = Agency::where('id', $userAgencyId)->get();
        }

        // Only show users from the same agency
        $usersQuery = User::query();
        if (!$user->hasRole('super-admin')) {
            $usersQuery->where('agency_id', $userAgencyId);
        }
        $users = $usersQuery->get();

        return view('backoffice.agents.partials._modal_create', compact('agencies', 'users'));
    }

    /**
     * Store a newly created agent in storage.
     */
    public function store(AgentStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('agents.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des agents.');
        }

        $validated = $request->validated();

        // Ensure the agent is created for the user's agency if not super-admin
        $user = auth()->user();
        if (!$user->hasRole('super-admin')) {
            $validated['agency_id'] = $user->agency_id;
        }

        try {
            DB::beginTransaction();

            // Extract avatar file from validated data
            $avatar = $validated['avatar'] ?? null;
            unset($validated['avatar']);

            // Create agent with non-file fields
            $agent = Agent::create($validated);

            // Attach avatar to media collection if provided
            if ($avatar) {
                $agent->addMediaFromRequest('avatar')
                    ->toMediaCollection('agent_avatar');
            }
            
            $this->createNotification('store', 'agent', $agent);

            DB::commit();

            return redirect()
                ->route('backoffice.agents.index')
                ->with('toast', [
                    'title'   => 'Créé',
                    'message' => 'Agent créé avec succès.',
                    'dot'     => '#198754',
                    'delay'   => 3500,
                    'time'    => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('toast', [
                    'title'   => 'Erreur',
                    'message' => 'Erreur lors de la création: ' . $e->getMessage(),
                    'dot'     => '#dc3545',
                    'delay'   => 3500,
                    'time'    => 'now',
                ]);
        }
    }

    /**
     * Display the specified agent.
     */
    public function show(Agent $agent)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('agents.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les agents.');
        }

        // Check if user has access to this agent
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $agent->agency_id !== $user->agency_id) {
            abort(403, 'Vous n\'avez pas accès à cet agent.');
        }

        $agent->load(['agency', 'user']);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('agents.general.edit'),
            'can_delete' => auth()->user()->can('agents.general.delete'),
        ];

        return view('backoffice.agents.show', compact('agent', 'permissions'));
    }

    /**
     * Show the form for editing the specified agent.
     */
    public function edit(Agent $agent)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('agents.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les agents.');
        }

        // Check if user has access to this agent
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $agent->agency_id !== $user->agency_id) {
            abort(403, 'Vous n\'avez pas accès à cet agent.');
        }

        $agent->load(['agency', 'user']);
        
        // Only show agencies the user has access to
        if ($user->hasRole('super-admin')) {
            $agencies = Agency::all();
        } else {
            $agencies = Agency::where('id', $user->agency_id)->get();
        }

        // Only show users from the same agency
        $usersQuery = User::query();
        if (!$user->hasRole('super-admin')) {
            $usersQuery->where('agency_id', $user->agency_id);
        }
        $users = $usersQuery->get();

        return view('backoffice.agents.partials._modal_edit', compact('agent', 'agencies', 'users'));
    }

    /**
     * Update the specified agent in storage.
     */
    public function update(AgentUpdateRequest $request, Agent $agent)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('agents.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les agents.');
        }

        // Check if user has access to this agent
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $agent->agency_id !== $user->agency_id) {
            abort(403, 'Vous n\'avez pas accès à cet agent.');
        }

        $validated = $request->validated();

        // Ensure the agent stays in the user's agency if not super-admin
        if (!$user->hasRole('super-admin')) {
            $validated['agency_id'] = $user->agency_id;
        }

        try {
            DB::beginTransaction();

            // Extract avatar file from validated data
            $avatar = $validated['avatar'] ?? null;
            unset($validated['avatar']);
            unset($validated['remove_avatar']);

            // Update non-file fields
            $agent->update($validated);

            // Handle avatar removal
            if ($request->boolean('remove_avatar')) {
                $agent->clearMediaCollection('agent_avatar');
            }

            // Handle avatar update
            if ($avatar) {
                $agent->clearMediaCollection('agent_avatar');
                $agent->addMediaFromRequest('avatar')
                    ->toMediaCollection('agent_avatar');
            }
            
            $this->createNotification('update', 'agent', $agent);

            DB::commit();

            return redirect()
                ->route('backoffice.agents.index')
                ->with('toast', [
                    'title'   => 'Mis à jour',
                    'message' => 'Agent mis à jour avec succès.',
                    'dot'     => '#0d6efd',
                    'delay'   => 3500,
                    'time'    => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('toast', [
                    'title'   => 'Erreur',
                    'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage(),
                    'dot'     => '#dc3545',
                    'delay'   => 3500,
                    'time'    => 'now',
                ]);
        }
    }

    /**
     * Remove the specified agent from storage.
     */
    public function destroy(Agent $agent)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('agents.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les agents.');
        }

        // Check if user has access to this agent
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $agent->agency_id !== $user->agency_id) {
            abort(403, 'Vous n\'avez pas accès à cet agent.');
        }

        try {
            DB::beginTransaction();

            // Store agent data for notification before delete
            $agentData = clone $agent;
            
            // Media Library will automatically delete associated media on model deletion
            $agent->delete();
            
            $this->createNotification('destroy', 'agent', $agentData);

            DB::commit();

            return redirect()
                ->route('backoffice.agents.index')
                ->with('toast', [
                    'title'   => 'Supprimé',
                    'message' => 'Agent supprimé avec succès.',
                    'dot'     => '#dc3545',
                    'delay'   => 3500,
                    'time'    => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('toast', [
                    'title'   => 'Erreur',
                    'message' => 'Erreur lors de la suppression: ' . $e->getMessage(),
                    'dot'     => '#dc3545',
                    'delay'   => 3500,
                    'time'    => 'now',
                ]);
        }
    }
}