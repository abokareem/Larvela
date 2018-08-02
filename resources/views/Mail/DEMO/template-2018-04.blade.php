<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body bgcolor='white'>
<div align="center">
	@yield('pre-header')
	@include('Mail.RD.header')
	@yield('content')
	@include('Mail.RD.footer')
</div>
</body>
</html>
