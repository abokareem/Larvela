@extends($THEME_HOME.'master-storefront')
@section('content')

<div class="container">
	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("about_header") !!}
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("about_body") !!}
		</div>
	</div>
</div>
<!-- END:about-->
@stop
