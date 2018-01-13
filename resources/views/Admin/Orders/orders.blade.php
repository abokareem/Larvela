@extends('admin-master')
@section('title','Order Management')
@section('content')

	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-users"></i> Orders</h3></div>
	</div>

	@include('Templates.messages')

	<div class="row">
		<table class="table table-hover">
			<thead>
				<tr>
				<th>Order#</th>
				<th>Status</th>
				<th class="text-center">Actions</th>
				<th>Order Ref</th>
				<th>Date</th>
				<th>Time</th>
				<th>Customer</th>
				<th>Mobile</th>
				<th class="text-right">Order Value</th>
				<th class="text-right">Payment Status</th>
				<th class="text-right">Dispatch Status</th>
				<th class="text-right">Shipping Method</th>
				<th class="text-right">Shipping Cost</th>
				<th class="text-center">Total Item Count</th>
				</tr>
			</thead>
			<tbody>
			@foreach($orders as $o)
				<!--tr onclick='select({{ $o->id }})'  style="cursor: pointer" -->
				<tr style="cursor: pointer">
				<td>{{ sprintf("%08d",$o->id) }}</td>
				@if($o->order_status == "H")
					<td style="color:red;">On Hold</td>
				@elseif($o->order_status == "W")
					<td style="color:blue;">Waiting To process</td>
				@elseif($o->order_status == "D")
					<td style="color:green;">Dispatched</td>
				@else
					<td>{{ $o->order_status }}</td>
				@endif
				<td>
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Actions <span class="caret"></span></button>
						<ul class="dropdown-menu">
							<li><a href="/admin/order/view/{{$o->id}}"> View Order </a></li>
							<li role="separator" class="divider"></li>
							@if($o->order_payment_status=="W")
							<li><a href="/admin/order/update/paid/{{$o->id}}"> Mark as Paid </a></li>
							@else
							<li><a href="/admin/order/update/unpaid/{{$o->id}}"> Mark as UnPaid </a></li>
							@endif
							<li role="separator" class="divider"></li>
							@if($o->order_status=="H")
							<li><a href="/admin/order/update/waiting/{{$o->id}}"> Set to waiting </a></li>
							@else
							<li><a href="/admin/order/update/onhold/{{$o->id}}"> Put Order on Hold </a></li>
							@endif
							<li><a href="/admin/order/cancel/{{$o->id}}"> Cancel Order </a></li>
						</ul>
					</div>
				</td>


				<td>{{ $o->order_ref }}</td>
				<td>{{ $o->order_date }}</td>
				<td>{{ $o->order_time }}</td>
				<td>{{ $o->order_customer_name }}<br/><i>{{ $o->order_customer_email }}</i></td>
				<td>{{ $o->order_customer_mobile }}</td>
				<td class="text-right">${{ number_format($o->order_value,2) }}</td>
				@if( $o->order_payment_status=="P")
					<td class="text-right"  style="color:green;font-weight:bold;">Paid</td>
				@elseif($o->order_payment_status=="W")
					<td class="text-right" style="color:red;font-weight:bold;">Waiting for Payment</td>
				@else
					<td class="text-center">{{ $o->order_payment_status }}</td>
				@endif
				<td class="text-right">{{ $o->order_dispatch_status }}</td>
				@if( $o->order_shipping_method ==0 )
					<td class="text-right"> PICKUP </td>
				@else
					<td class="text-right">{{ $o->order_shipping_method }}</td>
				@endif
				<td class="text-right">${{ number_format($o->order_shipping_value,2) }}</td>
				<td class="text-center">{{ $o->order_item_count }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>


	<div class="row">
		<a href='/admin/order/addnew'><button class="btn btn-success">
			<i class="fa fa-user-plus"></i> Add Manual Order </button></a>
	</div>
<script>


function select(id)
{
var url = '/admin/order/view/'+id;
window.location.href = url;
}
</script>
@stop
