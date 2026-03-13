<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ route('backoffice.dashboard') }}" class="logo logo-normal">
            <img src="{{ URL::asset('admin_assets/img/logo.svg') }}" alt="Logo">
        </a>
        <a href="{{ route('backoffice.dashboard') }}" class="logo-small">
            <img src="{{ URL::asset('admin_assets/img/logo-small.svg') }}" alt="Logo">
        </a>
        <a href="{{ route('backoffice.dashboard') }}" class="dark-logo">
            <img src="{{ URL::asset('admin_assets/img/logo-white.svg') }}" alt="Logo">
        </a>
    </div>

    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- MAIN SECTION -->
                <li class="menu-title"><span>Main</span></li>
                <li>
                    <ul>
                        {{-- Dashboard - visible si permission view --}}
                        @can('dashboard.general.view')
                            <li class="{{ request()->routeIs('backoffice.dashboard') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.dashboard') }}">
                                    <i class="ti ti-layout-dashboard"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>

                <!-- AGENCIES SECTION -->
                @canany(['agencies.general.view', 'agency-subscriptions.general.view'])
                    <li class="menu-title"><span>AGENCIES</span></li>
                    <li>
                        <ul>
                            {{-- Agencies - visible si permission view --}}
                            @can('agencies.general.view')
                                <li class="{{ request()->routeIs('backoffice.agencies.*') ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.agencies.index') }}">
                                        <i class="ti ti-building"></i>
                                        <span>Agencies</span>
                                    </a>
                                </li>
                            @endcan

                            {{-- Subscriptions - visible si permission view --}}
                            @can('agency-subscriptions.general.view')
                                <li class="{{ request()->routeIs('backoffice.agency-subscriptions.*') ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.agency-subscriptions.index') }}">
                                        <i class="ti ti-credit-card"></i>
                                        <span>Subscriptions</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- ACCÈS SECTION -->
                @can('roles-permissions.general.view')
                    <li class="menu-title"><span>ACCÈS</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.roles-permissions.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.roles-permissions.roles') }}">
                                    <i class="ti ti-shield"></i>
                                    <span>Rôles & Permissions</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- EMPLOYEE SECTION -->
                @can('agents.general.view')
                    <li class="menu-title"><span>EMPLOYEE</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.agents.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.agents.index') }}">
                                    <i class="ti ti-users"></i>
                                    <span>Agents</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

