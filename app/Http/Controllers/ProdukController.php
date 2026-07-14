<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\StokMutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with('kategori');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_produk', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        $produk   = $query->orderBy('nama')->get();
        $kategori = KategoriProduk::orderBy('nama')->get();

        return view('produk.index', compact('produk', 'kategori'));
    }

    public function create()
    {
        $kategori = KategoriProduk::orderBy('nama')->get();
        return view('produk.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_id'  => ['required', 'exists:kategori_produk,id'],
            'nama'         => ['required', 'string', 'max:150'],
            'harga_beli'   => ['required', 'numeric', 'min:0'],
            'harga_jual'   => ['required', 'numeric', 'min:0'],
            'stok'         => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'gambar'       => ['nullable', 'image', 'max:2048'],
            'galeri.*'     => ['nullable', 'image', 'max:2048'],
        ]);

        $data['kode_produk'] = Produk::generateKode($data['kategori_id']);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        DB::transaction(function () use ($data, $request) {
            $produk = Produk::create($data);

            if ($request->hasFile('galeri')) {
                foreach ($request->file('galeri') as $file) {
                    $path = $file->store('produk/galeri', 'public');
                    $produk->galeri()->create(['path' => $path]);
                }
            }

            if ($produk->stok > 0) {
                StokMutasi::create([
                    'produk_id'  => $produk->id,
                    'user_id'    => auth()->id(),
                    'tipe'       => 'masuk',
                    'jumlah'     => $produk->stok,
                    'keterangan' => 'Stok awal produk',
                ]);
            }
        });

        return redirect()->route('produk.index')->with('swal_success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        $produk->load('galeri');
        $kategori = KategoriProduk::orderBy('nama')->get();
        return view('produk.edit', compact('produk', 'kategori'));
    }

    public function update(Request $request, Produk $produk)
    {
        $data = $request->validate([
            'kategori_id'  => ['required', 'exists:kategori_produk,id'],
            'nama'         => ['required', 'string', 'max:150'],
            'harga_beli'   => ['required', 'numeric', 'min:0'],
            'harga_jual'   => ['required', 'numeric', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'gambar'       => ['nullable', 'image', 'max:2048'],
            'galeri.*'     => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar) Storage::disk('public')->delete($produk->gambar);
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }

        $produk->update($data);

        if ($request->has('hapus_galeri')) {
            $galeriToHapus = $produk->galeri()->whereIn('id', $request->hapus_galeri)->get();
            foreach ($galeriToHapus as $gal) {
                Storage::disk('public')->delete($gal->path);
                $gal->delete();
            }
        }

        if ($request->hasFile('galeri')) {
            foreach ($request->file('galeri') as $file) {
                $path = $file->store('produk/galeri', 'public');
                $produk->galeri()->create(['path' => $path]);
            }
        }

        return redirect()->route('produk.index')->with('swal_success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        if ($produk->transaksiDetail()->exists() || $produk->stokMutasi()->exists()) {
            return back()->with('swal_error', 'Produk tidak dapat dihapus karena memiliki riwayat transaksi atau mutasi stok.');
        }

        $produk->load('galeri');
        if ($produk->gambar) Storage::disk('public')->delete($produk->gambar);
        foreach ($produk->galeri as $galeri) {
            Storage::disk('public')->delete($galeri->path);
        }
        
        $produk->delete();
        return back()->with('swal_success', 'Produk berhasil dihapus.');
    }
}
