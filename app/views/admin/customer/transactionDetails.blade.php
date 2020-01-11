@extends('_layouts.default')
@section('content')
	<div class="row print_disable">
		<div class="span12">@include('_sessionMessage')</div>
		<div class="span4">				
			<div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>Basic Details</h3>
            </div>
			{{ Form::open(array('route' => 'customer.saveSupplierPayment.post', 'id' => 'formCustomerPayment', 'class' => 'form-horizontal')) }}
				{{ Form::hidden('cus_id', $get_customer->cus_id)}}
				<table class="table table-bordered" style="margin: 0; padding:0;">
					<tbody>
						<tr>
							<td><strong style="float:right;">Customer Type </strong></td>
							<td style="padding-left:10px;"><strong style="color: red; font-size: 1.0em;"> {{ $get_customer->cus_type_name }}</strong></td>
						</tr>
						 <tr>
							<td><strong style="float:right;">Customer ID </strong></td>
							<td style="padding-left:10px;;"><strong style="color: green; font-size: 1.0em;"> {{ $get_customer->cus_card_id }}</strong></td>
						</tr>
						<tr>
							<td><strong style="float:right;">Full Name </strong></td>
							<td style="padding-left:10px;;">{{ $get_customer->full_name }}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">Mobile No </strong></td>
							<td style="padding-left:10px;;">{{ $get_customer->mobile }}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">Present Address </strong></td>
							<td style="padding-left:10px;;">{{ $get_customer->present_address }}</td>
						</tr>
						<tr>
							<td><strong style="float:right; ">Registration Date </strong></td>
							<td style="padding-left:10px;;">{{ Helper::dateFormat($get_customer->created_at) }}</td>
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
							<td><strong style="float:right;">Net Purchase</strong></td>
							<td style="padding-left:10px;">{{ $calculate->total_purchase-$calculate->return_amount }}</strong></td>
						</tr>
						<tr>
							<td><strong style="float:right;">Total Return </strong></td>
							<td style="padding-left:10px;">{{{ isset($calculate->return_amount) ? $calculate->return_amount :0   }}} </td>
						</tr>
						 <tr>
							<td><strong style="float:right;">(+)Total Purchase </strong></td>
							<td style="padding-left:10px;"><strong style="color: green; font-size: 1.0em;">{{ $calculate->total_purchase }} Tk</strong></td>
						</tr>


						 <tr>
							<td><strong style="float:right; ">Net Paid</strong></td>
							<td style="padding-left:10px;">{{ $calculate->total_paid }}</td>
						</tr>
						<tr>
							<td><strong style="float:right; ">Total Discount</strong></td>
							<td style="padding-left:10px;">{{ $calculate->discount }}</td>
						</tr>
						<tr>
							<td><strong style="float:right;">(+)Total Paid</strong></td>
							<td style="padding-left:10px;"><strong style="color: green; font-size: 1.0em;">{{ $calculate->total_paid+$calculate->discount }} Tk</strong></td>
						</tr>
						<tr>
							<td>
								<strong style="float:right; margin-right:50px;">
									Balance @if($calculate->total_due >= 0) {{ ' Due' }} @else {{ 'Advanced' }} @endif 
								</strong>
							</td>
							<td style="padding-left:10px;"><strong style="color: red; font-size: 1.0em;" id='due_amount'>{{ abs($calculate->total_due) }}</strong></td>
						</tr>
						 
						 
					</tbody>
				</table>
			</div>
		
		<div class="span4">
			<div class="search-box">
			{{ Form::open(array('url' => "admin/customer/transactionDetails/$get_customer->cus_id", 'class' => 'form-horizontal')) }}
				<div class="widget-header setup-title"> <i class="icon-search"></i>
				  <h3>Search Transaction Payment </h3>
				</div>	
				<div class="control-group">
					<label for="username" class="control-label"><i class="icon-user"></i>&nbsp; Report Type &nbsp;&nbsp;&nbsp;: &nbsp;</label>
					<div class="controls">
						{{ Form::select('report_type',array('3'=>'Purchase'), null, ['class' => 'span2']) }}
					</div> <!-- /controls -->	
				</div> <!-- /control-group -->
				<?php
				    $fromDate = '2018-02-01';
				    $toDate = date("Y-m-d");
				?>
				<div class="control-group" align="center">		
					<i class="icon-calendar"></i> Form : {{ Form::text('from', $fromDate, array('class' => 'input-small datepicker', 'id'=>'auto_search_item', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
					&nbsp;&nbsp;&nbsp;
					<i class="icon-calendar"></i> To   : {{ Form::text('to', $toDate, array('class' => 'input-small datepicker', 'id'=>'auto_search_item', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
				</div> <!-- /control-group -->	
				<center><input class="btn btn-info" type="submit" value="Search"></center>
				{{ Form::close() }}
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="span12" id="print_preview">
			<div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>View Transaction History: 
				  @if($report_type==1) {{ 'Point Withdraw' }}
					@elseif($report_type==2) {{ 'Point Increasing' }} 
					@else {{ 'Purchase' }} 
				  @endif
			  </h3>

			  <span style="float:right; margin:0 15px;"><button class='btn btn-info btn-small print-btn' onclick="window.print();">Print</button></span>
			</div>
			<div class="print">	
				<div class="payment-header">
					<strong>
						Customer Name : <span style="font-weight:normal; color: green;">{{ $get_customer->full_name }}</span>
					</strong>
					<strong style="float:right;">
						Report : <span style="font-weight:normal;">{{ Helper::onlyDMY($date_exp[0]) }}  <b>To: </b> {{ Helper::onlyDMY($date_exp[1]) }}</span>
					</strong>
				</div>
				@if($report_type==1)
				@else
				<table class="table table-striped table-bordered" width="100%">
					<thead class="table-head" >
						<tr>
							<th>SL</th>
							<th>Invoice ID</th>
							<th>Payment Type</th>
							<th>Sub Total Amount</th>
							<th>Discount (Tk)</th>
							<th>Payable</th>
							<th>Paid</th>
							<th>Due</th>
							<th>Sold By</th>
							<th>Sold At</th>
						</tr>
					</thead>
					<tbody>
				<? $i=0;$total_amount_taka=0;$total_discount=0;$total_point_use_taka=0;$total_payable=0;$total_pay=0;$total_due=0; //echo'<pre>';print_r($items);exit;?>
				@if($reports)
				   @foreach($reports as $invoice)
					   <tr>
							<td>{{++$i}}</td>
							<td>
								<a href="#saleDetailsModal" onclick="saleDetails({{$invoice->sale_invoice_id}})" data-toggle="modal">
									{{$invoice->sale_invoice_id}}
								</a>
							</td>
							<td>{{$invoice->payment_type_name}}</td>
							<td>{{$invoice->amount + $invoice->discount + $invoice->point_use_taka}}<? $total_amount_taka=$total_amount_taka+$invoice->amount; ?></td>
							<td>{{$invoice->discount}}<? $total_discount=$total_discount+$invoice->discount; ?></td>
							<td>{{$invoice->pay+$invoice->due}}<? $total_payable=$total_payable+$invoice->pay+$invoice->due ?></td>
							<td>{{$invoice->pay}}<? $total_pay=$total_pay+$invoice->pay; ?></td>
							<td>{{$invoice->due}}<? $total_due=$total_due+$invoice->due; ?></td>
							<td>{{$invoice->invoiced_employee}}</td>
							<td>{{Helper::dateFormat($invoice->invoiced_datetime)}}</td>
							
						</tr>				   
				   @endforeach              
                        <tr bgcolor="#DBEAF9">
						   <td colspan="4"><strong style="font-size: 1.3em;">Total<strong></td>
						   <td>{{$total_discount}}</td>
						   <td><strong>{{$total_payable}}</td>
                           <td><strong style="color: green;">{{$total_pay}}<strong></td>
						   <td colspan="5"><strong style="color: red;">{{$total_due}}<strong></td>   
					   </tr>
				@else
					<tr>
						<td colspan="10" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
			</tbody>
				</table>
				@endif
			</div>	
		</div>
	</div>
		<!--Sale Details Model-->
		<div id="saleDetailsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="saleDetailsLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="saleDetailsLabel"><i class="icon-zoom-in"></i>&nbsp; Sale Details</h3>
			</div>
			<div id="printable">
				<div class="modal-body print_modal" id="saleDetailsBody">
					<div id="loading">
						{{ HTML::image('img/loader.gif', 'Please Wait...')  }}
					</div>			
				</div>
			</div>	
		</div>				

	<script>
		function loadingImg(){
			$('#loading').ajaxStart(function() {
				$(this).show();
			}).ajaxComplete(function() {
				$(this).hide();
			});
		}
		function saleDetails(saleInvoiceId){
			$(function(){									
				loadingImg();
				$("#saleDetailsBody").load("{{ URL::to('admin/sale/saleReportDetails') }}"+"/"+saleInvoiceId);
			});
		}

	$('.Customers').addClass('active btn btn-fill');
		$(function(){  
			
			$('#amount').keyup(function(){
				this.value  = Math.abs(this.value);
				var this_value = parseFloat(this.value); 
				if(isNaN(this_value)){
					this.value = 0;
				}else{
					var total_due=parseFloat($("#due_amount").html());
					var now_pay=$("#amount").val();
					if(now_pay>total_due){
					alert("You can't pay more than Due");
					this.value=0;
				}
				}
			});
			
			var formCustomerPayment	 = $('#formCustomerPayment');
			// validate form for Item Brand
			formCustomerPayment.validate({
			  rules: {
			   amount: {
				   number: true,
				   required: true
				}
			  }, messages: {
					//'brand_name'	: { required:  '<span class="error">Brand Name required.</span>' },					
				},
				ignore				: ':hidden'	
			});
		});
	</script>
@stop
@section('stickyInfo')
<?php
    $string = 'Customers';
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