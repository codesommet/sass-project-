{{--
    Smart Pagination Component
    Usage: <x-backoffice.smart-pagination :paginator="$clients" label="clients" />
--}}
@props(['paginator', 'label' => 'éléments'])

@if($paginator->total() > 0)
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4">
    <div class="pagination-info mb-3 mb-md-0">
        Affichage de <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
        à <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
        sur <span class="fw-semibold">{{ $paginator->total() }}</span> {{ $label }}
    </div>
    @if ($paginator->hasPages())
    <nav aria-label="Navigation des pages">
        <ul class="pagination justify-content-center mb-0">
            {{-- Previous --}}
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if ($paginator->onFirstPage())
                    <span class="page-link" aria-hidden="true"><i class="ti ti-chevron-left"></i></span>
                @else
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="ti ti-chevron-left"></i>
                    </a>
                @endif
            </li>

            {{-- Page Numbers --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp

            @if($start > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if($start > 2)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endif

            @for($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $i == $current ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            @if($end < $last)
                @if($end < $last - 1)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($last) }}">{{ $last }}</a>
                </li>
            @endif

            {{-- Next --}}
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                @if ($paginator->hasMorePages())
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="ti ti-chevron-right"></i>
                    </a>
                @else
                    <span class="page-link" aria-hidden="true"><i class="ti ti-chevron-right"></i></span>
                @endif
            </li>
        </ul>
    </nav>
    @endif
</div>
@endif
