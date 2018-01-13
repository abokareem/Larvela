<?php
#
# CART Include module
# Renders cart display
?>
<!-- START:{{$THEME_INCLUDES}}cart -->
	<div class="row">
		<div class="cart-wrapper">
			<div class="cart pull-right" style="padding-right:75px;padding-bottom:25px;">
				<a href="/cart"><span id='cartitemcount'> $0.00 (0) Items in Cart</span> <i class="fa fa-shopping-cart"></i></a>
			</div>
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
