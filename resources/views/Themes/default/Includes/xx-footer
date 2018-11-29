<!-- START:footer -->
<?php if(!isset($categories)) $categories = array(); ?>
<style>
.reverse { unicode-bidi: bidi-override; direction: rtl; }
</style>
<div class="container">
	<div class="row clearfix">
		<div class="col-xs-12 col-md-3 column">
			<h3>ONLINE SHOP</h3>
			<a href="/">Home</a><br/>
			@foreach($categories as $c)
				@if($c->category_parent_id==0)
				<a href="/category/{{ $c->id }}">{{ $c->category_title }}</a><br/>
				@endif
			@endforeach			
		</div>
		<div class="col-xs-12 col-md-3 column">
			<h3>FOLLOW US</h3>
			<a href="{{ config('app.twitter.url','#') }}">
				<span class="fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i><i class="fa fa-twitter fa-stack-1x"></i>
				</span> Twitter
			</a><br/>
			<a href="{{ config('app.facebook.url','#') }}"><span class="fa-stack fa-lg">
				<i class="fa fa-square-o fa-stack-2x"></i> <i class="fa fa-facebook fa-stack-1x"></i>
				</span>Facebook</a><br/>
			<a href="{{ config('app.googleplu.url','#') }}">
				<span class="fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i> <i class="fa fa-google-plus fa-stack-1x"></i>
				</span> Google+
			</a><br/>
		</div>
		<div class="col-xs-12 col-md-3 column">
			<h3>CONTACT INFO</h3>
			{{ $store->store_address }}<br>
			{{ $store->store_address2 }}<br>
			<strong>Mobile: </strong>{{ $store->store_contact }}<br>
			<strong>Hours:</strong> <i>( {{ $store->store_hours }} )</i><br>
			<strong>E-Mail: </strong><span class="reverse">
			{!!	strrev( $store->store_sales_email ) !!}
			</span>
		</div>
		<div class="col-xs-12 col-md-3 column">
			<h3>MAILING LIST</h3>
			<form action="/subscribe" method="post">
				<div class="input-prepend">
					<span class="add-on"><i class="icon-envelope"></i></span>
					<input type="text" id="email" name="email" placeholder="your@email.com"><br>
					<div class="g-recaptcha" data-sitekey="{{ env("NOCAPTCHA_SITEKEY") }}"></div>
					<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
				</div>
				<br/>
				<button id='btnsubscribe' data-toggle="tooltip" data-placement="top" title="We promise not to spam you!" class="btn btn-success btn-sm">Subscribe now!</button>
			{!! Form::close() !!}
		</div>
	</div>
</div>

<div class="container-fluid bottomfooter">
	<div class="container">
		<div class="footer-menu pull-right">
			<a href="/about">About</a>
			<a href="/support">Help &amp; Support</a>
			<a href="/tandc">Terms of Service</a>
			<a href="/privacy">Privacy Policy</a>
			<a href="/contact">Contact Us</a>
		</div>
	</div>
</div>
<br/>
<br/>
<script>

//grecaptcha.render('btnsubscribe', {'sitekey':'{{ env("NOCAPTCHA_SITEKEY") }}'}); 

function InitFooter()
{
console.log('InitFooter()');
}
</script>
<!-- END:footer -->
