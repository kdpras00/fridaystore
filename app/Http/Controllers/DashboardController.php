<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProduk    = Produk::count();
        $totalKasir     = User::role('kasir')->count();
        // Only count products that have a meaningful minimum stock set (> 0)
        $stokRendah = Produk::where('stok_minimum', '>', 0)
                        ->whereColumn('stok', '<=', 'stok_minimum')
                        ->count();
        $transaksiHari  = Transaksi::whereDate('created_at', today())->count();
        $omzetHari      = Transaksi::whereDate('created_at', today())->sum('total_bayar');
        $omzetBulan     = Transaksi::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->sum('total_bayar');

        // Chart: penjualan 7 hari terakhir — single query instead of 7
        $last7 = Transaksi::selectRaw('DATE(created_at) as date, SUM(total_bayar) as total')
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy('date')
            ->pluck('total', 'date');

        $chartData = collect(range(6, 0))->map(function ($daysAgo) use ($last7) {
            $date = now()->subDays($daysAgo)->toDateString();
            return [
                'label' => now()->subDays($daysAgo)->format('d M'),
                'total' => $last7[$date] ?? 0,
            ];
        });

        return view('dashboard', compact(
            'totalProduk', 'totalKasir', 'stokRendah',
            'transaksiHari', 'omzetHari', 'omzetBulan', 'chartData'
        ));
    }
}
