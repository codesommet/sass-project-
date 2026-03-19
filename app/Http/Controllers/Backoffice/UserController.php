<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\User\UserStoreRequest;
use App\Http\Requests\Backoffice\User\UserUpdateRequest;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('users.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les utilisateurs.');
        }

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::guard('backoffice')->user();

        $query = User::query();

        // 🔒 Multi-tenant
        if (!$currentUser->hasRole('super-admin')) {
            $query->where('agency_id', $currentUser->agency_id);
        }

        // 🔎 SEARCH
        if (request()->filled('search')) {
            $search = request('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // 📌 STATUS FILTER
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        // 🔤 SORT
        if (request('sort') === 'az') {
            $query->orderBy('name', 'asc');
        } elseif (request('sort') === 'za') {
            $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate(15)->withQueryString();

        $agencies = collect();
        if ($currentUser->hasRole('super-admin')) {
            $agencies = Agency::orderBy('name')->get();
        }

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('users.general.view'),
            'can_create' => auth()->user()->can('users.general.create'),
            'can_edit' => auth()->user()->can('users.general.edit'),
            'can_delete' => auth()->user()->can('users.general.delete'),
        ];

        return view('backoffice.users.index', compact('users', 'agencies', 'permissions'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('users.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des utilisateurs.');
        }

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::guard('backoffice')->user();

        $agencies = collect();
        if ($currentUser->hasRole('super-admin')) {
            $agencies = Agency::query()->orderBy('name')->get();
        }

        return redirect()->route('backoffice.users.index');
    }

    /**
     * Store a newly created user.
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('users.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des utilisateurs.');
        }

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::guard('backoffice')->user();

        $data = $request->validated();

        // Force agency_id for non super-admin
        if (!$currentUser->hasRole('super-admin')) {
            $data['agency_id'] = $currentUser->agency_id;
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        // ✅ Media Library avatar
        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }
        
        $this->createNotification('store', 'user', $user);

        return redirect()
            ->route('backoffice.users.index')
            ->with('toast', [
                'title'   => 'Création réussie',
                'message' => "L'utilisateur « {$user->name} » a été créé avec succès.",
                'dot'     => '#198754',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('users.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les utilisateurs.');
        }

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::guard('backoffice')->user();

        if (!$currentUser->hasRole('super-admin') && $user->agency_id !== $currentUser->agency_id) {
            abort(403);
        }

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('users.general.edit'),
            'can_delete' => auth()->user()->can('users.general.delete'),
        ];

        return redirect()->route('backoffice.users.index');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('users.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les utilisateurs.');
        }

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::guard('backoffice')->user();

        if (!$currentUser->hasRole('super-admin') && $user->agency_id !== $currentUser->agency_id) {
            abort(403);
        }

        $agencies = collect();
        if ($currentUser->hasRole('super-admin')) {
            $agencies = Agency::query()->orderBy('name')->get();
        }

        return redirect()->route('backoffice.users.index');
    }

    /**
     * Update the specified user.
     */
    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('users.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les utilisateurs.');
        }

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::guard('backoffice')->user();

        if (!$currentUser->hasRole('super-admin') && $user->agency_id !== $currentUser->agency_id) {
            abort(403);
        }

        $data = $request->validated();

        // Force agency_id for non super-admin
        if (!$currentUser->hasRole('super-admin')) {
            $data['agency_id'] = $currentUser->agency_id;
        }

        // Password optional in update
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // ✅ Remove avatar if requested
        if ($request->boolean('remove_avatar')) {
            $user->clearMediaCollection('avatar');
        }

        // ✅ Replace avatar if uploaded
        if ($request->hasFile('avatar')) {
            $user->clearMediaCollection('avatar');
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }
        
        $this->createNotification('update', 'user', $user);

        return redirect()
            ->route('backoffice.users.index')
            ->with('toast', [
                'title'   => 'Modification réussie',
                'message' => "L'utilisateur « {$user->name} » a été modifié avec succès.",
                'dot'     => '#0d6efd',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('users.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les utilisateurs.');
        }

        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::guard('backoffice')->user();

        if (!$currentUser->hasRole('super-admin') && $user->agency_id !== $currentUser->agency_id) {
            abort(403);
        }

        $name = $user->name;
        
        $userData = clone $user;
        $user->delete();
        
        $this->createNotification('destroy', 'user', $userData);

        return redirect()
            ->route('backoffice.users.index')
            ->with('toast', [
                'title'   => 'Suppression réussie',
                'message' => "L'utilisateur « {$name} » a été supprimé avec succès.",
                'dot'     => '#dc3545',
                'delay'   => 3500,
                'time'    => 'now',
            ]);
    }
}