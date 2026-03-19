<table class="table datatable align-middle">
    <thead class="thead-light">
        <tr>
            @can('clients.general.delete')
            <th class="no-sort" width="50">
                <div class="form-check form-check-md d-flex justify-content-center m-0 p-0">
                    <input class="form-check-input" type="checkbox" id="select-all">
                </div>
            </th>
            @endcan
            <th>Client</th>
            <th>Contact</th>
            <th>Documents</th>
            <th>Statut</th>
            <th>Ajouté le</th>
            @canany(['clients.general.view', 'clients.general.edit', 'clients.general.delete'])
            <th width="80">Actions</th>
            @endcanany
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $client)
            <tr class="{{ $client->status === 'blacklisted' ? 'client-row-blacklisted' : '' }}">
                @can('clients.general.delete')
                <td class="text-center">
                    <div class="form-check form-check-md d-flex justify-content-center m-0 p-0">
                        <input class="form-check-input client-checkbox" type="checkbox" value="{{ $client->id }}">
                    </div>
                </td>
                @endcan

                {{-- Client: Avatar + Name + Agency (merged for cleaner look) --}}
                <td>
                    <div class="d-flex align-items-center">
                        <div class="position-relative me-3" style="width:42px;height:42px;flex-shrink:0;">
                            @if ($client->hasAvatar())
                                <img src="{{ $client->avatar_url }}" alt="{{ $client->full_name }}"
                                    class="avatar-table"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @else
                                <img src="" style="display:none;" alt="" class="avatar-table">
                            @endif
                            <div class="avatar avatar-md border position-absolute top-0 start-0"
                                @if ($client->hasAvatar()) style="display: none;" @endif>
                                <span class="avatar-title">
                                    {{ strtoupper(mb_substr($client->first_name, 0, 1) . mb_substr($client->last_name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <h6 class="fw-medium mb-0">
                                @can('clients.general.view')
                                    <a href="{{ route('backoffice.clients.show', $client) }}" class="text-dark">
                                        {{ $client->full_name }}
                                    </a>
                                @else
                                    <span>{{ $client->full_name }}</span>
                                @endcan
                            </h6>
                            <small class="text-muted">
                                <i class="ti ti-building me-1"></i>{{ $client->agency->name ?? '—' }}
                            </small>
                        </div>
                    </div>
                </td>

                {{-- Contact --}}
                <td>
                    <div class="d-flex flex-column">
                        @if ($client->email)
                            <a href="mailto:{{ $client->email }}" class="text-primary small">
                                <i class="ti ti-mail me-1"></i>{{ Str::limit($client->email, 22) }}
                            </a>
                        @endif
                        @if ($client->phone)
                            <a href="tel:{{ $client->phone }}" class="text-success small {{ $client->email ? 'mt-1' : '' }}">
                                <i class="ti ti-phone me-1"></i>{{ $client->phone }}
                            </a>
                        @endif
                        @if (!$client->email && !$client->phone)
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </td>

                {{-- Documents (CIN + License in one column) --}}
                <td>
                    <div class="d-flex flex-column gap-1">
                        @if ($client->cin_number)
                            <span class="badge bg-secondary-transparent small">
                                <i class="ti ti-id me-1"></i>CIN: {{ Str::limit($client->cin_number, 12) }}
                            </span>
                        @endif
                        @if ($client->driving_license_number)
                            <span class="badge bg-info-transparent small">
                                <i class="ti ti-license me-1"></i>{{ Str::limit($client->driving_license_number, 12) }}
                            </span>
                        @endif
                        @if (!$client->cin_number && !$client->driving_license_number)
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </td>

                {{-- Status (reusable component) --}}
                <td>
                    <x-backoffice.status-badge :status="$client->status" />
                </td>

                {{-- Date --}}
                <td>
                    <div class="d-flex flex-column">
                        <small class="fw-medium">{{ $client->created_at->format('d/m/Y') }}</small>
                        <small class="text-muted">{{ $client->created_at->format('H:i') }}</small>
                    </div>
                </td>

                {{-- Actions --}}
                @canany(['clients.general.view', 'clients.general.edit', 'clients.general.delete'])
                <td>
                    @include('backoffice.clients.partials._actions', ['client' => $client])
                </td>
                @endcanany
            </tr>
        @empty
            <x-backoffice.empty-state
                icon="ti-users-off"
                title="Aucun client trouvé"
                message="Commencez par ajouter votre premier client"
                :create-url="route('backoffice.clients.create')"
                create-label="Ajouter un client"
                create-permission="clients.general.create"
                :colspan="(auth()->user()->can('clients.general.delete') ? 1 : 0) + 5 + (auth()->user()->canAny(['clients.general.view', 'clients.general.edit', 'clients.general.delete']) ? 1 : 0)"
            />
        @endforelse
    </tbody>
</table>

@can('clients.general.delete')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.client-checkbox').forEach(cb => cb.checked = this.checked);
        });
    }
});
</script>
@endcan
