@extends('Templates.admin-master')
@section('title','Advert Management')
@section('content')

	<div class="row">
		<div class="col-lg-12"><h3 class="page-header"><i class='fa fa-tasks fa-fw'></i> Advert Management</h3></div>
	</div>

	@include('Templates.messages') 
	
	<div class='row'>
		<div class="col-md-12">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Status</th>
						<th>Store ID</th>
						<th>HTML</th>
						<th>From Date</th>
						<th>To Date</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				@foreach($adverts as $advert)
					<tr onclick='editrow( {{ $advert->id }} );' style="cursor: pointer">
						<td>{{ $advert->id }}</td>
						<td>{{ $advert->advert_name }}</td>
						<td>{{ $advert->advert_status }}</td>
						<td>{{ $advert->advert_store_id }}</td>
						<td>{{ $advert->advert_html_code }}</td>
						<td>{{ $advert->advert_date_from }}</td>
						<td>{{ $advert->advert_date_to }}</td>
						<td><i class="fa fa-delete"></i> Delete</td>
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
				<button id='btnaddnew' type="button" class="btn btn-success">Add New Advert</button>
			</div>
		</div>
	</div>



<script>



function editrow(id)
{
var url = '/admin/advert/edit/'+id;
window.location.href = url;
}


$('#btnaddnew').click( function()
{
var url = '/admin/advert/add';
window.location.href = url;
});

</script>
@stop
