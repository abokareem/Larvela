@extends('Templates.admin-master')
@section('title','SEO Management')
@section('content')

<div class="container-fluid">
	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-th"></i> SEO Blocks</h3></div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>ID</th>
				<th>Token</th>
				<th>status</th>
				<th>Data</th>
				<th>Store</th>
				</tr>
			</thead>
			<tbody>
			@foreach($blocks as $b)
				<tr onclick='select({{ $b->id }})';>
				<td>{{ $b->id }}</td>
				<td>{{ $b->seo_token }}</td>
				<td>{{ $b->seo_status }}</td>
				<td>{{ $b->seo_html_data }}</td>
				<td>{{ $b->seo_store_id }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>


	<div class="row">
		<div class='col-xs-6'>
			{-!-!- $products->render() !-!-}
		</div>
	</div>


	<div class="row">
		<a href='/admin/seo/addnew'><button class="btn btn-success">
			<i class="fa fa-user-plus"></i> Add SEO Block </button></a>
	</div>
</div>
<script>
function select(id)
{
var url = '/admin/seo/edit/'+id;
window.location.href = url;
}
</script>
@stop
