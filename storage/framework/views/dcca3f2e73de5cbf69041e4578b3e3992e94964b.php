<?php if($staff->type == 1): ?>
	<?php $__env->startSection('title', 'View Sample Transporter'); ?>
<?php else: ?>
	<?php $__env->startSection('title', 'View Staff Member'); ?>
<?php endif; ?>

<?php $__env->startSection('content'); ?>
<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title"></h3>
  </div>
  <!-- /.box-header -->
  <div class="box-body no-padding">
  <div class="col-xs-12 table-responsive">
    <table class="table">
      <tbody>
      <tr>
          <td>Facility</td>
          <td><?php if($staff->hubid): ?><?php echo e($staff->facility->name); ?><?php endif; ?></td>
        </tr>
        <tr>
          <td>First Name</td>
          <td><?php echo e($staff->firstname); ?></td>
        </tr>
        <tr>
          <td>Last Name</td>
          <td><?php echo e($staff->lastname); ?></td>
        </tr>
         <tr>
          <td>Other Names</td>
          <td><?php echo e($staff->othernames); ?></td>
        </tr>
        <tr>
          <td>Email Address</td>
          <td><?php echo e($staff->emailaddress); ?></td>
        </tr>
        <tr>
          <td>Telephone Number</td>
          <td><?php echo e($staff->telephonenumber); ?></td>
        </tr>
        <?php if($staff->type == 2): ?>
        <tr>
          <td>Designation</td>
          <td><?php echo e($staff->designation); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($staff->type == 1): ?>
        <tr>
          <td>Has Driving Permit</td>
          <td><?php echo e(getLookupValueDescription('YES_NO', $staff->hasdrivingpermit)); ?>

          <?php if($staff->hasdrivingpermit): ?>
            , Expires On <?php echo e(changeMySQLDateToPageFormat($staff->permitexpirydate)); ?>

          <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td>Has Defensive Driving</td>
          <td><?php echo e(getLookupValueDescription('YES_NO', $staff->hasdefensiveriding)); ?>

          </td>
        </tr>
        <tr>
          <td>Has BB Training</td>
          <td><?php echo e(getLookupValueDescription('YES_NO', $staff->hasbbtraining)); ?>

          </td>
        </tr>
        <tr>
          <td>Is Immunized for Hepatitis B</td>
          <td><?php echo e(getLookupValueDescription('YES_NO', $staff->isimmunizedforhb)); ?>

          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
    </div>
    <div class="box-footer clearfix">  
                <a href="<?php echo e(URL::previous()); ?>" class="btn btn-sm btn-default pull-left">Back</a>
                <a href="<?php echo e(route('staff.edit', $staff->id)); ?>" class="btn btn-sm btn-warning pull-right">Update Sample Transporter</a> </div>
  </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>