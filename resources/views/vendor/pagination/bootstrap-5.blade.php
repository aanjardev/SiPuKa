@if ($paginator->hasPages())
    <nav class="admin-pagination" aria-label="Navigasi halaman">
        <div class="d-flex justify-content-between flex-fill d-sm-none">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">Sebelumnya</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">Berikutnya</span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
            <div>
                <p class="small text-muted">
                    Menampilkan
                    <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
                    dari
                    <span class="fw-semibold">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true" aria-label="Sebelumnya">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">&lsaquo;</a>
                        </li>
                    @endif

                    {{-- Pagination Elements (Custom Logic: 3 first + 3 last) --}}
                    @php
                        $lastPage = $paginator->lastPage();
                        $currentPage = $paginator->currentPage();
                        
                        $numbers = array_unique(array_merge(
                            [1, $lastPage],
                            range(max(1, $currentPage - 1), min($lastPage, $currentPage + 1))
                        ));
                        sort($numbers);
                        $pages = [];
                        $previous = 0;
                        foreach ($numbers as $number) {
                            if ($previous && $number - $previous > 1) {
                                $pages[] = '...';
                            }
                            $pages[] = $number;
                            $previous = $number;
                        }
                    @endphp
                    
                    @foreach ($pages as $page)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($page) && $page === '...')
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        {{-- Page Links --}}
                        @else
                            @php
                                $pageUrl = $paginator->url($page);
                            @endphp
                            
                            @if ($page == $currentPage)
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $pageUrl }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">&rsaquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true" aria-label="Berikutnya">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
