@extends('Mail.DEMO.template')

@section("content")

<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<p style="text-align:center;font-family: 'Open Sans', sans-serif;font-size:18px;">
<br>
<span style="font-size:18px;">Thanks you for purchasing from my store!</span><br/><br/>
<p>Your order has been placed and is awaiting picking and dispatch.</p>

The order has the following items:<br/><br/>
<center>
<p>Order Item Detail</p>
<table class="table table-stripped">
@foreach($order_items as $oi)
	<tr>
		<td>{{ $oi->order_item_sku }}</td>
		<td>{{ $oi->order_item_desc }}</td>
		<td>{{ $oi->order_item_qty_purchased}}</td>
		<td>{{ $oi->order_item_price}}</td>
	</tr>
@endforeach
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

@endsection

