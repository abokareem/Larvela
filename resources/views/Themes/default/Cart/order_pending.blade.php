@extends($THEME_HOME."master-storefront")
<?php
#
# Customer has placed an order but not yet paid!
# 
# Vars Passed In:
# ---------------
# OBJECT $store - The current store
# OBJECT $order - The pending orde rin the system
#
#
#?>
@section('content')
<style>
.media-body { padding:15px; }
table {border-collapse: separate; border-spacing:0; }
td { position: relative; padding: 1px; }
td { right: 10px; }
td:first-child { right: 10px; }
</style>


<div class="container">

	<div class="row">
		<br/>
		<br/>
		<p class='text-center'>
		Thanks for placing an order, you should receive some emails on the progress of your order and when payment is confirmed the order will be fulfilled.<br/>
		<br/>If the order was "Cash on Delivery" then please contact us within 7 days to complete the order else it will be cancelled and the stock returned to the store for sale (this is an automatic process).
		</p>
		<br/>
	</div>

	<div class="row">
		<div class="text-center">
			<a href="/"><button type="button" class="btn btn-default"><span class="fa fa-shopping-cart"></span> Continue Shopping </button></a>&nbsp;&nbsp;
		</div>
	</div>
	<br/>
	<br/>
</div>

@endsection
