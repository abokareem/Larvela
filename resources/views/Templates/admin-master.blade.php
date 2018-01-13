<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>@yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
<link href="/css/store.css" rel="stylesheet">

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="//npmcdn.com/isotope-layout@3.0/dist/isotope.pkgd.min.js"></script>
<script src="/js/bootstrap-notify.min.js"></script>
</head>
<body>
<!-- Inject Navigation Content -->
@if( view()->exists('nav-admin') )
    @include('nav-admin')
@endif
<div class="container-fluid">
	<h1>Administration Console</h1>
	@yield('content')
</div>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

@if( view()->exists('Includes.analytics') )
@include('Includes.analytics')
@endif
</body>
</html>
