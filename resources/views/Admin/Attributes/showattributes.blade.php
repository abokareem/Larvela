@extends('Templates.admin-master')
@section('title','Product Attributes Management')
@section('content')

<div class="container-fluid">
	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-tool"></i> Product Attributes</h3></div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>ID</th>
				<th>Attribute Name</th>
				<th>Assigned Store</th>
				<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach($attributes as $a)
				<tr onclick='select({{ $a->id }})'  style="cursor: pointer">
				<td>{{ $a->id }}</td>
				<td>{{ $a->attribute_name }}</td>
				<td>{!! $stores[$a->store_id] !!}</td>
				<td><i class="fa fa-trash"></i> <a href="/admin/attribute/delete/{{ $a->id }}">Delete</a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>


	<div class="row">
		<a href='/admin/producttype/addnew'><button class="btn btn-success">
			<i class="fa fa-user-plus"></i> Add New Type </button></a>
	</div>
</div>
<script>


function select(id)
{
var url = '/admin/producttype/edit/'+id;
window.location.href = url;
}


</script>
@stop
