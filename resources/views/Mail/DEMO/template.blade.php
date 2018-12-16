<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body bgcolor='white'>
<div align="center">
	@yield('pre-header')
	@include('Mail.DEMO.header')
	@yield('content')
	@include('Mail.DEMO.footer')
</div>
</body>
</html>
