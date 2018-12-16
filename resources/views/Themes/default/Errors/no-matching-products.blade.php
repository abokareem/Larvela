@extends($THEME_HOME."master-storefront")
@section("content")


<div class="container">

	<div class="row">
		<div class="col-xs-12">
			<h1 class="text-red">No Matching Products!</h1>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 p-3">
			<p class="text-black text-3xl">There appears to be no products of that type in stock just yet (give us time to add it).</p>
		</div>
	</div>
</div>

<script>setTimeout(function(){window.location.href="/"},7000);</script>

@stop
