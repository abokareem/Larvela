@extends("admin-pdfmaster")
@section("title","Packing Slip")
<?php
#
# Admin.Orders.pdf-packingslip
#
# OBJECT order
# ARRAY of OBJECTS orderitems
# OBJECT store
# OBJECT customer
# OBJECT address
# OBJECT shipping (Product Object)
#
#
#?>
@section("content")
<div class="container-fluid">

	<div class="row">
		<div class="col-xs-6 form-horizontal">
			<div class="form-group">
				<div>{{ $store->store_name }}</div>
			</div>
			<div class="form-group">
				<div>{{ $store->store_contact }}</div>
			</div>
			<br/>
			<br/>
			<div class="col-xs-9">
				{{ $address->customer_address }}<br/>
				{{ $address->customer_suburb }}<br/>
				{{ $address->customer_city }},{{ $address->customer_postcode}}<br/>
				{{ $address->customer_state }}<br/>
				{{ $address->customer_country }}<br/>
			{{ $address->customer_email }}<br/> </div>

		</div>


		<div class="col-xs-6 form-horizontal">
			<div class="form-group">
				<label class="col-xs-6 text-right">Order #</label>
				<div class="col-xs-6"><span style="font-size:24px;">{{ sprintf("%08d",$order->id) }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-6 text-right">Date:</label>
				<div class="col-xs-6">{{ $order->order_date }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-6 text-right">Dispatched:</label>
				<div class="col-xs-6">{{ $order->order_dispatch_date }}</div>
			</div>
			<div class="form-group">
				<label class="col-xs-2 text-right">Ref:</label>
				<div class="col-xs-10">{{ $order->order_ref }}</div>
			</div>
		</div>
	</div>


	<div class="row">
		<div><h3>Order Details</h3></div>
	</div>
	<?php $total = 0; ?>
	<div class="row">
		<table class="table table-stripped">
			<thead>
				<th>SKU</th>
				<th>Description</th>
				<th>Qty Purchased</th>
				<th>Qty Supplied</th>
				<th>Qty Backordered</th>
				<th class="text-right">Unit Price</th>
			</thead>
			<tbody>
			@foreach($orderitems as $item)
			<tr>
				<td>{{ $item->order_item_sku}}</td>
				<td>{{ $item->order_item_desc}}</td>
				<td>{{ $item->order_item_qty_purchased}}</td>
				<td>{{ $item->order_item_qty_supplied}}</td>
				<td>{{ $item->order_item_qty_backorder}}</td>
				<td class="text-right">${{ number_format($item->order_item_price,2)}}</td>
			</tr>
			<?php $total += $item->order_item_price; ?>
		@endforeach
			<tr>
				<td> </td>
				<td> </td>
				<td> </td>
				<td> </td>
				<td class="text-left">Shipping:</td>
				@if( !is_null($shipping))
				<td class="text-right">${{ number_format($shipping->prod_retail_cost,2)}}</td>
				@else
				<td class="text-right">$0.00</td>
				@endif
			</tr>
			<tr>
				<td> </td>
				<td> </td>
				<td> </td>
				<td> </td>
				<td class="text-left">Tax:</td>
				<td class="text-right">$0.00</td>
			</tr>
			<tr>
				<td> </td>
				<td> </td>
				<td> </td>
				<td> </td>
				<td class="text-left">Total:</td>
				<td class="text-right">${{ number_format($order->order_value,2)}}</td>
			</tr>
			</tbody>
		</table>
	</div>

	<div class="row">
		<br/>
		<br/>
		<br/>
		<br/>
		<div><span class="text-left">Tracking Number __________________________________________</span></div>
	</div>

</div>
@stop
