<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="Dreamsrent - Bootstrap Admin Template">
    <meta name="keywords" content="admin, estimates, bootstrap, business, html5, responsive, Projects">
    <meta name="author" content="Dreams technologies - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Dreamsrent - Admin Template</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('admin_assets/img/favicon.png') }}">

    @if(app()->getLocale() === 'ar')
        <!-- Arabic Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    @endif

    @include('layout.partials.head_admin')

    @if(app()->getLocale() === 'ar')
        <!-- RTL CSS -->
        <link rel="stylesheet" href="{{ URL::asset('admin_assets/css/rtl.css') }}">
    @endif
</head>

<body class="{{ Route::is(['login','otp','reset-password','forgot-password']) ? 'login-page' : '' }}">

    <div class="main-wrapper">
        @if (! Route::is(['login','otp','reset-password','forgot-password']))
            @include('layout.partials.header_admin')
            @include('layout.partials.sidebar_admin')
        @endif

        @yield('content')
    </div>

    {{-- ✅ Toasts --}}
    @include('backoffice.layout.partials._toasts')

    @component('components.modalpopup')
    @endcomponent

    {{-- ✅ Scripts --}}
    @include('layout.partials.footer_admin-script')

    <!-- Notification System Scripts - UPDATED DESIGN -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @auth('backoffice')
        loadNotifications();
        setInterval(loadNotifications, 30000);
        
        const notificationBtn = document.getElementById('notification_popup');
        if (notificationBtn) {
            notificationBtn.addEventListener('click', function() {
                loadNotifications();
            });
        }
        
        const markAllReadBtn = document.getElementById('markAllReadHeader');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetch('/backoffice/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                    }
                });
            });
        }
        
        const clearAllBtn = document.getElementById('clearAllHeader');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Archiver toutes les notifications ?')) {
                    fetch('/backoffice/notifications/clear-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadNotifications();
                        }
                    });
                }
            });
        }
        @endauth
    });

    function loadNotifications() {
        fetch('/backoffice/notifications/recent')
            .then(response => response.json())
            .then(data => {
                document.getElementById('activeCount').textContent = data.counts.active || 0;
                document.getElementById('unreadCount').textContent = data.counts.unread || 0;
                document.getElementById('archivedCount').textContent = data.counts.archived || 0;
                
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    if (data.counts.unread > 0) {
                        badge.textContent = data.counts.unread;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
                
                renderNotifications('activeNotificationsList', data.active);
                renderNotifications('unreadNotificationsList', data.unread);
                renderNotifications('archivedNotificationsList', data.archived);
            });
    }

    function renderNotifications(containerId, notifications) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        if (!notifications || notifications.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="ti ti-bell-off fs-24 text-muted mb-2"></i>
                    <p class="text-muted small">Aucune notification</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        notifications.forEach(notif => {
            const performer = notif.performer_name || 'System';
            const timeAgo = notif.time_ago || 'récemment';
            
            html += `
                <div class="notification-list px-3 py-2 border-bottom" data-id="${notif.id}" style="cursor: pointer;">
                    <div class="d-flex align-items-start">
                        <div class="me-2 flex-shrink-0">
                            <div class="avatar avatar-md" style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden;">
                                <img src="{{ URL::asset('assets/place-holder.webp') }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 small">
                                <span class="fw-semibold text-dark">${performer}</span> 
                                <span class="text-muted">${notif.message}</span>
                            </p>
                            <small class="text-muted">
                                <i class="ti ti-clock me-1"></i>${timeAgo}
                            </small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }

    function getNotificationColor(type) {
        const colors = {
            'success': '#198754',
            'error': '#dc3545',
            'warning': '#ffc107',
            'info': '#0d6efd',
            'login': '#0d6efd',
            'logout': '#6c757d',
            'vidange': '#ffc107',
            'contract': '#198754'
        };
        return colors[type] || '#0d6efd';
    }

    function getNotificationIcon(type) {
        const icons = {
            'success': 'ti ti-circle-check',
            'error': 'ti ti-circle-x',
            'warning': 'ti ti-alert-triangle',
            'info': 'ti ti-info-circle',
            'login': 'ti ti-login',
            'logout': 'ti ti-logout',
            'vidange': 'ti ti-droplet',
            'contract': 'ti ti-file-text'
        };
        return icons[type] || 'ti ti-bell';
    }
    </script>

</body>
</html>