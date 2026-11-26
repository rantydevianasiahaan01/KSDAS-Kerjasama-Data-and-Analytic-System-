

<?php $__env->startSection('title', 'Logout'); ?>

<?php $__env->startSection('content'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Remove auth token from local storage
            localStorage.removeItem('authToken');
            window.location.href = '/auth/login';
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdul\Repos\itdel\itdel-app-starter\resources\views/features/auth/logout-page.blade.php ENDPATH**/ ?>