@extends('_layouts.default')

@section('content')
	<div class="row print_disable">
		<div class="span12">@include('_sessionMessage')</div>
		<div class="span4">				
			<div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>Basic Details</h3>
            </div>
			{{ Form::open(array('route' => 'customer.saveSupplierPayment.post', 'id' => 'formCustomerPayment', 'class' => 'form-horizontal')) }}
				{{ Form::hidden('supp_id', $get_supplier->supp_id)}}
				<table class="table table-bordered" style="margin: 0; padding:0;">
					<tbody>
						 <tr>
							<td><strong style="float:right;">Supplier ID </strong></td>
							<td style="padding-left:10px;;"><strong style="color: green; font-size: 1.0em;"> {{ $get_supplier->supp_id }}</strong></td>
						</tr>
						<tr>
							<td><strong style="float:right;">Supplier/Company Name </strong></td>
							<td style="padding-left:10px;;">{{ $get_supplier->supp_or_comp_name }}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">User Name </strong></td>
							<td style="padding-left:10px;;">{{ $get_supplier->user_name }}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">Mobile No </strong></td>
							<td style="padding-left:10px;;">{{ $get_supplier->mobile }}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">Permanent Address </strong></td>
							<td style="padding-left:10px;;">{{ $get_supplier->permanent_address }}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">Present Address </strong></td>
							<td style="padding-left:10px;;">{{ $get_supplier->present_address }}</td>
						</tr>
						<tr>
							<td><strong style="float:right; ">Registration Date </strong></td>
							<td style="padding-left:10px;;">{{ Helper::dateFormat($get_supplier->created_at) }}</td>
						</tr>
					</tbody>
				</table>
				{{ Form::close() }}		
		</div>

		<div class="span4">				
			<div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>Total Balance( Till now )</h3>
            </div>
			<table class="table table-bordered" style="margin: 0; padding:0;">
					<tbody>
						<tr>
							<td><strong style="float:right;">Total Purchase</strong></td>
							<td style="padding-left:10px;">{{ $purchase->total_purchase_amount+ $purchase->total_discount}}</td>
						</tr>
						<tr>
							<td><strong style="float:right; ">Total Discount</strong></td>
							<td style="padding-left:10px;"> {{$purchase->total_discount}}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">Net Purchase</strong></td>
							<td style="padding-left:10px;"><strong style="color: green; font-size: 1.2em;">{{ $purchase->total_purchase_amount}}</strong></td>
						</tr>
						<tr>
							<td><strong style="float:right;">Total Paid </strong></td>
							<td style="padding-left:10px;">{{$purchase->total_paid}}</td>
						</tr>

						<tr>
							<td><strong style="float:right;">Total Return</strong></td>
							<td style="padding-left:10px;">{{$purchaseReturn->total_purchase_return_amount+$purchaseReturn->total_less_amount}}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">Total Less</strong></td>
							<td style="padding-left:10px;">{{$purchaseReturn->total_less_amount}}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">Net Return</strong></td>
							<td style="padding-left:10px;"><strong style="color: green; font-size: 1.2em;">{{ $purchaseReturn->total_purchase_return_amount}}</strong></td>
						</tr>


						 <tr>
							<td><strong style="float:right; ">Balance Due</strong></td>
							<td style="padding-left:10px;"><strong style="color: red; font-size: 1.5em;">{{ $get_supplier->due }}</strong></td>
						</tr>
						
						
					</tbody>
				</table>
			</div>

			<div class="span4">
			<div class="search-box">
			{{ Form::open(array('url' => "admin/supplier/transactionDetails/$get_supplier->supp_id", 'class' => 'form-horizontal')) }}
				<div class="widget-header setup-title"> <i class="icon-search"></i>
				  <h3>Search Transaction Payment </h3>
				</div>	
				<div class="control-group">
					<label for="username" class="control-label"><i class="icon-user"></i>&nbsp; Report Type &nbsp;&nbsp;&nbsp;: &nbsp;</label>
					<div class="controls">
						{{ Form::select('report_type',array('1'=>'Purchase','2'=>'Purchase Return'), null, ['class' => 'span2']) }}
					</div> <!-- /controls -->	
				</div> <!-- /control-group -->
				
				<div class="control-group" align="center">		
					<i class="icon-calendar"></i> Form : {{ Form::text('from', null, array('class' => 'input-small datepicker', 'id'=>'auto_search_item', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
					&nbsp;&nbsp;&nbsp;
					<i class="icon-calendar"></i> To   : {{ Form::text('to', null, array('class' => 'input-small datepicker', 'id'=>'auto_search_item', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
				</div> <!-- /control-group -->	
				<center><input class="btn btn-info" type="submit" value="Search"></center>
				{{ Form::close() }}
			</div>
			</div>


	<div class="row print_disable">
		<div class="span12" id="print_preview">
		<div class="span12">
			<div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>View Transaction History: 
				  @if($report_type==1) {{ 'Purchase Report' }}
					@else {{ 'Purchase Return Report' }} 
				  @endif
			  </h3>

			  <span style="float:right; margin:0 15px;"><button class='btn btn-info btn-small print-btn' onclick="window.print();">Print</button></span>
			</div>
			<div class="print">	
				<div class="payment-header">
					<strong>
						Supplier Name : <span style="font-weight:normal; color: green;">{{ $get_supplier->user_name }}</span>
					</strong>
					<strong style="float:right;">
						Report : <span style="font-weight:normal;">{{ Helper::onlyDMY($date_exp[0]) }}  <b>To: </b> {{ Helper::onlyDMY($date_exp[1]) }}</span>
					</strong>
				</div>
				

				@if($report_type==2)
					<table class="table table-striped" width="100%">
						<thead class="table-head">
							<tr>
								<th># SL No</th>
								<th>Return Invoice Id</th>
								<th>Payment Type</th>
								<th>Sub Total</th>
								<th>less Amount</th>
								<th>Total</th>
								<th>Purchased Return By</th>
								<th>Purchased Return At</th>
							</tr>
						</thead>
						<tbody>
							<?php $i=0; $total_less=0; $total_amount=0;?>
						@if($reports)
							@foreach($reports as $report)
								<tr>
									<td>{{++$i}}</td>
									<td><span class="span3"><a href="#purchaseReturnDetailsModal" onclick="purchaseReturnDetails({{ $report->sup_r_invoice_id }})" data-toggle="modal">{{$report->sup_r_invoice_id}}</a></span></td>
									<td><span class="span3">{{$report->payment_type_name}}</span></td>
									<td><span class="span3">{{$report->amount+$report->less_amount}}</span></td>
									<td><span class="span3">{{$report->less_amount}}</span></td><?php $total_less+=$report->less_amount; ?>
									<td><span class="span3">{{$report->amount}}</span></td><?php $total_amount+=$report->amount; ?>
									<td><span class="span3">{{$report->purchased_return_by}}</span></td>
									<td><span class="span3">{{$report->created_at}}</span></td>
								</tr>
								@endforeach
								<tr bgcolor="#DBEAF9">
								   <td colspan="3"><strong style="font-size: 1.3em;">Total<strong></td>

								   <td>{{$total_less+$total_amount}}</td>
								   <td>{{$total_less}}</td>
								   <td><strong>{{$total_amount}}</td>
		                           <td></td>
								   <td></td>  
						   		</tr>
							@endif
						</tbody>
					</table>	
				@else
					<table class="table table-striped" width="100%">
						<thead class="table-head">
							<tr>
								<th># SL No</th>
								<th>Invoice Id</th>
								<th>Payment Type</th>
								<th>Sub Total</th>
								<th>Discount</th>
								<th>Total</th>
								<th>Pay</th>
								<th>Due</th>
								<th>Purchased By</th>
								<th>Purchased At</th>
							</tr>
						</thead>
						<tbody>
							<?php $i=0; $total_discount=0; $total_amount=0;$total_pay=0;$total_due=0; ?>
						@if($reports)
							@foreach($reports as $report)
								<tr>
									<td>{{++$i}}</td>
									<td><span class="span3"><a href="#purDetailsModal" onclick="purchaseDetails({{ $report->sup_invoice_id }})" data-toggle="modal">{{$report->sup_invoice_id}}</a></span></td>
									<td><span class="span3">{{$report->payment_type_name}}</span></td>
									<td><span class="span3">{{$report->amount+$report->discount}}</span></td>
									<td><span class="span3">{{$report->discount}}</span></td><?php $total_discount+=$report->discount; ?>
									<td><span class="span3">{{$report->amount}}</span></td><?php $total_amount+=$report->amount; ?>
									<td><span class="span3">{{$report->pay}}</span></td><?php $total_pay+=$report->pay; ?>
									<td><span class="span3">{{$report->due}}</span></td><?php $total_due+=$report->due; ?>
									<td><span class="span3">{{$report->purchased_by}}</span></td>
									<td><span class="span3">{{$report->created_at}}</span></td>
								</tr>
								@endforeach
								<tr bgcolor="#DBEAF9">
								   <td colspan="3"><strong style="font-size: 1.3em;">Total<strong></td>

								   <td>{{$total_discount+$total_amount}}</td>
								   <td>{{$total_discount}}</td>
								   <td><strong>{{$total_amount}}</td>
		                           <td><strong style="color: green;">{{$total_pay}}<strong></td>
								   <td><strong style="color: red;">{{$total_due}}<strong></td>  
								   <td></td>
								   <td></td>  
						   		</tr>
							@endif
						</tbody>
					</table>
				@endif
			</div>	
		</div>
		</div>
	</div>



			

							

		
	<div id="purDetailsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="purDetailsLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="purDetailsLabel"><i class="icon-zoom-in"></i>&nbsp; Purchase Details</h3>
		</div>
		
		<div id="printable">
			<div class="modal-body print_modal" id="purDetailsBody">
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
			</div>
		</div>	
	</div>

	<div id="purchaseReturnDetailsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="purchaseReturnDetailsLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="purchaseReturnDetailsLabel"><i class="icon-zoom-in"></i>&nbsp; Purchase Return Details</h3>
		</div>
		
		<div id="printable">
			<div class="modal-body print_modal" id="purchaseReturnDetailsBody">
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
			</div>
		</div>	
	</div>

<script>	
        function printElement(elem, append, delimiter) {
			var domClone = elem.cloneNode(true);			
			var $printSection = document.getElementById("printSection");
			if (!$printSection) {
				var $printSection = document.createElement("div");
				$printSection.id = "printSection";
				document.body.appendChild($printSection);
			}
			if (append !== true) {
				$printSection.innerHTML = "";
			}
			else if (append === true) {
				if (typeof(delimiter) === "string") {
					$printSection.innerHTML += delimiter;
				}
				else if (typeof(delimiter) === "object") {
					$printSection.appendChlid(delimiter);
				}
			}
			$printSection.appendChild(domClone);
		}
		function loadingImg(){
			$('#loading').ajaxStart(function() {
				$(this).show();
			}).ajaxComplete(function() {
				$(this).hide();
			});
		}

		function purchaseDetails(purchaseInvoiceId){
			$(function(){	
				loadingImg();
				$("#purDetailsBody").load("{{ URL::to('admin/purchase/purchaseReportDetails') }}"+"/"+purchaseInvoiceId);
			});
		}
		
		function purchaseReturnDetails(purchaseReturnInvoiceId){
			$(function(){									
				loadingImg();
				$("#purchaseReturnDetailsBody").load("{{ URL::to('admin/purchasereturn/purchaseReturnReportDetails') }}"+"/"+purchaseReturnInvoiceId);
			});
		}

	</script>

@stop
@section('stickyInfo')
<?php
    $string = 'Supplier';
    $li = '';
    for($j=0;$j<strlen($string);$j++){
        $li .= '<li>'.substr($string,$j,1).'</li>';
    }
?>
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@stop