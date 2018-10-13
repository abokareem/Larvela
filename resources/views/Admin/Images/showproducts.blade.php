@extends('Templates.admin-master')
@section('title','Product Selection')
@section('content')

	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-users"></i> Products</h3></div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<div class='pull-right'>
			<select class="form-control" id="store_id" name="store_id">
			@foreach($stores as $s)
				@if($s->id == $store_id)
				<option value="{{ $s->id }}" selected>{{ $s->store_name }}</option>
				@else
				<option value="{{ $s->id }}">{{ $s->store_name }}</option>
				@endif
			@endforeach
			</select>
		</div>
		<div class='pull-right'>
			<select class="form-control" id="category_id" name="category_id">
			@foreach($categories as $c)
				@if($c->id == $category_id)
				<option value="{{ $c->id }}" selected>{{ $c->category_title }}</option>
				@else
				<option value="{{ $c->id }}">{{ $c->category_title }}</option>
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
				<th>SKU</th>
				<th>Name</th>
				<th>Short Description</th>
				<th>Visible</th>
				<th>Base Cost</th>
				<th>Retail Cost</th>
				<th>Weight</th>
				<th>Qty in Stock</th>
				<th>Reorder Qty</th>
				<th>Free Shipping</th>
				<th>Created</th>
				<th>Combine Code</th>
				<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach($products as $p)
				<tr onclick='select({{ $p->id }})'  style="cursor: pointer">
				<td>{{ $p->id }}</td>
				<td>{{ $p->prod_sku }}</td>
				<td>{{ $p->prod_title }}</td>
				<td>{{ $p->prod_short_desc }}</td>
				<td>{{ $p->prod_visible }}</td>
				<td>${{ number_format($p->prod_base_cost,2) }}</td>
				<td>${{ number_format($p->prod_retail_cost,2) }}</td>
				<td>{{ $p->prod_weight }}g</td>
				<td>{{ $p->prod_qty }}</td>
				<td>{{ $p->prod_reorder_qty }}</td>
				@if( $p->prod_has_free_shipping > 0)
				<td>YES</td>
				@else
				<td> </td>
				@endif
				<td>{{ $p->prod_date_created }} - {{ $p->prod_time_created }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>


<script>

$('#category_id').change(function(){
var cid = $('#category_id').val();
var sid = $('#store_id').val();
var url = '/admin/images?c='+cid+'&s='+sid;
window.location.href = url;
});




$('#store_id').change(function(){
var cid = $('#category_id').val();
var sid = $('#store_id').val();
var url = '/admin/images?s='+sid+'&c='+cid;
window.location.href = url;
});


function select(id)
{
var url = '/admin/image/show/'+id+"?s={{$store_id}}&c={{$category_id}}";
window.location.href = url;
}
</script>
@stop
