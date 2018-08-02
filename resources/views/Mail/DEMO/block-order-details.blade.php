<table>
	<tr>
		<td colspan="2"><b style="text-align:right;font-size:20px;">Order Detail:</></td>
	</tr>
	<tr><td width="50%">Date:</td><td width="50%">{{ $order->order_date }}{{ $order->order_time }}</td></tr>
	<tr><td>Order Status:</td><td>{{ $order->order_status }}</td></tr>
	<tr><td>Payment Status:</td>
		@if( $order->order_payment_status == "W")
		<td><b style="color:red;">Waiting for payment</b></td>
		@elseif($order->order_payment_status == "P")
		<td><b style="color:green;">Paid</b></td>
		@elseif($order->order_payment_status == "C")
		<td><b style="color:red;">Cancelled</b></td>
		@else
		<td><b style="color:red;">Status {{$order->order_payment_status}} unknown</b></td>
		@endif
	</tr>

	<tr><td>Dispatch Status:</td>
		@if( $order->order_dispatch_status == "W")
		<td><b>Waiting to dispatch</b></td>
		@elseif( $order->order_dispatch_status == "D")
		<td><b>Dispatched</b></td>
		@else
		<td><b>HOLD</b></td>
		@endif
	</tr>
	<tr><td>Value:</td><td>${{ number_format($order->order_value,2) }}</td></tr>
</table>
