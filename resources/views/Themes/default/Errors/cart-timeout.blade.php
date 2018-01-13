@extends($THEME_HOME."master-storefront")
@section("content")

<div class="container">

	<div class="row">
		<div class="col-xs-12" style="padding:20px;" align="center">
			<h3 style="color:red;">Cart Timeout Alert!</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12" align="center">
			<p style="font-size:18px; font-family:'Open Sans';">Once you have gone through the majority of the checkout process you need to Accept payment. If you sit on the final stage and do not complete a payment action then the products you have in your cart remain locked from purchase. To prevent this, you are given a reasonable amount of time to complete payment. To arrive at this page you have exceeded that time span and the cart items are being released back so someone else can purchase them.</p><br/>
		</div>
	</div>


	<div class="row">
		<div class="text-center">
			<a href="/"><button type="button" class="btn btn-default"><span class="fa fa-shopping-cart"></span> Continue Shopping </button></a>
		</div>
		<br/>
		<br/>
		<br/>
		<br/>
		<br/>
	</div>
</div>
@stop
