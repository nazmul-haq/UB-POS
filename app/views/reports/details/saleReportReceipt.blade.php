@extends('_layouts.default')

@section('content')

<?	$created = substr($receipt_info->created_at , 0, 10); ?>
	<!--Receipt-->	
	<div class="row">
		<div class="span12">    	
			<article class="head-receipt">
			<ul style="list-style-type:none; margin: 0;">
			 @if ($company_info)	
				  <li><strong class="company-name">{{ $company_info->company_name }}</strong></li>
				  <li>{{ $company_info->address }}</li>
				  <li>{{ $company_info->mobile }}</li>
			 @endif
		 	</ul> 
			@if ($receipt_info)
			 <center>
			 <table>
				<tbody style='line-height:12px; font-size:10px'>
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
					<tr style='' width='100%'>
						<td align="right">Sales Date</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">
							@if($created == $receipt_info->date)
								{{ Helper::dateFormat($receipt_info->created_at) }}
							@else
								{{ Helper::dateFormat($receipt_info->date) }}
							@endif
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
			<table class="item-sales" style="border: 0px;" >
				<thead class="table-receipt-head">
					<tr>
						<th></th>
						<th width='40%'>Item</th>
						<th width='20%'>Price</th>
						<th width='10%'>Qty.</th>
						<th width='5%'>Disc<br>(tk)</th>
						<th width="25%">Total</th>
					</tr>
				</thead>
				<tbody style='font-size:13px'>
					<? $i = 0;  $quantity=0;?>
					@foreach($receipt_item_infos as $receipt_item_info)
					<? $i++; ?>
					<tr class="tr-receipt">
						<td>{{ $i }}</td>
						<td align='left' style=' text-align: left; ' width='40%' class='uppercase'><span>{{ substr($receipt_item_info->item_name,0,50) }}</span></td>
						<td width='20%'>{{ $receipt_item_info->sale_price }}</td>
						<td width='10%'>{{ $receipt_item_info->quantity }} <? $quantity += $receipt_item_info->quantity; ?></td>
						<td width='5%' >{{ $receipt_item_info->discount }}</td>
						<td width='25%'>{{ $receipt_item_info->amount }}</td>
					</tr>
					@endforeach
				@endif
				@if ($receipt_info)
					<tr>						
						<td colspan='3' style="text-align: right;font-weight:bold;">Total Qty.
						    <b style='text-align:left; font-size:14px;'>(<? echo $quantity; ?>)</b></td>
						<td colspan='2' style="line-height: 5px; text-align: right;">SubTotal</td>
						<td style="line-height: 5px; text-align: right; padding-right: 14px;">{{ $receipt_info->amount + $receipt_info->discount + $receipt_info->point_use_taka }}</td>
					</tr>
					<tr>						
						<td colspan='5'  style="line-height: 20px; text-align: right;">Payment Type</td>
						<td style="line-height: 5px; text-align: right; padding-right: 14px;">{{ $receipt_info->payment_type_name }}</td>
					</tr>
					<tr>						
						<td colspan='5' style="line-height: 5px; text-align: right;">Discount&nbsp;(Tk)</td>
						<td style="line-height: 5px; text-align: right; padding-right: 14px;">{{ $receipt_info->discount }}</td>
					</tr>
					@if($receipt_info->point_use_taka != 0)
					<tr>						
						<td colspan='5' style="line-height: 5px; text-align: right;">Use Point</td>
						<? $user_point = $receipt_info->point_use_taka / $receipt_info->point_unit; ?>
						<td style="line-height: 5px; text-align: right; padding-right: 14px;">{{ $user_point }}</td>
					</tr>
					<tr>						
						<td colspan='5' style="line-height: 5px; text-align: right;">Point Tk.</td>
						<td style="line-height: 5px; text-align: right; padding-right: 14px;">{{ $receipt_info->point_use_taka }}</td>
					</tr>
					@endif
					<tr>						
						<td colspan='5' style="line-height: 14px; text-align: right; font-weight:bold;">Total</td>
						<td style="line-height: 14px; text-align: right; font-weight:bold; padding-right: 14px;">{{ $receipt_info->amount }}</td>
					</tr>
					<tr>						
						<td colspan='5' style="line-height: 5px; text-align: right;"><b>Paid</b></td>
						<td style="line-height: 5px; text-align: right;  padding-right: 14px;"><b>{{ $receipt_info->pay  }}</b></td>
					</tr>
					<tr>						
						<td colspan='5' style="line-height: 5px; text-align: right;">Due</td>
						<td style="line-height: 5px; text-align: right;  padding-right: 14px;">{{ $receipt_info->due }}</td>
					</tr>
					<tr>						
						<td colspan='5' style="line-height: 5px; text-align: right;">Pay Note</td>
						<td style="line-height: 5px; text-align: right; padding-right: 14px;">{{ $receipt_info->pay_note }}</td>
					</tr>
					<tr>						
						<td colspan='5' style="line-height: 5px; text-align: right;">Return</td>
						<? $return = $receipt_info->pay_note - $receipt_info->pay; ?>
						<td style="line-height: 5px; text-align: right; padding-right: 14px;">{{ round($return) }}</td>
					</tr>	
				</tbody>
			</table>
				
			<article style="clear:both; text-align: center;">
				<h6>Thanks for being with us</h6>
				<div align="center">
					{{ DNS1D::getBarcodeHTML($receipt_info->sale_invoice_id, "C128", 1, 25) }}					
					<strong>{{ $receipt_info->sale_invoice_id }}</strong>				
				</div>
				<p style="float:right;"><strong>Developed By : Unitech IT</strong></p>
				
			</article>	
			@endif
		</div>		
	</div>
	
	<script type="text/javascript">
	  window.onload = function () {
	  if({{Session::get('isAutoPrintAllow')}}==1){
		 window.print();
		 setTimeout(function(){window.location = "{{ URL::to('sale/sales') }}"}, 3000);
	  }
	}
</script>
@stop

