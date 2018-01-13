@extends($THEME_HOME.'master-storefront')
@section('content')

<div class="container">
	<div class="row">
		<div class="col-lg-12">
			{!! App\Helpers\SEOHelper::getText("support_header") !!}
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			{!! App\Helpers\SEOHelper::getText("support_body") !!}
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			{!! App\Helpers\SEOHelper::getText("support_footer") !!}
		</div>
	</div>
</div>
<!-- END:about-->
@stop
