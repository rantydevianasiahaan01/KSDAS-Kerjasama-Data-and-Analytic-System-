<div>
    <?php echo $__env->make('components.breadcrumb', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="card">
        <div class="card-header">
            <div class="d-flex">
                <div class="flex-fill">
                    <h3><i class="ti ti-lock"></i> Hak Akses</h3>
                </div>
                <div>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari..."
                            wire:model.live.debounce.500ms="search">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->isEditor): ?>
                            <button class="btn btn-primary" type="button" wire:click="prepareAdd"
                                wire:loading.attr="disabled" wire:target="prepareAdd">
                                <span wire:loading wire:target="prepareAdd"
                                    class="spinner-border spinner-border-sm me-1"></span>
                                <i wire:loading.remove wire:target="prepareAdd" class="ti ti-plus"></i>
                                Tambah
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-borderless mb-0">
                <tr class="table-info">
                    <th>Identitas</th>
                    <th>Akses</th>
                    <th>Tindakan</th>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $aksesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $akses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="border-bottom">
                        <td>
                            <small class="text-muted"><?php echo e('@' . ($akses->user->username ?? '-')); ?></small><br>
                            <span><?php echo e($akses->user->name ?? '-'); ?></span>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = explode(',', (string) $akses->akses); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><i class="ti ti-minus"></i> <?php echo e($hak); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->isEditor): ?>
                                <?php ($rowTarget = "prepareEdit('{$akses->id}'), prepareDelete('{$akses->id}')"); ?>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-info" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false" aria-label="Tindakan"
                                        wire:loading.attr="disabled"
                                        wire:target="<?php echo e($rowTarget); ?>">
                                        <span wire:loading.remove wire:target="<?php echo e($rowTarget); ?>">
                                            <i class="ti ti-dots-vertical"></i>
                                        </span>
                                        <span wire:loading wire:target="<?php echo e($rowTarget); ?>"
                                            class="spinner-border spinner-border-sm" role="status"
                                            aria-label="Memuat"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="#" class="dropdown-item text-warning"
                                                wire:click.prevent="prepareEdit('<?php echo e($akses->id); ?>')"
                                                wire:loading.class="disabled"
                                                wire:target="<?php echo e($rowTarget); ?>">
                                                <i class="ti ti-edit me-2"></i> Ubah
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-item text-danger"
                                                wire:click.prevent="prepareDelete('<?php echo e($akses->id); ?>')"
                                                wire:loading.class="disabled"
                                                wire:target="<?php echo e($rowTarget); ?>">
                                                <i class="ti ti-trash me-2"></i> Hapus
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="3" class="text-muted p-3">Belum ada data hak akses.</td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </table>
        </div>
    </div>

    <?php echo $__env->make('features.hak-akses.modals.add', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('features.hak-akses.modals.edit', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('features.hak-akses.modals.delete', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH C:\Users\abdul\Repos\itdel\itdel-app-starter\resources\views/features/hak-akses/livewire.blade.php ENDPATH**/ ?>