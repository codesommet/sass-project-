@auth
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Logo -->
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
        <!-- /Logo -->
        <div class="sidebar-inner slimscroll">
            <div id="sidebar-menu" class="sidebar-menu">

                <div class="form-group">
                    <div class="input-group input-group-flat d-inline-flex">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search menu...">
                        <span class="group-text">
                            <i class="ti ti-command"></i>
                        </span>
                    </div>
                </div>

                <ul>
                    @foreach (config('sidebar.sections', []) as $key => $section)
                        @include('components.sidebar.section', ['section' => $section])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <!-- /Sidebar -->
@endauth
