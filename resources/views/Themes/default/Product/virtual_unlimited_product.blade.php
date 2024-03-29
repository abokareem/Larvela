@extends($THEME_HOME."master-storefront")
<?php
#
# This page renders a single product. It is passed the following variables:
#
# Object   $product - product object
# Array    $images - Array of image objects
# Array    $thumbnails - An Array of image objects, these are thumbnails of the main images where image_parent_id > 0
# Array    $categories - The Current Store Categories
# Array    $settings - An array of store setting objects, settings for the current store.
# Array    $related - An array of related products as product objects.
# Object   $attributes - Not defined in code.
# Object   $store - The Current store object.
# 
# Variable $main_image_file_name
# Variable $main_image_folder_name
# 
# Debugging: use dd($product); to dump the products
?>
@section("content")
<script src="//www.paypalobjects.com/api/checkout.js"></script>
<xxxscript src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.6.7/jquery.lazy.min.js"></xxxscript>

<?php

# sandbox: 'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R'
$token = csrf_token();
$shipping = "0.00";
?>

<style>
.media-body { padding:15px; }
.zoom { display:inline-block; position: relative; }
.zoom img { display: block; }
.zoom:after { content:''; display:block; width:33px; height:33px; position:absolute; top:0; right:0; background:url(icon.png);
.zoom img::selection { background-color: transparent; }
.prodpage-add-to-cart-btn { align-content: center; align-items:center}
}
</style>																										 

<!-- START:productpage mobile-first-->
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="col-xs-12">
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
		</div>
	
		<div class="col-xs-12 col-sm-6">
			<form id='purchase' name='purchase' method='post'>
			<div class="prodpage-title-block">
				<span class="prodpage-title">Product Name:</span>&nbsp;
				<span class="prodpage-title"><p>{{ $product->prod_title }}</p></span>
				<span class="prodpage-title">Price:</span>&nbsp;
				@if($product->prod_retail_cost == 0)
					<span class="prod-matrix-price">Free!</p></span>
				@else
					<span class="prod-matrix-price">${{ number_format($product->prod_retail_cost,2) }}</p></span>
				@endif
				@if($product->prod_has_free_shipping > 0)
					<span style="color:green;font-size:18px;font_weight:bold;">*** Free Shipping ***</span><br/>
					<?php $shipping_cost = 0; ?>
				@endif
				<div class="prodpage-desc-short">{!! $product->prod_short_desc !!}</div>

				<div class='prodpage-add-to-cart-btn text-center'>
					<span style="text-align:center;" id="paypal-button"></span>
					</br>To Pay via Credit Card select your card type above </br><b>or</b>...</br>
					<span style="text-align:center;" align="center">
						<button style="text-align:center;" id="addtocart" class="btn btn-danger">Add to Cart</button>
					</span>
				</div>

				<div class="prodpage-desc-short">Full Description:</div>
				<div class="prodpage-desc-short">{!! $product->prod_long_desc !!}</div>
			</div>
			<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
			</form>
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
var maxqty = {{$product->prod_qty}};

$('#incqty').click(function()
{
	console.log("INC Qty pressed");
	if(qty < maxqty)
	{
		qty++;
		$('#qty').val(qty);
		$('#qtydisp').text(qty.toString());
	}
}); 

$('#decqty').click(function()
{
	console.log("DEC qty pressed");
	if(qty>1)
	{
		qty--;
		$('#qty').val(qty);
		$('#qtydisp').text(qty.toString());
	}
});

$("#purchase").submit(function(e){ e.preventDefault(); });

$('#addtocart').click(function()
{
var url="/addtocart/{{$product->id}}?"+qty;
window.location.href = url;
return false;
});

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


paypal.Button.render({
	env:'sandbox', commit:true,
	client: {
			sandbox: 'AUirUBG6ImLCuDCocB3FXbU9ufAsW3xSQlCxgx0T-fthAPl4_o8t4CVqas5iP-5DuX3Vxbt3V88FiXCi',
			production: 'AbmINr3QL340DWkPf6WjKJaEKKbOKiTCd3roAcR4u0sqTs2q6fcFGqw7nE4J5t-61DTUXBd3bCZ4d4Jr'
			},
	style: {
			label: 'buynow',
			fundingicons: true, // optional
			branding: true, // optional
			size:  'small', // small | medium | large | responsive
			shape: 'rect',   // pill | rect
			color: 'gold'   // gold | blue | silve | black
			},

	payment:function(data, actions)
	{
		console.log("payment()");
		return actions.payment.create(
		{
		payment:
		{
			transactions:[{ 
				amount: { total:'{{ number_format($product->prod_retail_cost,2)+$shipping }}', currency:'AUD',
					details:{
						'subtotal':'{{ number_format($product->prod_retail_cost,2)}}',
						'tax':'0.00',
						'shipping':'{{ $shipping }}', }
						},
				item_list:
				{
					items:[
						{name:'{{ $product->prod_sku }}',
						description:'{{ $product->prod_short_desc}}',
						quantity:'1',
						price:'{{ number_format($product->prod_retail_cost,2) }}', currency: 'AUD'}
						]
				}
			}]
		}});
	},
	onAuthorize:function(data, actions)
	{
		return actions.payment.execute().then(function(payment)
		{
			console.log("payment.execute called");
			document.querySelector('#paypal-button').innerText = 'Payment Complete!';
			console.log(payment);
			var values = encodeURIComponent(JSON.stringify(payment));
			$.ajaxSetup({headers:{'X-CSRF-TOKEN':'{{ $token }}' } } );
			ajaxRequest= $.ajax({ url: "/ajax/payment", type: "post",data:values });
			ajaxRequest.done(function(response, textStatus, jqXHR)
			{
				console.log("Text = "+textStatus);
				console.log("Response Dump:");
				console.log(response);
				var result = $.parseJSON(response);
				console.log("Result Dump:");
				console.log(result);

				ordRequest= $.ajax({ url: "/instant/order/{{$product->id}}", type: "post",data:values});
				ordRequest.done(function(response, textStatus, jqXHR)
				{
					var result = $.parseJSON(response);
					console.log("Show Order Save Result:");
					console.log(result);
				});
			});
		});
	},
	onCancel: function(data,actions)
	{
		console.log(data);
		console.log(actions);
	}
}, '#paypal-button');


</script>
<script src='/zoom/jquery.zoom.js'></script>
<!-- END:productpage - unlimited-vitual-product -->
@stop
