@extends('_layouts.default')
@section('content')
<?	
	$created = substr(Session::get('receipt_info.created_at'),0,10);
?> 
	<!--Receipt-->
	@if (Session::has('company_info'))
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
		@endif
		@if (Session::has('receipt_info'))
			 <table border="1" style="width: 500px;border: 1px solid gray; margin-bottom: 5px;">
				<tbody style='line-height:12px; font-size:10px'>
					@if(!empty(Session::get('receipt_info.customer_name')))
					<tr>
						<td style="width: 40%;" align="left">Customer : {{ Session::get('receipt_info.customer_full_name') }}</td>
						<td align="center" style="width: 20%;" rowspan="2">Sale Return Invoice</td>
						<td style="width: 40%;" align="left">Invoice No : {{ Session::get('receipt_info.invoice_id') }}</td>
					</tr>
					<tr>
						<td align="left">Address : {{ Session::get('receipt_info.present_address') }}</td>
						<td align="left">Date : 
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
					@else
					<tr>
						<td style="width: 40%;" align="left">Customer : </td>
						<td align="center" style="width: 20%;" rowspan="2">Sale Return Invoice</td>
						<td style="width: 40%;" align="left">Invoice No : {{ Session::get('receipt_info.invoice_id') }}</td>
					</tr>
					<tr>
						<td align="left">Address : </td>
						<td align="left">Date : 
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
					@endif
				</tbody>
			 </table> 
			 @endif
			</article>	

			@if(Session::has('receipt_return_item_infos'))
			
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
					<? $i = 0;$quantity=0; ?>
					@foreach(Session::get('receipt_return_item_infos') as $receipt_returnItem_info)
					<tr class="tr-receipt">
						<td>{{ ++$i }}</td>
						<td width='50%' class='uppercase' nowrap><span style='text-align:left'>{{ substr($receipt_returnItem_info['item_name'],0,100) }}</span></td>
						<td width='10%'>
							{{ $receipt_returnItem_info['quantity'] }}
							<?php $quantity += $receipt_returnItem_info['quantity']; ?>
						</td>
						<td width='20%'>{{ ($receipt_returnItem_info['total']/$receipt_returnItem_info['quantity']) }}</td>
						<td width='20%'>{{ $receipt_returnItem_info['total'] }}</td>
					</tr>
					@endforeach
				@endif
				@if (Session::has('receipt_info'))
					<tr>
						<td></td>
						<td colspan="2" style="text-align: right;font-weight:bold;" align="left">Total Qty.
							<b style='text-align:left; font-size:14px;'>
								(<? echo $quantity; ?>) pcs
							</b>
						</td>
						<td align="left" colspan="2">Sub Total &nbsp;&nbsp;&nbsp;
							{{ Session::get('receipt_info.total_amount') + Session::get('receipt_info.less_amount') }}
						</td>
					</tr>
					
					<tr>
						<td></td>
						<td colspan="2" style="line-height: 5px; font-size: 11px;">Less Amount: {{Session::get('receipt_info.less_amount')}}</td>
						<td colspan="2" style="line-height: 5px; text-align: center;">Total : {{ Session::get('receipt_info.total_amount') }}</td>
					</tr>
					<tr>
						<td colspan="5" align="right">Returned By : {{Session::get('receipt_info.emp_name')}}</td>
						
					</tr>
				</tbody>
			</table>
			<article style="clear:both; text-align: center;">
				<h6>Sold goods can't be return.</h6>
			</article>
			@endif
		</div>		
	</div>
	
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

