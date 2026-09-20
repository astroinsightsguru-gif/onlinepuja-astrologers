@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation">
        {{-- Pagination Info --}}
        <div class="mb-3">
            <span class="text-muted">
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} items (Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }})
            </span>
        </div>

        {{-- Pagination Controls --}}
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">
                        {!! __('pagination.previous') !!}
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="page-link">
                        {!! __('pagination.previous') !!}
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="page-link">
                        {!! __('pagination.next') !!}
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">
                        {!! __('pagination.next') !!}
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
