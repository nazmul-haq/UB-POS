@extends('_layouts.default')
@section('content')
<? $created=substr(Session::get('receipt_info.created_at'),0,10); 
?>
	<!--Receipt-->	
	<div class="row">
		<div class="span6 mainDiv" style="margin-left: 410px !important;">    	
			<article class="head-receipt">
				<table align="center" style="width: 500px;">
					@if (Session::has('company_info'))
					<tr>
						<td style="font-size: 10px;">
				  			<img src="{{asset('img/company_logo.png')}}" class="" style="padding-right: 15px;height: 70px; margin-top: 20px !important;'" alt="title">
						</td>
					</tr>
					<tr>
						<td style="font-size: 10px;">
							{{Session::get('receipt_info.branch_name')}}
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
					@endif
				</table>
			 @if (Session::has('receipt_info'))	
			 <table border="1" style="width: 500px;border: 1px solid gray; margin-bottom: 5px;">
				<tbody style='line-height:12px; font-size:10px'>
					@if(!empty(Session::get('receipt_info.customer_full_name')))
					<tr>
						<td style="width: 40%;" align="left">Customer : {{ Session::get('receipt_info.customer_full_name') }}</td>
						<td align="center" style="width: 20%;" rowspan="2">Sale Order Invoice</td>
						<td style="width: 40%;" align="left">Order Invoice No : {{ Session::get('receipt_info.invoice_id') }}</td>
					</tr>
					<tr>
						<td align="left">Address : {{ substr(Session::get('receipt_info.cust_address'),0,100) }}</td>
						<td align="left">Date : <?php 
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
						<td align="center" style="width: 20%;" rowspan="2">Sale Invoice</td>
						<td style="width: 40%;" align="left">Order Invoice No : {{ Session::get('receipt_info.invoice_id') }}</td>
					</tr>
					<tr>
						<td align="left">Address : </td>
						<td align="left">Date : <?php 
								if($created==Session::get('receipt_info.date')){
									$transDateArr =explode(' ', Helper::dateFormat(Session::get('receipt_info.created_at')));
									echo $transDateArr[0];
								}else{
									 echo Helper::dateFormat(Session::get('receipt_info.date'));
								}
							?></td>
					</tr>
					@endif
				</tbody>
			 @endif
			 </table> 
			</article>	
			@if(Session::has('receipt_item_infos'))
			<table class="item-sales" border="1" style='width: 500px;'>
				<thead class="table-receipt-head">
					<tr>
						<th>SL.</th>
						<th width='50%'>Item</th>
						<th width='10%'>Qty.)</th>
						<th width='20%'>Price</th>
						<th width="20%">Total</th>
					</tr>
				</thead>
				<tbody style='font-size:13px'>
					<? $i = 0;  $quantity=0; $totalPoint = 0;?>
					@foreach(Session::get('receipt_item_infos') as $receipt_item_info)
					<? $i++;?>
					@if($receipt_item_info['sale_quantity']>0)
					<tr class="tr-receipt">
						<td>{{ $i }}</td>
						<td width='50%' class='uppercase' nowrap><span style='text-align:left'>{{ substr($receipt_item_info['item_name'],0,100) }}</span></td>
						<td width='10%'><? $quantity+=$receipt_item_info['sale_quantity']; ?>{{ $receipt_item_info['sale_quantity'] }}</td>
						<td width='20%'>{{ $receipt_item_info['sale_price'] }}</td>
						<!--<td><?// echo $receipt_item_info['tax'] ?></td>-->
						<td width='20%'>{{ $receipt_item_info['total'] }}</td>
					</tr>
					@endif
					@endforeach
				@endif
				@if (Session::has('receipt_info'))
					<tr>
						<td></td>
						<td colspan="2" style="text-align: right;font-weight:bold;" align="left">Total Qty.
						<b style='text-align:left; font-size:14px;'>(<? echo $quantity; ?>) pcs</b></td>
						<td align="left" colspan="2">Sub Total &nbsp;&nbsp;&nbsp;
						{{ Session::get('receipt_info.total_amount')+Session::get('receipt_info.invoice_discount')+Session::get('receipt_info.point_taka') }}</td>
					</tr>
					<tr>
						<td colspan="3" style="line-height: 5px; font-size: 11px;">In Word: {{Helper::convert_number(Session::get('receipt_info.total_amount'))}}</td>
						<td colspan="2" style="line-height: 5px; text-align: center;">Total : {{ Session::get('receipt_info.total_amount')}}</td>
					</tr>
					<tr>
						<td colspan="3">Order made by : {{ Session::get('receipt_info.emp_name')}}</td>
						<td colspan="2" style="line-height: 5px; font-size: 11px;">
							Paid : {{ Session::get('receipt_info.pay')}}
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="2" style="line-height: 5px; text-align: right; padding-right: 18px;"> Due : {{ Session::get('receipt_info.due')}} </td>
						<td colspan="2" style="line-height: 5px; text-align: right; padding-right: 18px;"> Prev. Due : {{ Session::get('receipt_info.customer_due')}} </td>
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

