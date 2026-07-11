@extends('layouts.app')
@section('title', 'Transaksi')
@section('page-title', 'Transaksi Penjualan')
@section('hide-header', true)

@section('content')
<div class="pos-toolbar">
    <div class="pos-toolbar-copy">
        <div class="pos-toolbar-kicker">Point of Sale</div>
        <div class="pos-toolbar-title">Transaksi Penjualan</div>
        <div class="pos-toolbar-sub">Cari barang, scan kode, lalu selesaikan pembayaran tanpa pindah layar.</div>
    </div>
    <div class="shortcut-strip" aria-label="Shortcut kasir">
        <span class="shortcut-chip"><kbd>F2</kbd> Cari</span>
        <span class="shortcut-chip"><kbd>Enter</kbd> Tambah</span>
        <span class="shortcut-chip"><kbd>F4</kbd> Batal</span>
        <span class="shortcut-chip"><kbd>F9</kbd> Bayar</span>
    </div>
</div>

<div class="pos-layout" style="padding:0; margin:-20px -24px; height:calc(100vh - 64px);">

    {{-- Left: Produk --}}
    <div class="pos-product-panel" style="padding:16px 0 16px 16px;">

        {{-- Search --}}
        <div style="position:relative;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--color-ink-4);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" id="search-produk" placeholder="Cari produk atau kode… [F2]" class="form-input" style="padding-left:34px;">
        </div>

        {{-- Grid --}}
        <div id="produk-grid" class="pos-product-grid">
            @foreach($produk as $p)
            <button type="button" class="pos-product-card"
                 data-id="{{ $p->id }}" data-nama="{{ $p->nama }}"
                 data-harga="{{ $p->harga_jual }}" data-stok="{{ $p->stok }}"
                 data-kode="{{ $p->kode_produk }}"
                 aria-label="Tambah {{ $p->nama }} ke keranjang"
                 onclick="addToCartFromButton(this)">
                <div class="pos-product-thumb">
                    @if($p->gambar)
                    <img src="{{ asset('storage/'.$p->gambar) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                    @else
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="var(--color-ink-4)" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    @endif
                </div>
                <p style="font-size:12px;font-weight:500;color:var(--color-ink);line-height:1.3;margin-bottom:4px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $p->nama }}</p>
                <p style="font-size:12px;font-weight:600;color:var(--color-amber);font-family:var(--font-mono);">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</p>
                <p style="font-size:10px;color:var(--color-ink-4);margin-top:2px;">stok: {{ $p->stok }}</p>
            </button>
            @endforeach
        </div>

        <div id="produk-empty" style="display:none; flex:1; align-items:center; justify-content:center;">
            <p style="font-size:13px;color:var(--color-ink-4);">Tidak ada produk ditemukan</p>
        </div>

        <div class="pos-scan-note">
            Tips: gunakan pencarian untuk barcode/kode produk, lalu tekan <strong>Enter</strong> untuk memasukkan item pertama yang cocok.
        </div>
    </div>

    {{-- Right: Cart --}}
    <div class="pos-cart" style="margin:12px 16px 12px 0; flex-shrink:0; width:268px;">

        <div class="pos-cart-meta">
            <div>
                <p class="pos-cart-title">Keranjang</p>
                <p class="pos-cart-hint">Pantau item sebelum bayar</p>
            </div>
            <button onclick="clearCart()" style="font-size:11px;color:var(--color-danger);background:none;border:none;cursor:pointer;padding:0;">Batal [F4]</button>
        </div>

        <div class="pos-cart-total">
            <div class="pos-cart-total-label">Total Bayar</div>
            <div id="display-total" class="pos-cart-total-value">Rp 0</div>
        </div>

        <div id="cart-items" class="pos-cart-items">
            <div id="cart-empty" style="height:100%;display:flex;align-items:center;justify-content:center;">
                <p style="font-size:12px;color:var(--color-ink-4);">Keranjang kosong</p>
            </div>
        </div>

        <div class="pos-cart-footer" style="display:flex;flex-direction:column;gap:8px;">
            <hr class="divider" style="margin:0;">

            <div style="display:flex;justify-content:space-between;font-size:12px;">
                <span style="color:var(--color-ink-3);">Subtotal</span>
                <span id="display-subtotal" style="font-family:var(--font-mono);color:var(--color-ink);">Rp 0</span>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;font-size:12px;">
                <label for="diskon-input" style="color:var(--color-ink-3);flex-shrink:0;">Diskon (Rp)</label>
                <input type="number" id="diskon-input" min="0" value="0" class="form-input" style="text-align:right;width:100px;padding:4px 8px;font-size:12px;font-family:var(--font-mono);" oninput="updateTotal()">
            </div>

            <hr class="divider" style="margin:0;">

            <div>
                <label class="form-label" for="uang-bayar">Uang Bayar</label>
                <input type="number" id="uang-bayar" min="0" value="0" class="form-input" style="text-align:right;font-family:var(--font-mono);" oninput="updateKembalian()">
            </div>

            <div style="display:flex;justify-content:space-between;font-size:12px;">
                <span style="color:var(--color-ink-3);">Kembalian</span>
                <span id="display-kembalian" style="font-weight:700;font-family:var(--font-mono);color:var(--color-success);">Rp 0</span>
            </div>

            <button id="proses-transaksi" type="button" onclick="prosesTransaksi()" class="btn btn-primary" style="width:100%;justify-content:center;padding:9px;font-size:13px;margin-top:4px;">
                Proses Bayar [F9]
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const cart = {};

