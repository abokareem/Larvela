@extends('admin-master')
@section('title','Bulk Update')
@section('content')

<script>
<?php
$token = csrf_token();
?>
</script>

<div class='container-fluid'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">Bulk Update</h3></div>
	</div>


	<form class='form-horizontal' name='update' id='update' method='post' enctype='multipart/form-data'>
	<div class="row">
		<table class='table table-stripped'>
			@foreach($products as $product)
			<tr>
				<td>{{ $product->prod_sku }}</td>
				<td>{{ $product->prod_title }}</td>
				<td>{{ $product->prod_qty }}</td>
				<td><input type="text" class="form-control" id='qty-{{$product->id}}' name="qty-{{$product->id}}"/></td>
				<td><button id="btn-{{$product->id}}" type="button" class='btn btn-small btn-success'>Update</button></td>
				<script>
				$("#btn-{{$product->id}}").prop('disabled',true);
				$("#btn-{{$product->id}}").click(function()
				{
					var v = $("#qty-{{$product->id}}").val(); console.log("Update value to "+v);
					$.ajaxSetup({headers:{'X-CSRF-TOKEN':'{{ $token }}' } } );
					$.ajax({url:"/ajax/update/{{$product->id}}",type:"POST",data:{"q":v},
						success: function(data) {console.log(data);}
					});

				});

				$("#qty-{{$product->id}}").change(function() {if($(this).val())$("#btn-{{$product->id}}").removeAttr('disabled');});
				</script>
			</tr>
			@endforeach
		</table>
	</div>

	<div class="row">
		<div class="form-group">
			<label class="control-label col-xs-2"> </label>
			<div class="col-xs-6">
				<button id='btndone'   type="button" class="btn btn-success">Save Product</button>
		 	</div>
		</div>
	</div>
	<input type='hidden' name='id' value='{{ $product->id }}'>
	{!! Form::token() !!}
	{!! Form::close() !!}
</div>

<script>
$('#btndone').click(function()
{
	var url = '/admin/products';
	window.location.href = url;
});

</script>

<!-- Admin.Products.bulkupdate -->
@stop
