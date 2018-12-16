@extends($THEME_HOME."master-storefront")
@section("content")


<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<h1 class="text-red">Page or Route not Found!</h1>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 p-3">
			<p class="text-black text-3xl">The requested page does not exist.<br>
			You either typed it in wrong or we have a malfunction..... :(</p>
		</div>
	</div>
</div>

<script>setTimeout(function(){window.location.href="/"},5000);</script>

@stop
