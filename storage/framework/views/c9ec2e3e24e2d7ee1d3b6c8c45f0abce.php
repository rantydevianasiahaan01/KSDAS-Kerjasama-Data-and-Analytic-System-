<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb bg-info-subtle px-3 py-2 rounded">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $breadcrumb; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($value): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo e($value); ?>" class="text-info d-flex align-items-center">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($key != 'Home'): ?>
                            <?php echo e($key); ?>

                        <?php else: ?>
                            <i class="ti ti-home fs-4 mt-1"></i>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </a>
                </li>
            <?php else: ?>
                <li class="breadcrumb-item text-info active" aria-current="page">
                    <?php echo e($key); ?>

                </li>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ol>
</nav>
<?php /**PATH C:\Users\abdul\Repos\itdel\itdel-app-starter\resources\views/components/breadcrumb.blade.php ENDPATH**/ ?>