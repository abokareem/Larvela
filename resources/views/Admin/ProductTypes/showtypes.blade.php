@extends('admin-master')
@section('title','Product Type Management')
@section('content')

<div class="container-fluid">
	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-tool"></i> Product Types</h3></div>
	</div>
	
	@include('Templates.messages')

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>ID</th>
				<th>Product Type</th>
				<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach($product_types as $p)
				<tr onclick='select({{ $p->id }})'  style="cursor: pointer">
				<td>{{ $p->id }}</td>
				<td>{{ $p->product_type }}</td>
				<td><i class="fa fa-trash"></i> <a href="/admin/producttype/delete/{{ $p->id }}">Delete</a></td>
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
