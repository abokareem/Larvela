<!DOCTYPE html>
<html lang="en">
<head>
<title>Welcome to {{ $store->store_name }}!</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="{{ $store->store_name}}" />
<meta name="robots" content="index">
<meta name="robots" content="follow">
<meta name="viewport" content="width=device-width, initial-scale=1">
@yield('css')
<!-- link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
-->
<link href="https://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet"> 
<link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet"  type='text/css'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css">
<link href="/css/store.css" rel="stylesheet" type='text/css'>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>

<!-- script src='//www.google.com/recaptcha/api.js'></script -->
@yield('header-css')
</head>
<body>
@if (view()->exists( $THEME_HEADER.'header') )
@include( $THEME_HEADER.'header')
@endif
<div class='row'>
	<h2 class='text-center'><!-- IMPORTANT MESSAGES HERE --></h2> 
</div>
@yield('content')

@if (view()->exists( $THEME_FOOTER.'footer') )
@include( $THEME_FOOTER.'footer')
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>

<script>
$(document).ready(function()
{
if(typeof InitHeader == 'function') { InitHeader(); }
if(typeof InitTopBar == 'function') { InitTopBar(); }
if(typeof InitFooter == 'function') { InitFooter(); }
if(typeof InitCategoryMenu == 'function') { InitCategoryMenu(); }
if(typeof InitProductGrid == 'function') { InitProductGrid(); }
if(typeof InitSlider == 'function') { InitSlider(); }

if(typeof UpdateCartStatus == 'function') { UpdateCartStatus(); }

$('[data-toggle="tooltip"]').tooltip();
$('[data-toggle="popover"]').popover();

@if (view()->exists( $THEME_INCLUDES.'analytics') )
@include( $THEME_INCLUDES.'analytics')
@endif

});
</script>
<!-- Framework Version: {{ app()::VERSION }} -->
<!-- Theme Name: {{ $THEME_NAME }} -->
<!-- Theme Home: {{ $THEME_HOME }} -->
<!-- Store Code: {{ $store->store_env_code }} -->
<!-- https://larvela.org/ -->
</body>
</html>
