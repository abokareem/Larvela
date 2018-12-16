@extends($THEME_HOME."master-storefront")
@section("content")


<div class="container">

	<div class="row">
		<div class="col-xs-12">
			<h1 class="text-red">Subscription Routing Error</h1>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 p-3">
			<p class="text-black text-3xl">There appears to be an error with the subscription record or the route used to retrieve it..... :(</p>
		</div>
	</div>
</div>

<script>setTimeout(function(){window.location.href="/"},6000);</script>

@stop
