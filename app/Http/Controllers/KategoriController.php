<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = KategoriProduk::withCount('produk')->orderBy('nama')->get();
        return view('kategori.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => ['required', 'string', 'max:100', 'unique:kategori_produk,nama']]);
        KategoriProduk::create(['nama' => $request->nama]);
        return back()->with('swal_success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, KategoriProduk $kategori)
    {
        $request->validate(['nama' => ['required', 'string', 'max:100', 'unique:kategori_produk,nama,' . $kategori->id]]);
        $kategori->update(['nama' => $request->nama]);
        return back()->with('swal_success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriProduk $kategori)
    {
        if ($kategori->produk()->exists()) {
            return back()->with('swal_error', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
        }
        $kategori->delete();
        return back()->with('swal_success', 'Kategori berhasil dihapus.');
    }
}
