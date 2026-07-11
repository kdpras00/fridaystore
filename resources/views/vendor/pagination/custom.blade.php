@if ($paginator->hasPages())
    <nav style="display:flex; justify-content:space-between; align-items:center; width:100%; font-size:13.5px;">
        <div>
            <p style="color:var(--color-ink-3); margin:0;">
                Menampilkan <span style="font-weight:600;color:var(--color-ink);">{{ $paginator->firstItem() }}</span> 
                sampai <span style="font-weight:600;color:var(--color-ink);">{{ $paginator->lastItem() }}</span> 
                dari <span style="font-weight:600;color:var(--color-ink);">{{ $paginator->total() }}</span> data
            </p>
        </div>
        <div style="display:flex; gap:8px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="btn btn-ghost" style="opacity:0.5; pointer-events:none; padding:5px 12px; font-size:13px; cursor:default;">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-ghost" style="padding:5px 12px; font-size:13px; text-decoration:none;">Prev</a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-ghost" style="padding:5px 12px; font-size:13px; text-decoration:none;">Next</a>
            @else
                <span class="btn btn-ghost" style="opacity:0.5; pointer-events:none; padding:5px 12px; font-size:13px; cursor:default;">Next</span>
            @endif
        </div>
    </nav>
@endif
