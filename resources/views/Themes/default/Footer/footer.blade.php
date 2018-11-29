<!-- START:footer -->
<?php
#
# $store
# Config(app.twitter.url))
# Config(app.facebook.url))
# Config(app.instagram.url))
#
#
#?>
<style>
.reverse { unicode-bidi: bidi-override; direction: rtl; }
.bottomfooter {background-color:black;display:inline-block;}
.footer-menu  {display:inline-block; padding-top:30px; padding-right:10px; padding-bottom:2px; padding-left:10px; color:#F0F0F0;}
.footer-menu  a { font-size:16px; line-height:20px; padding-left:20px; color:#F8F8F8; }
a {color: #7f7f7f;}
a:hover {color:white;}
.pink { color:#FFC0CB}
.deeppink { color:#FF1493}
.ghostwhite {color:#F8F8FF}
</style>
</div>

<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12 col-md-6">
		<!-- subscribe block was here -->
		</div>

		<div class="col-xs-12 col-md-6">
			<div class="pull-right" style="padding-right:25px;">
				<a href="https://larvela.org/">
					<font face="verdana" size="1">Powered by:</font></br><img src="https://larvela.org/larvela-cart-20180211.jpg">
				</a>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid" style="background-color:black;padding-bottom:25px;">
	<div class="row text-center">
		<div class="col-xs-12 col-md-6 ghostwhite">
			<h3>Contact Information</h3>
			<span class="store-name"><h3 style="color:white;">{{ $store->store_name }}</h3></span>
			<span class="deeppink">
			{{ $store->store_address }}<br>
			{{ $store->store_address2 }}<br>
			<strong>Mobile: </strong>{{ $store->store_contact }}<br>
			<strong>Hours:</strong> <i>( {{ $store->store_hours }} )</i><br>
			<strong>E-Mail: </strong>
			<span class="reverse" style="color:white;">{!!	strrev( $store->store_sales_email ) !!}</span>
			</span>
		</div>
		<div class="col-xs-12 col-md-3">
			<div class="ghostwhite text-left">
				<h3>Quick Links</h3>
				<a href="/about">About</a><br>
				<a href="/support">Help &amp; Support</a><br>
				<a href="/returns">Returns </a><br>
				<a href="/tandc">Terms of Service</a><br>
				<a href="/privacy">Privacy Policy</a><br>
			</div>
		</div>
		<div class="col-xs-12 col-md-3 ghostwhite text-left">
			<h3>Follow Us!</h3>
			<a href="{{ config('app.twitter.url','#') }}">
				<span class="fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i><i class="fa fa-twitter fa-stack-1x"></i>
				</span> Twitter </a><br/>
			<a href="{{ config('app.facebook.url','#') }}"><span class="fa-stack fa-lg">
				<i class="fa fa-square-o fa-stack-2x"></i> <i class="fa fa-facebook fa-stack-1x"></i>
				</span> Facebook </a><br/>
			<a href="{{ config('app.instagram.url','#') }}">
				<span class="fa-stack fa-lg">
					<i class="fa fa-square-o fa-stack-2x"></i> <i class="fa fa-instagram fa-stack-1x"></i>
				</span> Instagram </a><br/>
		</div>
	</div>
</div>

<script>
//grecaptcha.render('btnsubscribe', {'sitekey':'{{ env("NOCAPTCHA_SITEKEY") }}'}); 
<?php
#
# Special Initialization code here, called when rendered
#
#?>
function InitFooter()
{
console.log('InitFooter()');
}
</script>
<!-- END:{{$THEME_FOOTER}} -->
