@extends('admin-master')
@section('title','Sales Dashboard')
@section('content')

<?php
# JSON={"tab_title":"Sales Today","tab_anchor":"salestoday"}
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class='container-fluid'>
	<div class="row">
		<div class="col-xs-8">
			<h4 class="page-header">Pending Orders</h4>
			<table class="table table-hover">
				<thead>
					<th>Date</th>
					<th>Order ID</th>
					<th>Status</th>
					<th>Payment Status</th>
					<th>Dispatch Status</th>
					<th>Time</th>
				</thead>
				<tbody>
				@foreach($orders as $o)
				<tr onclick="select({{$o->id}});" style="cursor:pointer;">
					<td>{{ $o->order_date }}</td>
					<td>{{ $o->id }}</td>
					@if($o->order_status=="W")
						<td style="color:red;font-weight:bold;">Waiting</td>
					@elseif($o->order_status=="C")
						<td style="color:blue;font-weight:bold;>Cancelled</td>
					@elseif($o->order_status=="D")
						<td style="color:green;font-weight:bold;">Dispatched</td>
					@elseif($o->order_status=="H")
						<td style="color:red;font-weight:bold;">On Hold</td>
					@else
						<td>{{$o->order_status}}</td>
					@endif
					@if($o->order_payment_status=="P")
						<td style="color:green;font-weight:bold;">Paid</td>
					@elseif($o->order_payment_status=="W")
						<td style="color:red;font-weight:bold;">Waiting</td>
					@else
						<td>Unknown -- {{ $o->order_payment_status }}</td>
					@endif
					@if($o->order_dispatch_status=="W")
						<td style="color:red;font-weight:bold;">Waiting to dispatch</td>
					@else
						<td>{{ $o->order_dispatch_status }}</td>
					@endif
					<td>{{ $o->order_time }}</td>
				</tr>
				@endforeach
				</tbody>
			</table>
			
		</div>

		<div class="col-xs-4">
			<h4 class="page-header">New Subscriptions</h4>
			@if( isset($subscriptions) )
				<table class="table table-hover">
				@foreach($subscriptions as $s)
					<tr>
						<td>{{ $s->id }}</td>
						<td>{{ $s->customer_name }} ( {{ $s->customer_email }} )</td>
						<td>{{ $s->customer_mobile }}</td>
						<td>{{ $s->customer_source_id }}</td>
						<td>{{ $s->customer_date_created }}</td>
					</tr>
				@endforeach
				</table>
			@else
			No Data....
			@endif
		</div>
	</div>



	<div class="row">
		<div id='my-tab-content' class='tab-content'>
			<div class='tab-pane active' id='sales'>
				<h3>Sales</h3>
				<div id='saleschart' style="height:600px;"></div>
			</div>
		</div>
	</div>

<script>
google.charts.load('current', {packages: ['corechart','bar']});
google.charts.setOnLoadCallback(drawChart);

function drawChart()
{
	var data = google.visualization.arrayToDataTable([
		['Date', 'Sales', { role: "style" } ],
		@foreach($datarows as $d)
		[ '{{ $d->day }}', {{ $d->count_sold }}, '{{ $d->colour }}' ],
		@endforeach
		]);
	var options = {
		title: 'Sales',
		width: 1324,
		height: 600,
		legend: { position: 'right', maxLines: 3 },
		bar: { groupWidth: '95%' },
		bars: 'horizontal',
		};
	var chart = new google.visualization.ColumnChart( document.getElementById('saleschart') );
	chart.draw(data,options);
}
</script>


	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover">
				<thead>
				<tr>
					<th>ID</th>
					<th>SKU</th>
					<th>Description</th>
					<th>Status</th>
					<th>Payment Method</th>
					<th>Sale Value</th>
					<th>Shipping Cost</th>
				</tr>
				</thead>
				<tbody>
				@define $cnt = 0
				@if(sizeof($monthlysalesdata)>0)
				@foreach($monthlysalesdata as $d)
				<tr onclick='select( {{ $d->id }} );' style="cursor: pointer">
					<td> -todo- </td>
					<td> -todo- </td>
					<td> -todo- </td>
					<td> -todo- </td>
					<td> -todo- </td>
					<td> -todo- </td>
					<td> -todo- </td>
				</tr>
				<?php $cnt++; ?>
				@endforeach
				@else
				<tr><td colspan=6><i style="color:red;">No data available.....</i></td></tr>
				@endif
				</tbody>
			</table>
		</div>
	</div>
<script>

function select(id)
{
var url = '/admin/order/view/'+id;
window.location.href = url;
}
</script>
@stop
