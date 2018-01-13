@extends($THEME_HOME."master-storefront")
@section('content')
<style>
.media-body { padding:15px; }
</style>
	<div class="row cartpage-block">
		<div class='text-right' style="padding-right:50px;"> <b style="color:green;"> Cart</b>
			<span class="fa fa-play"></span> Shipping
			<span class="fa fa-play"></span> Confirm
			<span class="fa fa-play"></span> Payment
			<span class="fa fa-play"></span> Done!
		</div>
	</div>

	<div class="row cartpage-block">
		<div class="col-sm-12 col-md-10 col-md-offset-1">
			<table class="table table-hover">
				<thead>
				<tr>
					<th>Product</th>
					<th>Description</th>
					<th class="text-center">Qty</th>
					<th class="text-center">Total</th>
					<th>&nbsp;</th>
				</tr>
				</thead>
				<tbody>
					<?php $idx=0; ?>
					@foreach($products as $p)
					<tr>
						<td class="col-xs-1 col-sm-2 col-md-2">
							<div class="media">
								<a class="thumbnail pull-left" href="/product/{{$p->id}}">
									<img class="media-object" src="{{ $p->thumbnail }}" style="width: 100px; height: 72px;">
								</a>
							</div>
						</td>
						<td class="col-xs-4 col-sm-2 col-md-2">
								<h4 class="media-heading"><a href="/product/{{$p->id}}">{{$p->prod_title}}</a></h4>
								<p>{{$p->prod_short_desc}}</p>
								<p><i>Unit Cost: <span class="prod-retail-cost">${{ $p->prod_retail_cost }}</span>&nbsp;
								@if($p->prod_weight > 1000)
									<?php $weight = $p->prod_weight/1000; ?>
									Weight: <b>{{$weight}}</b> Kg</i></p>
								@else
									Weight: <b>{{$p->prod_weight}}</b> grams</i></p>
								@endif
						</td>
						<td class="col-xs-2 col-sm-2 col-md-2 text-center">
							<div class="input-group number-spinner">
								<span class="input-group-btn">
									<a href="/cart/incqty/{{ $items[$idx]->cart_id }}/{{ $items[$idx]->id }}">
									<button class="btn btn-default" data-dir="up"><i class="fa fa-plus"></i></button>
									</a>
								</span>
								<input type="text" class="form-control text-center" id="btnqty" value="{{ $p->qty }}">
								<span class="input-group-btn">
									<a href="/cart/decqty/{{ $items[$idx]->cart_id }}/{{ $items[$idx]->id }}">
									<button class="btn btn-default" data-dir="dwn"><span class="glyphicon glyphicon-minus"></span></button>
									</a>
								</span>
							</div>
						</td>
						<td class="col-xs-1 text-right"><strong>${{ number_format($p->sub_total,2) }}</strong></td>
						<td class="col-xs-1 col-sm-2 col-md-2 text-center">
							<a href="/removeItem/{{$p->id}}"><button type="button" class="btn btn-danger btn-sm"><span class="fa fa-remove"></span> Remove </button></a>
						</td>
					</tr>
					<?php $idx++; ?>
					@endforeach
					<tr>
						<td> </td>
						<td> </td>
						<td class="text-right"><h4>Sales Tax</h4></td>
						<td class="text-right"><strong>${{ number_format($tax,2) }}</strong></td>
						<td> </td>
					</tr>
					<tr>
						<td> </td>
						<td> </td>
						<td class="text-right"><h4>Shipping</h4></td>
						<td class="text-right"><strong>${{ number_format($shipping,2) }}</strong></td>
						<td> </td>
					</tr>
					<tr>
						<td> </td>
						<td> </td>
						<td class="text-right"><h3>Total</h3></td>
						<td class="text-right"><h3><strong>${{ number_format($total,2) }}</strong></h3></td>
						<td> </td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="row">
		<div class="text-center">
			<a href="/"><button type="button" class="btn btn-default"><span class="fa fa-shopping-cart"></span> Continue Shopping </button></a></td>&nbsp;&nbsp;
			@if(sizeof($products)>0)
			<button type="button" class="btn btn-success" id='btnshipping'> Shipping <span class="fa fa-play"></span></button>
			@endif
		</div>
	</div>


	<div class="row">
		<div class="text-center">
			<!-- b style='font-size:18px;color:red;'>Payment Gateway Down</b -->
		</div>
	</div>
	<br/>
	<br/>


<script>
$('#btnshipping').click(function(){ var url = '/shipping'; window.location.href = url; });

$(document).on('click', '.number-spinner button',function()
{
	var btn = $(this),oldValue = btn.closest('.number-spinner').find('input').val().trim(),newVal = 0;
	if (btn.attr('data-dir') == 'up') {
		newVal = parseInt(oldValue) + 1;
		} else {
		if (oldValue > 1) {
		newVal = parseInt(oldValue) - 1;
		} else {
		newVal = 1;
		}
		}
		btn.closest('.number-spinner').find('input').val(newVal);
});
</script>
@endsection
