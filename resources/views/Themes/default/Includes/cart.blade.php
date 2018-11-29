<?php
#
# CART Include module
# Renders cart display
?>
<!-- START:{{$THEME_INCLUDES}}cart -->
<?php $min_spend = 0; ?>
@foreach($settings as $s)
	@if($s->setting_name == "MIN_SPEND_VALUE")
	<?php $min_spend =  $s->setting_value; ?>
@endif
@endforeach
	<div>
		<div class="relative text-2xl text-blue font-medium hover:text-red pull-right pr-8 pb-8">
			@if($min_spend > 0)
			<script>var min_spend = {{ number_format($min_spend,2) }}; </script>
			Minimum Spend is <b style="color:green;">${{ number_format($min_spend,2) }}</b><br>
			@endif
			<a href="/cart" text-2xl class="hover:text-pink hover:no-underline"><span id='cartitemcount'> $0.00 (0) Items in Cart</span> <i class="fa fa-shopping-cart"></i></a>
		</div>
	</div>
	<form name='cartstatus' method='post' id='cartstatus'>
	<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
	</form>

<script>
function UpdateCartStatus()
{
	console.log("Update Cart Status");
	var form = $('#cartstatus');
	$.ajax({type:"POST",url:"/cart/getcartdata",data:form.serialize(),success:function(data)
		{
			var response = jQuery.parseJSON(data);
			if(response.c !="0")
			{
				text = "$"+response.v+" ("+response.c+") Items in Cart";
				$('#cartitemcount').replaceWith(text);
			}
		}
	});
}
</script>
<!-- END:{{$THEME_INCLUDES}}cart -->
