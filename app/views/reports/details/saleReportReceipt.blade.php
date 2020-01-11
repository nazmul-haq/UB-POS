@extends('_layouts.default')
@section('content')
<?	
	$created = substr($receipt_info->created_at , 0, 10); 
?> 
	<!--Receipt-->	
	@if (count($receipt_info) > 0)
	<div class="row">
		<div class="span6 mainDiv" style="margin-left: 410px !important;">    	
			<article class="head-receipt">
				<table align="center" style="width: 500px;">
					<tr>
						<td style="font-size: 10px;">
				  			<img src="{{asset('img/company_logo.png')}}" class="" style="padding-right: 15px;height: 70px; margin-top: 20px !important;'" alt="title">
						</td>
					</tr>
					<tr>
						<td style="font-size: 10px;">
							{{Helper::getBranchName()}}
						</td>
					</tr>
					<tr>
						<td style="font-size: 10px;">
							66, MOULOVIBAZAR, TAJMAHAL TOWER, CHAWKBAZAR, DHAKA,BANGLADESH.
						</td>
					</tr>
					<tr>
						<td style="font-size: 10px;">
							Phone: 027343195,Cell: 01797185240, E-mail: shohag4321@gmail.com
						</td>
					</tr>
					<tr>
						<td style="font-size: 10px;">
							Web: www.mbtrade-bd.com
						</td>
					</tr>
				</table>
			 <table border="1" style="width: 500px;border: 1px solid gray; margin-bottom: 5px;">
				<tbody style='line-height:12px; font-size:10px'>
					@if(!empty($receipt_info->customer_name))
					<tr>
						<td style="width: 40%;" align="left">Customer : {{ $receipt_info->customer_name }}</td>
						<td align="center" style="width: 20%;" rowspan="2">Sale Invoice</td>
						<td style="width: 40%;" align="left">Invoice No : {{ $receipt_info->sale_invoice_id }}</td>
					</tr>
					<tr>
						<td align="left">Address : {{ $receipt_info->present_address }}</td>
						<td align="left">Date : 
							@if($created == $receipt_info->date)
								{{ Helper::dateFormat($receipt_info->created_at) }}
							@else
								{{ Helper::dateFormat($receipt_info->date) }}
							@endif
						</td>
					</tr>
					@else
					<tr>
						<td style="width: 40%;" align="left">Customer : </td>
						<td align="center" style="width: 20%;" rowspan="2">Sale Invoice</td>
						<td style="width: 40%;" align="left">Invoice No : {{ $receipt_info->sale_invoice_id }}</td>
					</tr>
					<tr>
						<td align="left">Address : </td>
						<td align="left">Date : 
							@if($created == $receipt_info->date)
								{{ Helper::dateFormat($receipt_info->created_at) }}
							@else
								{{ Helper::dateFormat($receipt_info->date) }}
							@endif
						</td>
					</tr>
					@endif
				</tbody>
			 </table> 
			</article>	

			@if($receipt_item_infos)
			
			<table class="item-sales" border="1" style='width: 500px;'>
				<thead class="table-receipt-head">
					<tr>
						<th>SL.</th>
						<th width='50%'>Item</th>
						<th width='10%'>Qty.</th>
						<th width='20%'>Price</th>
						<th width="20%">Total</th>
					</tr>
				</thead>
				<tbody style='font-size:13px'>
					<? $i = 0;  $quantity=0;?>
					@foreach($receipt_item_infos as $key => $receipt_item_info)
					@if($receipt_item_info->quantity>0)
					<tr class="tr-receipt">
						<td>{{ ++$key }}</td>
						<td width='50%' class='uppercase' nowrap><span style='text-align:left'>{{ substr($receipt_item_info->item_name,0,100) }}</span></td>
						<td width='10%'>{{ $receipt_item_info->quantity }} <? $quantity += $receipt_item_info->quantity; ?></td>
						<td width='20%'>{{ ($receipt_item_info->amount/$receipt_item_info->quantity) }}</td>
						<td width='20%'>{{ $receipt_item_info->amount }}</td>
					</tr>
					@endif
					@endforeach
				@endif
				@if ($receipt_info)
					<tr>
						<td></td>
						<td colspan="2" style="text-align: right;font-weight:bold;" align="left">Total Qty.
							<b style='text-align:left; font-size:14px;'>
								(<? echo $quantity; ?>) pcs
							</b>
						</td>
						<td align="left" colspan="2">Sub Total &nbsp;&nbsp;&nbsp;
							{{ $receipt_info->amount + $receipt_info->discount + $receipt_info->point_use_taka }}
						</td>
					</tr>
					
					<tr>
						<td colspan="3" style="line-height: 5px; font-size: 11px;">In Word: {{Helper::convert_number($receipt_info->amount)}}</td>
						<td colspan="2" style="line-height: 5px; text-align: center;">Total : {{ $receipt_info->amount }}</td>
					</tr>
					<tr>
						<td colspan="3">Sold By : {{ $receipt_info->invoiced_employee}}</td>
						<td colspan="1" style="line-height: 5px; font-size: 11px;">
							Paid : {{ $receipt_info->pay  }}
						</td>
						<td colspan="1" style="line-height: 5px; text-align: right; padding-right: 18px;"> Due : {{ $receipt_info->due }} </td>
					</tr>
				</tbody>
			</table>
			<article style="clear:both; text-align: center;">
				<h6>Sold goods can't be return.</h6>
			</article>
			@endif
		</div>		
	</div>
	@endif
	
	<script type="text/javascript">
	  window.onload = function () {
		window.print();
	  if({{Session::get('isAutoPrintAllow')}}==1){
		  window.print();
		  setTimeout(function(){window.location = "{{ URL::to('sale/sales') }}"}, 200000);
	  }
	}
</script>
@stop
