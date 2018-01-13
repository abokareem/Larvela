@extends("admin-master")
@section("title","Order Management")
<?php
#
# Admin.Orders.showorder
#
# OBJECT order
# ARRAY of OBJECTS orderitems
# OBJECT customer
# OBJECT shipping (Product Object)
#
#
#?>
@section("content")
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header"><i class="fa fa-users"></i>Customer Order - <span style="color:blue;">{{ $customer->customer_email }}</span></h3></div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<form class="form-horizontal" method="post" name="codata" id="codata">
		<div class="col-xs-6 form-horizontal">
			<div><h3>Customer Details</h3></div>
			<div class="form-group">
				<label class="col-xs-3 text-right">Name:</label>
				<div class="col-xs-9">{{ $customer->customer_name }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-3 text-right">Contact Number:</label>
				<div class="col-xs-9">{{ $customer->customer_mobile }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-3 text-right">Email:</label>
				<div class="col-xs-9">{{ $customer->customer_email }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-3 text-right">Address:</label>
				<div class="col-xs-9">
					{{ $address->customer_address }}<br/>
					{{ $address->customer_suburb }}<br/>
					{{ $address->customer_city }},{{ $address->customer_postcode}}<br/>
					{{ $address->customer_state }}<br/>
					{{ $address->customer_country }}<br/>
					{{ $address->customer_email }}<br/>
				</div>
			</div>
		</div>
		</form>


		<div class="col-xs-6 form-horizontal">
			<div><h3>Order Details</h3></div>
			<div class="form-group">
				<label class="col-xs-2 text-right">Order Number:</label>
				<div class="col-xs-10"><span style="font-size:24px;">{{ sprintf("%08d",$order->id) }}</span>&nbsp;&nbsp;&nbsp;<i>( {{ $order->order_date }} - {{ $order->order_time }} )</i></div>
			</div>
			<div class="form-group">
				<label class="col-xs-2 text-right">Order Ref:</label>
				<div class="col-xs-10">{{ $order->order_ref }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-2 text-right">Source:</label>
				<div class="col-xs-10">{{ $order->order_src }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-2 text-right">Status:</label>
				<div class="col-xs-10">Order: {{ $order->order_status }} Dispatch: {{ $order->order_dispatch_status}}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-2 text-right">Payment Status:</label>
				@if($order->order_payment_status == "P")
					<div class="col-xs-10" style="color:green;font-weight:bold;">PAID</div>
				@elseif($order->order_payment_status == "W")
					<div class="col-xs-10" style="color:red;font-weight:bold;">Waiting</div>
				@else
					<div class="col-xs-10">unknown -> {{ $order->order_src }}</div>
				@endif
			</div>
			<div class="form-group">
				<label class="col-xs-2 text-right">Shipping:</label>
				<div class="col-xs-10">${{ number_format($order->order_shipping_value,2) }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-2 text-right">Total:</label>
				<div class="col-xs-10">${{ number_format($order->order_value,2) }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-2 text-right">Shipping Method:</label>
				@if(!is_null($shipping))
					<div class="col-xs-10">{{ $shipping->prod_title }} Weight: {{ $shipping->prod_weight }}</div>
				@else
					@if($order->order_shipping_method == 0)
						<div class="col-xs-10"><b>Local Pickup - Check with Purchaser</b></div>
					@else
						<div class="col-xs-10"><b>SHIPPING METHOD NOT SPECIFIED</b></div>
					@endif
				@endif
			</div>
		</div>
	</div>


	<div class="row">
		<div><h3>Order Item Detail</h3></div>
	</div>

	<div class="row">
		<form name="orderdata" id="orderdata" method="POST" class="form-horizontal">
		<table class="table table-stripped">
			<thead>
				<th>ID</th>
				<th>Status</th>
				<th>Dispatch Status</th>
				<th>SKU</th>
				<th>Description</th>
				<th class="text-right">Unit Price</th>
				<th>Qty Purchased</th>
				<th>Qty Supplied</th>
				<th>Qty Backordered</th>
				<th>Actions</th>
			</thead>
			<tbody>
			@foreach($orderitems as $item)
			<tr>
				<td>{{ $item->id}}</td>
				<td>{{ $item->order_item_status}}</td>
				<td>{{ $item->order_item_dispatch_status}}</td>
				<td>{{ $item->order_item_sku}}</td>
				<td>{{ $item->order_item_desc}}</td>
				<td class="text-right">${{ number_format($item->order_item_price,2)}}</td>
				<td>{{ $item->order_item_qty_purchased}}</td>
				<td><input type="text" name="su-{{$item->id}}" value="{{ $item->order_item_qty_purchased}}"></td>
				<td><input type="text" name="bo-{{$item->id}}" value="{{ $item->order_item_qty_backorder}}"></td>
				<td><button id="btnbo-{{$item->id}}" data-bo="{{$item->id}}" class="btn btn-sm btn-warning"> Backorder Item </button></td>
			</div>
		@endforeach
			</tbody>
		</table>
		{!! Form::token(); !!}
		</form>
	</div>
	<div class="row">
		<br/><br/>
		<div  class="text-center">
			<button id="btnps" class="btn btn-success"> Packing Slip </button>&nbsp;&nbsp;
			<button id="btninv" class="btn btn-success"> Store Invoice </button>&nbsp;&nbsp;
			<button id="btndispatch" class="btn btn-danger"> Dispatch Goods </button>
		</div>
	</div>
</div>
<script>
$('#btninv').click(function()
{
var url = '/admin/order/pdf/shopinvoice/{{$order->id}}';
window.location.href = url;
});

$('#btnps').click(function()
{
var url = '/admin/order/pdf/packingslip/{{$order->id}}';
window.location.href = url;
});

$('#btndispatch').click(function()
{
	$('#orderdata').attr('action','/admin/order/dispatch/{{$order->id}}');
    $('#orderdata').submit();
});

@foreach($orderitems as $item)
$('#btnbo-{{$item->id}}').click(function() { var url = '/admin/order/backorder/{{$item->id}}'; window.location.href = url; });
@endforeach
</script>
@stop
