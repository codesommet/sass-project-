<!-- Header -->
<div class="header">
    <div class="main-header">

        <div class="header-left">
            <a href="{{ url('admin/index') }}" class="logo">
                <img src="{{ URL::asset('admin_assets/img/logo.svg') }}" alt="Logo">
            </a>
            <a href="{{ url('admin/index') }}" class="dark-logo">
                <img src="{{ URL::asset('admin_assets/img/logo-white.svg') }}" alt="Logo">
            </a>
        </div>

        <a id="mobile_btn" class="mobile_btn" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <div class="header-user">
            <div class="nav user-menu nav-list">

                <div class="me-auto d-flex align-items-center" id="header-search">
                    <a id="toggle_btn" href="javascript:void(0);">
                        <i class="ti ti-menu-deep"></i>
                    </a>
                    <div class="add-dropdown">
                        <a href="{{ url('admin/add-reservation') }}"
                            class="btn btn-dark d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>New Reservation
                        </a>
                    </div>
                </div>

                <div class="d-flex align-items-center header-icons">

                    <!-- Flag / Language Switcher -->
                    @php
                        $currentLocale = app()->getLocale();
                        $localeFlags = [
                            'fr' => ['flag' => 'france.svg', 'label' => 'Français'],
                            'en' => ['flag' => 'gb.svg', 'label' => 'English'],
                            'ar' => ['flag' => 'sa.svg', 'label' => 'العربية'],
                        ];
                        $currentFlag = $localeFlags[$currentLocale] ?? $localeFlags['fr'];
                    @endphp
                    <div class="nav-item dropdown has-arrow flag-nav nav-item-box">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"
                            role="button">
                            <img src="{{ URL::asset('admin_assets/img/flags/' . $currentFlag['flag']) }}" alt="Language"
                                class="img-fluid">
                        </a>
                        <ul class="dropdown-menu p-2">
                            {{-- French (main language - switches directly) --}}
                            <li>
                                <form method="POST" action="{{ route('locale.switch', 'fr') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center {{ $currentLocale === 'fr' ? 'active' : '' }}">
                                        <img src="{{ URL::asset('admin_assets/img/flags/france.svg') }}" alt="" height="16" class="me-2">
                                        Français
                                    </button>
                                </form>
                            </li>
                            {{-- English (starter version - show popup) --}}
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center {{ $currentLocale === 'en' ? 'active' : '' }}"
                                   onclick="showStarterVersionModal('en')">
                                    <img src="{{ URL::asset('admin_assets/img/flags/gb.svg') }}" alt="" height="16" class="me-2">
                                    English
                                </a>
                            </li>
                            {{-- Arabic (coming soon) --}}
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item d-flex align-items-center"
                                   onclick="showComingSoonModal('العربية')">
                                    <img src="{{ URL::asset('admin_assets/img/flags/sa.svg') }}" alt="" height="16" class="me-2">
                                    العربية
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- /Flag / Language Switcher -->

                    <!-- /Starter Version Language Modal (moved outside .header to fix z-index/backdrop issue) -->

                    <div class="theme-item">
                        <a href="javascript:void(0);" id="dark-mode-toggle" class="theme-toggle btn btn-menubar">
                            <i class="ti ti-moon"></i>
                        </a>
                        <a href="javascript:void(0);" id="light-mode-toggle" class="theme-toggle btn btn-menubar">
                            <i class="ti ti-sun-high"></i>
                        </a>
                    </div>

                    <!-- ✅ NEW: Trash Icon with Count -->
                    <div class="trash-item">
                        <a href="{{ route('backoffice.trash.index') }}" class="btn btn-menubar position-relative">
                            <i class="ti ti-trash"></i>
                            @php
                                $totalTrash = 0;
                                $totalTrash += App\Models\RentalContract::onlyTrashed()->count();
                                $totalTrash += App\Models\Vehicle::onlyTrashed()->count();
                                $totalTrash += App\Models\Client::onlyTrashed()->count();
                                $totalTrash += App\Models\Booking::onlyTrashed()->count();
                                $totalTrash += App\Models\Agency::onlyTrashed()->count();
                                $totalTrash += App\Models\Agent::onlyTrashed()->count();
                                $totalTrash += App\Models\User::onlyTrashed()->count();
                                $totalTrash += App\Models\Invoice::onlyTrashed()->count();
                                $totalTrash += App\Models\Payment::onlyTrashed()->count();
                            @endphp
                            @if($totalTrash > 0)
                            <span class="badge bg-danger rounded-pill position-absolute" 
                                  style="top: 5px; right: 5px; font-size: 0.65rem; padding: 2px 5px;">
                                {{ $totalTrash > 99 ? '99+' : $totalTrash }}
                            </span>
                            @endif
                        </a>
                    </div>

                    <!-- Dynamic Notification Section - UPDATED DESIGN -->
                    <div class="notification_item">
                        <a href="javascript:void(0);" class="btn btn-menubar position-relative" id="notification_popup"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="ti ti-bell"></i>
                            <span class="badge bg-violet rounded-pill" id="notificationBadge"
                                style="display: none;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown"
                            style="width: 380px; padding: 0;">
                            <div class="topnav-dropdown-header p-3 pb-0">
                                <h5 class="notification-title fw-semibold mb-2">Notifications</h5>
                                <ul class="nav nav-tabs nav-tabs-bottom d-flex"
                                    style="border-bottom: 1px solid #dee2e6;">
                                    <li class="nav-item flex-fill text-center">
                                        <a class="nav-link active pb-2 px-0" href="#active-notification"
                                            data-bs-toggle="tab"
                                            style="color: #0d6efd; border-bottom: 2px solid #0d6efd;">
                                            Active <span class="badge bg-primary ms-1" id="activeCount">0</span>
                                        </a>
                                    </li>
                                    <li class="nav-item flex-fill text-center">
                                        <a class="nav-link pb-2 px-0" href="#unread-notification" data-bs-toggle="tab"
                                            style="color: #6c757d;">
                                            Unread <span class="badge bg-secondary ms-1" id="unreadCount">0</span>
                                        </a>
                                    </li>
                                    <li class="nav-item flex-fill text-center">
                                        <a class="nav-link pb-2 px-0" href="#archieve-notification" data-bs-toggle="tab"
                                            style="color: #6c757d;">
                                            Archive <span class="badge bg-secondary ms-1" id="archivedCount">0</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <div class="noti-content" style="max-height: 350px; overflow-y: auto;">
                                <div class="tab-content">
                                    <!-- Active Tab -->
                                    <div class="tab-pane fade show active" id="active-notification">
                                        <div id="activeNotificationsList">
                                            <div class="text-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary"
                                                    role="status"></div>
                                                <span class="ms-2">Chargement...</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Unread Tab -->
                                    <div class="tab-pane fade" id="unread-notification">
                                        <div id="unreadNotificationsList">
                                            <div class="text-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary"
                                                    role="status"></div>
                                                <span class="ms-2">Chargement...</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Archive Tab -->
                                    <div class="tab-pane fade" id="archieve-notification">
                                        <div id="archivedNotificationsList">
                                            <div class="text-center py-3">
                                                <div class="spinner-border spinner-border-sm text-primary"
                                                    role="status"></div>
                                                <span class="ms-2">Chargement...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer with actions -->
                            <div class="d-flex align-items-center justify-content-between p-3 border-top">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);"
                                        class="text-primary text-decoration-underline me-3 small"
                                        id="markAllReadHeader">Mark all as Read</a>
                                    <a href="javascript:void(0);" class="text-danger text-decoration-underline small"
                                        id="clearAllHeader">Clear All</a>
                                </div>
                                <a href="{{ route('backoffice.notifications.index') }}"
                                    class="btn btn-primary btn-sm d-inline-flex align-items-center">
                                    View All <i class="ti ti-chevron-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div>
                        <a href="{{ url('admin/income-report') }}" class="btn btn-menubar">
                            <i class="ti ti-chart-bar"></i>
                        </a>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="btn btn-menubar" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside">
                            <i class="ti ti-grid-dots"></i>
                        </a>
                        <div class="dropdown-menu p-3">
                            <ul>
                                <li>
                                    <a href="{{ url('admin/add-car') }}"
                                        class="dropdown-item d-inline-flex align-items-center">
                                        <i class="ti ti-car me-2"></i>Car
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/add-quotations') }}"
                                        class="dropdown-item d-inline-flex align-items-center">
                                        <i class="ti ti-file-symlink me-2"></i>Quotation
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/pricing') }}"
                                        class="dropdown-item d-inline-flex align-items-center">
                                        <i class="ti ti-file-dollar me-2"></i>Seasonal Pricing
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/extra-services') }}"
                                        class="dropdown-item d-inline-flex align-items-center">
                                        <i class="ti ti-script-plus me-2"></i>Extra Service
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/inspections') }}"
                                        class="dropdown-item d-inline-flex align-items-center">
                                        <i class="ti ti-dice-6 me-2"></i>Inspection
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('admin/maintenance') }}"
                                        class="dropdown-item d-inline-flex align-items-center">
                                        <i class="ti ti-color-filter me-2"></i>Maintenance
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="dropdown profile-dropdown">
                        <a href="javascript:void(0);" class="d-flex align-items-center" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside">
                            <span class="avatar avatar-sm">
                                <img src="{{ URL::asset('admin_assets/img/profiles/avatar-05.jpg') }}" alt="Img"
                                    class="img-fluid rounded-circle">
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="profileset d-flex align-items-center">
                                <span class="user-img me-2">
                                    <img src="{{ URL::asset('admin_assets/img/profiles/avatar-05.jpg') }}"
                                        alt="">
                                </span>
                                <div>
                                    <h6 class="fw-semibold mb-1">
                                        {{ auth()->user()->name ?? auth()->user()->email }}
                                    </h6>
                                    <p class="fs-13">
                                        {{ auth()->user()->email }}
                                    </p>
                                </div>
                            </div>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ url('/backoffice/admin/profile-setting') }}">
                                <i class="ti ti-user-edit me-2"></i>Edit Profile
                            </a>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('backoffice.payments.index') }}">
                                <i class="ti ti-credit-card me-2"></i>Payments
                            </a>
                            <div class="dropdown-divider my-2"></div>
                            <div class="dropdown-item">
                                <div
                                    class="form-check form-switch  form-check-reverse  d-flex align-items-center justify-content-between">
                                    <label class="form-check-label" for="notify">
                                        <i class="ti ti-bell me-2"></i>Notifications</label>
                                    <input class="form-check-input" type="checkbox" role="switch" id="notify"
                                        checked>
                                </div>
                            </div>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ url('/backoffice/admin/security-setting') }}">
                                <i class="ti ti-exchange me-2"></i>Change Password
                            </a>
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ url('/backoffice/admin/profile-setting') }}">
                                <i class="ti ti-settings me-2"></i>Settings
                            </a>
                            <div class="dropdown-divider my-2"></div>
                            <a class="dropdown-item logout d-flex align-items-center justify-content-between"
                                href="{{ url('admin/login') }}">
                                <span><i class="ti ti-logout me-2"></i>Logout Account</span> <i
                                    class="ti ti-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="dropdown mobile-user-menu">
            <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ url('/backoffice/admin/profile-setting') }}">My Profile</a>
                <a class="dropdown-item" href="{{ url('/backoffice/admin/profile-setting') }}">Settings</a>
                <a class="dropdown-item" href="{{ url('admin/login') }}">Logout</a>
            </div>
        </div>
        <!-- /Mobile Menu -->

    </div>
