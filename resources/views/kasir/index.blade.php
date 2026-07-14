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

</div>

<div class="pos-layout" style="padding:0; margin:-20px -24px; height:calc(100vh - 64px);">

    {{-- ── LEFT: Product grid ─────────────────────────────── --}}
    <div class="pos-product-panel" style="padding:16px 0 16px 16px;">

        <div style="position:relative;">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--color-ink-4);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" id="search-produk" placeholder="Cari produk atau kode…" class="form-input" style="padding-left:34px;" autocomplete="off">
        </div>

        <div id="produk-grid" class="pos-product-grid">
            @foreach($produk as $p)
            @php($isLow = $p->stok <= $p->stok_minimum && $p->stok_minimum > 0)
            <button type="button"
                 class="pos-product-card {{ $isLow ? 'pos-product-card--low' : '' }}"
                 style="position:relative;"
                 data-id="{{ $p->id }}"
                 data-nama="{{ $p->nama }}"
                 data-harga="{{ $p->harga_jual }}"
                 data-stok="{{ $p->stok }}"
                 data-kode="{{ $p->kode_produk }}"
                 aria-label="Tambah {{ $p->nama }} ke keranjang"
                 onclick="addToCartFromButton(this)">
                <span id="badge-{{ $p->id }}" data-badge
                      style="display:none;position:absolute;top:6px;right:6px;
                             background:var(--color-amber);color:#fff;
                             font-size:10px;font-weight:700;min-width:18px;height:18px;
                             border-radius:999px;align-items:center;justify-content:center;
                             padding:0 4px;line-height:1;z-index:10;"></span>
                <div class="pos-product-thumb" style="position:relative;overflow:hidden;">
                    @if($p->gambar)
                        @if($p->galeri->count() > 0)
                            <div class="pos-img-hover-wrap" style="width:100%;height:100%;position:relative;">
                                <img src="{{ asset('storage/'.$p->gambar) }}" class="img-main" style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;transition:opacity 0.3s ease;" alt="{{ $p->nama }}">
                                <img src="{{ asset('storage/'.$p->galeri->first()->path) }}" class="img-hover" style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;opacity:0;transition:opacity 0.3s ease;" alt="{{ $p->nama }} (alt)">
                            </div>
                        @else
                            <img src="{{ asset('storage/'.$p->gambar) }}" style="width:100%;height:100%;object-fit:cover;" alt="{{ $p->nama }}">
                        @endif
                    @else
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="var(--color-ink-4)" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    @endif
                </div>
                <p style="font-size:12px;font-weight:500;color:var(--color-ink);line-height:1.35;margin-bottom:4px;
                           overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $p->nama }}</p>
                <p style="font-size:12px;font-weight:600;color:var(--color-amber);font-family:var(--font-mono);">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</p>
                <p data-stok-label style="font-size:10px;margin-top:2px;
                   color:{{ $isLow ? 'var(--color-danger)' : 'var(--color-ink-4)' }};">
                    stok: {{ $p->stok }}{{ $isLow ? ' !' : '' }}
                </p>
            </button>
            @endforeach
        </div>

        <div id="produk-empty" style="display:none;flex:1;align-items:center;justify-content:center;">
            <p style="font-size:13px;color:var(--color-ink-4);">Tidak ada produk ditemukan</p>
        </div>

    </div>

    {{-- ── RIGHT: Cart ────────────────────────────────────── --}}
    <div class="pos-cart" style="margin:12px 16px 12px 0;">

        {{-- Header --}}
        <div class="pos-cart-meta">
            <div>
                <p class="pos-cart-title">Keranjang</p>
                <p class="pos-cart-hint" id="cart-count-hint">Belum ada item</p>
            </div>
            <button onclick="clearCart()"
                    style="font-size:11px;color:var(--color-danger);background:none;border:none;cursor:pointer;padding:4px 0;">
                Batal
            </button>
        </div>


        {{-- Items list --}}
        <div id="cart-items" class="pos-cart-items">
            <div id="cart-empty" style="height:72px;display:flex;align-items:center;justify-content:center;">
                <p style="font-size:12px;color:var(--color-ink-4);">Keranjang kosong</p>
            </div>
            <div id="cart-list"></div>
        </div>

        {{-- Payment footer --}}
        <div class="pos-cart-footer" style="display:flex;flex-direction:column;gap:10px;">

            {{-- Subtotal row --}}
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12.5px;">
                <span style="color:var(--color-ink-3);">Subtotal</span>
                <span id="display-subtotal" style="font-family:var(--font-mono);font-weight:500;color:var(--color-ink);">Rp 0</span>
            </div>

            {{-- Diskon --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <span style="font-size:12.5px;color:var(--color-ink-3);flex-shrink:0;">Diskon</span>
                <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                    {{-- Mode toggle: Rp / % --}}
                    <div style="display:flex;border:1px solid var(--color-border);border-radius:6px;overflow:hidden;">
                        <button id="mode-rp" type="button" onclick="setDiskonMode('nominal')"
                                class="pos-diskon-mode-btn pos-diskon-mode-btn--active"
                                style="padding:3px 8px;font-size:11px;font-weight:600;cursor:pointer;border:none;transition:background 100ms,color 100ms;">Rp</button>
                        <button id="mode-pct" type="button" onclick="setDiskonMode('persen')"
                                class="pos-diskon-mode-btn"
                                style="padding:3px 8px;font-size:11px;font-weight:500;cursor:pointer;border:none;border-left:1px solid var(--color-border);transition:background 100ms,color 100ms;">%</button>
                    </div>
                    <input type="text" id="diskon-display" inputmode="numeric"
                           placeholder="0"
                           class="form-input"
                           style="text-align:right;width:80px;padding:4px 8px;font-size:12px;font-family:var(--font-mono);"
                           oninput="onDiskonInput(this)" onfocus="selectAll(this)" autocomplete="off">
                </div>
            </div>

            {{-- Potongan line --}}
            <div id="potongan-row" style="display:none;justify-content:space-between;align-items:center;font-size:12px;">
                <span style="color:var(--color-ink-4);">Potongan</span>
                <span id="display-diskon" style="font-family:var(--font-mono);color:var(--color-danger);"></span>
            </div>

            {{-- PPN 11% line --}}
            <div id="ppn-row" style="display:none;justify-content:space-between;align-items:center;font-size:12px;margin-top:4px;">
                <span style="color:var(--color-ink-4);">PPN 11%</span>
                <span id="display-ppn" style="font-family:var(--font-mono);color:var(--color-ink-2);"></span>
            </div>

            <hr style="margin:4px 0;border:none;border-top:1px dashed var(--color-border);">

            {{-- Total Bayar (Pindah ke bawah) --}}
            <div style="display:flex;justify-content:space-between;align-items:flex-end;margin:4px 0 8px;">
                <span style="font-size:14px;font-weight:600;color:var(--color-ink);">Total Bayar</span>
                <span id="display-total" style="font-family:var(--font-mono);font-size:24px;font-weight:700;color:var(--color-amber);letter-spacing:-0.02em;line-height:1;">Rp 0</span>
            </div>

            {{-- Metode Bayar --}}
            <div>
                <p style="font-size:12px;color:var(--color-ink-3);margin-bottom:6px;">Metode Pembayaran</p>
                <div style="display:flex;border:1px solid var(--color-border);border-radius:8px;overflow:hidden;">
                    <button id="pm-cash" type="button" onclick="setPaymentMethod('cash')"
                            class="pos-diskon-mode-btn pos-diskon-mode-btn--active"
                            style="flex:1;padding:7px 4px;font-size:12px;font-weight:600;cursor:pointer;border:none;
                                   display:flex;align-items:center;justify-content:center;gap:5px;transition:background 120ms,color 120ms;">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                        </svg>
                        Cash
                    </button>
                    <button id="pm-xendit" type="button" onclick="setPaymentMethod('xendit')"
                            class="pos-diskon-mode-btn"
                            style="flex:1;padding:7px 4px;font-size:12px;font-weight:500;cursor:pointer;border:none;
                                   border-left:1px solid var(--color-border);display:flex;align-items:center;
                                   justify-content:center;gap:5px;transition:background 120ms,color 120ms;">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01"/>
                        </svg>
                        QRIS / Transfer
                    </button>
                </div>
            </div>

            {{-- Uang Bayar (cash only) --}}
            <div id="cash-section">
                <label style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                    <span style="font-size:12.5px;font-weight:500;color:var(--color-ink-2);">Uang Bayar</span>
                    <span id="sisa-label" style="font-size:11px;color:var(--color-danger);display:none;font-family:var(--font-mono);"></span>
                </label>
                <input type="text" id="uang-bayar-display" inputmode="numeric"
                       placeholder="Masukkan nominal…"
                       class="form-input"
                       style="text-align:right;font-family:var(--font-mono);font-size:15px;font-weight:700;
                              padding:9px 12px;color:var(--color-ink);"
                       oninput="onUangBayarInput(this)" onfocus="selectAll(this)" autocomplete="off"
                       maxlength="14">

                {{-- Quick-pay chips --}}
                <div id="quick-pay-strip" style="display:flex;gap:4px;flex-wrap:wrap;margin-top:6px;"></div>

                {{-- Kembalian --}}
                <div id="kembalian-box"
                     style="display:none;justify-content:space-between;align-items:center;
                            padding:8px 12px;border-radius:8px;margin-top:6px;
                            background:var(--color-success-dim);border:1px solid oklch(0.45 0.14 145 / 0.25);">
                    <span style="font-size:12px;color:var(--color-ink-3);">Kembalian</span>
                    <span id="display-kembalian"
                          style="font-size:15px;font-weight:700;font-family:var(--font-mono);color:var(--color-success);"></span>
                </div>
            </div>

            {{-- Xendit info (xendit only) --}}
            <div id="xendit-section" style="display:none;">
                <div style="padding:10px 12px;border-radius:8px;background:var(--color-surface-2);
                            border:1px solid var(--color-border);font-size:12px;color:var(--color-ink-3);line-height:1.5;">
                    Invoice Xendit akan dibuka di tab baru. Kasir menunggu konfirmasi pembayaran sebelum transaksi selesai.
                </div>
            </div>

            {{-- Proses --}}
            <button id="proses-transaksi" type="button" onclick="prosesTransaksi()"
                    class="btn btn-primary"
                    style="width:100%;justify-content:center;padding:11px;font-size:13.5px;font-weight:600;margin-top:2px;">
                Proses Bayar
            </button>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── State ───────────────────────────────────────────────────
const cart = {};
let _diskonRaw    = 0;
let _diskonMode   = 'nominal';  // 'nominal' | 'persen'
let _uangBayar    = 0;
let _uangTouched  = false;
let _paymentMethod = 'cash';    // 'cash' | 'xendit'
let _pollInterval  = null;

const MAX_BAYAR = 100_000_000;

// ── Helpers ─────────────────────────────────────────────────
function fmt(n) {
    return 'Rp\u00a0' + Math.max(0, Math.round(n)).toLocaleString('id-ID');
}
function fmtNum(n) {
    return Math.max(0, Math.round(n)).toLocaleString('id-ID');
}
function parseRaw(str) {
    const digits = String(str).replace(/\D/g, '');
    return digits === '' ? 0 : (parseInt(digits, 10) || 0);
}
function selectAll(el) {
    // defer so browser doesn't cancel the selection on mouseup
    setTimeout(() => el.select(), 0);
}
function escapeHtml(v) {
    return String(v).replace(/[&<>'"]/g,
        c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'})[c]);
}

// ── Payment method toggle ────────────────────────────────────
function setPaymentMethod(method) {
    _paymentMethod = method;
    const isCash = method === 'cash';

    document.getElementById('cash-section').style.display   = isCash ? '' : 'none';
    document.getElementById('xendit-section').style.display = isCash ? 'none' : '';

    document.getElementById('pm-cash').classList.toggle('pos-diskon-mode-btn--active', isCash);
    document.getElementById('pm-xendit').classList.toggle('pos-diskon-mode-btn--active', !isCash);

    const btn = document.getElementById('proses-transaksi');
    btn.textContent = isCash ? 'Proses Bayar' : 'Buat Invoice Xendit';
}

// ── Diskon mode toggle ───────────────────────────────────────
function setDiskonMode(mode) {
    _diskonMode = mode;
    _diskonRaw  = 0;
    document.getElementById('diskon-display').value = '';
    document.getElementById('diskon-display').placeholder = mode === 'persen' ? '0' : '0';

    const btnRp  = document.getElementById('mode-rp');
    const btnPct = document.getElementById('mode-pct');
    if (mode === 'nominal') {
        btnRp.classList.add('pos-diskon-mode-btn--active');
        btnPct.classList.remove('pos-diskon-mode-btn--active');
    } else {
        btnPct.classList.add('pos-diskon-mode-btn--active');
        btnRp.classList.remove('pos-diskon-mode-btn--active');
    }
    updateTotal();
}

function onDiskonInput(el) {
    const raw = parseRaw(el.value);
    if (_diskonMode === 'persen') {
        _diskonRaw = Math.min(raw, 100);
        el.value   = raw === 0 ? '' : String(_diskonRaw);
    } else {
        _diskonRaw = Math.min(raw, subtotal());
        el.value   = raw === 0 ? '' : fmtNum(_diskonRaw);
    }
    updateTotal();
}

// ── Uang Bayar ───────────────────────────────────────────────
function onUangBayarInput(el) {
    _uangTouched = true;
    _uangBayar   = Math.min(parseRaw(el.value), MAX_BAYAR);
    const raw = parseRaw(el.value);
    el.value = raw === 0 ? '' : fmtNum(_uangBayar);
    updateKembalian();
}

// ── Cart mutations ───────────────────────────────────────────
function addToCartFromButton(button) {
    const id    = Number(button.dataset.id);
    const harga = Math.round(parseFloat(button.dataset.harga));
    const stok  = Number(button.dataset.stok);
    addToCart(id, button.dataset.nama, harga, stok);
}

function addToCart(id, nama, harga, stok) {
    if (!cart[id]) cart[id] = { id, nama, harga, qty: 0, stok };
    if (cart[id].qty >= cart[id].stok) {
        toast('warning', `Stok ${nama} hanya ${cart[id].stok}`);
        return;
    }
    cart[id].qty++;
    syncCardState(id);
    renderCart();
}

function changeQty(id, delta) {
    id = Number(id);
    if (!cart[id]) return;
    if (delta > 0 && cart[id].qty >= cart[id].stok) {
        toast('warning', `Stok ${cart[id].nama} hanya ${cart[id].stok}`);
        return;
    }
    const newQty = cart[id].qty + delta;
    if (newQty <= 0) {
        delete cart[id];
        syncCardState(id);
        renderCart();
        return;
    }
    cart[id].qty = newQty;
    syncCardState(id);
    renderCart();
}

// Sync product card border + badge with current cart state
function syncCardState(id) {
    id = Number(id);
    const btn   = document.querySelector(`.pos-product-card[data-id="${id}"]`);
    if (!btn) return;
    const item  = cart[id];
    const qty   = item ? item.qty : 0;
    const stok  = item ? item.stok : Number(btn.dataset.stok);
    const label = btn.querySelector('[data-stok-label]');
    const badge = btn.querySelector('[data-badge]');

    if (label) {
        const rem = stok - qty;
        label.textContent = `stok: ${rem}${rem <= 0 ? ' !' : ''}`;
        label.style.color = rem <= 0 ? 'var(--color-danger)' : 'var(--color-ink-4)';
    }
    if (qty > 0) {
        btn.classList.add('pos-product-card--selected');
        if (badge) { badge.textContent = qty; badge.style.display = 'inline-flex'; }
    } else {
        btn.classList.remove('pos-product-card--selected');
        if (badge) badge.style.display = 'none';
    }
    if (item && item.qty >= item.stok) {
        btn.setAttribute('disabled', 'disabled');
    } else {
        btn.removeAttribute('disabled');
    }
}

// ── Render cart list ─────────────────────────────────────────
function renderCart() {
    const listEl  = document.getElementById('cart-list');
    const emptyEl = document.getElementById('cart-empty');
    const hintEl  = document.getElementById('cart-count-hint');
    const items   = Object.values(cart);

    if (!items.length) {
        listEl.innerHTML = '';
        emptyEl.style.display = 'flex';
        hintEl.textContent = 'Belum ada item';
        return updateTotal();
    }

    emptyEl.style.display = 'none';
    const totalItems = items.reduce((s, i) => s + i.qty, 0);
    hintEl.textContent = `${totalItems} item${totalItems > 1 ? '' : ''} dipilih`;

    listEl.innerHTML = items.map(item => `
    <div class="pos-cart-item">
        <div style="flex:1;min-width:0;padding-right:6px;">
            <p style="font-size:12.5px;font-weight:500;color:var(--color-ink);
                      line-height:1.35;word-break:break-word;">${escapeHtml(item.nama)}</p>
            <p style="font-size:11px;color:var(--color-ink-4);font-family:var(--font-mono);margin-top:1px;">
                ${fmt(item.harga)} × ${item.qty} = <strong style="color:var(--color-ink-2);">${fmt(item.harga * item.qty)}</strong>
            </p>
        </div>
        <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
            <button type="button" class="qty-btn qty-btn--minus"
                    aria-label="Kurangi ${escapeHtml(item.nama)}"
                    onclick="changeQty(${item.id},-1)">−</button>
            <span style="font-size:13px;font-weight:700;min-width:24px;text-align:center;
                         color:var(--color-ink);font-family:var(--font-mono);">${item.qty}</span>
            <button type="button" class="qty-btn"
                    aria-label="Tambah ${escapeHtml(item.nama)}"
                    onclick="changeQty(${item.id},1)">+</button>
        </div>
    </div>`).join('');

    updateTotal();
}

// ── Calculations ─────────────────────────────────────────────
function subtotal() {
    return Object.values(cart).reduce((s, i) => s + Math.round(i.harga) * i.qty, 0);
}
function diskonNominal() {
    const sub = subtotal();
    if (!sub) return 0;
    if (_diskonMode === 'persen') return Math.round(sub * Math.min(_diskonRaw, 100) / 100);
    return Math.min(_diskonRaw, sub);
}
function dpp() {
    return Math.max(0, subtotal() - diskonNominal());
}
function ppn() {
    return Math.round(dpp() * 0.11);
}
function total() {
    return Math.max(0, dpp() + ppn());
}

function updateTotal() {
    const sub = subtotal();
    const dis = diskonNominal();
    const ppnAmount = ppn();
    const tot = total();

    document.getElementById('display-subtotal').textContent = fmt(sub);
    document.getElementById('display-total').textContent    = fmt(tot);

    const potonganRow = document.getElementById('potongan-row');
    const diskonEl    = document.getElementById('display-diskon');
    if (dis > 0) {
        diskonEl.textContent       = '− ' + fmt(dis);
        potonganRow.style.display  = 'flex';
    } else {
        potonganRow.style.display  = 'none';
    }

    const ppnRow = document.getElementById('ppn-row');
    const ppnEl  = document.getElementById('display-ppn');
    if (ppnAmount > 0) {
        ppnEl.textContent      = '+ ' + fmt(ppnAmount);
        ppnRow.style.display   = 'flex';
    } else {
        ppnRow.style.display   = 'none';
    }

    buildQuickPay();
    updateKembalian();
}

function updateKembalian() {
    const tot  = total();
    const box  = document.getElementById('kembalian-box');
    const el   = document.getElementById('display-kembalian');
    const sisa = document.getElementById('sisa-label');

    if (!_uangTouched || _uangBayar === 0) {
        // User hasn't typed yet — hide kembalian, hide sisa
        box.style.display  = 'none';
        sisa.style.display = 'none';
        return;
    }

    const kem = _uangBayar - tot;

    if (kem >= 0) {
        // Enough — show green kembalian box
        el.textContent     = fmt(kem);
        el.style.color     = 'var(--color-success)';
        box.style.background    = 'var(--color-success-dim)';
        box.style.borderColor   = 'oklch(0.45 0.14 145 / 0.25)';
        box.style.display  = 'flex';
        sisa.style.display = 'none';
    } else {
        // Not enough — show red "kurang" hint, no kembalian box
        box.style.display  = 'none';
        sisa.textContent   = 'Kurang ' + fmt(Math.abs(kem));
        sisa.style.display = 'inline';
    }
}

// ── Quick-pay chips ──────────────────────────────────────────
// Only offer clean denominations: exact, then round up to nearest
// 5k/10k/20k/50k/100k — skip any that's identical to "Pas"
function setUangBayar(amount) {
    _uangTouched = true;
    _uangBayar   = Math.min(amount, MAX_BAYAR);
    const el = document.getElementById('uang-bayar-display');
    el.value = fmtNum(_uangBayar);
    updateKembalian();
}

function buildQuickPay() {
    const strip = document.getElementById('quick-pay-strip');
    const tot   = total();
    if (!tot) { strip.innerHTML = ''; return; }

    const tiers   = [5000, 10000, 20000, 50000, 100000, 500000, 1000000];
    const results = [tot]; // first is always "Pas" (exact)
    for (const d of tiers) {
        const rounded = Math.ceil(tot / d) * d;
        if (rounded !== tot) results.push(rounded);
        if (results.length >= 4) break;
    }

    strip.innerHTML = results.slice(0, 4).map((v, i) =>
        `<button type="button" onclick="setUangBayar(${v})"
            class="btn btn-ghost btn-sm pos-qp-btn"
            style="flex:1;justify-content:center;font-family:var(--font-mono);
                   font-size:11.5px;padding:6px 4px;${i === 0 ? 'font-weight:600;' : ''}">
            ${i === 0 ? 'Pas' : fmt(v)}
        </button>`
    ).join('');
}

// ── Clear cart ───────────────────────────────────────────────
function clearCart() {
    if (!Object.keys(cart).length) return;
    Swal.fire({
        title: 'Batalkan transaksi?',
        icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Ya, batal', cancelButtonText: 'Tidak',
        confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
    }).then(r => {
        if (!r.isConfirmed) return;
        const ids = Object.keys(cart).map(Number);
        ids.forEach(k => delete cart[k]);
        ids.forEach(k => syncCardState(k));
        resetPaymentFields();
        renderCart();
    });
}

function resetPaymentFields() {
    _diskonRaw    = 0;
    _uangBayar    = 0;
    _uangTouched  = false;
    _diskonMode   = 'nominal';
    _paymentMethod = 'cash';
    document.getElementById('diskon-display').value      = '';
    document.getElementById('uang-bayar-display').value  = '';
    document.getElementById('quick-pay-strip').innerHTML = '';
    document.getElementById('kembalian-box').style.display = 'none';
    document.getElementById('sisa-label').style.display    = 'none';
    document.getElementById('potongan-row').style.display  = 'none';
    document.getElementById('ppn-row').style.display       = 'none';
    setDiskonMode('nominal');
    setPaymentMethod('cash');
}

function resetAndReload() {
    const ids = Object.keys(cart).map(Number);
    ids.forEach(k => delete cart[k]);
    ids.forEach(k => syncCardState(k));
    resetPaymentFields();
    renderCart();
    window.location.reload();
}

// ── Proses transaksi ─────────────────────────────────────────
async function prosesTransaksi() {
    const items = Object.values(cart);
    if (!items.length) { toast('warning', 'Keranjang masih kosong'); return; }

    const t = total();

    // Cash-only validations
    if (_paymentMethod === 'cash') {
        if (!_uangTouched || _uangBayar === 0) {
            document.getElementById('uang-bayar-display').focus();
            toast('warning', 'Masukkan jumlah uang yang diterima');
            return;
        }
        if (_uangBayar < t) {
            toast('error', `Uang bayar kurang Rp\u00a0${(t - _uangBayar).toLocaleString('id-ID')}`);
            document.getElementById('uang-bayar-display').focus();
            return;
        }
    }

    const confirmHtml = _paymentMethod === 'cash'
        ? `<div style="font-size:13px;color:var(--color-ink-2);line-height:1.6;">
               Total &nbsp;<strong style="color:var(--color-ink);">${fmt(t)}</strong><br>
               Kembali <strong style="color:var(--color-success);">${fmt(_uangBayar - t)}</strong>
           </div>`
        : `<div style="font-size:13px;color:var(--color-ink-2);line-height:1.6;">
               Total &nbsp;<strong style="color:var(--color-ink);">${fmt(t)}</strong><br>
               Invoice Xendit akan dibuka di tab baru.
           </div>`;

    const conf = await Swal.fire({
        ...window.swalTheme,
        title: 'Proses transaksi?',
        html: confirmHtml,
        icon: 'question', showCancelButton: true,
        confirmButtonText: 'Proses!', cancelButtonText: 'Batal',
    });
    if (!conf.isConfirmed) return;

    const btn = document.getElementById('proses-transaksi');
    btn.disabled = true;
    Swal.fire({ ...window.swalTheme, title: 'Memproses…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    try {
        const payload = {
            items:          items.map(i => ({ id: i.id, qty: i.qty })),
            diskon:         diskonNominal(),
            payment_method: _paymentMethod,
        };
        if (_paymentMethod === 'cash') payload.uang_bayar = _uangBayar;

        const res = await fetch('{{ route('kasir.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(payload),
        });

        const data = res.headers.get('content-type')?.includes('application/json')
            ? await res.json()
            : { success: false, message: 'Respons server tidak dapat diproses. Muat ulang halaman.' };

        if (!data.success) {
            Swal.fire({ ...window.swalTheme, title: 'Gagal', text: data.message, icon: 'error' });
            return;
        }

        if (_paymentMethod === 'cash') {
            Swal.fire({
                ...window.swalTheme,
                title: 'Transaksi Berhasil',
                html: `<div style="font-size:13px;color:var(--color-ink-2);line-height:1.6;">
                    Invoice <span style="font-family:var(--font-mono);color:var(--color-ink);">${data.no_invoice}</span><br>
                    Kembalian <strong style="color:var(--color-success);">${fmt(data.kembalian)}</strong>
                </div>`,
                icon: 'success', showCancelButton: true,
                confirmButtonText: 'Cetak Struk', cancelButtonText: 'Selesai',
            }).then(r => {
                if (r.isConfirmed) window.open(`/kasir/struk/${data.transaksi}`, '_blank');
                resetAndReload();
            });
        } else {
            // Xendit: buka invoice URL, mulai polling
            window.open(data.invoice_url, '_blank');
            Swal.close();
            startPolling(data.transaksi, data.no_invoice);
        }

    } catch {
        Swal.fire({ ...window.swalTheme, title: 'Error', text: 'Koneksi gagal. Coba lagi.', icon: 'error' });
    } finally {
        btn.disabled = false;
    }
}

// ── Xendit polling ───────────────────────────────────────────
function startPolling(transaksiId, noInvoice) {
    stopPolling();

    Swal.fire({
        ...window.swalTheme,
        title: 'Menunggu Pembayaran',
        html: `<div style="font-size:13px;color:var(--color-ink-2);line-height:1.8;">
            Invoice <span style="font-family:var(--font-mono);color:var(--color-ink);">${noInvoice}</span><br>
            <span style="color:var(--color-ink-3);">Halaman pembayaran sudah dibuka di tab baru.</span><br><br>
            <span id="poll-status" style="font-size:12px;color:var(--color-ink-4);">Memeriksa status…</span>
        </div>`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Cek Sekarang',
        cancelButtonText: 'Batalkan Tunggu',
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: async () => {
            const result = await checkOnce(transaksiId);
            const status = result?.status;

            if (status === 'paid') return result; // lanjut ke .then

            // Belum dibayar — tampilkan pesan, cegah modal tutup
            const msg = status === 'expired'
                ? 'Invoice sudah kedaluwarsa.'
                : 'Pembayaran belum diterima. Silakan selesaikan pembayaran terlebih dahulu.';
            Swal.showValidationMessage(msg);
            return false;
        },
    }).then(r => {
        stopPolling();
        if (r.isDismissed) return;
        if (r.value?.status === 'paid') {
            onPaymentSuccess(r.value, noInvoice, transaksiId);
        }
    });

    // Auto-poll setiap 4 detik — auto-close kalau sudah paid
    _pollInterval = setInterval(async () => {
        const result = await checkOnce(transaksiId);

        if (result?.status === 'paid') {
            stopPolling();
            Swal.close();
            onPaymentSuccess(result, noInvoice, transaksiId);
            return;
        }
        if (result?.status === 'expired') {
            stopPolling();
            Swal.fire({ ...window.swalTheme, title: 'Invoice Kedaluwarsa', text: 'Invoice sudah expired. Buat transaksi baru.', icon: 'warning' });
            resetAndReload();
            return;
        }
        const el = document.getElementById('poll-status');
        if (el) el.textContent = 'Terakhir dicek: ' + new Date().toLocaleTimeString('id-ID');
    }, 4000);
}

function stopPolling() {
    if (_pollInterval) { clearInterval(_pollInterval); _pollInterval = null; }
}

async function checkOnce(transaksiId) {
    try {
        const res = await fetch(`/kasir/payment-status/${transaksiId}`, {
            headers: { 'Accept': 'application/json' }
        });
        return res.ok ? await res.json() : null;
    } catch { return null; }
}

function onPaymentSuccess(data, noInvoice, transaksiId) {
    Swal.fire({
        ...window.swalTheme,
        title: 'Pembayaran Diterima',
        html: `<div style="font-size:13px;color:var(--color-ink-2);line-height:1.6;">
            Invoice <span style="font-family:var(--font-mono);color:var(--color-ink);">${noInvoice}</span><br>
            <strong style="color:var(--color-success);">Pembayaran berhasil dikonfirmasi.</strong>
        </div>`,
        icon: 'success', showCancelButton: true,
        confirmButtonText: 'Cetak Struk', cancelButtonText: 'Selesai',
    }).then(r => {
        if (r.isConfirmed) window.open(`/kasir/struk/${transaksiId}`, '_blank');
        resetAndReload();
    });
}

// ── Search ───────────────────────────────────────────────────
document.getElementById('search-produk').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    let visible = 0;
    document.querySelectorAll('.pos-product-card').forEach(c => {
        const match = c.dataset.nama.toLowerCase().includes(q) || c.dataset.kode.toLowerCase().includes(q);
        c.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('produk-empty').style.display = visible ? 'none' : 'flex';
    document.getElementById('produk-grid').style.display  = visible ? '' : 'none';
});

// ── Keyboard shortcuts ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('search-produk').focus();
    updateTotal(); // initialise displays
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'F2') {
        e.preventDefault();
        const s = document.getElementById('search-produk');
        s.focus(); s.select();
    }
    if (e.key === 'F4') { e.preventDefault(); clearCart(); }
    if (e.key === 'F9') { e.preventDefault(); prosesTransaksi(); }
    if (e.key === 'Enter' && document.activeElement === document.getElementById('search-produk')) {
        e.preventDefault();
        const visible = Array.from(document.querySelectorAll('.pos-product-card'))
            .filter(c => c.style.display !== 'none' && !c.disabled);
        if (visible.length) {
            visible[0].click();
            const s = document.getElementById('search-produk');
            s.value = '';
            s.dispatchEvent(new Event('input'));
        }
    }
});
</script>
@endpush
