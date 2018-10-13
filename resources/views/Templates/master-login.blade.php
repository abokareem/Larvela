<!DOCTYPE html>
<html lang="en">
<head>
<title>{{ $store->store_name}}</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="{{ $store->store_name}}" />
<meta name="robots" content="index">
<meta name="robots" content="follow">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"  type='text/css'>

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-T8Gy5hrqNKT+hzMclPo118YTQO6cYprQmhrYwIiQ/3axmI1hQomh7Ud2hPOy8SP1" crossorigin="anonymous">

<link href="/css/store.css" rel="stylesheet" type='text/css'>


<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>

@if(view()->exists('layout-widths') )
@include('layout-widths')
@endif
</head>
<body>
<!-- START:Header BLOCK -->
<div class="store-header">
@if(view()->exists($THEME_INCLUDES.'topbar') )
@include($THEME_INCLUDES.'topbar')
@endif
<div class="container">
	@if(view()->exists($THEME_INCLUDES.'cart') )
		@include($THEME_INCLUDES.'cart')
	@endif
<!-- START:StoreName -->
	<div class="row">
		<div class="store-name text-center"><h2 id='store-logo-home'>{{ $store->store_name }}</h2></div>
	</div>
<!-- END:StoreName -->

</br>
</br>
</br>
@yield('content')
</br>
</br>
</br>
</div>

@if (view()->exists($THEME_FOOTER.'footer') )
@include($THEME_FOOTER.'footer')
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>

<script>
@if (view()->exists($THEME_INCLUDES.'analytics') )
@include( $THEME_INCLUDES.'analytics')
@endif
</script>

<script>
function InitHeader()
{
	console.log('InitHeader()');
	$('#store-logo-home').hover(function() { $(this).css('cursor','pointer'); });
	$("#store-logo-home").click(function()
	{
		var url = '/';
		window.location.href = url;
	});
}

$(document).ready(function()
{
if(typeof InitHeader == 'function') { InitHeader(); }
if(typeof InitTopBar == 'function') { InitTopBar(); }
if(typeof InitFooter == 'function') { InitFooter(); }
if(typeof InitProductGrid == 'function') { InitProductGrid(); }
if(typeof InitSlider == 'function') { InitSlider(); }
$('[data-toggle="tooltip"]').tooltip();
$('[data-toggle="popover"]').popover();
});
</script>
</body>
</html>
