@extends('admin-master')
@section('title','System Settings')
@section('content')

<div class="container">
	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-spanner"></i>Global & Store Settings</h3></div>
	</div>

	@include('Templates.messages')
	
	<div class="row">
		<div class='pull-right'>
			<select class="form-control" id="store_id" name="store_id">
				<option value="0">Global - All Stores</option>
				@foreach($stores as $s)
					@if($s->id == $store_id)
					<option value="{{$s->id}}" selected>{{$s->store_name}}</option>
					@else
					<option value="{{$s->id}}">{{$s->store_name}}</option>
					@endif
				@endforeach
			</select>
		</div>
	</div>

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>ID</th>
				<th>Parameter</th>
				<th>Value</th>
				<th>Store ID</th>
				<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				@foreach($global_settings as $s)
				<tr onclick='select( {{ $s->id }} )' style="cursor:pointer;">
					<td>{{ $s->id }} <i style="color:green;">(Global)</i></td>
					<td>{{ $s->setting_name }}</td>
					<td>{{ $s->setting_value }}</td>
					<td>
						@foreach($stores as $st)
							@if($st->id == $s->setting_store_id)
								{{ $st->store_name }}
							@endif
						@endforeach
					</td>
					<td><a href="/admin/setting/delete/{{ $s->id }}"><i class="fa fa-trash"></i> Delete</a></td>
				</tr>
				@endforeach
				@foreach($settings as $s)
				<tr onclick='select( {{ $s->id }} )' style="cursor:pointer;">
					<td>{{ $s->id }}</td>
					<td>{{ $s->setting_name }}</td>
					<td>{{ $s->setting_value }}</td>
					<td>
						@foreach($stores as $st)
							@if($st->id == $s->setting_store_id)
								{{ $st->store_name }}
							@endif
						@endforeach
					</td>
					<td><a href="/admin/setting/delete/{{ $s->id }}"><i class="fa fa-trash"></i> Delete</a></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	

	<div class="row">
		<a href='/admin/setting/add'><button class="btn btn-success"><i class="fa fa-user-plus"></i> Add Setting </button></a>
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
<!-- Admin.Settings.showsettings -->
@stop
