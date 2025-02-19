@extends('_layouts.default')

@section('content')
	<!--Receipt-->

<?
$created = substr(Session::get('receipt_info.created_at'),0,10);
?>
	<div class="row">
		<div class="span12">
			<article class="head-receipt">
				<ul style="list-style-type:none; margin: 0;">
             @if (Session::has('company_info'))
				  <li><strong class="company-name">{{ Session::get('company_info')->company_name }}</strong></li>
				  <li>{{ Session::get('company_info')->address }}</li>
				  <li>{{ Session::get('company_info')->mobile }}</li>
			 @endif
				</ul>
			 @if (Session::has('receipt_info'))
			 <center>
			 <table>
				<tbody>
					<tr style="border-bottom: 1px solid #ccc; width:50%; margin: 3px auto;">
						<td colspan="3" style="text-align:center;"><b>Purchase Receipt</b></td>
					</tr>
					@if(!empty(Session::get('receipt_info.supplier_name')))
					<tr>
						<td align="right">Supplier Name</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ Session::get('receipt_info.supplier_name') }}</td>
					</tr>
					@endif
					<tr>
						<td align="right">Transaction Date</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">
							<?php 
								if($created==Session::get('receipt_info.date')){

									$transDateArr =explode(' ', Helper::dateFormat(Session::get('receipt_info.created_at')));
									echo $transDateArr[0];
								}else{
									 echo Helper::dateFormat(Session::get('receipt_info.date'));
								}
							?>
						</td>
					</tr>
					<tr>
						<td align="right">Purchase ID</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ Session::get('receipt_info.invoice_id') }}</td>
					</tr>
					@if(!empty(Session::get('receipt_info.supplier_memo_no')))
						<tr bgcolor="red">
							<td align="right">Supplier Memo No</td>
							<td>&nbsp;&nbsp; : &nbsp;</td>
							<td align="left">{{ Session::get('receipt_info.supplier_memo_no') }}</td>
						</tr>
					@endif
					<tr>
						<td align="right">Employee</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ Session::get('receipt_info.emp_name') }}</td>
					</tr>
				</tbody>
			 @endif
			 </table>
			 </center>
			</article>
			@if(Session::has('receipt_item_infos'))
			<table class="table-sales-receipt">
				<thead>
					<tr>
						<th class="span1">#SL No</th>
						<th>Item</th>
						<th>Purchase Price</th>
                        <th>Sale Price</th>
						<th>Qty.</th>
						<th>Disc (Tk)</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody><? $i=0; $total_quantity=0;?>
					@foreach(Session::get('receipt_item_infos') as $receipt_item_info)
					<tr>
						<td>{{++$i}}</td>
						<td>{{ $receipt_item_info['item_name'] }}</td>
						<td>{{ $receipt_item_info['purchase_price'] }}</td>
                        <td>{{ $receipt_item_info['sale_price'] }}</td>
						<td>{{ $receipt_item_info['quantity'] }}</td>
						<td>{{ $receipt_item_info['discount'] }}</td>
						<td>{{ $receipt_item_info['total'] }}</td>
					</tr>
					<? $total_quantity+=$receipt_item_info['quantity']; ?>
					@endforeach
				</tbody>
			</table>
			@endif
			@if (Session::has('receipt_info'))
			<article class="btn-sale-receipt">
				<table>
					<tbody>
						<tr style="font-weight: bolder;">
							<td>Sub Total</td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ (Session::get('receipt_info.total_amount')+Session::get('receipt_info.invoice_discount')) }}</td>
						</tr>
						<tr>
							<td>Payment Type</td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ Session::get('receipt_info.payment_type_name') }}</td>
						</tr>
						<tr>
							<td>Discount</td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ Session::get('receipt_info.invoice_discount') }}</td>
						</tr>
                                                <tr>
							<td>Total</td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ Session::get('receipt_info.total_amount')}}</td>
						</tr>

						<tr>
							<td>Pay</td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ Session::get('receipt_info.pay') }}</td>
						</tr>
						<tr>
							<td>Due</td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ Session::get('receipt_info.due') }}</td>
						</tr>
						<tr style='font-weight:bolder;'>
							<td>Total Qty.</td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ $total_quantity }}</td>
						</tr>

					</tbody>
				</table>
			</article>
			<article style="clear:both; text-align: center;">
				<div align="center">
					{{ DNS1D::getBarcodeHTML(Session::get('receipt_info.invoice_id'), "C128", 1, 25) }}
					<strong>{{ Session::get('receipt_info.invoice_id') }}</strong>
				</div>
				<p style="float:right;">Developed By : <strong>Unitech IT</strong></p>
			</article>
			@endif
		</div>
	</div>
@stop

