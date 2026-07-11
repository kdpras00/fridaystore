<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — POS Friday Store</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- Mobile overlay --}}
<div id="sidebar-overlay" class="sidebar-overlay hidden no-print" onclick="closeSidebar()"></div>

<div class="layout">
    {{-- SIDEBAR --}}
    <aside id="sidebar" class="sidebar no-print">

        {{-- Logo --}}
        <div class="sidebar-logo">
            <a href="{{ route('dashboard') }}" style="display: flex; justify-content: center; align-items: center; width: 100%;">
                <img src="{{ asset('images/fridaylogo.png') }}" alt="Friday Logo" style="height: 36px; max-width: 100%; object-fit: contain;">
            </a>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav">

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            @if(auth()->user()->hasRole('admin'))
            <p class="sidebar-section-label">Master Data</p>
            
            <a href="{{ route('kategori.index') }}" class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Kategori Produk
            </a>

            <a href="{{ route('produk.index') }}" class="nav-item {{ request()->routeIs('produk.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Kelola Produk
            </a>

            <p class="sidebar-section-label">Inventaris</p>

            <a href="{{ route('stok.index') }}" class="nav-item {{ request()->routeIs('stok.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Stok Barang
            </a>

            <p class="sidebar-section-label">Laporan & Pengguna</p>
            
            <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan
            </a>

            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Kelola User
            </a>
            @endif

            @if(auth()->user()->hasRole('kasir'))
            <p class="sidebar-section-label">Penjualan</p>
            <a href="{{ route('kasir.index') }}" class="nav-item {{ request()->routeIs('kasir.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Transaksi POS
            </a>
            @endif

            @if(auth()->user()->hasRole('owner'))
            <p class="sidebar-section-label">Pantauan Owner</p>
            <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Laporan Penjualan
            </a>
            <a href="{{ route('owner.stok') }}" class="nav-item {{ request()->routeIs('owner.*') ? 'active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Stok Barang
            </a>
            @endif

        </nav>


    </aside>

    {{-- MAIN --}}
    <div class="main">
        {{-- Topbar --}}
        <header class="topbar no-print">
            <button id="sidebar-toggle" type="button" onclick="toggleSidebar()" class="btn btn-ghost btn-icon" style="display:none;" aria-label="Buka navigasi" aria-controls="sidebar" aria-expanded="false">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div style="flex:1;"></div>
            
            {{-- Date & Profile Section --}}
            <div style="display: flex; align-items: center; gap: 16px;">
                {{-- Date --}}
                <div class="topbar-date" style="display: flex; align-items: center; gap: 6px; color: rgba(255, 255, 255, 0.65); font-size: 12.5px; font-weight: 500;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    <span>{{ now()->translatedFormat('d M Y') }}</span>
                </div>
                
                {{-- Divider --}}
                <div class="topbar-divider" style="width: 1px; height: 16px; background: rgba(255, 255, 255, 0.15);"></div>
                
                {{-- Profile Dropdown --}}
                <div style="position: relative; display: inline-block;" id="profile-dropdown-container">
                    <button id="profile-dropdown-toggle" type="button" onclick="toggleProfileDropdown()" aria-label="Menu akun" aria-haspopup="true" aria-expanded="false" style="display:flex; align-items:center; gap:10px; background:none; border:none; color:#ffffff; cursor:pointer; padding:4px 8px; transition: opacity 150ms; font-family:inherit;">
                        <span class="topbar-user-name" style="font-size:14px; font-weight:500; letter-spacing: -0.01em;">{{ auth()->user()->name }}</span>
                        <div style="width:28px; height:28px; border-radius:50%; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); display:flex; align-items:center; justify-content:center; font-size:11.5px; font-weight:700; color:#ffffff;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <svg id="profile-chevron" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="opacity:0.85; transition: transform 200ms ease;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    
                    {{-- Dropdown Menu --}}
                    <div id="profile-dropdown-menu" role="menu" aria-labelledby="profile-dropdown-toggle" style="display:none; position:absolute; right:0; top:100%; margin-top:8px; width:160px; background:var(--color-surface); border:1px solid var(--color-border); border-radius:10px; box-shadow: var(--shadow-md); z-index:100; padding:6px;">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">@csrf</form>
                        <button type="button" onclick="confirmLogout()" onmouseover="this.style.background='oklch(0.97 0.02 18)'" onmouseout="this.style.background='transparent'" style="width:100%; text-align:left; background:transparent; border:none; padding:8px 12px; font-size:13.5px; color:var(--color-danger); font-weight:500; cursor:pointer; display:flex; align-items:center; gap:10px; transition: background 150ms ease; font-family:var(--font-sans); border-radius:6px;">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="content @yield('content-class')">
            @if(!View::hasSection('hide-header'))
            <div class="page-header">
                <div>
                    <div style="display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap;">
                        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    @hasSection('page-sub')
                        <p class="page-sub">@yield('page-sub')</p>
                    @endif
                </div>
                @hasSection('page-actions')
                    <div style="display: flex; gap: 8px; align-items: center;">
                        @yield('page-actions')
                    </div>
                @endif
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- SweetAlert2 flashes --}}
@if(session('swal_success'))
<script>document.addEventListener('DOMContentLoaded',()=>toast('success',@json(session('swal_success'))));</script>
@endif
@if(session('swal_error'))
<script>document.addEventListener('DOMContentLoaded',()=>toast('error',@json(session('swal_error'))));</script>
@endif

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    sidebar.classList.toggle('open');
    document.getElementById('sidebar-overlay').classList.toggle('hidden');
    toggle.setAttribute('aria-expanded', sidebar.classList.contains('open'));
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').classList.add('hidden');
    document.getElementById('sidebar-toggle').setAttribute('aria-expanded', 'false');
}
function confirmLogout() {
    Swal.fire({
        title: 'Keluar dari sistem?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, logout',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
    }).then(r => r.isConfirmed && document.getElementById('logout-form').submit());
}

function toggleProfileDropdown() {
    const menu = document.getElementById('profile-dropdown-menu');
    const chevron = document.getElementById('profile-chevron');
    const toggle = document.getElementById('profile-dropdown-toggle');
    if (menu.style.display === 'none') {
        menu.style.display = 'block';
        if(chevron) chevron.style.transform = 'rotate(180deg)';
        toggle.setAttribute('aria-expanded', 'true');
    } else {
        menu.style.display = 'none';
        if(chevron) chevron.style.transform = 'rotate(0deg)';
        toggle.setAttribute('aria-expanded', 'false');
    }
}

// Close dropdown if clicked outside
document.addEventListener('click', function(event) {
    const container = document.getElementById('profile-dropdown-container');
    const menu = document.getElementById('profile-dropdown-menu');
    const chevron = document.getElementById('profile-chevron');
    if (container && !container.contains(event.target)) {
        if (menu && menu.style.display === 'block') {
            menu.style.display = 'none';
            if(chevron) chevron.style.transform = 'rotate(0deg)';
            document.getElementById('profile-dropdown-toggle').setAttribute('aria-expanded', 'false');
        }
    }
});

// Show hamburger on mobile
function syncSidebarToggle() {
    const toggle = document.getElementById('sidebar-toggle');
    if (window.innerWidth < 1024) {
        toggle.style.display = '';
    } else {
        toggle.style.display = 'none';
        closeSidebar();
    }
}
window.addEventListener('resize', syncSidebarToggle);
syncSidebarToggle();
</script>

@stack('scripts')
</body>
</html>
