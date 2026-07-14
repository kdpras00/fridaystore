<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk — {{ $transaksi->no_invoice }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #111111;
            --muted: #555555;
            --line: #d7d7d7;
            --brand: #05371a;
        }

        /* Screen styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: oklch(0.97 0 0);
            color: var(--ink);
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 2rem 1rem;
            min-height: 100vh;
        }
        .struk-wrapper {
            background: white;
            color: var(--ink);
            width: 320px;
            padding: 1.25rem 1.1rem;
            border-radius: 8px;
            border: 1px solid var(--line);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07);
        }
        .struk-wrapper * { font-family: 'Poppins', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.3rem 0.55rem;
            border-radius: 999px;
            background: rgba(5, 55, 26, 0.08);
            color: var(--brand);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.12em;
        }
        .divider { border: none; border-top: 1px dashed var(--line); margin: 0.75rem 0; }
        .row { display: flex; justify-content: space-between; font-size: 0.78rem; margin-bottom: 2px; gap: 0.75rem; }
        .row.bold { font-weight: 700; font-size: 0.82rem; }
        .center { text-align: center; }
        .item-nama { font-size: 0.75rem; margin-bottom: 0.1rem; font-weight: 600; }
        .item-detail { font-size: 0.7rem; display: flex; justify-content: space-between; color: var(--muted); gap: 0.5rem; }
        .btn-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            justify-content: center;
        }
        .btn {
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-size: 0.875rem;
            cursor: pointer;
            border: none;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }
        .btn-print { background: var(--brand); color: white; font-weight: 600; }
        .btn-back  { background: transparent; color: #111; border: 1px solid #ddd; }
        .btn-print:hover { opacity: 0.88; }
        .btn-back:hover { background: #f5f5f5; }

        .store-name {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .store-meta {
            font-size: 0.7rem;
            color: var(--muted);
        }

        @page { margin: 0; }
        /* Print styles */
        @media print {
            body { background: white; padding: 0; }
            .struk-wrapper { box-shadow: none; border-radius: 0; border: none; width: 80mm; padding: 4mm; }
            .btn-actions { display: none !important; }
            .brand-mark { padding: 0; background: transparent; }
        }
    </style>
</head>
<body>
    <div>
        <div class="struk-wrapper" id="struk">
            {{-- Header --}}
            <div class="center" style="margin-bottom:0.75rem;">
                <div class="store-name">{{ config('store.name') }}</div>
                @if(config('store.tagline'))
                <div class="store-meta">{{ config('store.tagline') }}</div>
                @endif
                <div class="store-meta">{{ config('store.address') }}</div>
                @if(config('store.phone'))
                <div class="store-meta">Telp: {{ config('store.phone') }}</div>
                @endif
            </div>

            <hr class="divider">

            {{-- Info Transaksi --}}
            <div class="row"><span>No. Invoice</span><span class="mono" style="font-weight:600;">{{ $transaksi->no_invoice }}</span></div>
            <div class="row"><span>Tanggal</span><span>{{ $transaksi->created_at->format('d/m/Y H:i') }}</span></div>
            <div class="row"><span>Kasir</span><span>{{ $transaksi->kasir->name }}</span></div>

            <hr class="divider">

            {{-- Items --}}
            @foreach($transaksi->detail as $item)
            <div style="margin-bottom:0.5rem;">
                <div class="item-nama">{{ $item->nama_produk }}</div>
                <div class="item-detail">
                    <span class="mono">{{ $item->qty }} × Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</span>
                    <span class="mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach

            <hr class="divider">

            {{-- Summary --}}
            <div class="row"><span>Subtotal</span><span class="mono">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span></div>
            @if($transaksi->diskon > 0)
            <div class="row"><span>Diskon</span><span class="mono">- Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span></div>
            @endif
            @php($totalPpn = $transaksi->detail->sum('ppn'))
            @if($totalPpn > 0)
            <div class="row"><span>PPN 11%</span><span class="mono">+ Rp {{ number_format($totalPpn, 0, ',', '.') }}</span></div>
            @endif
            <div class="row bold" style="margin-top:0.25rem;">
                <span>TOTAL</span>
                <span class="mono">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="row"><span>Bayar</span><span class="mono">Rp {{ number_format($transaksi->uang_bayar, 0, ',', '.') }}</span></div>
            <div class="row bold"><span>Kembali</span><span class="mono">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span></div>

            <hr class="divider">

            <div class="center" style="font-size:0.7rem;color:#555;">
                Terima kasih telah berbelanja!<br>
                {{ config('store.receipt_note') }}
            </div>
        </div>

        {{-- Actions (screen only) --}}
        <div class="btn-actions no-print">
            <button class="btn btn-back" onclick="window.close()">&#8592; Tutup</button>
            <button class="btn btn-print" onclick="window.print()">Cetak Struk</button>
        </div>
    </div>
</body>
</html>
