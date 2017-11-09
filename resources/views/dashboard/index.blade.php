@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
@section('js') 
<script src="{{ asset('js/fastclick.js') }}"></script> 
<script src="{{ asset('js/jquery.sparkline.min.js') }}"></script> 
<script src="{{ asset('js/jquery-jvectormap-1.2.2.min.js') }}"></script> 
<script src="{{ asset('js/jquery-jvectormap-world-mill-en.js') }}"></script> 
<script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script> 
<script src="{{ asset('js/Chart.js') }}"></script> 
<script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script> 
@append 
<!-- Info boxes -->
 @role('Admin','Regional_hub_coordinator','Program_officer')
<div class="row">
  
  <!-- /.col -->
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box"> <span class="info-box-icon bg-red"><i class="fa fa-motorcycle"></i></span>
      <div class="info-box-content"> <span class="info-box-text"><a class="link-tip" href="#" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Bikes Broken down">Bikes Broken down</a></span> <span class="info-box-number">16</span> </div>
      <!-- /.info-box-content --> 
    </div>
    <!-- /.info-box --> 
  </div>
  <!-- /.col --> 
   <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box"> <span class="info-box-icon bg-yellow"><i class="fa fa-motorcycle"></i></span>
      <div class="info-box-content"> <span class="info-box-text"><a class="link-tip" href="#" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Bikes without Service Contract">Bikes without Service Contract</a></span> <span class="info-box-number">17</span> </div>
      <!-- /.info-box-content --> 
    </div>
    <!-- /.info-box --> 
  </div>
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box"> <span class="info-box-icon bg-aqua"><i class="fa fa-motorcycle"></i></span>
      <div class="info-box-content"> <span class="info-box-text"><a class="link-tip" href="#" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Bikes Due for Service">Bikes Due for Service</a></span> <span class="info-box-number">21</span> </div>
      <!-- /.info-box-content --> 
    </div>
    <!-- /.info-box --> 
  </div>
  <!-- fix for small devices only -->
  <div class="clearfix visible-sm-block"></div>
  <div class="col-md-3 col-sm-6 col-xs-12">
    <div class="info-box"> <span class="info-box-icon bg-green"><i class="fa fa-motorcycle"></i></span>
      <div class="info-box-content"> <span class="info-box-text"><a class="link-tip" href="#" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Bikes not assigned">Bikes not assigned</a></span> <span class="info-box-number">7</span> </div>
      <!-- /.info-box-content --> 
    </div>
    <!-- /.info-box --> 
  </div>
  <!-- /.col -->
 
  <!-- /.col --> 
</div>
@endrole
<!-- /.row -->
 @role('Admin','Regional_hub_coordinator','Program_officer') 
<div class="row panel-body">
  <div class="btn-group container col-md-12">
    <div class="row mid-dashboard">
      
      <div class="btn-group container col-md-4"> <a class="link-tip" href="{{ route('organization.index') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Click here to create new, update or view a list of IPs">
        <div class="panel panel-default"> <span class="fa fa-institution icon"></span> <br>
          <span class="nav_title">Manage IPs</span> </div>
        </a> </div>
      <div class="btn-group container col-md-4"> <a class="link-tip" href="{{ route('hub.index') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Click here to create new, update or view a list of hubs">
        <div class="panel panel-default"> <span class="fa fa-hospital-o icon"></span> <br>
          <span class="nav_title">Manage Hubs</span> </div>
        </a> </div>
        
        <div class="btn-group container col-md-4"> <a class="link-tip" href="{{ route('facility.index') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Click here to create new, update or view a list of facilities">
        <div class="panel panel-default"> <span class="fa fa-plus icon"></span> <br>
          <span class="nav_title">Manage Facilities</span> </div>
        </a> </div>
        
    </div>
    <div class="row">
      <div class="btn-group container col-md-4"> <a class="link-tip" href="{{ route('equipment.index') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Click here create new, update or view a list of hubs">
        <div class="panel panel-default"> <span class="fa fa-motorcycle icon"></span> <br>
          <span class="nav_title">Manage Bikes</span> </div>
        </a> </div>
      <div class="btn-group container col-md-4"> <a class="link-tip" href="{{ url('staff/list/2') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Click here to create new, update or view a list of Staff members">
        <div class="panel panel-default"> <span class="fa fa-users icon"></span> <br>
          <span class="nav_title">Manage Staff Members</span> </div>
        </a> </div>
      <div class="btn-group container col-md-4"> <a class="link-tip" href="{{ url('staff/list/1') }}" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Click here to create new, update or view a list of hubs">
        <div class="panel panel-default"> <span class="fa fa-cogs icon"></span> <br>
          <span class="nav_title">Manage Sample Transporters</span> </div>
        </a> </div>
    </div>
  </div>
</div>
@endrole
@endsection