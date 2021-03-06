@extends('layouts.app')
@section('title', 'Results Tracking')
@section('content')
@section('css')
<link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}">
@append
@section('listpagejs') 
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script> 
<script>
		$(document).ready(function() {
			$('#listtable').DataTable();
		} );
	</script> 
@append
<div class="box box-info"> 
  
  <!-- /.box-header -->
  <div class="box-body table-responsive">
    <table id="listtable" class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>Result Id</th>
          <th>Facility</th>
          <th>Hub</th>
          <th>Delivered at</th>
          <th>Delivered by</th>
        </tr>
      </thead>
      <tbody>
      
      @foreach ($results as $result)
      <tr>
        <td>{{$result->locator_id}}</td>
        <td>{{$result->facility}}</td>
        <td>{{$result->hubname}}</td>
        <td>{{$result->delivered_at}}</td>
         <td>{{$result->delivered_by}}</td>
      </tr>
      @endforeach
        </tbody>
      
    </table>
  </div>
  <!-- /.box-body --> 
</div>
@endsection