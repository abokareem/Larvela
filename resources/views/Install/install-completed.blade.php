@extends('Install.install-template')
@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="text-center" style="padding-to:50px;padding-bottom:50px;font-size:48px;color:green;">Installation Complete!</div>
		<div class="text-center" style="padding-to:50px;padding-bottom:50px;">Welcome to your Larvela eCommerce Store.</div>
	</div>

	<div class="row">
		<div class="form-group">
			<div class="col-xs-12 text-center">
				<button id='btnnext'  type="button" class="btn btn-success">Start My Store</button>
			</div>
		</div>
	</div>
</div>
<script>$('#btnnext').click( function() { window.location.href = "/";});</script>
@endsection
