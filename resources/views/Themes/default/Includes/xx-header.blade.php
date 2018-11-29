<!-- START:Header BLOCK -->
<div class="store-header">
@if(view()->exists($THEME_INCLUDES.'topbar') )
@include($THEME_INCLUDES.'topbar')
@endif
	<div class="container">
	@if(view()->exists($THEME_INCLUDES.'cart') )
	@include($THEME_INCLUDES.'cart')
	@endif
<!-- START:StoreName -->
		<div class="row">
			<div class="store-name text-center"><h2 id='store-logo-home'>{{ $store->store_name }}</h2></div>
		</div>
	</div>
<!-- END:StoreName -->
	@if(view()->exists($THEME_INCLUDES.'categorymenu'))
	@include($THEME_INCLUDES.'categorymenu')
	@endif

@if(view()->exists($THEME_INCLUDES.'breadcrumbs') )
@include($THEME_INCLUDES.'breadcrumbs')
@endif

</div>
<script>
function InitHeader()
{
	console.log('InitHeader()');
	$('#store-logo-home').hover(function() { $(this).css('cursor','pointer'); });
	$("#store-logo-home").click(function()
	{
		var url = '/';
		window.location.href = url;
	});
}
</script>
<!-- END:header BLOCK-->
