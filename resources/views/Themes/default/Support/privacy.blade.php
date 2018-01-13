@extends($THEME_HOME.'master-storefront')
@section('content')

<div class="container">
	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("privacy_header") !!}
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("privacy_body") !!}
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			{!! App\Helpers\SEOHelper::getText("privacy_footer") !!}
		</div>
	</div>
</div>
<!-- END:about-->
@stop
