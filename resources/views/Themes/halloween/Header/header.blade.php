<!-- START:{{$THEME_HEADER}}Header BLOCK -->
<div class="store-header">
	@if(view()->exists($THEME_INCLUDES.'topbar') )
		@include($THEME_INCLUDES.'topbar')
	@endif

<!-- START:StoreName -->
	<div class="row" style="padding-top:70px;" align="center">
		<img src="/halloween-flying-witch-1.jpg" style="padding:5px;height:100px; width:100px;">
		<span class="store-name text-center" id='store-logo-home' style="font-size:48px;">{{ StoreHelper::StoreData()->store_name }}</span>
		<img src="/halloween-flying-witch-1.jpg" style="padding:5px;height:100px; width:100px;">
	</div>
	<div class="row" align="center">
		<span class="store-name" style="font-size:24px;font-style:italic;">Have a Spooktacular Halloween!</span>
	</div>
<!-- END:StoreName -->

	@if(view()->exists($THEME_INCLUDES.'cart') )
		<!-- START:cart include -->
		@include($THEME_INCLUDES.'cart')
		<!-- END:cart include -->
	@endif


	@if(view()->exists($THEME_INCLUDES.'categorymenu'))
		@include($THEME_INCLUDES.'categorymenu')
	@endif

	@if(view()->exists($THEME_INCLUDES.'breadcrumbs') )
		@include($THEME_INCLUDES.'breadcrumbs')
	@endif
	</div>
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
<!-- END:{{$THEME_HEADER}}header BLOCK-->
