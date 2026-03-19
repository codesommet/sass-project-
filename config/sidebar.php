<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sidebar Navigation Configuration
    |--------------------------------------------------------------------------
    |
    | This file defines the sidebar menu structure for the backoffice.
    | Each section contains menu items with their route, icon, permission,
    | and optional children (submenus).
    |
    */

    'sections' => [

        // ─── ACCUEIL ────────────────────────────────────────────
        'accueil' => [
            'title' => 'Accueil',
            'items' => [
                [
                    'label'      => 'Tableau de bord',
                    'icon'       => 'ti ti-layout-dashboard',
                    'route'      => 'backoffice.dashboard',
                    'routeMatch' => 'backoffice.dashboard*',
                    'permission' => 'dashboard.general.view',
                ],
                [
                    'label'      => 'Notifications',
                    'icon'       => 'ti ti-bell',
                    'route'      => 'backoffice.notifications.index',
                    'routeMatch' => 'backoffice.notifications.*',
                    'badge'      => 'unread_notifications',
                ],
            ],
        ],

        // ─── GESTION DES RÉSERVATIONS ───────────────────────────
        'reservations' => [
            'title'      => 'Gestion des Réservations',
            'permission' => ['bookings.general.view', 'rental-contracts.general.view'],
            'items'      => [
                [
                    'label'      => 'Réservations',
                    'icon'       => 'ti ti-calendar-event',
                    'route'      => 'backoffice.bookings.index',
                    'routeMatch' => ['backoffice.bookings.index', 'backoffice.bookings.show', 'backoffice.bookings.create', 'backoffice.bookings.edit'],
                    'permission' => 'bookings.general.view',
                ],
                [
                    'label'      => 'Calendrier',
                    'icon'       => 'ti ti-calendar-bolt',
                    'route'      => 'backoffice.bookings.calendar',
                    'routeMatch' => 'backoffice.bookings.calendar',
                    'permission' => 'bookings.general.view',
                ],
                [
                    'label'      => 'Contrats de location',
                    'icon'       => 'ti ti-file-text',
                    'route'      => 'backoffice.rental-contracts.index',
                    'routeMatch' => 'backoffice.rental-contracts.*',
                    'permission' => 'rental-contracts.general.view',
                ],
            ],
        ],

        // ─── GESTION DES CLIENTS ────────────────────────────────
        'clients' => [
            'title'      => 'Gestion des Clients',
            'permission' => 'clients.general.view',
            'items'      => [
                [
                    'label'      => 'Tous les clients',
                    'icon'       => 'ti ti-users-group',
                    'route'      => 'backoffice.clients.index',
                    'routeMatch' => ['backoffice.clients.index', 'backoffice.clients.show', 'backoffice.clients.create', 'backoffice.clients.edit'],
                    'permission' => 'clients.general.view',
                ],
                [
                    'label'      => 'Liste noire',
                    'icon'       => 'ti ti-user-x',
                    'route'      => 'backoffice.clients.blacklist.index',
                    'routeMatch' => 'backoffice.clients.blacklist.*',
                    'permission' => 'clients.general.view',
                ],
            ],
        ],

        // ─── GESTION DES VOITURES ───────────────────────────────
        'voitures' => [
            'title'      => 'Gestion des Voitures',
            'permission' => ['vehicles.general.view', 'vehicle-brands.general.view', 'vehicle-models.general.view'],
            'items'      => [
                [
                    'label'      => 'Véhicules',
                    'icon'       => 'ti ti-car',
                    'route'      => 'backoffice.vehicles.index',
                    'routeMatch' => 'backoffice.vehicles.*',
                    'permission' => 'vehicles.general.view',
                ],
                [
                    'label'      => 'Marques & Modèles',
                    'icon'       => 'ti ti-tags',
                    'route'      => 'backoffice.brands-models.index',
                    'routeMatch' => ['backoffice.brands-models.*', 'backoffice.vehicle-brands.*', 'backoffice.vehicle-models.*'],
                    'permission' => ['vehicle-brands.general.view', 'vehicle-models.general.view'],
                ],
                [
                    'label'      => 'Entretien & Documents',
                    'icon'       => 'ti ti-tool',
                    'route'      => 'backoffice.vehicle-documents.index',
                    'routeMatch' => 'backoffice.vehicle-documents.*',
                    'permission' => ['vehicle-vignettes.general.view', 'vehicle-insurances.general.view', 'vehicle-technical-checks.general.view', 'vehicle-oil-changes.general.view'],
                ],
                [
                    'label'      => 'Contrôles véhicules',
                    'icon'       => 'ti ti-clipboard-check',
                    'route'      => 'backoffice.controls.index',
                    'routeMatch' => 'backoffice.controls.*',
                    'permission' => 'vehicle-controls.general.view',
                ],
                [
                    'label'      => 'Crédits véhicules',
                    'icon'       => 'ti ti-receipt-2',
                    'route'      => 'backoffice.vehicle-credits.index',
                    'routeMatch' => 'backoffice.vehicle-credits.*',
                    'permission' => 'vehicle-credits.general.view',
                ],
            ],
        ],

        // ─── GESTION FINANCIÈRE ─────────────────────────────────
        'finance' => [
            'title'      => 'Gestion Financière',
            'permission' => ['invoices.general.view', 'payments.general.view', 'financial-accounts.general.view', 'financial-transactions.general.view', 'transaction-categories.general.view'],
            'items'      => [
                [
                    'label'      => 'Factures',
                    'icon'       => 'ti ti-file-invoice',
                    'route'      => 'backoffice.invoices.index',
                    'routeMatch' => 'backoffice.invoices.*',
                    'permission' => 'invoices.general.view',
                ],
                [
                    'label'      => 'Paiements',
                    'icon'       => 'ti ti-credit-card',
                    'route'      => 'backoffice.payments.index',
                    'routeMatch' => 'backoffice.payments.*',
                    'permission' => 'payments.general.view',
                ],
                [
                    'label'      => 'Transactions',
                    'icon'       => 'ti ti-arrows-exchange',
                    'route'      => 'backoffice.finance.transactions.index',
                    'routeMatch' => 'backoffice.finance.transactions.*',
                    'permission' => 'financial-transactions.general.view',
                ],
                [
                    'label'      => 'Comptes bancaires',
                    'icon'       => 'ti ti-building-bank',
                    'route'      => 'backoffice.finance.accounts.index',
                    'routeMatch' => 'backoffice.finance.accounts.*',
                    'permission' => 'financial-accounts.general.view',
                ],
                [
                    'label'      => 'Catégories',
                    'icon'       => 'ti ti-category',
                    'route'      => 'backoffice.finance.categories.index',
                    'routeMatch' => 'backoffice.finance.categories.*',
                    'permission' => 'transaction-categories.general.view',
                ],
            ],
        ],

        // ─── RAPPORTS ───────────────────────────────────────────
        'rapports' => [
            'title'      => 'Rapports',
            'permission' => 'reports.general.view',
            'items' => [
                [
                    'label'      => 'Revenus & Dépenses',
                    'icon'       => 'ti ti-chart-histogram',
                    'route'      => 'backoffice.reports.income',
                    'routeMatch' => 'backoffice.reports.income',
                ],
                [
                    'label'      => 'Bénéfices',
                    'icon'       => 'ti ti-chart-line',
                    'route'      => 'backoffice.reports.earnings',
                    'routeMatch' => 'backoffice.reports.earnings',
                ],
                [
                    'label'      => 'Locations',
                    'icon'       => 'ti ti-chart-infographic',
                    'route'      => 'backoffice.reports.rentals',
                    'routeMatch' => 'backoffice.reports.rentals',
                ],
            ],
        ],

        // ─── GESTION DES AGENTS ─────────────────────────────────
        'agents' => [
            'title'      => 'Gestion des Agents',
            'permission' => ['agencies.general.view', 'agents.general.view', 'agency-subscriptions.general.view'],
            'items'      => [
                [
                    'label'      => 'Agences',
                    'icon'       => 'ti ti-building',
                    'route'      => 'backoffice.agencies.index',
                    'routeMatch' => 'backoffice.agencies.*',
                    'permission' => 'agencies.general.view',
                ],
                [
                    'label'      => 'Agents',
                    'icon'       => 'ti ti-user-bolt',
                    'route'      => 'backoffice.agents.index',
                    'routeMatch' => 'backoffice.agents.*',
                    'permission' => 'agents.general.view',
                ],
                [
                    'label'      => 'Abonnements',
                    'icon'       => 'ti ti-license',
                    'route'      => 'backoffice.agency-subscriptions.index',
                    'routeMatch' => 'backoffice.agency-subscriptions.*',
                    'permission' => 'agency-subscriptions.general.view',
                ],
            ],
        ],

        // ─── GESTION DES UTILISATEURS ───────────────────────────
        'utilisateurs' => [
            'title'      => 'Gestion des Utilisateurs',
            'permission' => ['users.general.view', 'roles-permissions.general.view'],
            'items'      => [
                [
                    'label'      => 'Utilisateurs',
                    'icon'       => 'ti ti-user-circle',
                    'route'      => 'backoffice.users.index',
                    'routeMatch' => 'backoffice.users.*',
                    'permission' => 'users.general.view',
                ],
                [
                    'label'      => 'Rôles & Permissions',
                    'icon'       => 'ti ti-shield-lock',
                    'route'      => 'backoffice.roles-permissions.roles',
                    'routeMatch' => ['backoffice.roles-permissions.*', 'backoffice.roles.*'],
                    'permission' => 'roles-permissions.general.view',
                ],
            ],
        ],

        // ─── PARAMÈTRES ─────────────────────────────────────────
        'parametres' => [
            'title' => 'Paramètres',
            'items' => [
                [
                    'label'      => 'Mon profil',
                    'icon'       => 'ti ti-user-cog',
                    'route'      => 'backoffice.profile.edit',
                    'routeMatch' => 'backoffice.profile.edit',
                ],
                [
                    'label'      => 'Changer le mot de passe',
                    'icon'       => 'ti ti-lock',
                    'route'      => 'backoffice.profile.change-password',
                    'routeMatch' => 'backoffice.profile.change-password*',
                ],
                [
                    'label'          => 'Paramètres agence',
                    'icon'           => 'ti ti-settings',
                    'requiresAgency' => true,
                    'permission'     => 'agency-settings.general.edit',
                    'children'       => [
                        [
                            'label'      => 'Général',
                            'routeKey'   => 'backoffice.agencies.settings.edit',
                            'routeMatch' => 'backoffice.agencies.settings.edit',
                        ],
                        [
                            'label'      => 'Infos entreprise',
                            'routeKey'   => 'backoffice.agencies.settings.company',
                            'routeMatch' => 'backoffice.agencies.settings.company',
                        ],
                        [
                            'label'      => 'Paramètres factures',
                            'routeKey'   => 'backoffice.agencies.settings.invoice-settings',
                            'routeMatch' => 'backoffice.agencies.settings.invoice-settings',
                        ],
                        [
                            'label'      => 'Signatures & Logo',
                            'routeKey'   => 'backoffice.agencies.settings.signatures',
                            'routeMatch' => 'backoffice.agencies.settings.signatures',
                        ],
                    ],
                ],
                [
                    'label'      => 'Corbeille',
                    'icon'       => 'ti ti-trash',
                    'route'      => 'backoffice.trash.index',
                    'routeMatch' => 'backoffice.trash.*',
                    'permission' => 'trash.general.view',
                ],
            ],
        ],

    ],

];
