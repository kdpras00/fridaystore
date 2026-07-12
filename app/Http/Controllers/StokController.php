<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\StokMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('kategori');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nama', 'like', "%$s%")
                                      ->orWhere('kode_produk', 'like', "%$s%"));
        }

        if ($request->status === 'rendah') {
            $query->whereColumn('stok', '<=', 'stok_minimum');
        } elseif ($request->status === 'aman') {
            $query->whereColumn('stok', '>', 'stok_minimum');
        }

        $produk = $query->orderBy('nama')->get();

        // Count across the entire dataset, not just the current page.
        $lowStockCount = Produk::whereColumn('stok', '<=', 'stok_minimum')->count();

        return view('stok.index', compact('produk', 'lowStockCount'));
    }

    public function restock(Request $request)
    {
        $request->validate([
            'produk_id'   => ['required', 'exists:produk,id'],
            'jumlah'      => ['required', 'integer', 'min:1'],
            'keterangan'  => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request) {
            $produk = Produk::lockForUpdate()->findOrFail($request->produk_id);
            $produk->increment('stok', $request->jumlah);

            StokMutasi::create([
                'produk_id'  => $produk->id,
                'user_id'    => auth()->id(),
                'tipe'       => 'masuk',
                'jumlah'     => $request->jumlah,
                'keterangan' => $request->keterangan ?? 'Restock manual',
            ]);
        });

        $produk = Produk::findOrFail($request->produk_id);
        return back()->with('swal_success', "Stok {$produk->nama} berhasil ditambah {$request->jumlah} unit.");
    }

    public function riwayat(Request $request)
    {
        $mutasi = StokMutasi::with(['produk', 'user'])
            ->when($request->filled('produk_id'), fn($q) => $q->where('produk_id', $request->produk_id))
            ->when($request->filled('tipe'), fn($q) => $q->where('tipe', $request->tipe))
            ->orderByDesc('created_at')
            ->get();

        $produkList = Produk::orderBy('nama')->get();
        return view('stok.riwayat', compact('mutasi', 'produkList'));
    }

    // Owner: read-only stok view
    public function ownerIndex(Request $request)
    {
        $query = Produk::with('kategori');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nama', 'like', "%$s%")
                                      ->orWhere('kode_produk', 'like', "%$s%"));
        }
        $produk = $query->orderBy('nama')->paginate(20)->withQueryString();
        return view('stok.owner', compact('produk'));
    }
}
