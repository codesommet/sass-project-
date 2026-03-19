{{--
    Status Badge Component
    Usage: <x-backoffice.status-badge status="active" />
    Usage: <x-backoffice.status-badge status="pending" :custom-map="['pending' => ['En attente', 'warning']]" />
--}}
@props(['status', 'customMap' => []])

@php
    $defaultMap = [
        // General
        'active'        => ['Actif', 'success'],
        'inactive'      => ['Inactif', 'secondary'],
        'blacklisted'   => ['Blacklisté', 'danger'],
        'blocked'       => ['Bloqué', 'danger'],

        // Bookings / Contracts
        'pending'       => ['En attente', 'warning'],
        'confirmed'     => ['Confirmé', 'info'],
        'cancelled'     => ['Annulé', 'danger'],
        'converted'     => ['Converti', 'primary'],
        'draft'         => ['Brouillon', 'secondary'],
        'in_progress'   => ['En cours', 'info'],
        'completed'     => ['Terminé', 'success'],

        // Vehicles
        'available'     => ['Disponible', 'success'],
        'unavailable'   => ['Indisponible', 'secondary'],
        'maintenance'   => ['Maintenance', 'warning'],
        'rented'        => ['Loué', 'info'],
        'sold'          => ['Vendu', 'dark'],

        // Payments / Invoices
        'paid'          => ['Payé', 'success'],
        'unpaid'        => ['Impayé', 'danger'],
        'partially_paid'=> ['Partiel', 'warning'],
        'overdue'       => ['En retard', 'danger'],

        // Acceptance
        'accepted'      => ['Accepté', 'success'],
        'rejected'      => ['Refusé', 'danger'],
    ];

    $map = array_merge($defaultMap, $customMap);
    $config = $map[$status] ?? [ucfirst($status), 'secondary'];
    [$label, $color] = $config;
@endphp

<span class="badge bg-{{ $color }}-transparent">
    <i class="ti ti-point-filled text-{{ $color }} me-1"></i>
    {{ $label }}
</span>
