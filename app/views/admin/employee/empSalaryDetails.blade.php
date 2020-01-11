@extends('_layouts.default')
@section('content')
	<!--Receipt-->	
	<div class="row" >
		<div class="span12" style="text-align: center; margin-top: 20px;">    	
			<article class="head-receipt">
			 
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
						<td colspan="3" style="text-align:center;"><b>Salary Receipt</b></td>
					</tr>
					<tr>
						<td align="right">Employee Name</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{ $receipt_info->employee_name }}</td>
					</tr>
					<tr style='' width='100%'>
						<td align="right">Transaction Date</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">
							{{$receipt_info->transaction_date}}
						</td>
					</tr>
					<tr>
						<td align="right">Invoice No</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">{{$receipt_info->invoice_id}}</td>
					</tr>
					<tr>
						<td align="right">Given By</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left"><b>{{ $receipt_info->given_by }}</b></td>
					</tr>
				</tbody>
			 </table> 
			 </center>
			</article>	
			 
			<table class="item-sales" style='width:100%'>
				<thead class="table-receipt-head">
					<tr>
						<th></th>
						<th width='20%'>Salary Month</th>
						<th width='20%'>Fixed Salary</th>
						<th width='20%'>Salary Given</th>
						<th width='20%'>Salary Due</th>
						<th width="20%">Total</th>
					</tr>
				</thead>
				<tbody style='font-size:13px'>
					<tr>
						<td></td>
						<td>{{ $receipt_info->salary_month }}</td>
						<td>BDT {{ $receipt_info->fixed_salary }}</td>
						<td>BDT {{ $receipt_info->amount }}</td>
						<td>BDT {{ $receipt_info->due }}</td>
						<td>BDT {{ $receipt_info->amount }}</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th></th>
						<th width='20%'> </th>
						<th width='20%'> </th>
						<th width='20%'> </th>
						<th width='20%'> </th>
						<th width="20%"> &nbsp;</th>
					</tr>
					<tr>
						<th></th>
						<th width='20%'></th>
						<th width='20%'></th>
						<th width='20%'></th>
						<th width='20%'></th>
						<th width="20%" style="border-top: 1px solid black;">Total : BDT {{ $receipt_info->amount }} </th>
					</tr>
					<tr>
						<th colspan="6"></th>
					</tr>
					<tr>
						<th colspan="6"></th>
					</tr>
					<tr>
						<th colspan="6"></th>
					</tr>
					<tr>
						<th colspan="6"></th>
					</tr>
					<tr>
						<th colspan="6"></th>
					</tr>
					<tr>
						<th></th>
						<th width='20%'></th>
						<th width='20%'></th>
						<th width="20%" style="border-top: 1px solid black;">Authorize Sign</th>
						<th width='20%'></th>
						<th width="20%" style="border-top: 1px solid black;">Employee Sign</th>
					</tr>
				</tfoot>
			</table>
			<article style="clear:both; padding-top: 20px; text-align: center;">
				<div align="center">
					{{ DNS1D::getBarcodeHTML($receipt_info->invoice_id, "C128", 1, 25) }}		
					<strong>{{ $receipt_info->invoice_id }}</strong>				
				</div>
				<p style="float:right;">Developed By : <strong>Unitech IT</strong></p>
			</article>
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