<!-- CLIENTS SECTION -->
@can('clients.general.view')
    <li class="menu-title"><span>CLIENTS</span></li>
    <li>
        <ul>
            <li class="{{ request()->routeIs('backoffice.clients.index') ? 'active' : '' }}">
                <a href="{{ route('backoffice.clients.index') }}">
                    <i class="ti ti-user-circle"></i>
                    <span>Clients</span>
                </a>
            </li>
            
            {{-- BLACKLIST LINK --}}
            <li class="{{ request()->routeIs('backoffice.clients.blacklist.index') ? 'active' : '' }}">
                <a href="{{ route('backoffice.clients.blacklist.index') }}">
                    <i class="ti ti-alert-triangle text-danger"></i>
                    <span>Blacklist</span>
                    @php
                        $blacklistCount = 0;
                        if (class_exists('App\Models\BlacklistedClient')) {
                            $blacklistCount = App\Models\BlacklistedClient::count();
                        }
                    @endphp
                    @if($blacklistCount > 0)
                        <span class="badge bg-danger ms-auto">{{ $blacklistCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
@endcan

                <!-- MANAGEMENT SECTION -->
                @can('users.general.view')
                    <li class="menu-title"><span>MANAGEMENT</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.users.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.users.index') }}">
                                    <i class="ti ti-users"></i>
                                    <span>Users</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- ==================== VÉHICULE SUIVI SECTION ==================== -->
                @canany([
                    'vehicle-vignettes.general.view',
                    'vehicle-insurances.general.view',
                    'vehicle-oil-changes.general.view',
                    'vehicle-technical-checks.general.view',
                    'vehicle-controls.general.view',
                    'vehicle-control-items.general.view'
                ])
                    <li class="menu-title"><span>VÉHICULE SUIVI</span></li>
                    <li>
                        <ul>
                            {{-- Vignettes - visible si permission view --}}
                            @can('vehicle-vignettes.general.view')
                                <li class="{{ request()->routeIs('backoffice.vehicles.vignettes.index') && request('vehicle') == 'all' ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.vehicles.vignettes.index', ['vehicle' => 'all']) }}">
                                        <i class="ti ti-ticket"></i>
                                        <span>Vignettes</span>
                                    </a>
                                </li>
                            @endcan

                            {{-- Assurances - visible si permission view --}}
                            @can('vehicle-insurances.general.view')
                                <li class="{{ request()->routeIs('backoffice.vehicles.insurances.index') && request('vehicle') == 'all' ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.vehicles.insurances.index', ['vehicle' => 'all']) }}">
                                        <i class="ti ti-shield"></i>
                                        <span>Assurances</span>
                                    </a>
                                </li>
                            @endcan

                            {{-- Vidanges - visible si permission view --}}
                            @can('vehicle-oil-changes.general.view')
                                <li class="{{ request()->routeIs('backoffice.vehicles.oil-changes.index') && request('vehicle') == 'all' ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.vehicles.oil-changes.index', ['vehicle' => 'all']) }}">
                                        <i class="ti ti-droplet"></i>
                                        <span>Vidanges</span>
                                    </a>
                                </li>
                            @endcan

                            {{-- Contrôle technique - visible si permission view --}}
                            @can('vehicle-technical-checks.general.view')
                                <li class="{{ request()->routeIs('backoffice.vehicles.technical-checks.index') && request('vehicle') == 'all' ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.vehicles.technical-checks.index', ['vehicle' => 'all']) }}">
                                        <i class="ti ti-clipboard-check"></i>
                                        <span>Contrôle technique</span>
                                    </a>
                                </li>
                            @endcan

                            <!-- Contrôles Section with Submenu -->
                            @canany(['vehicle-controls.general.view', 'vehicle-control-items.general.view'])
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->routeIs('backoffice.controls.*') || request()->routeIs('backoffice.control-items.*') ? 'active subdrop' : '' }}">
                                        <i class="ti ti-clipboard-list"></i>
                                        <span>Contrôles</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        {{-- Tous les contrôles - visible si permission view --}}
                                        @can('vehicle-controls.general.view')
                                            <li>
                                                <a href="{{ route('backoffice.controls.index') }}"
                                                    class="{{ request()->routeIs('backoffice.controls.*') && !request()->routeIs('backoffice.control-items.*') ? 'active' : '' }}">
                                                    <i class="ti ti-list me-1"></i>
                                                    Tous les contrôles
                                                </a>
                                            </li>
                                        @endcan
                                        
                                        {{-- Tous les éléments - visible si permission view --}}
                                        @can('vehicle-control-items.general.view')
                                            <li>
                                                <a href="{{ route('backoffice.control-items.index') }}"
                                                    class="{{ request()->routeIs('backoffice.control-items.*') ? 'active' : '' }}">
                                                    <i class="ti ti-checklist me-1"></i>
                                                    Tous les éléments
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                <!-- ==================== CONTRATS SECTION ==================== -->
                @canany(['rental-contracts.general.view', 'contract-clients.general.view'])
                    <li class="menu-title"><span>CONTRATS</span></li>
                    <li>
                        <ul>
                            {{-- Contrats - visible si permission view --}}
                            @can('rental-contracts.general.view')
                                <li class="{{ request()->routeIs('backoffice.rental-contracts.index') ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.rental-contracts.index') }}">
                                        <i class="ti ti-file-text"></i>
                                        <span>Contrats</span>
                                    </a>
                                </li>
                            @endcan

                            {{-- Clients du contrat - visible si permission view --}}
                            @can('contract-clients.general.view')
                                <li class="{{ request()->routeIs('backoffice.contract-clients.*') ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.contract-clients.index') }}">
                                        <i class="ti ti-users"></i>
                                        <span>Clients du contrat</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- ==================== VÉHICULES SECTION ==================== -->
                @canany(['vehicles.general.view', 'vehicle-credits.general.view', 'vehicle-brands.general.view', 'vehicle-models.general.view'])
                    <li class="menu-title"><span>VÉHICULES</span></li>
                    <li>
                        <ul>
                            {{-- Cars - visible si permission view --}}
                            @can('vehicles.general.view')
                                <li class="{{ request()->routeIs('backoffice.vehicles.index') && !request()->routeIs('backoffice.vehicles.vignettes.*', 'backoffice.vehicles.insurances.*', 'backoffice.vehicles.oil-changes.*', 'backoffice.vehicle-credits.*') ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.vehicles.index') }}">
                                        <i class="ti ti-car"></i>
                                        <span>Cars</span>
                                    </a>
                                </li>
                            @endcan

                            {{-- Crédits - FIXED VERSION with null checking --}}
                            @can('vehicle-credits.general.view')
                                <li class="{{ request()->routeIs('backoffice.vehicle-credits.*') ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.vehicle-credits.index') }}">
                                        <i class="ti ti-credit-card"></i>
                                        <span>Crédits</span>
                                        @php
                                            $latePayments = 0;
                                            $user = auth()->user();
                                            
                                            // Only calculate late payments if user has an agency
                                            if ($user && $user->agency) {
                                                try {
                                                    if (class_exists('App\Models\VehicleCredit') && class_exists('App\Models\CreditPayment')) {
                                                        $latePayments = \App\Models\CreditPayment::whereHas('credit', function($q) use ($user) {
                                                            $q->where('agency_id', $user->agency_id)
                                                              ->where('status', 'active');
                                                        })
                                                        ->where('status', 'pending')
                                                        ->where('due_date', '<', now())
                                                        ->count();
                                                    }
                                                } catch (\Exception $e) {
                                                    // Log error silently
                                                    \Log::error('Error counting late payments: ' . $e->getMessage());
                                                }
                                            }
                                        @endphp
                                        @if($latePayments > 0)
                                            <span class="badge bg-danger float-end">{{ $latePayments }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endcan

                            <!-- Car Attributes with Submenu -->
                            @canany(['vehicle-brands.general.view', 'vehicle-models.general.view'])
                                <li class="submenu">
                                    <a href="javascript:void(0);"
                                        class="{{ request()->routeIs('backoffice.vehicle-brands.*', 'backoffice.vehicle-models.*') ? 'active subdrop' : '' }}">
                                        <i class="ti ti-device-camera-phone"></i>
                                        <span>Car Attributes</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        {{-- Brands - visible si permission view --}}
                                        @can('vehicle-brands.general.view')
                                            <li>
                                                <a href="{{ route('backoffice.vehicle-brands.index') }}"
                                                    class="{{ request()->routeIs('backoffice.vehicle-brands.*') ? 'active' : '' }}">
                                                    Brands
                                                </a>
                                            </li>
                                        @endcan
                                        
                                        {{-- Models - visible si permission view --}}
                                        @can('vehicle-models.general.view')
                                            <li>
                                                <a href="{{ route('backoffice.vehicle-models.index') }}"
                                                    class="{{ request()->routeIs('backoffice.vehicle-models.*') ? 'active' : '' }}">
                                                    Models
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                @endcanany

                <!-- ==================== RÉSERVATIONS SECTION ==================== -->
                @can('bookings.general.view')
                    <li class="menu-title"><span>RÉSERVATIONS</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.bookings.index') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.bookings.index') }}">
                                    <i class="ti ti-calendar-stats"></i>
                                    <span>Réservations</span>
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('backoffice.bookings.calendar') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.bookings.calendar') }}">
                                    <i class="ti ti-calendar"></i>
                                    <span>Booking Calendar</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                <!-- ==================== FINANCE SECTION ==================== -->
                @canany(['financial-accounts.general.view', 'transaction-categories.general.view', 'financial-transactions.general.view'])
                    <li class="menu-title"><span>FINANCE</span></li>
                    <li>
                        <ul>
                            <li class="submenu">
                                <a href="javascript:void(0);"
                                    class="{{ request()->routeIs('backoffice.finance.accounts.*') || request()->routeIs('backoffice.finance.categories.*') || request()->routeIs('backoffice.finance.transactions.*') ? 'active subdrop' : '' }}">
                                    <i class="ti ti-coin"></i>
                                    <span>Gestion financière</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul>
                                    {{-- Comptes - visible si permission view --}}
                                    @can('financial-accounts.general.view')
                                        <li>
                                            <a href="{{ route('backoffice.finance.accounts.index') }}"
                                                class="{{ request()->routeIs('backoffice.finance.accounts.*') ? 'active' : '' }}">
                                                <i class="ti ti-building-bank me-1"></i>
                                                Comptes
                                            </a>
                                        </li>
                                    @endcan
                                    
                                    {{-- Catégories - visible si permission view --}}
                                    @can('transaction-categories.general.view')
                                        <li>
                                            <a href="{{ route('backoffice.finance.categories.index') }}"
                                                class="{{ request()->routeIs('backoffice.finance.categories.*') ? 'active' : '' }}">
                                                <i class="ti ti-category me-1"></i>
                                                Catégories
                                            </a>
                                        </li>
                                    @endcan
                                    
                                    {{-- Transactions - visible si permission view --}}
                                    @can('financial-transactions.general.view')
                                        <li>
                                            <a href="{{ route('backoffice.finance.transactions.index') }}"
                                                class="{{ request()->routeIs('backoffice.finance.transactions.*') ? 'active' : '' }}">
                                                <i class="ti ti-transfer me-1"></i>
                                                Transactions
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endcanany

                <!-- ==================== FACTURATION SECTION ==================== -->
                @canany(['invoices.general.view', 'invoice-items.general.view'])
                    <li class="menu-title"><span>FACTURATION</span></li>
                    <li>
                        <ul>
                            {{-- Factures - visible si permission view --}}
                            @can('invoices.general.view')
                                <li class="{{ request()->routeIs('backoffice.invoices.*') ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.invoices.index') }}">
                                        <i class="ti ti-file-invoice"></i>
                                        <span>Factures</span>
                                    </a>
                                </li>
                            @endcan

                            {{-- Items de facture - visible si permission view --}}
                            @can('invoice-items.general.view')
                                <li class="{{ request()->routeIs('backoffice.invoice-items.*') ? 'active' : '' }}">
                                    <a href="{{ route('backoffice.invoice-items.index') }}">
                                        <i class="ti ti-file-description"></i>
                                        <span>Items de facture</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                <!-- ==================== PAIEMENTS SECTION ==================== -->
                @can('payments.general.view')
                    <li class="menu-title"><span>PAIEMENTS</span></li>
                    <li>
                        <ul>
                            <li class="{{ request()->routeIs('backoffice.payments.*') ? 'active' : '' }}">
                                <a href="{{ route('backoffice.payments.index') }}">
                                    <i class="ti ti-currency-dollar"></i>
                                    <span>Paiements</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->