@extends('_layouts.default')

@section('content')

<?	$created = substr(Session::get('receipt_info.created_at'),0,10); ?>
	<!--Receipt-->	
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
						<td colspan="3" style="text-align:center;"><b>Sales Return Receipt</b></td>
					</tr>
					@if(!empty(Session::get('receipt_info.customer_name')))
					<tr>
						<td align="right">Customer Name</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ Session::get('receipt_info.customer_name') }}</td>
					</tr>
					@endif
					<tr>
						<td align="right">Transaction Date</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">
						  @if($created==Session::get('receipt_info.date'))
							{{ Helper::dateFormat(Session::get('receipt_info.created_at')) }}
						  @else
							{{ Helper::dateFormat(Session::get('receipt_info.date')) }}
						  @endif
						</td>
					</tr>
					<tr>
						<td align="right">Invoice No</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ Session::get('receipt_info.invoice_id') }}</td>
					</tr>
					<tr>
						<td align="right">Sold By</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ Session::get('receipt_info.emp_name')  }}</td>
					</tr>
				</tbody>
			 @endif
			 </table> 
			 </center>	
			</article>	
			@if(Session::has('receipt_return_item_infos'))
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
					@foreach(Session::get('receipt_return_item_infos') as $receipt_returnItem_info)
					<? $i++; ?>
					<tr>
						<td>{{ $i }}</td>
						<td>{{ $receipt_returnItem_info['item_name'] }}</td>
						<td>{{ $receipt_returnItem_info['sale_price'] }}</td>
						<td>{{ $receipt_returnItem_info['quantity'] }}</td>
						<td>{{ $receipt_returnItem_info['discount'] }}</td>
						<td>{{ $receipt_returnItem_info['tax'] }}</td>
						<td>{{ $receipt_returnItem_info['total'] }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			@endif
			@if (Session::has('receipt_info'))
                        <?php // echo '<pre>';print_r(Session::get('receipt_info'));exit;?>
			<article class="btn-sale-receipt">
				<table>
					<tbody>
						<tr style="font-weight: bolder;">
							<td>Sub Total</td>
							<td>&nbsp;:&nbsp;</td>
							<? $sub_total = Session::get('receipt_info.total_amount') + Session::get('receipt_info.less_amount'); ?>
							<td align="right">{{ $sub_total }}</td>
						</tr>
						<tr>
							<td>Payment Type</td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ Session::get('receipt_info.payment_type_name') }}</td>
						</tr>
						<tr>
							<td>Less Amount</td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ Session::get('receipt_info.less_amount') }}</td>
						</tr>
                        <tr>
							<td>Total </td>
							<td>&nbsp;:&nbsp;</td>
							<td align="right">{{ Session::get('receipt_info.total_amount') }}</td>
						</tr>
					</tbody>
				</table>
			</article>
			<article style="clear:both; text-align: center;">
				<h6>Thanks for being with us</h6>
				<div align="center">
					{{ DNS1D::getBarcodeHTML(Session::get('receipt_info.invoice_id'), "C128", 1, 25) }}					
					<strong>{{ Session::get('receipt_info.invoice_id') }}</strong>				
				</div>
				<p style="float:right;">Developed By : <strong>Unitech IT</strong></p>
				
			</article>
			@endif
		</div>		
	</div>
	
	<script type="text/javascript">
		window.onload = function () {
			if({{Session::get('isAutoPrintAllow')}}==1){
			  window.print();
			  setTimeout(function(){window.location = "{{ URL::to('admin/returnQtyFromCustomer') }}"}, 3000);
			}
		}
	</script>
@stop

