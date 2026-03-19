<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\Vehicle;
use App\Http\Requests\Backoffice\Booking\BookingStoreRequest;
use App\Http\Requests\Backoffice\Booking\BookingUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the bookings.
     */
    public function index(Request $request)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('bookings.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les réservations.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $query = Booking::where('agency_id', $agencyId)
            ->with(['client', 'vehicle']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($sub) use ($search) {
                    $sub->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('vehicle', function ($sub) use ($search) {
                    $sub->where('registration_number', 'like', "%{$search}%");
                })->orWhere('pickup_location', 'like', "%{$search}%")
                    ->orWhere('dropoff_location', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'start_date_asc') {
            $query->orderBy('start_date', 'asc');
        } elseif ($sort === 'start_date_desc') {
            $query->orderBy('start_date', 'desc');
        } elseif ($sort === 'amount_asc') {
            $query->orderBy('estimated_total', 'asc');
        } elseif ($sort === 'amount_desc') {
            $query->orderBy('estimated_total', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $bookings = $query->paginate(15)->withQueryString();

        // Get clients, vehicles for filters
        $clients = Client::where('agency_id', $agencyId)->orderBy('first_name')->get();
        $vehicles = Vehicle::where('agency_id', $agencyId)->orderBy('registration_number')->get();

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_view' => auth()->user()->can('bookings.general.view'),
            'can_create' => auth()->user()->can('bookings.general.create'),
            'can_edit' => auth()->user()->can('bookings.general.edit'),
            'can_delete' => auth()->user()->can('bookings.general.delete'),
            'can_convert' => auth()->user()->can('bookings.general.edit'), // Convertir nécessite permission edit
        ];

        return view('backoffice.bookings.index', compact('bookings', 'clients', 'vehicles', 'permissions'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('bookings.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des réservations.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $clients = Client::where('agency_id', $agencyId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $vehicles = Vehicle::where('agency_id', $agencyId)
            ->where('status', 'available')
            ->orderBy('registration_number')
            ->get();

        return view('backoffice.bookings.partials._modal_create', compact('clients', 'vehicles'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(BookingStoreRequest $request)
    {
        // ✅ Vérifier la permission CREATE
        if (!auth()->user()->can('bookings.general.create')) {
            abort(403, 'Vous n\'avez pas la permission de créer des réservations.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Add agency
            $data['agency_id'] = Auth::guard('backoffice')->user()->agency_id;

            // Calculate booked days (include both start and end day)
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $data['booked_days'] = max($startDate->diffInDays($endDate) + 1, 1);

            // Set datetime fields
            $data['start_at'] = Carbon::parse($data['start_date'] . ' 00:00:00');
            $data['end_at'] = Carbon::parse($data['end_date'] . ' 23:59:59');

            // If vehicle is selected and estimated_total not provided, calculate it
            if (!empty($data['vehicle_id']) && empty($data['estimated_total'])) {
                $vehicle = Vehicle::find($data['vehicle_id']);
                if ($vehicle && $vehicle->daily_rate) {
                    $data['estimated_total'] = $vehicle->daily_rate * $data['booked_days'];
                }
            }

            $booking = Booking::create($data);

            $this->createNotification('store', 'booking', $booking);

            DB::commit();

            return redirect()
                ->route('backoffice.bookings.show', $booking)
                ->with('toast', [
                    'title' => 'Créé',
                    'message' => 'Réservation créée avec succès.',
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
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('bookings.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les réservations.');
        }

        $booking->load(['client', 'vehicle', 'agency']);

        // ✅ Passer les permissions à la vue
        $permissions = [
            'can_edit' => auth()->user()->can('bookings.general.edit'),
            'can_delete' => auth()->user()->can('bookings.general.delete'),
            'can_convert' => auth()->user()->can('bookings.general.edit'), // Convertir nécessite permission edit
        ];

        return view('backoffice.bookings.show', compact('booking', 'permissions'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('bookings.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les réservations.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $clients = Client::where('agency_id', $agencyId)->orderBy('first_name')->get();
        $vehicles = Vehicle::where('agency_id', $agencyId)->orderBy('registration_number')->get();

        return view('backoffice.bookings.partials._modal_edit', compact('booking', 'clients', 'vehicles'));
    }

    /**
     * Update the specified booking.
     */
    public function update(BookingUpdateRequest $request, Booking $booking)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('bookings.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les réservations.');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Recalculate booked days if dates changed
            if (isset($data['start_date']) || isset($data['end_date'])) {
                $startDate = isset($data['start_date']) ? Carbon::parse($data['start_date']) : $booking->start_date;
                $endDate = isset($data['end_date']) ? Carbon::parse($data['end_date']) : $booking->end_date;
                $data['booked_days'] = max($startDate->diffInDays($endDate) + 1, 1);

                // Update datetime fields
                $data['start_at'] = Carbon::parse($startDate->format('Y-m-d') . ' 00:00:00');
                $data['end_at'] = Carbon::parse($endDate->format('Y-m-d') . ' 23:59:59');
            }

            // If vehicle changed and estimated_total not provided, recalculate
            if (isset($data['vehicle_id']) && $data['vehicle_id'] != $booking->vehicle_id && empty($data['estimated_total'])) {
                $vehicle = Vehicle::find($data['vehicle_id']);
                if ($vehicle && $vehicle->daily_rate) {
                    $days = $data['booked_days'] ?? $booking->booked_days;
                    $data['estimated_total'] = $vehicle->daily_rate * $days;
                }
            }

            $booking->update($data);

            $this->createNotification('update', 'booking', $booking);

            DB::commit();

            return redirect()
                ->route('backoffice.bookings.show', $booking)
                ->with('toast', [
                    'title' => 'Mis à jour',
                    'message' => 'Réservation mise à jour avec succès.',
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
     * Remove the specified booking.
     */
    public function destroy(Booking $booking)
    {
        // ✅ Vérifier la permission DELETE
        if (!auth()->user()->can('bookings.general.delete')) {
            abort(403, 'Vous n\'avez pas la permission de supprimer les réservations.');
        }

        try {
            DB::beginTransaction();

            $bookingData = clone $booking;
            $booking->delete();

            $this->createNotification('destroy', 'booking', $bookingData);

            DB::commit();

            return redirect()
                ->route('backoffice.bookings.index')
                ->with('toast', [
                    'title' => 'Supprimé',
                    'message' => 'Réservation supprimée avec succès.',
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
     * Update booking status.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        // ✅ Vérifier la permission EDIT
        if (!auth()->user()->can('bookings.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de modifier les réservations.');
        }

        $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,converted'],
        ]);

        try {
            $oldStatus = $booking->status;
            $booking->update(['status' => $request->status]);

            $this->createNotification('status', 'booking', $booking);

            return redirect()
                ->route('backoffice.bookings.show', $booking)
                ->with('toast', [
                    'title' => 'Statut mis à jour',
                    'message' => 'Le statut de la réservation a été mis à jour.',
                    'dot' => '#0d6efd',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la mise à jour du statut: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Convert booking to contract.
     */
    public function convertToContract(Booking $booking)
    {
        // ✅ Vérifier la permission EDIT (pour convertir)
        if (!auth()->user()->can('bookings.general.edit')) {
            abort(403, 'Vous n\'avez pas la permission de convertir les réservations.');
        }

        try {
            DB::beginTransaction();

            // Update booking status
            $booking->update(['status' => 'converted']);

            $this->createNotification('status', 'booking', $booking);

            DB::commit();

            return redirect()
                ->route('backoffice.rental-contracts.create', [
                    'client_id' => $booking->client_id,
                    'vehicle_id' => $booking->vehicle_id,
                    'start_date' => $booking->start_date->format('Y-m-d'),
                    'end_date' => $booking->end_date->format('Y-m-d'),
                    'pickup_location' => $booking->pickup_location,
                    'dropoff_location' => $booking->dropoff_location,
                ])
                ->with('toast', [
                    'title' => 'Converti',
                    'message' => 'Réservation convertie en contrat.',
                    'dot' => '#198754',
                    'delay' => 3500,
                    'time' => 'now',
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('toast', [
                'title' => 'Erreur',
                'message' => 'Erreur lors de la conversion: ' . $e->getMessage(),
                'dot' => '#dc3545',
                'delay' => 3500,
                'time' => 'now',
            ]);
        }
    }

    /**
     * Display booking calendar view
     */
    public function calendar(Request $request)
    {
        // ✅ Vérifier la permission VIEW
        if (!auth()->user()->can('bookings.general.view')) {
            abort(403, 'Vous n\'avez pas la permission de voir les réservations.');
        }

        $agencyId = Auth::guard('backoffice')->user()->agency_id;

        $bookings = Booking::where('agency_id', $agencyId)
            ->with(['client', 'vehicle'])
            ->get();

        return view('backoffice.bookings.calendar', [
            'bookings' => $bookings,
        ]);
    }
}