</div>
<!-- /Header -->

<!-- Starter Version Language Modal (outside header to avoid z-index/backdrop issues) -->
<div class="modal fade" id="starterVersionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg rounded-3 text-center p-4">
            <div class="mb-3">
                <span class="d-flex align-items-center justify-content-center mx-auto rounded-circle" style="width:60px;height:60px;background:rgba(255,193,7,0.12);">
                    <i class="ti ti-language" style="font-size:28px;color:#f0a500;"></i>
                </span>
            </div>
            <h5 class="fw-bold mb-2">Version Starter</h5>
            <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">
                Cette traduction est une <strong class="text-dark">version de démarrage</strong> (Starter).<br>
                Certains textes peuvent ne pas être entièrement traduits.<br>
                Voulez-vous continuer ?
            </p>
            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                    Non, annuler
                </button>
                <form id="localeForm" method="POST" action="" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-check me-1"></i>Oui, continuer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function showStarterVersionModal(locale) {
    var form = document.getElementById('localeForm');
    form.action = '{{ url("/locale") }}/' + locale;
    var modal = new bootstrap.Modal(document.getElementById('starterVersionModal'));
    modal.show();
}
</script>
<!-- /Starter Version Language Modal -->

<!-- Coming Soon Language Modal -->
<div class="modal fade" id="comingSoonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-4">
            <div class="mb-3">
                <span class="avatar avatar-lg bg-info-transparent rounded-circle text-info mx-auto">
                    <i class="ti ti-clock fs-26"></i>
                </span>
            </div>
            <h5 class="mb-2">Traduction bientôt disponible</h5>
            <p class="text-muted mb-3">
                La traduction en <strong id="comingSoonLangName"></strong> sera disponible prochainement.
            </p>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="ti ti-check me-1"></i>OK, compris
                </button>
            </div>
        </div>
    </div>
</div>
<script>
function showComingSoonModal(langName) {
    document.getElementById('comingSoonLangName').textContent = langName;
    var modal = new bootstrap.Modal(document.getElementById('comingSoonModal'));
    modal.show();
}
</script>
<!-- /Coming Soon Language Modal -->