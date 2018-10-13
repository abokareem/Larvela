@extends('Templates.admin-master')
@section('title','Category Management')
@section('content')
<div class="container">

	<div class="row">
		<div class="col-lg-12"><h3 class="page-header"><i class='fa fa-tasks fa-fw'></i> Category Management</h3></div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<div class="col-xs-10">&nbsp;</div>
		<div class="col-xs-2">
			<span class="text-right">
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
			</span>
		</div>
	</div>

	<div class='row'>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Title</th>
						<th>URL</th>
						<th>Visible on Menu</th>
						<th>Status</th>
						<th>Parent ID</th>
						<th>Store</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
				@foreach($categories as $cat)
					<tr onclick='editcat( {{ $cat->id }} );' style="cursor: pointer">
						<td>{{ $cat->id }}</td>
						<td>{{ $cat->category_title }}</td>
						<td>{{ $cat->category_url }}</td>
						<td>{{ $cat->category_visible }}</td>
						<td>{{ $cat->category_status }}</td>
						<td>{{ $cat->category_parent_id }}</td>
						<td>
						@foreach($stores as $s)
							@if($s->id == $cat->category_store_id)
								{{ $s->store_name }}
							@endif
						@endforeach
						<td>{{ $cat->category_description }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
	</div>

	<div class='row'>
		<div class="form-group">
			<div class="col-xs-4">
				<button id='btnaddnew' type="button" class="btn btn-success">Add New Category</button>
			</div>
		</div>
	</div>
</div>



<script>

$('#store_id').change(function(){
var id = $('#store_id').val();
var url = '/admin/categories?s='+id;
window.location.href = url;
});



function editcat(id)
{
var url = '/admin/category/edit/'+id;
window.location.href = url;
}


$('#btnaddnew').click( function()
{
var url = '/admin/category/addnew';
window.location.href = url;
});

</script>
@stop
