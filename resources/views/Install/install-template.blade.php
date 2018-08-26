<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Larvela Installer</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
<!-- Inject Navigation Content -->
@if( view()->exists('install-header') )
    @include('install-header')
@endif
<div class="container-fluid">
<h2>Laravel Installer</h2>
<br/>
<br/>
</div>
@yield('content')
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

@if( view()->exists('install-footer') )
@include('install-footer')
@endif
</body>
</html>
