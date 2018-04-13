<?php $__env->startSection('title', 'Samples'); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/jquery.dataTables.min.css')); ?>">
<?php $__env->appendSection(); ?>
<?php $__env->startSection('listpagejs'); ?> 
<script src="<?php echo e(asset('js/jquery.dataTables.min.js')); ?>"></script> 
<script>
	$(document).ready(function() {
		$('#listtable').DataTable();
		$('.filter-date').datepicker({
		   format: 'mm/dd/yyyy',
		   endDate: '+0d',
		   autoclose: true
		});
	} );
	
</script> 
<?php $__env->appendSection(); ?>
<style>
	#searchbutton{
		margin-top: -4px;
	}
	.input-field{
		width:100px;
	}
	.selectdropdown{
		width:200px;
	}
	.input-field, .selectdropdown {
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
        border-top-color: rgb(204, 204, 204);
        border-right-color: rgb(204, 204, 204);
        border-bottom-color: rgb(204, 204, 204);
        border-left-color: rgb(204, 204, 204);
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
    -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
    transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
}
</style>
<div class="box box-info">
<div class="well firstrow list">
  <div class="row">
    <?php echo e(Form::open(array('route' => 'dailyrouting.samplelist', 'class' => 'form-search pull-left', 'id' => 'samplelist'))); ?>

            <?php echo e(csrf_field()); ?>

   <?php echo e(Form::text('from', old('from'), ['class' => 'input-field filter-date', 'id' => 'from', 'placeholder' => 'From'])); ?>

   <?php echo e(Form::text('to', old('to'), ['class' => 'input-field filter-date', 'id' => 'to', 'placeholder' => 'To'])); ?>

<?php if (\Entrust::hasRole(['national_hub_coordinator','administrator'])) : ?> 
    <?php echo e(Form::select('hubid', $hubs, old('hubid'), ['class'=>'selectdropdown autosubmitsearchform'])); ?>

    <?php endif; // Entrust::hasRole ?>
   <?php echo e(Form::select('facilityid', $facilities, old('facilityid'), ['class'=>'selectdropdown autosubmitsearchform'])); ?> 
   <?php if (\Entrust::hasRole(['national_hub_coordinator','administrator'])) : ?> 
   <?php echo e(Form::select('districtid', $districts, old('districtid'), ['class'=>'selectdropdown autosubmitsearchform'])); ?> 
   <?php endif; // Entrust::hasRole ?>
    
   	<button type="submit" id="searchbutton" class="btn btn-primary">Filter <i class="glyphicon glyphicon-filter"></i></button>
    <?php echo e(Form::close()); ?>

   
  </div>
  
</div>
  <div class="row" > 
      <!-- Left col -->
      <section class="col-lg-9"> 
         <div id="samples-chart" >
    <?php //echo lava::render('LineChart', 'samples', 'samples-chart'); ?>
     <?php echo lava::render('ColumnChart', 'samples', 'samples-chart'); ?>
    </div>
      </section>
       <section class="col-lg-3"> 
       <h2 style="margin-bottom:5px; margin-left:5px;">Summary</h2>
         <div class="table-responsive">
         	<table class="table table-bordered">
            	
                 <tr>
                    <th>Sample Type</th>
                    <th>No. samples</th>
                    
                    </tr>
                	<?php $__currentLoopData = $samples_graph; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                    <td><?php echo e($line->sampletype); ?></td>
                    <td><?php echo e($line->samples); ?></td>
                    
                    </tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
            </table>
         </div>
      </section>
      
     </div>
  <!-- /.box-header -->
  <div class="box-body table-responsive">
    <table id="listtable" class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>Hub</th>
          <th>Facility</th>
          <th>VL</th>
          <th>HIV EID</th>
          <th>GeneXpert</th>
          <th>Sickle Cell (SCD)</th>
          <th>CD4/CD8</th>          
          <th>CBC/FBC</th>
          <th>LFTS</th>
          <th>RFTS</th>
          <th>Culture & sensitivity</th>
          <th>MTB Culture & DST</th>
        </tr>
      </thead>
      <tbody>
      
      <?php $__currentLoopData = $samples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sample): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
        <td><?php echo e($sample->hub); ?></td>
        <td><?php echo e($sample->facility); ?></td>
        <td><?php echo e($sample->VL); ?></td>
        <td><?php echo e($sample->HIVEID); ?></td>
        <td><?php echo e($sample->Genexpert); ?></td>
        <td><?php echo e($sample->SickleCell); ?></td>
        <td><?php echo e($sample->CD4CD8); ?></td>        
        <td><?php echo e($sample->CBCFBC); ?></td>
        <td><?php echo e($sample->LFTS); ?></td>
        <td><?php echo e($sample->RFTS); ?></td>
        <td><?php echo e($sample->Culturesensitivity); ?></td>
        <td><?php echo e($sample->MTBCultureDST); ?></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      
    </table>
  </div>
  <!-- /.box-body --> 
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>