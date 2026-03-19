@extends('layout.mainlayout_admin')
@section('content')
    <div class="page-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header">
                <div class="add-item d-flex" style="justify-content: space-between; align-items: center;">
                    <div>
                        <h3>Calendrier des réservations</h3>
                        <ul class="breadcrumb">
                            <li><a href="{{ route('backoffice.dashboard') }}">Accueil</a></li>
                            <li class="active">Calendrier des réservations</li>
                        </ul>
                    </div>
                    <a href="{{ route('backoffice.bookings.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Nouvelle réservation
                    </a>
                </div>
            </div>
            <!-- /Page Header -->

            <!-- Calendar Section -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Calendrier des réservations</h5>
                        </div>
                        <div class="card-body">
                            <div id="calendar" style="background: white; padding: 20px; border-radius: 5px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des réservations -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Toutes les réservations</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Véhicule</th>
                                            <th>Date de début</th>
                                            <th>Date de fin</th>
                                            <th>Lieu de prise en charge</th>
                                            <th>Lieu de retour</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bookings as $booking)
                                            <tr>
                                                <td>
                                                    <strong>
                                                        {{ $booking->client->first_name ?? 'N/C' }}
                                                        {{ $booking->client->last_name ?? '' }}
                                                    </strong>
                                                </td>
                                                <td>{{ $booking->vehicle->registration_number ?? 'N/C' }}</td>
                                                <td>{{ $booking->start_date->format('d/m/Y') }}</td>
                                                <td>{{ $booking->end_date->format('d/m/Y') }}</td>
                                                <td>{{ $booking->pickup_location }}</td>
                                                <td>{{ $booking->dropoff_location }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                                        @switch($booking->status)
                                                            @case('pending') En attente @break
                                                            @case('confirmed') Confirmé @break
                                                            @case('cancelled') Annulé @break
                                                            @case('converted') Converti @break
                                                            @default {{ ucfirst($booking->status) }}
                                                        @endswitch
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('backoffice.bookings.show', $booking) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('backoffice.bookings.edit', $booking) }}"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    Aucune réservation trouvée. <a
                                                        href="{{ route('backoffice.bookings.create') }}">Créer une réservation</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- FullCalendar Library -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var events = [
                @foreach ($bookings as $booking)
                    {
                        title: '{{ $booking->client->first_name ?? 'Réservation' }} - {{ $booking->vehicle->registration_number ?? 'Véhicule' }}',
                        start: '{{ $booking->start_date->format('Y-m-d') }}',
                        end: '{{ $booking->end_date->format('Y-m-d\TH:i:s') }}',
                        backgroundColor: '{{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : '#dc3545') }}',
                        borderColor: '{{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : '#dc3545') }}',
                        extendedProps: {
                            bookingId: {{ $booking->id }},
                            client: '{{ $booking->client->first_name ?? '' }} {{ $booking->client->last_name ?? '' }}',
                            vehicle: '{{ $booking->vehicle->registration_number ?? '' }}',
                            pickupLocation: '{{ $booking->pickup_location }}',
                            dropoffLocation: '{{ $booking->dropoff_location }}',
                            status: '{{ $booking->status }}'
                        }
                    },
                @endforeach
            ];

            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'fr',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                events: events,
                eventClick: function(info) {
                    var event = info.event;
                    var props = event.extendedProps;
                    alert(
                        'Détails de la réservation :\n\n' +
                        'Client : ' + props.client + '\n' +
                        'Véhicule : ' + props.vehicle + '\n' +
                        'Prise en charge : ' + props.pickupLocation + '\n' +
                        'Restitution : ' + props.dropoffLocation + '\n' +
                        'Statut : ' + props.status
                    );
                },
                datesSet: function(info) {
                    console.log('Dates du calendrier changées', info.start, info.end);
                }
            });

            calendar.render();
        });
    </script>

    <style>
        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
    </style>
@endsection
