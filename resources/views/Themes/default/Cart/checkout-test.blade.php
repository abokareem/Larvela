@extends($THEME_HOME."master-storefront")
@section('content')
<script src="//www.paypalobjects.com/api/checkout.js"></script>
<style>
.media-body { padding:15px; }
</style>
	<div class="row"  style='padding:20px;'>
		<div id='paypal-button'></div>
	</div>
	<div class="row" style='padding:20px;'>
		Status: <span id='paypal-button-container'> - </span>
	</div>

<!-- sandbox: 'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R', -->

<?php
$token = csrf_token();
?>

<script>

paypal.Button.render({
	env:'sandbox',
	commit:true, 
	client: {
		sandbox: 'AUirUBG6ImLCuDCocB3FXbU9ufAsW3xSQlCxgx0T-fthAPl4_o8t4CVqas5iP-5DuX3Vxbt3V88FiXCi',
		production: 'AbmINr3QL340DWkPf6WjKJaEKKbOKiTCd3roAcR4u0sqTs2q6fcFGqw7nE4J5t-61DTUXBd3bCZ4d4Jr'
	},
	payment:function(data, actions)
	{
		console.log("payment()");
		return actions.payment.create(
		{
			payment:
			{
				transactions:[{
					amount: {
						total:'25.00',
						currency:'AUD',
						details:{ 'subtotal':'20.00', 'tax':'0', 'shipping':'5.00', }
						},
					item_list: {
						items: [{
								name: 'N24-BLACK-SMALL',
								description: 'Black Small Long Leg',
								quantity: '1',
								price: '10',
								currency: 'AUD'
								},
								{
								name: 'N20-PINK-SMALL',
								description: 'Pink Small Short Leg',
								quantity: '1',
								price: '10.00',
								currency: 'AUD'
								}]
							}
					}]
			}
		});
	},
	onAuthorize:function(data, actions)
	{
		console.log("onAuthorize()");
		console.log("Dumping DATA");
		console.log(data);
		console.log("Dumping ACTIONS");
		console.log(actions);
		return actions.payment.execute().then(function(payment)
		{
			console.log("payment.execute called");
			document.querySelector('#paypal-button-container').innerText = 'Payment Complete!';
			console.log("Dumping payment:");
			console.log("CART: "+payment['cart']);
			console.log(payment);

			var values = encodeURIComponent(JSON.stringify(payment));
			$.ajaxSetup({headers:{'X-CSRF-TOKEN':'{{ $token }}' } } );
			ajaxRequest= $.ajax({ url: "/ajax/payment", type: "post",data:values });
			ajaxRequest.done(function(response, textStatus, jqXHR)
			{
				console.log("PRE DUMP = "+response);
				var result = $.parseJSON(response);
				console.log("POST DUMP = "+result);
			});

		});
	},
	onCancel: function(data,actions)
	{
		console.log("Cancel called");
		console.log("Dumping DATA");
		console.log(data);
		console.log("Dumping ACTIONS");
		console.log(actions);
	}
}, '#paypal-button');
</script>

@endsection
