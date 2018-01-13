@extends('master-users')
@section('title','Reports')
@section('content')

<?php
# JSON={"tab_title":"Payments Received","tab_anchor":"paymentsrec"}
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class='container'>
	<div class="row">
		<div class="col-lg-12"><h3 class="page-header">My Reports</h3></div>
	</div>


	<div class="row">
		{!! $tabs !!}
	</div>


	<div class="row">
		<div id='my-tab-content' class='tab-content'>
			<div class='tab-pane active' id='paymentsrec'>
				<h3>Payments Received</h3>
				<div id='barchart' style="height:600px;"></div>
<script>
google.charts.load('current', {packages: ['corechart','bar']});
google.charts.setOnLoadCallback(drawChart);

function drawChart()
{
	var data = google.visualization.arrayToDataTable([
		['Debtor', 'Total Paid So far' ],
		@foreach($datarows as $d)
		[ '{{ $d->name }}', {{ $d->paid }} ],
		@endforeach
		]);
	var options = {
		title: 'Payment Recevied',
		width: 1024,
		height: 600,
		legend: { position: 'top', maxLines: 3 },
		bar: { groupWidth: '75%' },
		bars: 'vertical',
		colors: ['#c0430e']
		};

	var chart = new google.visualization.BarChart( document.getElementById('barchart') );
	chart.draw(data,options);
}
</script>

			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<table class="table table-hover">
				<thead>
				<tr>
					<th>ID</th>
					<th>Debtor</th>
					<th>Last Payment</th>
					<th>Owed</th>
					<th>Paid</th>
					<th>Balance</th>
				</tr>
				</thead>
				<tbody>
				@define $cnt = 0
				@foreach($datarows as $t)
				<tr onclick='select({{ $t->id }} );' style="cursor: pointer">
					<td >{{ $t->id }}</td>
					<td>{{ $t->name }} <i>({{ $t->code }})</i></td>
					<td> -todo- </td>
					<td>${{ $t->owed }}</td>
					@if($t->paid == 0)
					<td style="color:red;">${{ $t->paid}}</td>
					@else
					<td>${{ $t->paid}}</td>
					@endif
					<td>${{ $t->balance }}</td>
				</tr>
				<?php $cnt++; ?>
				@endforeach
				</tbody>
			</table>
		</div>
	</div>
<script>

function select(id)
{
var url = '/users/debtor/'+id;
window.location.href = url;
}
</script>
@stop
