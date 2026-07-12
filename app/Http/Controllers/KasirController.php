<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokMutasi;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

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
            'items'          => ['required', 'array', 'min:1'],
            'items.*.id'     => ['required', 'distinct', 'exists:produk,id'],
            'items.*.qty'    => ['required', 'integer', 'min:1'],
            'diskon'         => ['nullable', 'numeric', 'min:0'],
            'uang_bayar'     => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,xendit'],
        ]);

        $paymentMethod = $request->payment_method;

        // Cash requires uang_bayar
        if ($paymentMethod === 'cash') {
            $request->validate(['uang_bayar' => ['required', 'numeric', 'min:1']]);
        }

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

                $subtotal    = (int) round($produk->harga_jual * $item['qty']);
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

            $diskon = (int) round((float) ($request->diskon ?? 0));

            if ($diskon > $totalHarga) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Diskon tidak boleh melebihi subtotal.'], 422);
            }

            $totalBayar = max(0, $totalHarga - $diskon);

            // Cash-specific validation
            $uangBayar = 0;
            $kembalian = 0;
            if ($paymentMethod === 'cash') {
                $uangBayar = (int) round((float) $request->uang_bayar);
                $kembalian = $uangBayar - $totalBayar;
                if ($kembalian < 0) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Uang bayar kurang.'], 422);
                }
            }

            // Generate invoice number
            $prefix    = 'INV-' . date('Ymd') . '-';
            $noInvoice = null;
            for ($attempt = 1; $attempt <= 10; $attempt++) {
                $count     = Transaksi::whereDate('created_at', today())->count();
                $candidate = $prefix . str_pad($count + $attempt, 4, '0', STR_PAD_LEFT);
                if (!Transaksi::where('no_invoice', $candidate)->exists()) {
                    $noInvoice = $candidate;
                    break;
                }
            }
            if (!$noInvoice) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Gagal membuat nomor invoice. Coba lagi.'], 500);
            }

            $transaksi = Transaksi::create([
                'no_invoice'     => $noInvoice,
                'kasir_id'       => auth()->id(),
                'total_harga'    => $totalHarga,
                'diskon'         => $diskon,
                'total_bayar'    => $totalBayar,
                'uang_bayar'     => $uangBayar,
                'kembalian'      => $kembalian,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'cash' ? 'paid' : 'pending',
            ]);

            foreach ($details as $d) {
                $transaksi->detail()->create($d);
            }

            // Xendit: create invoice & rollback stok if Xendit fails
            if ($paymentMethod === 'xendit') {
                try {
                    $invoiceUrl = $this->createXenditInvoice($transaksi, $totalBayar);
                    $transaksi->update([
                        'xendit_invoice_id'  => $invoiceUrl['invoice_id'],
                        'xendit_invoice_url' => $invoiceUrl['invoice_url'],
                    ]);
                } catch (\Throwable $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membuat invoice Xendit: ' . $e->getMessage(),
                    ], 500);
                }
            }

            DB::commit();

            $response = [
                'success'        => true,
                'message'        => 'Transaksi berhasil!',
                'transaksi'      => $transaksi->id,
                'no_invoice'     => $noInvoice,
                'payment_method' => $paymentMethod,
            ];

            if ($paymentMethod === 'cash') {
                $response['kembalian'] = $kembalian;
            } else {
                $response['invoice_url']    = $transaksi->xendit_invoice_url;
                $response['xendit_invoice_id'] = $transaksi->xendit_invoice_id;
            }

            return response()->json($response);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan. Coba lagi.'], 500);
        }
    }

    private function createXenditInvoice(Transaksi $transaksi, int $amount): array
    {
        Configuration::setXenditKey(config('services.xendit.secret_key'));

        $apiInstance = new InvoiceApi();

        $createInvoiceRequest = new CreateInvoiceRequest([
            'external_id'      => $transaksi->no_invoice,
            'amount'           => $amount,
            'description'      => 'Pembayaran ' . $transaksi->no_invoice . ' - FridayStore',
            'invoice_duration' => 300, // 5 menit
            'currency'         => 'IDR',
        ]);

        $invoice = $apiInstance->createInvoice($createInvoiceRequest);

        return [
            'invoice_id'  => $invoice->getId(),
            'invoice_url' => $invoice->getInvoiceUrl(),
        ];
    }

    public function checkPaymentStatus(Transaksi $transaksi)
    {
        // Kasir hanya bisa cek miliknya sendiri
        abort_unless($transaksi->kasir_id === auth()->id(), 403);

        // Sudah paid, langsung return
        if ($transaksi->payment_status === 'paid') {
            return response()->json([
                'status'     => 'paid',
                'transaksi'  => $transaksi->id,
                'no_invoice' => $transaksi->no_invoice,
            ]);
        }

        // Cek ke Xendit
        try {
            Configuration::setXenditKey(config('services.xendit.secret_key'));
            $apiInstance = new InvoiceApi();
            $invoice     = $apiInstance->getInvoiceById($transaksi->xendit_invoice_id);
            $status      = strtolower($invoice->getStatus());

            if (in_array($status, ['settled', 'paid'])) {
                $transaksi->update(['payment_status' => 'paid']);
                return response()->json([
                    'status'     => 'paid',
                    'transaksi'  => $transaksi->id,
                    'no_invoice' => $transaksi->no_invoice,
                ]);
            }

            if ($status === 'expired') {
                $transaksi->update(['payment_status' => 'expired']);
            }

            return response()->json(['status' => $status]);

        } catch (\Throwable $e) {
            return response()->json(['status' => 'pending']);
        }
    }

    public function riwayat(Request $request)
    {
        $dari   = $request->filled('dari')   ? $request->dari   : now()->startOfMonth()->toDateString();
        $sampai = $request->filled('sampai') ? $request->sampai : now()->toDateString();

        $transaksi = Transaksi::with('detail')
            ->where('kasir_id', auth()->id())
            ->whereDate('created_at', '>=', $dari)
            ->whereDate('created_at', '<=', $sampai)
            ->orderByDesc('created_at')
            ->get();

        $totalOmzet = $transaksi->sum('total_bayar');

        return view('kasir.riwayat', compact('transaksi', 'totalOmzet', 'dari', 'sampai'));
    }

    public function struk(Transaksi $transaksi)
    {
        $user = auth()->user();
        if ($user->hasRole('kasir')) {
            abort_unless($transaksi->kasir_id === $user->id, 403);
        }

        $transaksi->load(['detail', 'kasir']);
        return view('kasir.struk', compact('transaksi'));
    }
}
