@extends($THEME_HOME."master-storefront")
@section("content")

<div class="container">

	<div class="row">
		<div class="col-xs-12 text-center p-3">
			<h3 class="text-red">Cart Timeout Alert!</h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 text-left p-3">
			<p class="text-black text-2xl">Once you have gone through the majority of the checkout process you need to <b>Accept payment</b>.<br>
			If you sit on the final stage and do not complete a payment action then the products you have in your cart remain locked from purchase.<br>
			To prevent this, you are given a reasonable amount of time to complete payment. To arrive at this page <b class="text-red">you have exceeded the time allowed</b> and the cart items are being released back so someone else can purchase them.</p>
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
