<?php $__env->startSection('content'); ?>
Hello world!!! My name is <?php echo e($name); ?>!
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>