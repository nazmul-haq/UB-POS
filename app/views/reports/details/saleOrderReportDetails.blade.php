<?	$created = substr($receipt_info->invoice_time , 0, 10); ?>
	<!--Receipt-->	  	
	<div class="modal-footer print-btn" style="clear:both;">
		<a class="btn btn-info" href='{{ URL::to("admin/sale/saleOrderReportReceipt/$receipt_info->sale_order_invoice_id") }}'>Print</a>
	<a href="{{URL::to('sale/sendOrderToSale')}}/{{ $receipt_info->sale_order_invoice_id }}" class="btn btn-success"> Send To Sale Form </a>
	
	<a href="{{URL::to('sale/editSaleOrder')}}/{{ $receipt_info->sale_order_invoice_id }}" class="btn btn-warning"> Edit Sale Order </a>
	</div>
		<article class="head-receipt">
			<ul style="list-style-type:none; margin: 0;">
		 @if ($company_info)
			  <li><strong class="company-name">{{ $company_info->company_name }}</strong></li>
			  <li>{{ $company_info->address }}</li>
			  <li>01797185240</li>
		 @endif
		 	</ul>
		 @if ($receipt_info)
			<center>
			<table>
				<tbody>
					<tr style="border-bottom: 1px solid #ccc; width:50%; margin: 3px auto;">
						<td colspan="3" style="text-align:center;"><b>Sale Order Receipt</b></td>
					</tr>
					<tr>
						<td align="right">Customer Name</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ $receipt_info->customer_name }}</td>
					</tr>
					<tr>
						<td align="right">Transaction Date</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">
							
							<?php 
								if($created == $receipt_info->date){
									$transDateArr =explode(' ', Helper::dateFormat($receipt_info->invoice_time));
									echo $transDateArr[0];
								}else{
									 echo Helper::dateFormat($receipt_info->date);
								}
							?>
						</td>
					</tr>
					<tr>
						<td align="right">Order Invoice ID</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ $receipt_info->sale_order_invoice_id }}</td>
					</tr>
					<tr>
						<td align="right">Sold By</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ $receipt_info->invoice_by }}</td>
					</tr>
					<tr>
						<td align="right">Sold At</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ date('d F, Y h:i a',strtotime($receipt_info->invoice_time)) }}</td>
					</tr>
				</tbody>
		 @endif
			</table> 
			</center>
		</article>	
		@if($receipt_item_infos)
		<table class="table-sales-receipt">
			<thead>
				<tr>
					<th></th>
					<th>Item</th>
					<th>Qty.</th>
					<th>Sale Price</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				@foreach($receipt_item_infos as $key => $receipt_item_info)
				<tr>
					<td>{{ ++$key }}</td>
					<td>{{ $receipt_item_info->item_name }}</td>
					<td>{{ $receipt_item_info->quantity }}</td>
					<td>{{ ($receipt_item_info->amount/$receipt_item_info->quantity) }}</td>
					<td>{{ $receipt_item_info->amount }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		@endif
		@if ($receipt_info)
		<article class="btn-sale-receipt-model">
			<table>
				<tbody>
					<tr style="font-weight: bolder;">
						<td>Sub Total</td>
						<td>&nbsp;:&nbsp;</td>
						<td align="right">{{ $receipt_info->amount}}</td>
					</tr>
					
					<tr>
						<td>Total</td>
						<td>&nbsp;:&nbsp;</td>
						<td align="right"><strong>{{ $receipt_info->amount }}<strong></td>
					</tr>
				</tbody>
			</table>
		</article>
		<article style="clear:both; text-align: center;">
			<h6>Thanks for being with us</h6>
		
			<p style="float:right; display: none;"><strong>Developed By : Unitech IT</strong></p>
			
		</article>	
		@endif	
		
		<script>
			$(function(){
				$('#print').on('click', function(){
					$("body").css('visibility','hidden');
					$('#printable').addClass('span12');
					printElement(document.getElementById("printable"));					
					window.print();
					$("body").css('visibility','visible');
					$('#printable').removeClass('span12');
				});
			});
		</script>
	

