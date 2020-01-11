<?	$created = substr($receipt_info->created_at , 0, 10); ?>
	<!--Receipt-->	  	
		<article class="print_top">
			<center>
		<a href='{{ URL::to("admin/sale/saleReportReceipt/$receipt_info->sale_invoice_id") }}' class="btn btn-info"  target = "_blank">Print</a>
		<hr/>
			<ul style="list-style-type:none; margin: 0;">
			 @if ($company_info)	
				  <li><strong class="company-name">{{ $company_info->company_name }}</strong></li>
				  <li>{{ $company_info->address }}</li>
				  <li>{{ $company_info->mobile }}</li>
			 @endif
		 	</ul> 
			</center>
		 @if ($receipt_info)
			<center>
			<table>
				<tbody>
					<tr style="border-bottom: 1px solid #ccc; width:50%; margin: 3px auto;">
						<td colspan="3" style="text-align:center;"><b>Sales Receipt</b></td>
					</tr>
					@if(!empty($receipt_info->customer_name))
					<tr>
						<td align="right">Customer Name</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ $receipt_info->customer_name }}</td>
					</tr>
					@endif
					<tr>
						<td align="right">Transaction Date</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">
							<!-- @if($created == $receipt_info->date)
								{{ Helper::dateFormat($receipt_info->created_at) }}
							@else
								{{ Helper::dateFormat($receipt_info->date) }}
							@endif -->
							<?php 
								if($created == $receipt_info->date){
									$transDateArr =explode(' ', Helper::dateFormat($receipt_info->created_at));
									echo $transDateArr[0];
								}else{
									 echo Helper::dateFormat($receipt_info->date);
								}
							?>
						</td>
					</tr>
					<tr>
						<td align="right">Invoice No</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ $receipt_info->sale_invoice_id }}</td>
					</tr>
					<tr>
						<td align="right">Sold By</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ $receipt_info->invoiced_employee }}</td>
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
					<th>Price</th>
					<th>Qty.</th>
					<th>Disc (tk)</th>
					<th>Tax (tk)</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<? $i = 0; ?>
				@foreach($receipt_item_infos as $receipt_item_info)
				<? $i++; ?>
				<tr @if($receipt_item_info->supplier_id == $supplierId) style="background-color:greenyellow;" @endif>
					<td>{{ $i }}</td>
					<td>{{ $receipt_item_info->item_name }}</td>
					<td>{{ $receipt_item_info->sale_price }}</td>
					<td>{{ $receipt_item_info->quantity }}</td>
					<td>{{ $receipt_item_info->discount }}</td>
					<td>{{ $receipt_item_info->tax }}</td>
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
						<td align="right">{{ $receipt_info->amount + $receipt_info->discount + $receipt_info->point_use_taka }}</td>
					</tr>
					<tr>
						<td>Payment Type</td>
						<td>&nbsp;:&nbsp;</td>
						<td align="right">{{ $receipt_info->payment_type_name }}</td>
					</tr>
					<tr>
					<td>Discount&nbsp;(Tk)</td>
						<td>&nbsp;:&nbsp;</td>
						<td align="right">{{ $receipt_info->discount }}</td>
					</tr>
					@if($receipt_info->point_use_taka != 0)
					<tr>
						<td>Use Point</td>
						<td>&nbsp;:&nbsp;</td>
						<? $user_pint = $receipt_info->point_use_taka / $receipt_info->point_unit; ?>
						<td align="right">{{ $user_pint }}</td>
					</tr>
					<tr>
						<td>Point Tk.</td>
						<td>&nbsp;:&nbsp;</td>
						<td align="right">{{ $receipt_info->point_use_taka }}</td>
					</tr>
					@endif
					<tr>
						<td>Total</td>
						<td>&nbsp;:&nbsp;</td>
						<td align="right"><strong>{{ $receipt_info->amount }}<strong></td>
					</tr>
					<tr>
						<td>Pay</td>
						<td>&nbsp;:&nbsp;</td>
						<td align="right">{{ $receipt_info->pay }}</td>
					</tr>
					<tr>
						<td>Due</td>
						<td>&nbsp;:&nbsp;</td>
						<td align="right">{{ $receipt_info->due }}</td>
					</tr>
					<tr>
						<td>Pay Note</td>
						<td>&nbsp;:&nbsp;</td>
						<td align="right">{{ $receipt_info->pay_note }}</td>
					</tr>
					<tr>
						<td>Return</td>
						<td>&nbsp;:&nbsp;</td>
						<? $return = $receipt_info->pay_note - $receipt_info->pay; ?>
						<td align="right">{{ round($return) }}</td>
					</tr>
				</tbody>
			</table>
		</article>
		<article style="clear:both; text-align: center;">
			<h6>Thanks for being with us</h6>
			<div align="center">
				{{ DNS1D::getBarcodeHTML($receipt_info->sale_invoice_id, "C128", 1, 25) }}					
				<strong>{{ $receipt_info->sale_invoice_id }}</strong>				
			</div>
			<p style="float:right;"><strong>Developed By : Unitech IT</strong></p>
			
		</article>	
		<div class="modal-footer print-btn" style="clear:both;">
			
		</div>
		@endif	
		<div style="height: 400px;"></div>
		<script>
			/*$(function(){
				 $('#print').on('click', function(){
					$("body").css('visibility','hidden');
					$('#printable').addClass('span12');
					printElement(document.getElementById("printable"));					
					window.print();
					$("body").css('visibility','visible');
					$('#printable').removeClass('span12');
				}); 
			});*/
		</script>
	
