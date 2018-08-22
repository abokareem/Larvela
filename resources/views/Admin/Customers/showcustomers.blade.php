@extends('admin-master')
@section('title','Customer Management')
@section('content')

<div class='container'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header"><i class='fa fa-users fa-fw'></i> Customer Management</h3>
			<button id='btnaddnew' type="button" class="btn btn-sm btn-success">Add</button>
		</div>
	</div>

	@include('Templates.messages')

	<div class='row'>
		<div class="col-md-12">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>email</th>
						<th>Mobile</th>
						<th>Status</th>
						<th>Source</th>
						<th>Store</th>
						<th>Created</th>
						<th>Updated</th>
					</tr>
				</thead>
				<tbody>
				@foreach($customers as $customer)
					<tr onclick='editrow( {{ $customer->id }} );' style="cursor: pointer">
						<td>{{ $customer->id }}</td>
						<td>{{ $customer->customer_name }}</td>
						<td>{{ $customer->customer_email }}</td>
						<td>{{ $customer->customer_mobile }}</td>
						<td>{{ $customer->customer_status }}</td>
						<td>
						@foreach($sources as $s)
							@if($s->id == $customer->customer_source_id)
								{{ $s->cs_name }}
							@endif
						@endforeach
						</td>
						<td>
						@foreach($stores as $s)
							@if($s->id == $customer->customer_store_id)
								{{ $s->store_name }}
							@endif
						@endforeach
						</td>
						<td>{{ $customer->customer_date_created }}</td>
						<td>{{ $customer->customer_date_updated }}</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			{!! $customers->render() !!}
		</div>
	</div>
	<div class='row'>
		<div class="form-group">
			<div class="col-xs-4">
				<button id='btnaddnew' type="button" class="btn btn-success">Add New Customer</button>
			</div>
		</div>
	</div>
</div>



<script>



function editrow(id)
{
var url = '/admin/customer/edit/'+id;
window.location.href = url;
}


$('#btnaddnew').click( function()
{
var url = '/admin/customer/addnew';
window.location.href = url;
});

</script>
@stop
