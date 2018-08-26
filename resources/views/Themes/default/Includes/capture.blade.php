<!-- START:Capture Logic -->
<?php
#
# This is a basic capture form which pops up after 45 seconds.
# It can be stopped with the session cookie being set to 'done'
# CID is customer DI fom the "customers" table.
#

$capture = true;

$s = Session::get('capture', 'capture');
$c = \Cookie::get('capture', 'capture');
$cid = \Cookie::get('cid','0');

if( $s != "capture" ) $capture = false;
if( $c != "capture" ) $capture = false;
if( $cid > 0 ) $capture = false;

echo "<!-- S=[".$s."] C=[".$c."] CID=[".$cid."] -->";
?>

@if($capture == true)
<form  id='cf' name='cf' action='/capture'  method='post'>
<div id="ec" class="modal fade">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title popup-title">Get 10% of your first order!</h4>
			</div>
			<div class="modal-body">
				<p class="popup-name text-center">For your first ever order, get 10% off by subscribing...</p>
				<div class="row">
					<div class="form-group">
						<label class="form-label col-xs-4 text-right">Your Name:</label>
						<div class='col-xs-6'>
							<input type="text" id="na" name="na" class="form-control" placeholder="Enter your name here"></br>
						</div>
					</div>
				</div>	
				<div class="row">
					<div class="form-group">
						<label class="form-label col-xs-4 text-right">Your eMail:</label>
						<div class='col-xs-8'>
						<input type="text" id="ea" name="ea" class="form-control" placeholder="Your email address here....">
						</div>
					</div>
				</div>	
			</div>
			<div class="modal-footer">
				<button type="button" id='btnsubscribe' class="btn btn-success" data-dismiss="modal">Subscribe</button>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
</form>
<script>
setTimeout(sd, 5000);

function sd()
{
	listCookies();
	var captured = getCookie("capture");
	if(captured!="")
	{
		console.log('Checked for capture - not required');
	}
	else
	{
		$('#ec').modal('show');
		$('#na').focus();
	}
}


$('#btnsubscribe').click(function()
{
	var form = $('#cf');
	$.ajax({type:"POST",url:form.attr('action'),data:form.serialize(),success:function(response)
	{
		console.log( response );
	}
	});
	setCookie("capture","yes",1);
});


function getCookie(cname)
{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i = 0; i < ca.length; i++)
	{
		var c = ca[i];
		while(c.charAt(0) == ' ')
		{
			c = c.substring(1);
		}
		if(c.indexOf(name) == 0)
		{
			return c.substring(name.length, c.length);
		}
	}
	return "";
}


function listCookies()
{
	var theCookies = document.cookie.split(';');
	var aString = '';
	for (var i = 1 ; i <= theCookies.length; i++)
	{
		aString += i + ' ' + theCookies[i-1] + "\n";
		console.log("I="+i+"Cookie="+theCookies[i-1]);
	}
	return aString;
}

function setCookie(cn,cv,days)
{
	var d = new Date();
	d.setTime(d.getTime()+(days*24*60*60*1000));
	var ex="expires="+d.toUTCString();
	document.cookie = cn+"="+cv+"; "+ex;
	console.log("Expires on: "+ex);
}
</script>
@endif
<!-- END:Capture -->

