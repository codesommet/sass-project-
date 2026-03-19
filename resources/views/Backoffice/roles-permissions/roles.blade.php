<?php $page = 'roles-permissions'; ?>

@extends('layout.mainlayout_admin')

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">

            {{-- Breadcrumb --}}
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">Rôles & Permissions</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('backoffice.dashboard') }}">Accueil</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Rôles</li>
                        </ol>
                    </nav>
                </div>

                {{-- Bouton Ajouter - contrôlé par permission CREATE --}}
                @can('roles-permissions.general.create')
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                    <div class="mb-2 me-2">
                        <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#add_role">
                            <i class="ti ti-plus me-2"></i>Ajouter un rôle
                        </a>
                    </div>
                </div>
                @endcan
            </div>
            <!-- /Breadcrumb -->

            <!-- Table Header (Search) -->
            <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-3 mb-3">
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search me-2">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" id="rolesSearchInput"
                                placeholder="Rechercher des rôles...">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->

            <!-- Roles Table -->
            <div class="card">
                <div class="card-body">
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>RÔLE</th>
                                    <th>PERMISSIONS</th>
                                    <th>DATE CRÉATION</th>
                                    <th>STATUT</th>
                                    {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                                    @canany(['roles-permissions.general.view', 'roles-permissions.general.edit', 'roles-permissions.general.delete'])
                                    <th></th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    @php
                                        $permCount = $role->permissions ? $role->permissions->count() : 0;
                                    @endphp
                                    <tr class="role-row" data-role-name="{{ strtolower($role->name) }}">
                                        <td>
                                            <p class="text-gray-9 mb-0">
                                                <strong>{{ $role->name }}</strong>
                                                @if($role->name === 'super-admin')
                                                    <span class="badge bg-danger ms-2">Super Admin</span>
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-gray-9 mb-0">
                                                <span class="badge bg-info">{{ $permCount }} permission(s)</span>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-gray-9 mb-0">{{ optional($role->created_at)->format('d M Y') }}
                                            </p>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark-transparent">
                                                <i class="ti ti-point-filled text-success me-1"></i>Actif
                                            </span>
                                        </td>
                                        
                                        {{-- Actions - visible seulement si au moins une permission d'action --}}
                                        @canany(['roles-permissions.general.view', 'roles-permissions.general.edit', 'roles-permissions.general.delete'])
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>

                                                @php
                                                    // agency-admin role is protected — only super-admin can edit/delete it
                                                    $isProtectedRole = in_array($role->name, ['super-admin', 'agency-admin']) && !($isSuperAdmin ?? false);
                                                @endphp
                                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                                    {{-- Gérer les permissions - contrôlé par permission VIEW --}}
                                                    @can('roles-permissions.general.view')
                                                    <li>
                                                        <a class="dropdown-item rounded-1"
                                                            href="{{ route('backoffice.roles-permissions.permissions', $role->id) }}">
                                                            <i class="ti ti-shield me-1"></i>Permissions
                                                        </a>
                                                    </li>
                                                    @endcan

                                                    @unless($isProtectedRole)
                                                    {{-- Modifier - contrôlé par permission EDIT --}}
                                                    @can('roles-permissions.general.edit')
                                                    <li>
                                                        <a class="dropdown-item rounded-1" href="javascript:void(0);"
                                                            data-bs-toggle="modal" data-bs-target="#edit_role"
                                                            data-edit-action="{{ route('backoffice.roles.update', $role->id) }}"
                                                            data-role-name="{{ $role->name }}"
                                                            data-role-permissions='@json($role->permissions->pluck('id')->values())'>
                                                            <i class="ti ti-edit me-1"></i>Modifier
                                                        </a>
                                                    </li>
                                                    @endcan

                                                    {{-- Supprimer - contrôlé par permission DELETE (sauf pour super-admin) --}}
                                                    @can('roles-permissions.general.delete')
                                                        @if($role->name !== 'super-admin')
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item rounded-1 text-danger"
                                                               href="#"
                                                               onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('deleteRoleForm').action = '{{ route('backoffice.roles.destroy', $role->id) }}'; document.getElementById('deleteRoleName').innerText = '{{ $role->name }}'; new bootstrap.Modal(document.getElementById('delete_role')).show(); return false;">
                                                                <i class="ti ti-trash me-1"></i>Supprimer
                                                            </a>
                                                        </li>
                                                        @endif
                                                    @endcan
                                                    @endunless
                                                </ul>
                                            </div>
                                        </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-gray-5 mb-0">Aucun rôle trouvé.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($roles->hasPages())
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mt-4">
                            <div>
                                <p class="text-gray-9 mb-0">
                                    Affichage {{ $roles->firstItem() }} à {{ $roles->lastItem() }} sur
                                    {{ $roles->total() }} rôle(s)
                                </p>
                            </div>
                            <nav aria-label="Page navigation">
                                {{ $roles->links() }}
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
            <!-- /Roles Table -->

        </div>

        <!-- Footer -->
        <div class="footer d-sm-flex align-items-center justify-content-between bg-white p-3">
            <p class="mb-0">
                <a href="javascript:void(0);">Politique de confidentialité</a>
                <a href="javascript:void(0);" class="ms-4">Conditions d'utilisation</a>
            </p>
            <p>&copy; 2025 Dreamsrent, Fait avec <span class="text-danger">❤</span> par
                <a href="javascript:void(0);" class="text-secondary">Dreams</a>
            </p>
        </div>
        <!-- /Footer -->
    </div>
    <!-- /Page Wrapper -->

    {{-- Modals --}}
    @can('roles-permissions.general.create')
        @include('backoffice.roles-permissions.partials._modal_create')
    @endcan

    @can('roles-permissions.general.edit')
        @include('backoffice.roles-permissions.partials._modal_edit')
    @endcan

    @can('roles-permissions.general.delete')
        @include('backoffice.roles-permissions.partials._modal_delete')
    @endcan

    {{-- JS --}}
    @include('backoffice.roles-permissions.partials._modals_js')

    {{-- Search filter --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('rolesSearchInput');
            if (!input) return;

            input.addEventListener('input', function() {
                const q = (this.value || '').toLowerCase().trim();
                const rows = document.querySelectorAll('table tbody .role-row');
                
                rows.forEach(row => {
                    const roleName = (row.getAttribute('data-role-name') || '').toLowerCase();
                    row.style.display = roleName.includes(q) ? '' : 'none';
                });
            });

            // Initialize delete modal functionality if delete permission exists
            @can('roles-permissions.general.delete')
            const deleteModal = document.getElementById('delete_role');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const deleteAction = button.getAttribute('data-delete-action');
                    const deleteName = button.getAttribute('data-delete-name');
                    
                    const form = this.querySelector('form');
                    const nameSpan = this.querySelector('#deleteRoleName');
                    
                    if (form) form.action = deleteAction;
                    if (nameSpan) nameSpan.innerText = deleteName;
                });
            }
            @endcan

            // Initialize edit modal functionality if edit permission exists
            @can('roles-permissions.general.edit')
            const editModal = document.getElementById('edit_role');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const editAction = button.getAttribute('data-edit-action');
                    const roleName = button.getAttribute('data-role-name');
                    
                    const form = this.querySelector('form');
                    const nameInput = this.querySelector('input[name="name"]');
                    
                    if (form) form.action = editAction;
                    if (nameInput) nameInput.value = roleName;
                });
            }
            @endcan
        });
    </script>
@endsection