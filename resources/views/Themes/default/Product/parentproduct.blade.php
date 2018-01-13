@extends($THEME_HOME."master-storefront")
@section("content")
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.6.7/jquery.lazy.min.js"></script>
<?php
$base500gbag = 800;
$regprice = ($product->prod_retail_cost+ $base500gbag);
$expressprice = $regprice+4;

$payments_enable = true;
foreach($settings as $s)
{
	if($s->setting_name == 'PAYMENTS_ENABLED')
	{
		if($s->setting_value == '0')
		{
			$payments_enable = false;
		}
	}
}
?>
<style>
.zoom { display:inline-block; position: relative; }
.zoom img { display: block; }
.zoom:after { content:''; display:block; width:33px; height:33px; position:absolute; top:0; right:0; background:url(icon.png);
.zoom img::selection { background-color: transparent; }

.prodpage-add-to-cart-btn { align-content: center; align-items:center}
}
</style>																										 

<!-- START:productpage mobile-first-->
<div class="container prodpage-block">

	<div class="row">
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
			<div class="prodpage-mainimage">
				<span class="zoom" id="main_image">
					<img id="zoom_image" src="{{ $main_image_folder_name }}/{{ $main_image_file_name }}" width="400" height="300"">
				</span>
			</div>
			<div class="prodpage-images">
				<div class="prodpage-image-gallery">
					@foreach($thumbnails as $thumb)
					<span class="prodpage-thumb">
						<img src="/{{ $thumb->image_folder_name }}/{{ $thumb->image_file_name }}" width="68" height="68" id="thumb{{ $thumb->id }}">
					</span>
					@endforeach
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-sm-3 col-md-4 col-lg-5">
			<div class="prodpage-title-block">
				<span class="prodpage-title">Product Name:</span>&nbsp;
				<span class="prodpage-title"><p>{{ $product->prod_title }}</p></span>
				<span class="prodpage-title">Price:</span>&nbsp;
				<span class="prod-matrix-price">${{ number_format($product->prod_retail_cost,2) }}</p></span>
				<div class="prodpage-desc-short">Description:</div>
				<div class="prodpage-desc-short"><p>{!! $product->prod_long_desc !!}</p></div>
			</div>
		</div>

		<div class="col-xs-12 col-sm-3 col-md-4 col-lg-3">
		<?php 
		#		Panel here<br>
		#	foreach($attribute_values as $av)
		#		{!! $attributes[$av->$attribute_id] !!}<br>
		#
		#	endforeach
		?>
		</div>
	</div>

	<div class="row">
		@if(sizeof($related)>0)
		<div class="col-xs-12">
			<div class="prodpage-related">Related Products</div>
			<div class="clearfix hidden-sm-up"></div>
		</div>
		@endif
	</div>
</div>
<script>

$('#btnnotify').click(function()
{
	console.log("Notify pressed");
	var form = $('#cf');
    $.ajax({type:"POST",url:"/notify/outofstock",data:form.serialize(),success:function(data)
		{
			var response = jQuery.parseJSON(data);
			if(response.status=="OK")
			{
				$('#nfbody').replaceWith("<h3>Notification will be sent when stock arrives</h3>");
			}
		}
	});
});



var qty=1;

$('#incqty').click(function() { console.log("INC Qty pressed"); }); 
$('#decqty').click(function() { console.log("DEC qty pressed"); });

$(function()
{
$('#main_image').zoom();

@foreach($thumbnails as $thumb)

$('#thumb{{ $thumb->id }}').click(function()
{
	$('#main_image').trigger('zoom.destroy');
	$('#zoom_image').attr('src',"/{{ $thumb->image_folder_name }}/{{ $thumb->image_file_name }}");
	$('#main_image').zoom();
	return false;
});

@endforeach



});
</script>
<script src='/zoom/jquery.zoom.js'></script>
<!-- END:productpage -->
@stop
