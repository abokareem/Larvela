<table align="center" border="0" cellpadding="0" cellspacing="0" style="background-color:transparent; border-color:transparent" width="100%">
	<tbody>
	<tr>
		<td align="center" colspan="5">
			<font style="font-size:11px">
				<font face="Helvetica, Arial, sans-serif">Don't miss our emails! Add&nbsp;</font>
			</font>
			<font>
				<font face="Trebuchet MS, Helvetica, sans-serif">
					<a href="mailto:{{$store->store_sales_email}}" title="{{$store->store_sales_email}}">
						<font style="font-size:11px">
							<font face="Helvetica, Arial, sans-serif">{{$store->store_sales_email}}</font>
						</font>
					</a>
				</font>
			</font>
			<font style="font-size:11px">
				<font face="Helvetica, Arial, sans-serif">&nbsp;to your contact list in your email.&nbsp;<br>This is a 'Product News' email. To change your email preferences and ensure all<br>our emails are&nbsp;relevant to you please manage your subscriptions below.
				</font>
			</font>
		</td>
	</tr>
	</tbody>
</table>

<div align="center">
	<table align="center" border="0" width="550">
		<tbody>
			<tr>
				<td align="center">
					<font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" style="font-size:10px">
						<a href="{{ $store->store_url }}/subscription/manage"  target="_blank">
							<font color="#000000" face="Arial, Helvetica, sans-serif" style="font-size:10px">Manage your Subscriptions</font>
						</a>
					</font> | <a href="{{ $store->store_url }}/unsubscribe?{{ $hash }}"  target="_blank">
						<font color="#000000" face="Arial, Helvetica, sans-serif" style="font-size:10px">Unsubscribe</font>
						</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<br clear="all" />

<div class="email_footer">
<table width="550" border="0" align="center">
	<tr>
		<td>
			<p align="center">
				<font face="Verdana, Arial, Helvetica, sans-serif" color="#555555" size="1">This email was sent by {{$store->store_name }}, {{ $store->store_address }}, {{$store->store_address2}}, {{$store->store_country}}<br>to: {{ $email }}
				</font>
			</p>
			<p align="center">
				<br/><br/><br/><br/>
			</p>
			<p align="center">
				<font face="Verdana, Arial, Helvetica, sans-serif" color="#555555" size="1">Powered by:</font></br>
				<a href="https://larvela.org/" target="_blank"><img src="https://larvela.org/larvela-cart-284-184.jpg" border="0"></a>
			</p>
		</td>
	</tr>
</table>
</div>
