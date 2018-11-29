<!-- START:Header BLOCK -->
<?php $user = Auth::user(); ?>
<style>
.store-name {font-family:'Dancing Script', cursive; cursor:pointer;font-size:48px; }
.navbar-default { border: none; background-color:white; }
@media screen and (max-width: 600px)
{
.store-name { font-size:24px; }
.mobile-nav { background: #f9c; display:block; }
.desktop-nav { display:none; }
}
@media screen and (min-width: 600px)
{
.mobile-nav { display:none;  }
.desktop-nav { display:block; }
}
</style>

<div class="container-fluid">
	<div class="row bg-black">
		<div class="text-white text-center">Enquiries: 0458 396 300</div>
	</div>

	<div class="row mobile-nav">
		<span id="storename-mobile" class="text-left store-name text-white p-4">{{ $store->store_name }}</span>
		<div class="pull-right m-2">
			@if(Auth::check())
			<a href="/myaccount"><i class="pr-10 fa fa-2x fa-cog fa-fw" aria-hidden="true"></i></a>
			<a href="/auth/logout"><i class="pr-10 fa fa-2x fa-sign-out fa-fw" aria-hidden="true"></i></a>
			@else
			<a href="/auth/login"><i class="pr-10 fa fa-2x fa-sign-in fa-fw" aria-hidden="true"></i></a>
			@endif
			<a href="/about"><i class="pr-10 fa fa-2x fa-question-circle fa-fw" aria-hidden="true"></i></a>
			<a href="/cart"><i class="pr-10 fa fa-2x fa-shopping-cart fa-fw" aria-hidden="true"></i></a>
		</div>
	</div>
	 <div class="row desktop-nav ">
	 	<span id="storename-desktop" class="text-left store-name p-4">{{ $store->store_name }}</span>
		<div class="pull-right m-2 text-2xl">
			@if(Auth::check())
				<?php $user = Auth::user(); ?>
				@if($user->id==1)
				<a class="pr-8 hover:text-pink hover:no-underline" href="/dashboard">Administration</a>
				@endif
			<a class="pr-8 hover:text-pink hover:no-underline" href="/myaccount">My Account</a>
			<a class="pr-8 hover:text-pink hover:no-underline" href="/auth/logout">Logout</a>
			@else
			<a class="pr-8 hover:text-pink hover:no-underline" href="/auth/login">Login</a>
			@endif
			<a class="pr-8 hover:text-pink hover:no-underline" href="/signup">Sign Up</a>
			<a class="pr-8 hover:text-pink hover:no-underline" href="/about">About</a>
		</div>
	</div>
</div>

<div class="store-header">
@if(view()->exists($THEME_INCLUDES.'cart') )
@include($THEME_INCLUDES.'cart')
@endif

@if(view()->exists($THEME_INCLUDES.'categorymenu'))
@include($THEME_INCLUDES.'categorymenu')
@endif

@if(view()->exists($THEME_INCLUDES.'breadcrumbs') )
@include($THEME_INCLUDES.'breadcrumbs')
@endif
</div>

<script>
function InitHeader()
{
	console.log('InitHeader()');
	$("#storename-desktop").on('click',function(){window.location.href ="/"});
	$("#storename-mobile").on('click',function(){window.location.href ="/"});
}
</script>
<!-- END:header BLOCK-->
