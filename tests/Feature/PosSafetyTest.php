<?php

use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;

function createProduk(): Produk
{
    $kategori = KategoriProduk::create(['nama' => 'Jaket']);

    return Produk::create([
        'kategori_id' => $kategori->id,
        'kode_produk' => 'JKT001',
        'nama' => 'Jaket Hoodie',
        'harga_beli' => 100000,
        'harga_jual' => 150000,
        'stok' => 10,
        'stok_minimum' => 2,
    ]);
}

function createTransaksi(User $kasir): Transaksi
{
    return Transaksi::create([
        'no_invoice' => 'INV-20260710-0001',
        'kasir_id' => $kasir->id,
        'total_harga' => 150000,
        'diskon' => 0,
        'total_bayar' => 150000,
        'uang_bayar' => 200000,
        'kembalian' => 50000,
    ]);
}

test('cashier can only open their own receipt', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $kasirLain = User::factory()->create(['role' => 'kasir']);
    $transaksi = createTransaksi($kasir);

    $this->actingAs($kasir)
        ->get(route('kasir.struk', $transaksi))
        ->assertOk();

    $this->actingAs($kasirLain)
        ->get(route('kasir.struk', $transaksi))
        ->assertForbidden();
});

test('cashier cannot submit a discount larger than the subtotal', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $produk = createProduk();

    $this->actingAs($kasir)
        ->postJson(route('kasir.store'), [
            'items' => [['id' => $produk->id, 'qty' => 1]],
            'diskon' => 200000,
            'uang_bayar' => 0,
        ])
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Diskon tidak boleh melebihi subtotal.');

    expect($produk->fresh()->stok)->toBe(10);
});

test('product with sales history cannot be deleted', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $kasir = User::factory()->create(['role' => 'kasir']);
    $produk = createProduk();
    $transaksi = createTransaksi($kasir);

    TransaksiDetail::create([
        'transaksi_id' => $transaksi->id,
        'produk_id' => $produk->id,
        'nama_produk' => $produk->nama,
        'harga_jual' => $produk->harga_jual,
        'qty' => 1,
        'subtotal' => $produk->harga_jual,
    ]);

    $this->actingAs($admin)
        ->delete(route('produk.destroy', $produk))
        ->assertRedirect();

    $this->assertDatabaseHas('produk', ['id' => $produk->id]);
    $this->assertDatabaseHas('transaksi_detail', ['produk_id' => $produk->id]);
});

test('user with transaction history cannot be deleted', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $kasir = User::factory()->create(['role' => 'kasir']);
    createTransaksi($kasir);

    $this->actingAs($admin)
        ->delete(route('users.destroy', $kasir))
        ->assertRedirect();

    $this->assertDatabaseHas('users', ['id' => $kasir->id]);
    $this->assertDatabaseHas('transaksi', ['kasir_id' => $kasir->id]);
});
