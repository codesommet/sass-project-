{{--
    Sidebar Section Component

    @param array $section - Section configuration from config/sidebar.php
    @param string $guard - Permission guard name (default: 'backoffice')
--}}
@php
    $guard = $guard ?? 'backoffice';
    $sectionPermission = $section['permission'] ?? null;
    $canViewSection = true;

    if ($sectionPermission) {
        if (is_array($sectionPermission)) {
            $canViewSection = auth()->user()->canAny(array_map(fn($p) => $p, $sectionPermission));
        } else {
            $canViewSection = auth()->user()->can($sectionPermission);
        }
    }
@endphp

@if ($canViewSection)
<li class="menu-title"><span>{{ $section['title'] }}</span></li>
<li>
    <ul>
        @foreach ($section['items'] as $item)
            @include('components.sidebar.menu-item', ['item' => $item, 'guard' => $guard])
        @endforeach
    </ul>
</li>
@endif
