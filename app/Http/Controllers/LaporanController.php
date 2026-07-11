<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with('kasir')
            ->when($request->filled('dari'), fn($q) => $q->whereDate('created_at', '>=', $request->dari))
            ->when($request->filled('sampai'), fn($q) => $q->whereDate('created_at', '<=', $request->sampai))
            ->when($request->filled('kasir_id'), fn($q) => $q->where('kasir_id', $request->kasir_id));

        $grandTotal = (clone $query)->sum('total_bayar');
        $transaksi  = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $kasirList  = User::role('kasir')->orderBy('name')->get();

        return view('laporan.index', compact('transaksi', 'grandTotal', 'kasirList'));
    }

    public function produk(Request $request)
    {
        $produkLaris = TransaksiDetail::selectRaw('produk_id, nama_produk, SUM(qty) as total_qty, SUM(subtotal) as total_revenue')
            ->when($request->filled('dari'), function ($q) use ($request) {
                $q->whereHas('transaksi', fn($tq) => $tq->whereDate('created_at', '>=', $request->dari));
            })
            ->when($request->filled('sampai'), function ($q) use ($request) {
                $q->whereHas('transaksi', fn($tq) => $tq->whereDate('created_at', '<=', $request->sampai));
            })
            ->groupBy('produk_id', 'nama_produk')
            ->orderByDesc('total_qty')
            ->paginate(20)
            ->withQueryString();

        return view('laporan.produk', compact('produkLaris'));
    }

    public function kasir(Request $request)
    {
        $kasirStats = User::role('kasir')
            ->withCount(['transaksi as total_transaksi' => function ($q) use ($request) {
                $q->when($request->filled('dari'), fn($sq) => $sq->whereDate('created_at', '>=', $request->dari))
                  ->when($request->filled('sampai'), fn($sq) => $sq->whereDate('created_at', '<=', $request->sampai));
            }])
            ->withSum(['transaksi as total_omzet' => function ($q) use ($request) {
                $q->when($request->filled('dari'), fn($sq) => $sq->whereDate('created_at', '>=', $request->dari))
                  ->when($request->filled('sampai'), fn($sq) => $sq->whereDate('created_at', '<=', $request->sampai));
            }], 'total_bayar')
            ->get();

        return view('laporan.kasir', compact('kasirStats'));
    }

    public function export(Request $request)
    {
        $transaksi = Transaksi::with(['kasir', 'detail'])
            ->when($request->filled('dari'), fn($q) => $q->whereDate('created_at', '>=', $request->dari))
            ->when($request->filled('sampai'), fn($q) => $q->whereDate('created_at', '<=', $request->sampai))
            ->when($request->filled('kasir_id'), fn($q) => $q->where('kasir_id', $request->kasir_id))
            ->orderByDesc('created_at')
            ->get();

        $filename = 'laporan_penjualan_' . date('Ymd_His') . '.csv';
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function () use ($transaksi) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No Invoice', 'Tanggal', 'Kasir', 'Total Harga', 'Diskon', 'Total Bayar', 'Uang Bayar', 'Kembalian']);
            foreach ($transaksi as $t) {
                fputcsv($handle, [
                    $t->no_invoice,
                    $t->created_at->format('d/m/Y H:i'),
                    $t->kasir->name,
                    $t->total_harga,
                    $t->diskon,
                    $t->total_bayar,
                    $t->uang_bayar,
                    $t->kembalian,
                ]);
            }
            fclose($handle);
        };

        // ponytail: fputcsv native, skip Laravel Excel
        return response()->stream($callback, 200, $headers);
    }
}
