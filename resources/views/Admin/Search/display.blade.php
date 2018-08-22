@extends('admin-master')
@section('title','Search Results')
@section('content')

<div class="container">
	<div class="row">
		<div class='col-lg-12'><h3 class='page-header'><i class="fa fa-spanner"></i>Search Results</h3></div>
	</div>

	@include('Templates.messages')
	
	<div class="row">
		<table class="table table-hover">
			<tbody>
				@foreach($customers as $c)
				<tr>
					<td>{{ $c->id }} {{ $c->customer_name }} {{ $c->customer_email }} {{ $c->customer_mobile}}</td>
				</tr>
				@endforeach

				<?php
				#
				# add products, etc here
				#
				#?>
			</tbody>
		</table>
	</div>
</div>
<script>
<!-- Admin.Search.display -->
@stop
