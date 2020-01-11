@extends('_layouts.default')
@section('content')
<?  $created=substr($receipt_info['created_at'],0,10); ?>
	<!--Receipt-->	
	<div class="row">
		<div class="span12">    	
			<article class="head-receipt">
				<ul style="list-style-type:none; margin: 0;">
             @if (count($company_info)>0)	
				  <!-- <li><strong class="company-name">{{ $company_info->company_name }}</strong></li> -->
				  <li>
				  	<img src="{{asset('img/logo-homeplus.png')}}" class="" style="padding-right: 15px;height: 70px;" alt="title">
				  </li>
				  <!-- <li><img src="{{asset('img/company_logo.jpg')}}" class="" style="padding:15px; height: 80px; width: 320px;" alt="title"></li> -->
				  <li>{{ $company_info->address }}</li>
				  <li>{{ $company_info->mobile }}</li>
			 @endif
				</ul>
			 @if (count($receipt_info) > 0)	
			 <center>
			 <table>
				<tbody style='line-height:12px; font-size:10px'>
					<tr style="border-bottom: 1px solid #ccc; width:50%; margin: 3px auto;">
						<td colspan="3" style="text-align:center;"><b>Inventory Dialog Receipt</b></td>
					</tr>
					<tr style='' width='100%'>
						<td align="right">Transaction Date</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">
							<?php 
								if($created== $receipt_info['date']){
									$transDateArr =explode(' ', Helper::dateFormat($receipt_info['created_at']));
									echo $transDateArr[0];
								}else{
									 echo Helper::dateFormat($receipt_info['date']);
								}
							?>
						</td>
					</tr>
					<tr>
						<td align="right">Invoice No</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ $receipt_info['inventory_invoice_id'] }}</td>
					</tr>
					<tr>
						<td align="right">Created By</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left"><b>{{ $receipt_info['emp_name'] }}</b></td>
					</tr>
				</tbody>
			 @endif
			 </table> 
			 </center>
			</article>	
			@if(count($receipt_item_infos) > 0)
			<table class="item-sales" style='width:100%'>
				<thead class="table-receipt-head">
					<tr>
						<th width='10%'>SL No.</th>
						<th width='35%'>Item</th>
						<th width='25%'>Upc Code</th>
						<th width='15%'>Qty (Ex)</th>
						<th width='15%'>Qty (New)</th>
					</tr>
				</thead>
				<tbody style='font-size:13px'>
					<? $totalQtyEx = $totalQtyNew = 0; ?>
					@foreach($receipt_item_infos as $key => $receipt_item_info)
					<tr class="tr-receipt">
						<td width='10%'>{{ ++$key }}</td>
						<td width='25%' class='uppercase' nowrap><span style='text-align:left'>{{ substr($receipt_item_info['item_name'],0,50) }}</span></td>
						<td width='25%' class='uppercase' nowrap><span style='text-align:left'>{{$receipt_item_info['upc_code']}}</span></td>
						<td width='15%'>
							{{ $receipt_item_info['ex_quantity'] }}
							<?php
								$totalQtyEx += $receipt_item_info['ex_quantity'];
								$totalQtyNew += $receipt_item_info['new_quantity'];
							?>
						</td>
						<td width='15%'>{{ $receipt_item_info['new_quantity'] }}</td>
					</tr>
					@endforeach
				@endif
				@if (count($receipt_info) > 0)
					<tr>
						<td>&nbsp;</td>
						<td colspan="3" style="text-align: right;font-weight:bold;" align="left">Total Qty (Ex)
						<b style='text-align:left; font-size:14px;'>(<? echo $totalQtyEx; ?>)</b></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="4" style="text-align: right;font-weight:bold;" align="left">Total Qty (New)
						<b style='text-align:left; font-size:14px;'>(<? echo $totalQtyNew; ?>)</b></td>
					</tr>
				</tbody>
			</table>
				
			<article style="clear:both; text-align: center;">
				<h6>Thanks for being with us</h6>
				<div align="center">
					{{ DNS1D::getBarcodeHTML($receipt_info['inventory_invoice_id'], "C128", 1, 25) }}					
					<strong>{{ $receipt_info['inventory_invoice_id'] }}</strong>				
				</div>
				<p style="float:right;">Developed By : <strong>Unitech IT</strong></p>
				
			</article>
			@endif
		</div>		
	</div>
	
	<script type="text/javascript">
	  window.onload = function () {
		window.print();
	  if({{Session::get('isAutoPrintAllow')}}==1){
		  window.print();
	  }
	}
</script>
@stop

