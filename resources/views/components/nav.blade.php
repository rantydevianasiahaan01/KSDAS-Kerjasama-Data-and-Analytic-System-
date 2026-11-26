<nav class="sidebar-nav scroll-sidebar" style="height: calc(100vh - 76px);" data-simplebar>
    <ul id="sidebarnav">
        {{-- ===== Header: Menu Utama ===== --}}
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon"></i>
            <span class="hide-menu">Menu Utama</span>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link {{ request()->routeIs('app.beranda') ? 'active' : '' }}"
                href="{{ route('app.beranda') }}" aria-expanded="false">
                <span><i class="ti ti-home"></i></span>
                <span class="hide-menu">Beranda</span>
            </a>
        </li>

        {{-- ===== Header: Admin ===== --}}
        @if (\App\Helper\ToolsHelper::hasAppAccess($auth, ['Kerjasama.Admin']))
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon"></i>
                <span class="hide-menu">Admin</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('app.hak-akses') ? 'active' : '' }}"
                    href="{{ route('app.hak-akses') }}" aria-expanded="false">
                    <span><i class="ti ti-lock"></i></span>
                    <span class="hide-menu">Hak Akses</span>
                </a>
            </li>
        @endif
    </ul>
</nav>
