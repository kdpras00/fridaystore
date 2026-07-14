@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-sub', 'Ringkasan aktivitas penjualan dan performa toko Anda.')

@section('content')

<div class="metric-grid">
    <div class="metric-card">
        <div class="metric-label">Total Produk</div>
        <div class="metric-value">{{ number_format($totalProduk) }}</div>
        <div class="metric-note">Seluruh item yang aktif di katalog</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Transaksi Hari Ini</div>
        <div class="metric-value">{{ number_format($transaksiHari) }}</div>
        <div class="metric-note">Jumlah transaksi yang sudah diproses</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Omzet Hari Ini</div>
        <div class="metric-value">Rp {{ number_format($omzetHari, 0, ',', '.') }}</div>
        <div class="metric-note">Pendapatan yang tercatat hari ini</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Profit Hari Ini</div>
        <div class="metric-value">Rp {{ number_format($profitHari, 0, ',', '.') }}</div>
        <div class="metric-note">Keuntungan bersih hari ini</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Stok Menipis</div>
        <div class="metric-value" style="{{ $stokRendah > 0 ? 'color:var(--color-danger);' : '' }}">{{ number_format($stokRendah) }}</div>
        <div class="metric-note">
            @if($stokRendah > 0)
            <a href="{{ route('stok.index', ['status' => 'rendah']) }}" style="color:var(--color-danger);font-weight:500;text-decoration:underline;">Lihat produk</a>
            @else
            Semua stok dalam kondisi aman
            @endif
        </div>
    </div>
</div>

{{-- Chart + Side info --}}
<div class="dashboard-detail-grid" style="display:grid; grid-template-columns:minmax(0, 1fr) 220px; gap:24px; align-items:start;">

    <div class="card table-card" style="min-width:0;">
        <div class="card-header">
            <p class="card-title">Penjualan 7 Hari Terakhir</p>
        </div>
        <div style="padding:16px; position:relative; width:100%; height:260px;">
            <canvas id="chartPenjualan" role="img" aria-label="Grafik penjualan tujuh hari terakhir"></canvas>
        </div>
    </div>

    <div style="display:flex; flex-direction:column; gap:16px;">
        <div class="metric-card">
            <div class="metric-label">Omzet Bulan Ini</div>
            <div class="metric-value">Rp {{ number_format($omzetBulan, 0, ',', '.') }}</div>
            <div class="metric-note">Akumulasi transaksi pada bulan aktif</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Profit Bulan Ini</div>
            <div class="metric-value">Rp {{ number_format($profitBulan, 0, ',', '.') }}</div>
            <div class="metric-note">Akumulasi profit bulan aktif</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Kasir Aktif</div>
            <div class="metric-value">{{ $totalKasir }}</div>
            <div class="metric-note">Pengguna yang bisa memproses transaksi</div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
Chart.defaults.color = 'oklch(0.556 0 0)';
Chart.defaults.font.family = 'JetBrains Mono, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace';
Chart.defaults.font.size = 11;

new Chart(document.getElementById('chartPenjualan'), {
    type: 'bar',
    data: {
        labels: {!! $chartData->pluck('label')->toJson() !!},
        datasets: [{
            data: {!! $chartData->pluck('total')->toJson() !!},
            backgroundColor: 'oklch(0.30 0.10 148 / 0.15)',
            borderColor: 'oklch(0.30 0.10 148)',
            borderWidth: 1.5,
            borderRadius: 4,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: {
            backgroundColor: 'oklch(1 0 0)',
            borderColor: 'oklch(0.922 0 0)',
            borderWidth: 1,
            titleColor: 'oklch(0.556 0 0)',
            bodyColor: 'oklch(0.145 0 0)',
            callbacks: { label: v => ' Rp ' + v.raw.toLocaleString('id-ID') }
        }},
        scales: {
            x: { grid: { color: 'oklch(0.922 0 0)', lineWidth: 1 }, border: { display: false }, ticks: { color: 'oklch(0.556 0 0)' } },
            y: { grid: { color: 'oklch(0.922 0 0)', lineWidth: 1 }, border: { display: false }, ticks: { color: 'oklch(0.556 0 0)', callback: v => 'Rp ' + (v/1000).toFixed(0) + 'k' } }
        }
    }
});
</script>
@endpush