function fmt(n) { return 'Rp ' + Math.max(0, parseInt(n)).toLocaleString('id-ID'); }

function escapeHtml(value) {
    return String(value).replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[char]);
}

function addToCartFromButton(button) {
    addToCart(Number(button.dataset.id), button.dataset.nama, Number(button.dataset.harga), Number(button.dataset.stok));
}

function addToCart(id, nama, harga, stok) {
    if (!cart[id]) cart[id] = { id, nama, harga, qty: 0, stok };
    if (cart[id].qty >= stok) { toast('warning', `Stok ${nama} hanya ${stok}`); return; }
    cart[id].qty++;
    renderCart();
}

function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].qty += delta;
    if (cart[id].qty <= 0) delete cart[id];
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const emptyEl   = document.getElementById('cart-empty');
    const items     = Object.values(cart);

    if (!items.length) {
        container.innerHTML = '';
        emptyEl.style.display = 'flex';
        container.appendChild(emptyEl);
        return updateTotal();
    }
    emptyEl.style.display = 'none';

    container.innerHTML = items.map(item => `
    <div class="pos-cart-item">
        <div style="flex:1;min-width:0;">
            <p style="font-size:12px;font-weight:500;color:var(--color-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(item.nama)}</p>
            <p style="font-size:10.5px;color:var(--color-ink-4);font-family:var(--font-mono);">${fmt(item.harga)}</p>
        </div>
        <div style="display:flex;align-items:center;gap:5px;flex-shrink:0;">
            <button type="button" aria-label="Kurangi jumlah ${escapeHtml(item.nama)}" onclick="changeQty(${item.id},-1)" class="btn btn-ghost btn-sm btn-icon" style="cursor:pointer;padding:2px 5px;font-size:13px;line-height:1;">−</button>
            <span style="font-size:12px;font-weight:700;min-width:18px;text-align:center;color:var(--color-ink);">${item.qty}</span>
            <button type="button" aria-label="Tambah jumlah ${escapeHtml(item.nama)}" onclick="changeQty(${item.id},1)" class="btn btn-ghost btn-sm btn-icon" style="cursor:pointer;padding:2px 5px;font-size:13px;line-height:1;">+</button>
        </div>
        <span style="font-size:11.5px;font-weight:600;font-family:var(--font-mono);color:var(--color-amber);width:68px;text-align:right;flex-shrink:0;">${fmt(item.harga*item.qty)}</span>
    </div>`).join('');

    updateTotal();
}

function subtotal() { return Object.values(cart).reduce((s,i)=>s+i.harga*i.qty, 0); }
function diskon()   { return Math.max(0, parseInt(document.getElementById('diskon-input').value)||0); }
function total()    { return Math.max(0, subtotal() - diskon()); }

function updateTotal() {
    document.getElementById('display-subtotal').textContent = fmt(subtotal());
    document.getElementById('display-total').textContent    = fmt(total());
    updateKembalian();
}

function updateKembalian() {
    const uang = parseInt(document.getElementById('uang-bayar').value)||0;
    const kem  = uang - total();
    const el   = document.getElementById('display-kembalian');
    el.textContent = fmt(Math.max(0, kem));
    el.style.color = kem < 0 ? 'var(--color-danger)' : 'var(--color-success)';
}

