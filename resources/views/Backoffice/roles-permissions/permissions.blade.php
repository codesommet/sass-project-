<?php $page = 'permissions'; ?>

@extends('layout.mainlayout_admin')

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">

            <!-- Fil d'Ariane / Lien retour -->
            <div class="my-auto mb-3 pb-1">
                <a href="{{ route('backoffice.roles-permissions.roles') }}" class="mb-1 text-gray-9 fw-medium">
                    <i class="ti ti-arrow-left me-1"></i>Retour à la liste
                </a>
            </div>
            <!-- /Breadcrumb -->

            {{-- En-tête du rôle --}}
            <div class="filterbox mb-3 d-flex align-items-center">
                <span class="avatar avatar-lg bg-white text-secondary rounded-2 me-2">
                    <i class="ti ti-user-shield fs-25 fw-normal"></i>
                </span>
                <div>
                    <p class="mb-0">Rôle</p>
                    <h6 class="fw-medium">{{ $role->name ?? '—' }}</h6>
                </div>
                @if($readOnly ?? false)
                    <span class="badge bg-secondary ms-3">Lecture seule</span>
                @endif
            </div>

            <form method="POST" action="{{ route('backoffice.roles-permissions.permissions.update', $role->id) }}"
                id="permForm">
                @csrf
                @method('PUT')

                @php
                    $actionOrder = ['create', 'edit', 'delete', 'view'];
                @endphp

                {{-- Cartes par module (groupe) --}}
                @forelse(($matrix ?? []) as $module => $resources)
                    @php
                        $groupId = 'select-all-' . preg_replace('/[^a-z0-9\-]/i', '-', strtolower($module));
                    @endphp

                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="ti ti-folder me-2"></i>{{ strtoupper($module) }}
                                </h6>

                                <div class="no-sort">
                                    <div class="form-check form-check-md">
                                        <input class="form-check-input js-group-allow-all" type="checkbox"
                                            id="{{ $groupId }}" data-group="{{ $groupId }}"
                                            @disabled($readOnly ?? false)>
                                        <label class="form-check-label" for="{{ $groupId }}">Tout sélectionner</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="custom-datatable-filter table-responsive">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>RESSOURCE</th>
                                            <th class="text-center">CRÉATION</th>
                                            <th class="text-center">MODIFICATION</th>
                                            <th class="text-center">SUPPRESSION</th>
                                            <th class="text-center">CONSULTATION</th>
                                            <th class="text-center">TOUT SÉLECTIONNER</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        {{-- Itérer sur les ressources de ce module --}}
                                        @forelse($resources as $resource => $actions)
                                            @php
                                                $rowKey = 'row-' . md5($module . '-' . $resource);
                                                $resourceLabel = ucfirst(str_replace('_', ' ', $resource));
                                            @endphp

                                            <tr data-group="{{ $groupId }}" data-row="{{ $rowKey }}">
                                                <td>
                                                    <p class="text-gray-9 fw-medium mb-0">{{ $resourceLabel }}</p>
                                                </td>

                                                @foreach ($actionOrder as $actionName)
                                                    @php
                                                        $actionData = $actions[$actionName] ?? null;
                                                        $permId = $actionData['id'] ?? null;
                                                        $checked = (bool) ($actionData['checked'] ?? false);
                                                        $name = $permId ? "permissions[{$permId}]" : null;
                                                    @endphp

                                                    <td class="text-center">
                                                        <div class="form-check form-check-md d-flex justify-content-center">
                                                            <input class="form-check-input js-perm" type="checkbox"
                                                                @if ($name && !($readOnly ?? false)) name="{{ $name }}" value="1" @endif
                                                                @checked($checked) @disabled(empty($permId) || ($readOnly ?? false))
                                                                data-group="{{ $groupId }}"
                                                                data-row="{{ $rowKey }}">
                                                        </div>
                                                    </td>
                                                @endforeach

                                                <td class="text-center">
                                                    <div class="form-check form-check-md d-flex justify-content-center">
                                                        <input class="form-check-input js-row-allow-all" type="checkbox"
                                                            data-group="{{ $groupId }}"
                                                            data-row="{{ $rowKey }}"
                                                            @disabled($readOnly ?? false)>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">
                                                    <p class="text-gray-5 mb-0">Aucune ressource trouvée.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card mb-3">
                        <div class="card-body">
                            <p class="text-gray-5 mb-0 text-center">Aucune permission trouvée.</p>
                        </div>
                    </div>
                @endforelse

                {{-- Actions du pied de page --}}
                @if(($permissions['can_edit'] ?? false) && !($readOnly ?? false))
                <div class="card mb-0">
                    <div class="card-body py-2 my-1">
                        <div class="d-flex justify-content-end align-items-center">
                            <a href="{{ route('backoffice.roles-permissions.roles') }}" class="btn btn-light me-2">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary me-2">
                                Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </form>

            <div class="table-footer"></div>
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

    {{-- JS (Logique des cases Tout sélectionner) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // "Tout sélectionner" par ligne -> coche/décoche toutes les permissions de la ligne (ignore les désactivées)
            document.querySelectorAll('.js-row-allow-all').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    const group = this.dataset.group;
                    const row = this.dataset.row;
                    const checked = this.checked;

                    document
                        .querySelectorAll(
                            `tr[data-group="${group}"][data-row="${row}"] .js-perm:not(:disabled)`)
                        .forEach(x => x.checked = checked);

                    syncGroupAllowAll(group);
                });
            });

            // "Tout sélectionner" par groupe -> coche/décoche toutes les permissions du groupe (ignore les désactivées)
            document.querySelectorAll('.js-group-allow-all').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    const group = this.dataset.group;
                    const checked = this.checked;

                    document
                        .querySelectorAll(`tr[data-group="${group}"] .js-perm:not(:disabled)`)
                        .forEach(x => x.checked = checked);

                    document
                        .querySelectorAll(`tr[data-group="${group}"] .js-row-allow-all`)
                        .forEach(x => x.checked = checked);
                });
            });

            // Quand une case de permission change -> synchroniser les états "tout sélectionner"
            document.querySelectorAll('.js-perm').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    syncRowAllowAll(this.dataset.group, this.dataset.row);
                    syncGroupAllowAll(this.dataset.group);
                });
            });

            // Init
            document.querySelectorAll('.js-group-allow-all').forEach(cb => syncGroupAllowAll(cb.dataset.group));

            function syncRowAllowAll(group, row) {
                const rowPerms = Array.from(
                    document.querySelectorAll(
                        `tr[data-group="${group}"][data-row="${row}"] .js-perm:not(:disabled)`)
                );
                if (!rowPerms.length) return;

                const allChecked = rowPerms.every(x => x.checked);
                const rowAllowAll = document.querySelector(
                    `tr[data-group="${group}"][data-row="${row}"] .js-row-allow-all`
                );
                if (rowAllowAll) rowAllowAll.checked = allChecked;
            }

            function syncGroupAllowAll(group) {
                const perms = Array.from(
                    document.querySelectorAll(`tr[data-group="${group}"] .js-perm:not(:disabled)`)
                );
                if (!perms.length) return;

                const allChecked = perms.every(x => x.checked);
                const groupAllowAll = document.querySelector(`.js-group-allow-all[data-group="${group}"]`);
                if (groupAllowAll) groupAllowAll.checked = allChecked;

                const rows = new Set(
                    Array.from(document.querySelectorAll(`tr[data-group="${group}"]`)).map(tr => tr.dataset.row)
                );
                rows.forEach(r => syncRowAllowAll(group, r));
            }
        });
    </script>
@endsection
