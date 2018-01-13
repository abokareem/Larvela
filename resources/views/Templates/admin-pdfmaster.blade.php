<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>@yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="/css/drs.css">
<style>
@page { size: 21cm 29.7cm; margin: 30mm 45mm 30mm 45mm; }
@media print { body, page[size="A4"] { margin: 0; box-shadow: 0; } }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js"></script>

@yield('header')
</head>
<body>
<div class="container-fluid">
<!-- Inject Content -->
@yield('content')
</div>

<!-- Footer Logic from master layout -->
<div id="footer">
	<div class="container">
		<p class="muted credit">@yield('footer')</p>
	</div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.js"></script>
</body>
</html>