function clearCart() {
    if (!Object.keys(cart).length) return;
    Swal.fire({
        title: 'Batalkan transaksi?',
        icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Ya, batal', cancelButtonText: 'Tidak',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
    }).then(r => { if(r.isConfirmed){ Object.keys(cart).forEach(k=>delete cart[k]); renderCart(); } });
}

async function prosesTransaksi() {
    const items = Object.values(cart);
    if (!items.length) { toast('warning','Keranjang masih kosong'); return; }
    const t   = total();
    const u   = parseInt(document.getElementById('uang-bayar').value)||0;
    if (u < t) { toast('error','Uang bayar kurang dari total'); return; }

    const conf = await Swal.fire({
        ...window.swalTheme,
        title: 'Proses transaksi?',
        html: `<div style="font-size:13px;color:var(--color-ink-2);">Total <span style="color:var(--color-ink);font-weight:700;">${fmt(t)}</span> · Kembali <span style="color:var(--color-success);font-weight:700;">${fmt(u-t)}</span></div>`,
        icon: 'question', showCancelButton: true,
        confirmButtonText: 'Proses!', cancelButtonText: 'Batal',
    });
    if (!conf.isConfirmed) return;

    const submitButton = document.getElementById('proses-transaksi');
    submitButton.disabled = true;
    Swal.fire({ ...window.swalTheme, title:'Memproses…', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });

    try {
        const res  = await fetch('{{ route('kasir.store') }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({ items: items.map(i=>({id:i.id,qty:i.qty})), diskon:diskon(), uang_bayar:u }),
        });
        const data = res.headers.get('content-type')?.includes('application/json')
            ? await res.json()
            : { success: false, message: 'Respons server tidak dapat diproses. Silakan masuk kembali.' };

        if (data.success) {
            Swal.fire({
                ...window.swalTheme,
                title:'Transaksi Berhasil',
                html:`<div style="font-size:13px;color:var(--color-ink-2);">Invoice <span style="font-family:var(--font-mono);color:var(--color-ink);">${data.no_invoice}</span><br>Kembalian <span style="color:var(--color-success);font-weight:700;">${fmt(data.kembalian)}</span></div>`,
                icon:'success', showCancelButton:true,
                confirmButtonText:'Cetak Struk', cancelButtonText:'Selesai',
            }).then(r => {
                if(r.isConfirmed) window.open(`/kasir/struk/${data.transaksi}`,'_blank');
                Object.keys(cart).forEach(k=>delete cart[k]);
                document.getElementById('diskon-input').value = 0;
                document.getElementById('uang-bayar').value   = 0;
                renderCart();
            });
        } else {
            Swal.fire({ ...window.swalTheme, title:'Gagal', text:data.message, icon:'error' });
        }
    } catch(e) {
        Swal.fire({ ...window.swalTheme, title:'Error', text:'Koneksi gagal.', icon:'error' });
    } finally {
        submitButton.disabled = false;
    }
}

document.getElementById('search-produk').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    let visible = 0;
    document.querySelectorAll('.pos-product-card').forEach(c => {
        const match = c.dataset.nama.toLowerCase().includes(q) || c.dataset.kode.toLowerCase().includes(q);
        c.style.display = match ? '' : 'none';
        if(match) visible++;
    });
    document.getElementById('produk-empty').style.display = visible ? 'none' : 'flex';
    document.getElementById('produk-grid').style.display = visible ? '' : 'none';
});

// Keyboard Shortcuts and Autofocus
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-produk').focus();
});

document.addEventListener('keydown', function(e) {
    // F2: Focus Search Input
    if (e.key === 'F2') {
        e.preventDefault();
        const searchInput = document.getElementById('search-produk');
        searchInput.focus();
        searchInput.select();
    }
    // F4: Cancel/Clear Cart
    if (e.key === 'F4') {
        e.preventDefault();
        clearCart();
    }
    // F9: Pay / Process transaction
    if (e.key === 'F9') {
        e.preventDefault();
        prosesTransaksi();
    }
    // Enter in search: Add first matched product
    if (e.key === 'Enter' && document.activeElement === document.getElementById('search-produk')) {
        e.preventDefault();
        const visibleCards = Array.from(document.querySelectorAll('.pos-product-card')).filter(c => c.style.display !== 'none');
        if (visibleCards.length > 0) {
            visibleCards[0].click();
            document.getElementById('search-produk').value = '';
            document.getElementById('search-produk').dispatchEvent(new Event('input'));
        }
    }
});
</script>
@endpush
