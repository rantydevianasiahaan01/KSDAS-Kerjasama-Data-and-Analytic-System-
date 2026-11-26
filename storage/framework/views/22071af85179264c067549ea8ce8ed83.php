<nav class="sidebar-nav scroll-sidebar" style="height: calc(100vh - 76px);" data-simplebar>
    <ul id="sidebarnav">
        
        <li class="nav-small-cap">
            <i class="ti ti-dots nav-small-cap-icon"></i>
            <span class="hide-menu">Menu Utama</span>
        </li>

        <li class="sidebar-item">
            <a class="sidebar-link <?php echo e(request()->routeIs('app.beranda') ? 'active' : ''); ?>"
                href="<?php echo e(route('app.beranda')); ?>" aria-expanded="false">
                <span><i class="ti ti-home"></i></span>
                <span class="hide-menu">Beranda</span>
            </a>
        </li>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Helper\ToolsHelper::hasAppAccess($auth, ['Kerjasama.Admin'])): ?>
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon"></i>
                <span class="hide-menu">Admin</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('app.hak-akses') ? 'active' : ''); ?>"
                    href="<?php echo e(route('app.hak-akses')); ?>" aria-expanded="false">
                    <span><i class="ti ti-lock"></i></span>
                    <span class="hide-menu">Hak Akses</span>
                </a>
            </li>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>
</nav>
<?php /**PATH C:\Users\abdul\Repos\itdel\itdel-app-starter\resources\views/components/nav.blade.php ENDPATH**/ ?>