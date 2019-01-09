@extends('Templates.admin-master')
@section('name','Store Management')
@section('content')

	<div class="row">
		<div class="col-lg-12"><h3 class="page-header"><i class='fa fa-tasks fa-fw'></i> Store Management</h3></div>
	</div>

	@include('Templates.messages')

	<div class='row'>
		<div class="col-md-12">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Store Code</th>
						<th>Name</th>
						<th>Analytics Code</th>
						<th>Contact Number</th>
						<th>Hours</th>
						<th>URL</th>
						<th>Parent ID</th>
						<th>Currency</th>
						<th>Main Logo</th>
						<th>Alt Text</th>
						<th>Thumb Img</th>
						<th>Invoice Img</th>
						<th>Email Img</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				@foreach($stores as $st)
					<tr onclick='editrow( {{ $store->id }} );'>
						<td>{{ $st->id }}</td>
						<td>{{ $st->store_env_code }}</td>
						<td>{{ $st->store_name }}</td>
						<td> N/A </td>
						<td>{{ $st->store_contact }}</td>
						<td>{{ $st->store_hours }}</td>
						<td>{{ $st->store_url }}</td>
						<td>{{ $st->store_parent_id }}</td>
						<td>{{ $st->store_currency }}</td>
						<td>{{ $st->store_logo_filename }}</td>
						<td>{{ $st->store_logo_alt_text }}</td>
						<td>{{ $st->store_logo_thumb }}</td>
						<td>{{ $st->store_logo_invoice }}</td>
						<td>{{ $st->store_logo_email }}</td>
						<td>{{ $st->store_status }}</td>
						<td><i class="fa fa-trash"></i> Delete</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			{-!-! $categories->render() !-!-}
		</div>
	</div>
	<div class='row'>
		<div class="form-group">
			<div class="col-xs-4">
				<button id='btnaddnew' type="button" class="btn btn-success">Add New Store</button>
			</div>
		</div>
	</div>


<script>



function editrow(id)
{
var url = '/admin/store/edit/'+id;
window.location.href = url;
}


$('#btnaddnew').click( function()
{
var url = '/admin/store/addnew';
window.location.href = url;
});

</script>
@stop
