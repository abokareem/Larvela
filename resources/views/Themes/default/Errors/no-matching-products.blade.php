@extends($THEME_HOME."master-storefront")
@section("content")


<div class="container prodpage-block">

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<h1 style="color:red;">hmmmm.....</h1>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12" style="padding:50px;">
			<h3 style="color:red;">There appears to be no products of that type in stock just yet (give us time to add it).<br>Click the logo to get back to the Home Page - or wait and be automatically redirected.</h3>
		</div>
	</div>
</div>

<script>setTimeout(function(){window.location.href="/"},5000);</script>

@stop
