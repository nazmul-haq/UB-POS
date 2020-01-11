@extends('_layouts.default')

@section('content')
<div class="row">
	<div class="span12">
		@include('_sessionMessage')	
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'saleReturn.viewSaleReturnReport', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">		
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', date('Y-m-d'), array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', date('Y-m-d'), array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">						
					</div> <!-- /control-group -->	
					
                {{ Form::close() }}
            </div>        
		
		@if(isset($reports))
		<table class="table table-striped">
			<article style="background: #EEEEEE; padding : 5px 0 5px; border-top: 1px solid #003454;">
				<strong style="font-size: 1.8em;"><i class="icon-undo"></i> Sales Return Reports : Details</strong>
				<strong style="float: right; margin: 2px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			<thead class="table-head">
				<tr>
					<th>SL</th>
					<th>Return Invoice ID</th>
					<th>Customer Name</th>
					<th>Payment Type</th>
					<th>Sub Total Amount</th>
					<th>Less Amount</th>
					<th>Total</th>
					<th>Total Loss Profit</th>
					<th>Sold By</th>
					<th>Sold At</th>
				</tr>
			</thead>
			<tbody>
				<? $i=0; $total_lessAmount = 0; $total_amount = 0; $total_loss_profit=0; ?>
				@if($reports)
				   @foreach($reports as $returnInvoice)
					   <tr>
							<td>{{++$i}}</td>
							<td>
								<a href="#saleReturnDetailsModal" onclick="saleReturnDetails({{ $returnInvoice->sale_r_invoice_id }})" data-toggle="modal">{{ $returnInvoice->sale_r_invoice_id }}</a>
							</td>
							<td>{{ $returnInvoice->customer_name }}</td>
							<td>{{ $returnInvoice->payment_type_name }}</td>
							<td>{{ $returnInvoice->amount + $returnInvoice->less_amount }}</td>
							<td>{{ $returnInvoice->less_amount }}</td>
							<? $total_lessAmount = $total_lessAmount + $returnInvoice->less_amount; ?>
							<td>{{ $returnInvoice->amount }}</td>
							<? $total_amount = $total_amount + $returnInvoice->amount; ?>
							<td>{{ $returnInvoice->loss_profit }}</td>
							<? $total_loss_profit += $returnInvoice->loss_profit; ?>
							<td>{{ $returnInvoice->invoiced_employee }}</td>
							<td>{{ $returnInvoice->return_invoiced_datetime }}</td>
						</tr>				   
				   @endforeach              
                        <tr bgcolor="#DBEAF9">
						   <td colspan="4"><strong style="font-size: 1.3em;">Total<strong></td>
						   <td>{{$total_lessAmount+$total_amount}}</td>
						   <td>{{$total_lessAmount}}</td>
						   <td><strong style="color: green;">{{ $total_amount }}<strong></td>
						   <td colspan="3"><strong style="color: red;">{{ $total_loss_profit }}<strong></td>   
					   </tr>
				@else
					<tr>
						<td colspan="8" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
                               
			</tbody>
		</table>
	 </div>	
		<!--Sale Details Model-->		
		<div id="saleReturnDetailsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="saleReturnDetailsLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="saleReturnDetailsLabel"><i class="icon-zoom-in"></i>&nbsp; Sale Return Details</h3>
			</div>
			
			<div id="printable">
				<div class="modal-body print_modal" id="saleReturnDetailsBody">
					<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
				</div>
			</div>	
		</div>
	 @endif
	</div>
	
	<script>	
		function loadingImg(){
			$('#loading').ajaxStart(function() {
				$(this).show();
			}).ajaxComplete(function() {
				$(this).hide();
			});
		}
		function saleReturnDetails(saleReturnInvoiceId){
			$(function(){									
				loadingImg();
				$("#saleReturnDetailsBody").load("{{ URL::to('admin/saleReturn/saleReturnReportDetails') }}"+"/"+saleReturnInvoiceId);
			});
		}
	</script>

@stop