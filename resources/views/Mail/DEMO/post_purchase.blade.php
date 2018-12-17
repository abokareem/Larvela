@extends('Mail.RD.template')

@section("content")

<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<p style="text-align:center;font-family: 'Open Sans', sans-serif;font-size:18px;">
<br>
<span style="font-size:18px;">Thank you for purchasing from our store!</span><br/><br/>
<p>We hope you receive your order safely and hope the experience and quality of our products encourages you to purchase more!</p>

You should receive the following items:<br/><br/>
<center>
<table class="table table-stripped">
	<thead>
		<th align="left">SKU</th>
		<th align="left">Description</th>
		<th align="right">Quantity</th>
		<th align="right">Unit Price</th>
	</thead>
	<tbody>
<?php $subtotal=0; ?>
@foreach($order_items as $oi)
	<tr>
		<td align="left">{{ $oi->order_item_sku }}</td>
		<td align="left">{{ $oi->order_item_desc }}</td>
		<td align="right">{{ $oi->order_item_qty_purchased}}</td>
		<td align="right">${{ number_format($oi->order_item_price,2) }}</td>
		<?php $subtotal += $oi->order_item_price * $oi->order_item_qty_purchased; ?>
	</tr>
@endforeach
	<tr> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> <td>&nbsp;</td> </tr>
	<tr> <td>&nbsp;</td> <td>&nbsp;</td> <td align="left"><b>Shipping:<b></td> <td align="right"><b>$<b></td> </tr>
	<tr> <td>&nbsp;</td> <td>&nbsp;</td> <td align="left"><b>Tax:<b></td> <td align="right"><b>$<b></td> </tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align="left"><b>Total<b></td>
		<td align="right"><b>${{ number_format($subtotal,2) }}<b></td>
	</tr>

	</tbody>
</table>
</center>
<br/>
<br/>
<br/>
</p>
@endsection

@section("DB")
MariaDB [rdstore]> desc order_items;
+----------------------------+------------------+------+-----+---------+----------------+
| Field                      | Type             | Null | Key | Default | Extra          |
+----------------------------+------------------+------+-----+---------+----------------+
| id                         | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
| order_item_oid             | int(10) unsigned | NO   | MUL | NULL    |                |
| order_item_sku             | varchar(255)     | NO   | MUL | NULL    |                |
| order_item_desc            | varchar(255)     | NO   |     | NULL    |                |
| order_item_email           | varchar(255)     | NO   | MUL | NULL    |                |
| order_item_qty_purchased   | int(11)          | NO   |     | 0       |                |
| order_item_qty_supplied    | int(11)          | NO   |     | 0       |                |
| order_item_qty_backorder   | int(11)          | NO   |     | 0       |                |
| order_item_dispatch_status | varchar(255)     | NO   |     | W       |                |
| order_item_price           | decimal(13,2)    | YES  |     | NULL    |                |
| order_item_date            | date             | NO   | MUL | NULL    |                |
| order_item_time            | time             | NO   |     | NULL    |                |
| created_at                 | timestamp        | YES  |     | NULL    |                |
| updated_at                 | timestamp        | YES  |     | NULL    |                |
+----------------------------+------------------+------+-----+---------+----------------+
14 rows in set (0.00 sec)
MariaDB [rdstore]> desc orders;
+-----------------------+------------------+------+-----+------------+----------------+
| Field                 | Type             | Null | Key | Default    | Extra          |
+-----------------------+------------------+------+-----+------------+----------------+
| id                    | int(10) unsigned | NO   | PRI | NULL       | auto_increment |
| order_ref             | varchar(128)     | YES  |     | NULL       |                |
| order_src             | varchar(8)       | YES  |     | NULL       |                |
| order_cart_id         | int(10) unsigned | NO   | MUL | NULL       |                |
| order_cid             | int(10) unsigned | NO   | MUL | NULL       |                |
| order_status          | char(2)          | NO   |     | W          |                |
| order_shipping_method | varchar(32)      | YES  |     | NULL       |                |
| order_shipping_value  | decimal(13,2)    | YES  |     | NULL       |                |
| order_value           | decimal(13,2)    | YES  |     | NULL       |                |
| order_payment_status  | char(1)          | NO   |     | W          |                |
| order_dispatch_status | char(1)          | NO   |     | W          |                |
| order_date            | date             | NO   |     | 0000-00-00 |                |
| order_time            | time             | NO   |     | 00:00:00   |                |
| order_dispatch_date   | date             | YES  |     | NULL       |                |
| order_dispatch_time   | time             | YES  |     | NULL       |                |
+-----------------------+------------------+------+-----+------------+----------------+
15 rows in set (0.00 sec)

MariaDB [rdstore]> 

@endsection

