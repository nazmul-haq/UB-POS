@extends('_layouts.default')

@section('content')
<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'companyWiseSale.report', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
					</div> <!-- /control-group -->
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Company :
						<select class="controls" name="company_id" style="margin-left:-2px !important">
						<option>Select Company</option>
						@if(isset($companies))
							@foreach($companies as $company)
							<option value="{{$company->company_id}}">{{$company->company_name}}</option>
							@endforeach
						@endif
						</select>
						&nbsp;&nbsp;&nbsp;
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->
                {{ Form::close() }}
            </div>

            <article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 1.5em;"><i class="icon-credit-card"></i> Summary:: Company Wise Sale Report</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			@if(count($companyWiseSale) > 0)
			<table class="table table-bordered" id="datatable">
				<thead>
					<tr>
						<th>SL</th>
						<th>Invoice ID</th>
						<th>Company Name</th>
						<th>Amount</th>
						<th>Discount (Tk)</th>
						<th>Paid</th>
						<th>Action</th>
					</tr>
				</thead>

				<tbody>
					<? $i=0;$total_amount_taka=0;$total_discount=0;$total_pay=0; ?>
					@if($companyWiseSale)

					   @foreach($companyWiseSale as $key => $companySale)
					    <?php
					    	$i++;
					    	$invoiceInfo = json_decode($key);
					    	// print_r($companySale);
					    	// exit;
					    ?>
						   <tr>
								<td>{{$i}}</td>
								<td>{{$invoiceInfo->sale_invoice_id}}</td>
								<td>{{$invoiceInfo->company_name}}</td>
								<td>
									{{$companySale[0]->totalAmount}}
									<?php 
										$total_amount_taka += $companySale[0]->totalAmount;
										$total_discount += $companySale[0]->totalDiscount;
										$total_pay += $companySale[0]->totalAmountPaid;
									?>
								</td>

								<td>
									{{$companySale[0]->totalDiscount}}
								</td>
								<td>
									{{$companySale[0]->totalAmountPaid}}
								</td>
								
								<td width="80px"><a href="#saleDetailsModal" onclick="saleDetails({{$invoiceInfo->sale_invoice_id}},{{$invoiceInfo->company_id}})" class="btn btn-warning btn-small" role="button" data-toggle="modal"><i class="icon-zoom-in"></i> Details</a></td>
							</tr>				   
					    @endforeach              
	                	@else
							<tr>
								<td colspan="12" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
							</tr>
						@endif
	                               
				</tbody>
				<input type="hidden" name="index" value="{{$i}}">
				  <tr style=" background:#DBEAF9; font-size: 15px;">
					   <td colspan="3"><strong style="font-size: 1.1em;">Total<strong></td>
					   <td>{{$total_amount_taka}}</td>
					   <td>{{$total_discount}}</td>
                       <td colspan="3"><strong style="color: green;">{{$total_pay}}<strong></td>
				   </tr>
			</table>
			@endif
	  	</div>
	</div>
</div>
<div id="saleDetailsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="purDetailsLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
		<h3 id="purDetailsLabel"><i class="icon-zoom-in"></i>&nbsp; Sale Details</h3>
	</div>
	
	<div id="printable">
		<div class="modal-body print_modal" id="saleDetailsBody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>	
		</div>
	</div>	
</div>
<script type="text/javascript">
	function saleDetails(saleInvoiceId,companyId){
		$(function(){									
			$("#saleDetailsBody").load("{{ URL::to('summary/saleReportDetailsCompanyWise') }}"+"/"+saleInvoiceId+"/"+companyId);
		});
	}
	$("#datatable").DataTable();
</script>
@stop