<table width="80%">
	<tr>
		<td style="text-align:left;padding-bottom:15px;font-size:20px;">The order has the following items:</td>
	</tr>
</table>
<table width="80%">
	<thead>
		<th style="text-align:left;">SKU</th>
		<th style="text-align:left;">Description</th>
		<th style="text-align:center;">Qty Purchased</th>
		<th style="text-align:center;">B/O</th>
		<th style="text-align:right;">Item Price</th>
	</thead>
	<tbody>
	<?php $sub_total = 0; ?>
	@foreach($order_items as $oi)
	<tr>
		<td style="text-align:left;">{{ $oi->order_item_sku }}</td>
		<td style="text-align:left;">{{ $oi->order_item_desc }}</td>
		<td style="text-align:center;">{{ $oi->order_item_qty_purchased}}</td>
		<td style="text-align:center;">{{ $oi->order_item_qty_backorder}}</td>
		<td style="text-align:right;">${{ number_format($oi->order_item_price,2)}}</td>
		<?php $sub_total += ($oi->order_item_price * $oi->order_item_qty_purchased); ?>
	</tr>
	@endforeach
	<tr>
		<td> </td>
		<td> </td>
		<td> </td>
		<td style="text-align:right;"><b style="font-size:22px;">Sub Total:</b></td>
		<td style="text-align:right;"><b style="font-size:22px;>${{ number_format($sub_total,2) }}</b></td>
	</tr>
	</tbody>
</table>
