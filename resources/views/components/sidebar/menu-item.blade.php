{{--
    Sidebar Menu Item Component

    @param array $item - Menu item configuration from config/sidebar.php
    @param string $guard - Permission guard name (default: 'backoffice')
--}}
@php
    $guard = $guard ?? 'backoffice';
    $hasChildren = !empty($item['children']);
    $isActive = false;

    // Check active state
    if (!empty($item['routeMatch'])) {
        $patterns = is_array($item['routeMatch']) ? $item['routeMatch'] : [$item['routeMatch']];
        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                $isActive = true;
                break;
            }
        }
    }

    // For items with children, also check children's active state
    if ($hasChildren && !$isActive) {
        foreach ($item['children'] as $child) {
            $childPatterns = is_array($child['routeMatch'] ?? []) ? ($child['routeMatch'] ?? []) : [$child['routeMatch']];
            foreach ($childPatterns as $pattern) {
                if (request()->routeIs($pattern)) {
                    $isActive = true;
                    break 2;
                }
            }
        }
    }

    // Check if item requires agency
    $requiresAgency = $item['requiresAgency'] ?? false;
    $userAgencyId = auth()->user()->agency_id ?? null;
@endphp

{{-- Skip if requires agency and user has none --}}
@if ($requiresAgency && !$userAgencyId)
    {{-- Hidden: user has no agency --}}
@elseif ($hasChildren)
    {{-- Submenu item --}}
    @php
        // Check section permission for submenu
        $submenuPermission = $item['permission'] ?? null;
        $canViewSubmenu = true;
        if ($submenuPermission) {
            if (is_array($submenuPermission)) {
                $canViewSubmenu = auth()->user()->canAny(array_map(fn($p) => $p, $submenuPermission));
            } else {
                $canViewSubmenu = auth()->user()->can($submenuPermission);
            }
        }
    @endphp

    @if ($canViewSubmenu)
    <li class="submenu">
        <a href="javascript:void(0);" class="{{ $isActive ? 'active subdrop' : '' }}">
            <i class="{{ $item['icon'] ?? '' }}"></i><span>{{ $item['label'] }}</span>
            <span class="menu-arrow"></span>
        </a>
        <ul>
            @foreach ($item['children'] as $child)
                @php
                    $childPermission = $child['permission'] ?? null;
                    $canViewChild = true;
                    if ($childPermission) {
                        $canViewChild = auth()->user()->can($childPermission);
                    }

                    $childActive = false;
                    if (!empty($child['routeMatch'])) {
                        $childPatterns = is_array($child['routeMatch']) ? $child['routeMatch'] : [$child['routeMatch']];
                        foreach ($childPatterns as $pattern) {
                            if (request()->routeIs($pattern)) {
                                $childActive = true;
                                break;
                            }
                        }
                    }

                    // Handle routes that need agency_id parameter
                    $childRoute = '#';
                    if (!empty($child['route'])) {
                        $childRoute = route($child['route']);
                    } elseif (!empty($child['routeKey']) && $userAgencyId) {
                        $childRoute = route($child['routeKey'], $userAgencyId);
                    }
                @endphp

                @if ($canViewChild)
                <li>
                    <a href="{{ $childRoute }}" class="{{ $childActive ? 'active' : '' }}">{{ $child['label'] }}</a>
                </li>
                @endif
            @endforeach
        </ul>
    </li>
    @endif
@else
    {{-- Simple menu item --}}
    @php
        $permission = $item['permission'] ?? null;
        $canView = true;
        if ($permission) {
            $canView = auth()->user()->can($permission);
        }
    @endphp

    @if ($canView)
    <li class="{{ $isActive ? 'active' : '' }}">
        <a href="{{ !empty($item['route']) ? route($item['route']) : '#' }}">
            <i class="{{ $item['icon'] ?? '' }}"></i><span>{{ $item['label'] }}</span>
            @if (!empty($item['badge']))
                @php
                    $badgeValue = 0;
                    if ($item['badge'] === 'unread_notifications') {
                        $badgeValue = \App\Models\Notification::where('user_id', auth()->id())
                            ->where('is_read', false)
                            ->where('is_archived', false)
                            ->count();
                    }
                @endphp
                @if ($badgeValue > 0)
                    <span class="count">{{ $badgeValue > 99 ? '99+' : $badgeValue }}</span>
                @endif
            @endif
        </a>
    </li>
    @endif
@endif
