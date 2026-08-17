<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POS System') }} - @yield('title', 'Dashboard')</title>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#E31B23">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 selection:bg-indomie-red/20 selection:text-indomie-red">
    @php
        $user = auth()->user();
        $isSuperAdmin = $user->isSuperAdmin();
        $inTenantContext = $isSuperAdmin && session('current_tenant_id');
        $showPosMenus = ! $isSuperAdmin || $inTenantContext;
        $showTenantAdmin = $user->isAdmin() && (! $isSuperAdmin || $inTenantContext);

        $tenantId = session('current_tenant_id') ?? $user->tenant_id;
        $receiptSetting = $tenantId ? \App\Models\ReceiptSetting::where('tenant_id', $tenantId)->first() : null;
        $storeLogo = $receiptSetting?->store_logo ?? $receiptSetting?->logo ?? null;
        $storeDisplayName = $receiptSetting?->store_name ?? ($currentTenant->name ?? 'NusaPOS UMKM');

        $themeColor = $receiptSetting?->theme_color ?? 'red';
        $themeGradients = [
            'red'    => 'from-indomie-red to-red-700',
            'green'  => 'from-emerald-600 to-emerald-800',
            'blue'   => 'from-blue-600 to-blue-800',
            'indigo' => 'from-indigo-600 to-indigo-800',
            'amber'  => 'from-amber-500 to-amber-700',
            'slate'  => 'from-slate-800 to-slate-950',
        ];
        $themeGradient = $themeGradients[$themeColor] ?? $themeGradients['red'];
    @endphp

    <div x-data="{ sidebarOpen: false, desktopSidebarCollapsed: false }" class="min-h-screen flex overflow-x-hidden">

        {{-- Mobile sidebar overlay --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        {{-- Sidebar --}}
        <aside
            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                desktopSidebarCollapsed ? 'lg:-ml-72' : 'lg:ml-0'
            ]"
            class="fixed inset-y-0 left-0 z-30 w-72 bg-white/80 backdrop-blur-xl border-r border-slate-200/60 shadow-[4px_0_24px_rgba(0,0,0,0.02)] transform transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col min-h-0 h-screen lg:h-auto lg:min-h-screen"
        >
            {{-- Logo --}}
            <div class="flex-shrink-0 flex items-center justify-center h-20 bg-gradient-to-br {{ $themeGradient }} relative overflow-hidden px-4 text-center">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                <div class="flex items-center justify-center gap-2.5 relative z-10 drop-shadow-md truncate">
                    @if($storeLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($storeLogo))
                        <img src="{{ asset('storage/' . $storeLogo) }}" alt="Logo Toko" class="w-9 h-9 rounded-xl object-contain bg-white/90 p-1 shadow-md border border-white/40 flex-shrink-0">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white flex-shrink-0 border border-white/30 shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                    @endif
                    <span class="text-white text-base font-black tracking-wider truncate">
                        {{ strtoupper($storeDisplayName) }}
                    </span>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto min-h-0 px-4 py-6 space-y-8">
                {{-- === Superadmin: hanya pengelolaan multi-tenant / keuangan === --}}
                @if ($isSuperAdmin)
                    <div>
                        <p class="px-2 mb-3 text-[11px] font-bold text-indomie-red uppercase tracking-wider">
                            Panel superadmin
                        </p>
                        <div class="space-y-1">
                            <a href="{{ route('superadmin.tenants.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                               {{ request()->routeIs('superadmin.tenants.*') ? 'bg-indomie-red text-white shadow-md' : 'text-gray-600 hover:bg-indomie-yellow/20 hover:text-indomie-red' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span>Tenant</span>
                            </a>
                            <a href="{{ route('superadmin.financial.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                               {{ request()->routeIs('superadmin.financial.*') ? 'bg-indomie-red text-white shadow-md' : 'text-gray-600 hover:bg-indomie-yellow/20 hover:text-indomie-red' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Laporan keuangan</span>
                            </a>
                        </div>
                        @if (! $inTenantContext)
                            <p class="mt-4 mx-2 px-3 py-3 text-xs text-indomie-red leading-relaxed rounded-xl bg-indomie-yellow/10 border border-indomie-yellow/30 font-medium">
                                Untuk membuka kasir &amp; menu toko, pilih <strong class="font-bold">Tenant</strong> lalu klik <strong class="font-bold">Masuk</strong>.
                            </p>
                        @endif
                    </div>
                @endif

                {{-- === Aplikasi POS (admin, kasir, atau superadmin yang sudah masuk tenant) === --}}
                @if ($showPosMenus)
                    <div>
                        <p class="px-2 mb-3 text-[11px] font-bold text-indomie-green uppercase tracking-wider">
                            @if ($isSuperAdmin && $inTenantContext)
                                Di tenant ini
                            @else
                                Operasional
                            @endif
                        </p>
                        <div class="space-y-1">
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indomie-green text-white shadow-md' : 'text-gray-600 hover:bg-indomie-green/10 hover:text-indomie-green' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('pos.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('pos.*') ? 'bg-indomie-green text-white shadow-md' : 'text-gray-600 hover:bg-indomie-green/10 hover:text-indomie-green' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>POS / Kasir</span>
                            </a>
                            <a href="{{ route('transactions.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('transactions.*') ? 'bg-indomie-green text-white shadow-md' : 'text-gray-600 hover:bg-indomie-green/10 hover:text-indomie-green' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                <span>Transaksi</span>
                            </a>
                        </div>
                    </div>

                    {{-- === Modul Akuntansi & Keuangan SAK Indonesia (Hanya Admin / Superadmin) === --}}
                    @if ($showTenantAdmin)
                    <div>
                        <p class="px-2 mb-3 text-[11px] font-bold text-blue-600 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Akuntansi SAK</span>
                        </p>
                        <div class="space-y-1">
                            <a href="{{ route('akuntansi.pengeluaran.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('akuntansi.pengeluaran.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Pengeluaran Kas</span>
                            </a>
                            <a href="{{ route('akuntansi.jurnal.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('akuntansi.jurnal.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <span>Jurnal Umum</span>
                            </a>
                            <a href="{{ route('akuntansi.laporan.laba-rugi') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('akuntansi.laporan.laba-rugi') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                <span>Laporan Laba Rugi</span>
                            </a>
                            <a href="{{ route('akuntansi.laporan.neraca') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('akuntansi.laporan.neraca') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l9-4 9 4v12l-9 4-9-4V6zm9-1v16"/></svg>
                                <span>Laporan Neraca</span>
                            </a>
                            <a href="{{ route('akuntansi.laporan.buku-besar') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('akuntansi.laporan.buku-besar') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                                <span>Buku Besar</span>
                            </a>
                            <a href="{{ route('akuntansi.akun.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('akuntansi.akun.*') ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span>Bagan Akun (COA)</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    @if ($showTenantAdmin)
                        <div>
                            <p class="px-2 mb-3 text-[11px] font-bold text-indomie-red uppercase tracking-wider">
                                Pengaturan toko
                            </p>
                            <div class="space-y-1">
                                <a href="{{ route('admin.categories.index') }}"
                                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-indomie-red text-white shadow-md' : 'text-gray-600 hover:bg-indomie-yellow/20 hover:text-indomie-red' }}">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span>Kategori</span>
                                </a>
                                <a href="{{ route('admin.products.index') }}"
                                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'bg-indomie-red text-white shadow-md' : 'text-gray-600 hover:bg-indomie-yellow/20 hover:text-indomie-red' }}">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <span>Produk</span>
                                </a>
                                <a href="{{ route('admin.stock.index') }}"
                                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.stock.*') ? 'bg-indomie-red text-white shadow-md' : 'text-gray-600 hover:bg-indomie-yellow/20 hover:text-indomie-red' }}">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                    <span>Manajemen Stok</span>
                                </a>
                                <a href="{{ route('admin.reports.index') }}"
                                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-indomie-red text-white shadow-md' : 'text-gray-600 hover:bg-indomie-yellow/20 hover:text-indomie-red' }}">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <span>Laporan Penjualan</span>
                                </a>
                                <a href="{{ route('admin.shifts.index') }}"
                                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.shifts.*') ? 'bg-indomie-red text-white shadow-md' : 'text-gray-600 hover:bg-indomie-yellow/20 hover:text-indomie-red' }}">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Audit Shift Kasir</span>
                                </a>
                                <a href="{{ route('admin.users.index') }}"
                                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-indomie-red text-white shadow-md' : 'text-gray-600 hover:bg-indomie-yellow/20 hover:text-indomie-red' }}">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span>Pengguna</span>
                                </a>
                                <a href="{{ route('admin.receipt-settings.edit') }}"
                                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.receipt-settings.*') ? 'bg-indomie-red text-white shadow-md' : 'text-gray-600 hover:bg-indomie-yellow/20 hover:text-indomie-red' }}">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Struk / receipt</span>
                                </a>
                            </div>
                        </div>
                    @endif
                @endif
            </nav>

            {{-- User info at bottom --}}
            <div class="flex-shrink-0 p-4 border-t border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indomie-yellow flex items-center justify-center flex-shrink-0 shadow-sm border border-indomie-yellow/50">
                        <span class="text-indomie-red text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ $user->name }}</p>
                        <p class="text-xs font-medium text-gray-500 truncate">
                            @if ($isSuperAdmin)
                                Superadmin
                            @elseif ($user->role === 'admin')
                                Admin toko
                            @else
                                Kasir
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </aside>


        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top Navbar --}}
            <header class="bg-white/70 backdrop-blur-lg shadow-sm border-b border-slate-200/60 z-20 sticky top-0 transition-all duration-300">
                <div class="flex items-center justify-between h-20 px-4 sm:px-6 lg:px-8">
                    {{-- Sidebar toggle button --}}
                    <button
                        @click="sidebarOpen = !sidebarOpen; desktopSidebarCollapsed = !desktopSidebarCollapsed"
                        class="text-gray-500 hover:text-indomie-red focus:outline-none transition-colors p-1 -ml-1 rounded-lg"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Page title --}}
                    <h1 class="text-xl font-bold text-gray-800 tracking-tight">
                        @yield('title', 'Dashboard')
                    </h1>

                    {{-- Right side --}}
                    <div class="flex items-center gap-5">
                        {{-- Current time --}}
                        <span class="hidden sm:flex items-center gap-2 text-sm font-medium text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full" x-data x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></span>

                        {{-- User dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 text-sm font-bold text-gray-700 hover:text-indomie-red transition-colors focus:outline-none p-1 rounded-lg">
                                <span class="hidden sm:block">{{ $user->name }}</span>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50 origin-top-right"
                            >
                                <div class="px-4 py-3 border-b border-gray-100 mb-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-indomie-yellow/10 hover:text-indomie-red transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm font-medium text-gray-700 hover:bg-indomie-red hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash Messages (Handled by SweetAlert2 at the bottom) --}}

            @if($isSuperAdmin && session('current_tenant_id') && !empty($currentTenant))
                <div class="mx-4 sm:mx-6 lg:mx-8 mt-6">
                    <div class="bg-blue-50 border border-blue-200 text-blue-900 px-5 py-4 rounded-xl flex flex-wrap items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-sm font-medium">Konteks tenant aktif: <strong class="text-blue-700 font-bold ml-1">{{ $currentTenant->name }}</strong></span>
                        </div>
                        <form action="{{ route('superadmin.leave-tenant') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition-colors flex items-center gap-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Keluar dari tenant
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Main Content Area --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Mobile Bottom Navigation Bar --}}
    @if ($showPosMenus)
    <div class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur border-t border-slate-200 lg:hidden flex justify-around items-center h-16 px-2 shadow-lg">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 text-[11px] font-semibold {{ request()->routeIs('dashboard') ? 'text-indomie-green font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Beranda</span>
        </a>
        <a href="{{ route('pos.index') }}" class="flex flex-col items-center gap-1 text-[11px] font-semibold {{ request()->routeIs('pos.*') ? 'text-indomie-red font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <span>Kasir</span>
        </a>
        <a href="{{ route('transactions.index') }}" class="flex flex-col items-center gap-1 text-[11px] font-semibold {{ request()->routeIs('transactions.*') ? 'text-indomie-green font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <span>Transaksi</span>
        </a>
        @if ($showTenantAdmin)
        <a href="{{ route('admin.products.index') }}" class="flex flex-col items-center gap-1 text-[11px] font-semibold {{ request()->routeIs('admin.products.*') ? 'text-indomie-red font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span>Produk</span>
        </a>
        @endif
        <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-1 text-[11px] font-semibold {{ request()->routeIs('profile.*') ? 'text-indomie-red font-bold' : 'text-slate-500 hover:text-slate-800' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Profil</span>
        </a>
    </div>
    @endif

    @stack('scripts')

    <!-- PWA Service Worker & Network Monitor -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('PWA ServiceWorker ready: ', reg.scope))
                    .catch(err => console.warn('PWA ServiceWorker error: ', err));
            });
        }

        window.addEventListener('offline', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'warning',
                    title: 'Koneksi terputus. Mode offline aktif.',
                    showConfirmButton: false,
                    timer: 4000
                });
            }
        });

        window.addEventListener('online', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top',
                    icon: 'success',
                    title: 'Terhubung kembali ke internet!',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#00A651',
                    customClass: { popup: 'rounded-2xl' }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#E11B22',
                    customClass: { popup: 'rounded-2xl' }
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: "{{ session('warning') }}",
                    confirmButtonColor: '#FFD100',
                    customClass: { popup: 'rounded-2xl' }
                });
            @endif
        });
    </script>
</body>
</html>
