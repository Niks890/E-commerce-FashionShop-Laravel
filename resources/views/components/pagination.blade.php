@php
    use App\Helpers\PaginatorHelper;

    $range = PaginatorHelper::getSmartPaginationRange($paginator);
@endphp

@if ($paginator->hasPages())
    <nav>
        <ul class="pagination justify-content-center">
            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}">&laquo;</a></li>
            @endif

            {{-- First + Ellipsis --}}
            @if ($range['start'] > 1)
                <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">1</a></li>
                @if ($range['start'] > 2)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
            @endif

            {{-- Pages --}}
            @for ($i = $range['start']; $i <= $range['end']; $i++)
                <li class="page-item {{ $i == $range['current'] ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            {{-- Ellipsis + Last --}}
            @if ($range['end'] < $range['last'])
                @if ($range['end'] < $range['last'] - 1)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                <li class="page-item"><a class="page-link" href="{{ $paginator->url($range['last']) }}">{{ $range['last'] }}</a></li>
            @endif

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}">&raquo;</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
@endif

