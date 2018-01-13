@extends($THEME_HOME."master-storefront")
@section("content")


<div class="container prodpage-block">

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<h1>We are sorry to see you go!</h1>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12" style="padding:50px;">
			<h3>Your Subscription has been REMOVED!</h3>
		</div>
	</div>
</div>

<script>setTimeout(function(){window.location.href="/"},10000);</script>

@stop
