@extends($THEME_HOME."master-storefront")
@section("content")

<div class="container">
	<div class="row">
		<div class="col-xs-12 p-3 text-center">
			<h1 class="text-red">Out of stock Alert!</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 p-3">
			<p class="text-black 3xl">Someone has beaten you and purchased one or more items that you have selected.<br/>
			Please select another item or variation or you can elect to be notified when its back in stock.</p>
		</div>
	</div>

	<div class="row">
		<span id='status' align="center"></span>
	</div>

	<div class="row">
		<form name='nf' id='nf'>
		<table class="table table-stripped">
			<thead>
				<tr>
				<th>SKU</th>
				<th>Product Title</th>
				<th class='text-right'>Price</th>
				<th class='text-right'>Quantity</th>
				<th class='text-right'>Notify Me When Back In stock</th>
				</tr>
			</thead>
			<tbody>
			@foreach($products as $p)
				<tr>
					<td>{{ $p->prod_sku }}</td>
					<td>{{ $p->prod_title }}</td>
					<td class='text-right'>${{ number_format($p->prod_retail_cost,2) }}</td>
					<td class='text-right'>{{ $p->prod_qty }}</td>
					<td class='text-right'><input type="checkbox" name="ck" id="{{ $p->prod_sku }}"></td>
				</tr>
			@endforeach
			</tbody>
		</table>
		{!! Form::token() !!}
		</form>
		<br/>
	</div>

	<div class="row">
		<div class="text-center">
			<a href="/"><button type="button" class="btn btn-default"><span class="fa fa-shopping-cart"></span> Continue Shopping </button></a></td>&nbsp;&nbsp;
			<button type="button" class="btn btn-success" id='btnnotify' disabled> Notify Me when back in stock <span class="fa fa-play"></span></button>
		</div>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
	</div>
</div>
<form name="ajax" id="ajax" method="post">
{!! Form::token() !!}
<input type="hidden" name="nf" value="{{$user->email}}">
<input type="hidden" name="sku" value="">
</form>



<script>
var reflag=0;
$('input[name="ck"]').click(function()
{
	if(reflag==0)
	{
		$('#btnnotify').attr("disabled", "disabled");
		$('#btnnotify').prop("disabled", false);
	}
});

$('#btnnotify').click(function()
{
	$("#nf input:checkbox:checked").each(function()
	{
		var id = $(this).attr("id");
		var form = $('#ajax');
		var sku = $("[name='sku']",form).val(id);
		$.ajax({type:"POST",url:"/notify/outofstock",data:form.serialize(),success:function(data)
		{
			var response = jQuery.parseJSON(data);
			if(response.status=="OK")
			{
				$('#status').replaceWith("<h3 style='color:green;' align='center'>Notification Recorded!</h3>");
				$('#btnnotify').prop("disabled", true);
				reflag++;
			}
			else
			{
				$('#status').replaceWith("<h3 style='color:red;' align='center'>Notification already Recorded!</h3>");
				$('#btnnotify').prop("disabled", true);
				reflag++;
			}
		}});
	});
});
</script>

@stop
