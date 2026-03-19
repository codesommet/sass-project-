
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Tableau de bord</h2>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('backoffice.dashboard') }}">Accueil</a>
                </li>
                <li class="breadcrumb-item active">Tableau de bord</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
        <div class="dropdown me-2">
            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white" data-bs-toggle="dropdown">
                <i class="ti ti-file-export me-1"></i>Exporter
            </a>
            <ul class="dropdown-menu p-2">
                <li>
                    <a href="{{ route('backoffice.dashboard.export.pdf') }}" class="dropdown-item">
                        <i class="ti ti-file-pdf me-2 text-danger"></i>PDF
                    </a>
                </li>
                <li>
                    <a href="{{ route('backoffice.dashboard.export.excel') }}" class="dropdown-item">
                        <i class="ti ti-file-spreadsheet me-2 text-success"></i>Excel
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('backoffice.dashboard.reports.custom') }}" class="btn btn-primary">
            <i class="ti ti-chart-bar me-1"></i>Rapports
        </a>
    </div>
</div>