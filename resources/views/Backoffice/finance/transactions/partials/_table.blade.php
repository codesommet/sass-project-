@props(['transactions'])

<style>
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #6c757d;
        background: transparent;
        border: 1px solid transparent;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .btn-icon:hover {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #0d6efd;
    }
    
    .btn-icon i {
        font-size: 18px;
    }
    
    .badge-income { background: #d4edda; color: #155724; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    .badge-expense { background: #f8d7da; color: #721c24; padding: 0.35rem 0.75rem; border-radius: 50px; font-weight: 500; }
    
    .table-responsive, 
    .custom-datatable-filter, 
    .dataTables_wrapper {
        overflow: visible !important;
    }
    
    .dropdown-menu {
        z-index: 9999 !important;
    }
    
    .form-check {
        display: flex;
        justify-content: center;
        margin: 0;
        padding: 0;
    }
    
    .amount-income { 
        color: #198754; 
        font-weight: 600; 
    }
    
    .amount-expense { 
        color: #dc3545; 
        font-weight: 600; 
    }
    
    .source-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
    }
    .source-badge i {
        margin-right: 0.25rem;
        font-size: 1rem;
    }
</style>

<div class="table-responsive">
    <table class="table align-middle">
        <thead class="thead-light">
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('financial-transactions.general.delete')
                <th width="50" class="text-center">
                    <div class="form-check form-check-md">
                        <input class="form-check-input" type="checkbox" id="select-all">
                    </div>
                </th>
                @endcan
                
                <th>Date</th>
                <th>Description</th>
                <th>Compte</th>
                <th>Catégorie</th>
                <th>Référence</th>
                <th>Source</th>
                <th>Montant</th>
                
                {{-- Colonne Actions - visible seulement si au moins une permission d'action --}}
                @canany(['financial-transactions.general.view', 'financial-transactions.general.edit', 'financial-transactions.general.delete'])
                <th width="80">Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                {{-- Case à cocher - visible seulement si permission DELETE --}}
                @can('financial-transactions.general.delete')
                <td class="text-center">
                    <div class="form-check form-check-md">
                        <input class="form-check-input transaction-checkbox" type="checkbox" value="{{ $transaction->id }}">
                    </div>
                </td>
                @endcan
                
                <td>{{ $transaction->formatted_date }}</td>
                <td>
                    @can('financial-transactions.general.view')
                        @if($transaction->source_link)
                            <a href="{{ $transaction->source_link }}" class="fw-medium">
                                {{ $transaction->description ?? '—' }}
                            </a>
                        @else
                            <a href="{{ route('backoffice.finance.transactions.show', $transaction) }}" class="fw-medium">
                                {{ $transaction->description ?? '—' }}
                            </a>
                        @endif
                    @else
                        <span class="fw-medium">{{ $transaction->description ?? '—' }}</span>
                    @endcan
                </td>
                <td>
                    @if($transaction->account)
                        @can('financial-accounts.general.view')
                            <a href="{{ route('backoffice.finance.accounts.show', $transaction->financial_account_id) }}">
                                {{ $transaction->account->name }}
                            </a>
                        @else
                            {{ $transaction->account->name }}
                        @endcan
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if($transaction->category)
                        {{ $transaction->category->name }}
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ $transaction->reference ?? '—' }}</td>
                <td>
                    @if($transaction->source_type)
                        @php
                            $sourceInfo = $transaction->source_info;
                        @endphp
                        <span class="source-badge" style="background-color: {{ $sourceInfo['color'] }}20; color: {{ $sourceInfo['color'] }}; border: 1px solid {{ $sourceInfo['color'] }}40;">
                            <i class="{{ $sourceInfo['icon'] }}"></i>
                            {{ $sourceInfo['label'] }}
                        </span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <span class="{{ $transaction->amount_color_class }}">
                        {{ $transaction->formatted_amount }}
                    </span>
                </td>
                
                {{-- Actions - visible seulement si au moins une permission d'action --}}
                @canany(['financial-transactions.general.view', 'financial-transactions.general.edit', 'financial-transactions.general.delete'])
                <td class="text-center">
                    @include('backoffice.finance.transactions.partials._actions', ['transaction' => $transaction])
                </td>
                @endcanany
            </tr>
            @empty
            <tr>
                @can('financial-transactions.general.delete')
                <td></td>
                @endcan
                
                <td colspan="{{ auth()->user()->can('financial-transactions.general.delete') ? 8 : 7 }}" class="text-center py-5">
                    <div class="text-center">
                        <i class="ti ti-transfer fs-48 text-gray-4 mb-3"></i>
                        <h5 class="mb-2">Aucune transaction trouvée</h5>
                        
                        @can('financial-transactions.general.create')
                            <p class="text-muted mb-3">Commencez par créer une nouvelle transaction</p>
                            <a href="{{ route('backoffice.finance.transactions.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>Nouvelle transaction
                            </a>
                        @endcan
                    </div>
                </td>
                
                @canany(['financial-transactions.general.view', 'financial-transactions.general.edit', 'financial-transactions.general.delete'])
                <td></td>
                @endcanany
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Script pour "Select All" - seulement si permission DELETE --}}
@can('financial-transactions.general.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.transaction-checkbox').forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endcan