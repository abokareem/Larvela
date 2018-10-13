@extends('Templates.admin-master')
@section('title','Template Management')
@section('content')

<div class="container">
	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-envelope"></i> Templates</h3></div>
	</div>

	<div class="row">
		<span class="pull-right text-right">
			<select class="form-control" id="store_id" name="store_id">
				@foreach($stores as $s)
					@if($selected == $s->id)
					<option value="{{$s->id}}" selected>{{$s->store_name}}</option>
					@else
					<option value="{{$s->id}}">{{$s->store_name}}</option>
					@endif
				@endforeach
			</select>
		</span>
	</div>

	@include('Templates.messages')		

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>Template File</th>
				<th>Size</th>
				<th>Date Created</th>
				<th>Date Modified</th>
				<th>Store ID</th>
				<th>Action Id</th>
				<th>Mapping Id</th>
				</tr>
			</thead>
			<tbody>
			@foreach($templates as $t)
				<td>{{ $t->name }}</td>
				<td>{{ $t->file_size }}</td>
				<td>{{ $t->date_created }}</td>
				<td>{{ $t->date_modified }}</td>
				<td>{{ $t->store_id }}</td>
				@if($t->action_id == 0)
					<td>{{ $t->action_id }}</td>
				@else
					<td>{{ $actions[$t->action_id] }}</td>
				@endif
				<td>{{ $t->mapping_id }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
	
	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>Mapping ID</th>
				<th>File Name</th>
				<th>Action</th>
				<th>Store</th>
				</tr>
			</thead>
			<tbody>
			@foreach($mappings as $m)
				<td>{{ $m->id }}</td>
				<td>{{ $m->template_name }}</td>
				<td>
					@foreach($actions as $a)
						@if($a->id == $m->template_action_id)
						{{ $a->action_name }}
						@endif
					@endforeach
				</td>
				<td>{{ $m->template_store_id }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>


	<div class="row">
		<div class='col-xs-6'>
		</div>
	</div>


	<div class="row">
		<button type="button" id="addnew" name="addnew" class="btn btn-success">
			<i class="fa fa-user-plus"></i> Add Mapping
		</button>
		<button type="button" id="addaction" name="addaction" class="btn btn-success">
			<i class="fa fa-user-plus"></i> Actions
		</button>
	</div>
</div>
<script>

$('#store_id').change(function(){
var id = $('#store_id').val();
var url = '/admin/templates?s='+id;
window.location.href = url;
});


$('#addnew').click(function()
{
var url = '/admin/template/add/';
window.location.href = url;
});


function select(id)
{
var url = '/admin/template/edit/'+id;
window.location.href = url;
}
</script>
@stop
