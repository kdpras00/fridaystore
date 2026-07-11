<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokMutasi;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index(Request $request)
    {
        $produk = Produk::with('kategori')
            ->where('stok', '>', 0)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($sq) use ($request) {
                    $sq->where('nama', 'like', '%' . $request->search . '%')
                       ->orWhere('kode_produk', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('nama')
            ->get();

        return view('kasir.index', compact('produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'      => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'distinct', 'exists:produk,id'],
            'items.*.qty'=> ['required', 'integer', 'min:1'],
            'diskon'     => ['nullable', 'numeric', 'min:0'],
            'uang_bayar' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $totalHarga = 0;
            $details    = [];

            foreach ($request->items as $item) {
                $produk = Produk::lockForUpdate()->findOrFail($item['id']);

                if ($produk->stok < $item['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$produk->nama} tidak mencukupi. Tersedia: {$produk->stok}",
                    ], 422);
                }

                $subtotal    = $produk->harga_jual * $item['qty'];
                $totalHarga += $subtotal;

                $details[] = [
                    'produk_id'   => $produk->id,
                    'nama_produk' => $produk->nama,
                    'harga_jual'  => $produk->harga_jual,
                    'qty'         => $item['qty'],
                    'subtotal'    => $subtotal,
                ];

                $produk->decrement('stok', $item['qty']);

                StokMutasi::create([
                    'produk_id'  => $produk->id,
                    'user_id'    => auth()->id(),
                    'tipe'       => 'keluar',
                    'jumlah'     => $item['qty'],
                    'keterangan' => 'Penjualan',
                ]);
            }

            $diskon     = (float) ($request->diskon ?? 0);

            if ($diskon > $totalHarga) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Diskon tidak boleh melebihi subtotal.'], 422);
            }

            $totalBayar = max(0, $totalHarga - $diskon);
            $kembalian  = $request->uang_bayar - $totalBayar;

            if ($kembalian < 0) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Uang bayar kurang.'], 422);
            }

            // Generate secure unique invoice number with collision check loop
            $invoiceSeed = 'INV-' . date('Ymd') . '-';
            $invoiceIndex = Transaksi::whereDate('created_at', today())->count() + 1;
            do {
                $noInvoice = $invoiceSeed . str_pad($invoiceIndex, 4, '0', STR_PAD_LEFT);
                $exists = Transaksi::where('no_invoice', $noInvoice)->exists();
                if ($exists) {
                    $invoiceIndex++;
                }
            } while ($exists);

            $transaksi = Transaksi::create([
                'no_invoice'  => $noInvoice,
                'kasir_id'    => auth()->id(),
                'total_harga' => $totalHarga,
                'diskon'      => $diskon,
                'total_bayar' => $totalBayar,
                'uang_bayar'  => $request->uang_bayar,
                'kembalian'   => $kembalian,
            ]);

            foreach ($details as $d) {
                $transaksi->detail()->create($d);
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Transaksi berhasil!',
                'transaksi'  => $transaksi->id,
                'no_invoice' => $noInvoice,
                'kembalian'  => $kembalian,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan. Coba lagi.'], 500);
        }
    }

    public function struk(Transaksi $transaksi)
    {
        abort_unless($transaksi->kasir_id === auth()->id(), 403);

        $transaksi->load(['detail', 'kasir']);
        return view('kasir.struk', compact('transaksi'));
    }
}
