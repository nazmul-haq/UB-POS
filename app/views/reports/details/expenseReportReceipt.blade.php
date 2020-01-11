@extends('_layouts.default')
@section('content')
	<!--Receipt-->	
	<div class="row">
		<div class="span12" style="text-align: center; margin-top: 20px;">    	
			<article class="head-receipt" style="width: 50%; margin-left: 450px !important;">
			 
				<ul style="list-style-type:none; margin: 0;">
             @if (count($company_info)>0)
				  <li>
				  	<img src="{{asset('img/company_logo.png')}}" class="" style="padding-left: 10px;height: 80px;width:80px;" alt="title">
				  </li>
				  <li>{{ $company_info->address }}</li>
				  <li>{{ $company_info->mobile }}</li>
			 @endif
				</ul>
			 @if (count($receipt_info)>0)	
			 <center>
			 <table style="padding-left: 55px;">
				<tbody style='line-height:12px; font-size:10px'>
					<tr style="border-bottom: 1px solid #ccc; width:50%; margin: 3px auto;">
						<td colspan="3" style="text-align:center;"><b>Expense Receipt</b></td>
					</tr>
					<tr style='' width='100%'>
						<td align="right">Transaction Date</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">
							{{$receipt_info->transaction_date}}
						</td>
					</tr>
					<tr>
						<td align="right">Given By</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left"><b>{{ $receipt_info->invoice_by }}</b></td>
					</tr>
					<tr>
						<td align="right">Branch Name </td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left"><b>{{ Helper::getBranchName() }}</b></td>
					</tr>
				</tbody>
			 </table> 
			 </center>
			</article>	
			<table class="item-sales" style='width:50%; margin-left: 450px !important;' >
				<thead class="table-receipt-head">
					<tr>
						<th></th>
						<th width='30%'>Type</th>
						<th width='30%'>Amount</th>
						<th width='40%'>Comment</th>
					</tr>
				</thead>
				<tbody style='font-size:13px'>
					<tr>
						<td></td>
						<td>{{ $receipt_info->type_name }}</td>
						<td>BDT {{ $receipt_info->amount }}</td>
						<td>{{ $receipt_info->comment }}</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th width='30%'> </th>
						<th width='30%'> </th>
						<th width="40%"> &nbsp;</th>
					</tr>
					<tr>
						<th></th>
						<th width='30%'></th>
						<th width="50%" colspan="2" style="border-top: 1px solid black;">Total : BDT {{ $receipt_info->amount }} </th>
					</tr>
					<tr>
						<th colspan="4"></th>
					</tr>
					<tr>
						<th colspan="4"></th>
					</tr>
					<tr>
						<th colspan="4"></th>
					</tr>
					<tr>
						<th colspan="4"></th>
					</tr>
					<tr>
						<th colspan="4"></th>
					</tr>
					<tr>
						<th></th>
						<th width="30%" style="border-top: 1px solid black;">Authorize Sign</th>
						<th width='40%'></th>
						<th width="30%" style="border-top: 1px solid black;">Receiver Sign</th>
					</tr>
				</tfoot>
			</table>
			
			@endif
		</div>		
	</div>
	<script type="text/javascript">
	  window.onload = function () {
		// window.print();
	  // if({{Session::get('isAutoPrintAllow')}}==1){
		 //  window.print();
		 //  setTimeout(function(){window.location = "{{ URL::to('sale/sales') }}"}, 200000);
	  // }
	}
</script>
@stop