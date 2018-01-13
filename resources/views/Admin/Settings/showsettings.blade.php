@extends('admin-master')
@section('title','System Settings')
@section('content')

<div class="container">
	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-spanner"></i> Settings</h3></div>
	</div>

	@include('Templates.messages')
	
	<div class="row">
		<div class='pull-right'>{!! $store_select_list !!}</div>
	</div>

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>ID</th>
				<th>Parameter</th>
				<th>Value</th>
				<th>Store ID</th>
				</tr>
			</thead>
			<tbody>
				@foreach($global_settings as $s)
				<tr onclick='select( {{ $s->id }} )' style="cursor:pointer;">
					<td>{{ $s->id }} <i style="color:green;">(Global)</i></td>
					<td>{{ $s->setting_name }}</td>
					<td>{{ $s->setting_value }}</td>
					<td>{!! $stores[$s->setting_store_id] !!}</td>
				</tr>
				@endforeach
				@foreach($settings as $s)
				<tr onclick='select( {{ $s->id }} )' style="cursor:pointer;">
					<td>{{ $s->id }}</td>
					<td>{{ $s->setting_name }}</td>
					<td>{{ $s->setting_value }}</td>
					<td>{!! $stores[$s->setting_store_id] !!}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	

	<div class="row">
		<a href='/admin/setting/addnew'><button class="btn btn-success"><i class="fa fa-user-plus"></i> Add Setting </button></a>
	</div>
</div>
<script>
$('#store_id').change(function(){
var sid = $('#store_id').val();
var url = '/admin/settings?s='+sid;
window.location.href = url;
});


function select(id)
{
var url = '/admin/setting/edit/'+id;
window.location.href = url;
}
</script>
@stop
