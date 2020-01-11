@extends('_layouts.default')
@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'sale.viewSaleReport', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>

		@if(isset($reports))
		<table class="table table-bordered" id="datatable">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
			
                            <strong style="font-size: 2em;"><i class="icon-credit-card"></i> Sales Report :</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{Helper::onlyDMY($to)}}</span></strong>
			</article>
			<thead>
				<tr>
					<th>SL</th>
					<th>Invoice ID</th>
					<th>Customer Name</th>
					<th>Sub Total Amount</th>
					<th>Discount (Tk)</th>
					<th>Payable</th>
					<th>Paid</th>
					<th>Due</th>
                    <th>Profit</th>
					<th>Sold By</th>
					<th>Sold At</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<? $i=0;$total_amount_taka=0;$total_discount=0;$total_point_use_taka=0;$total_payable=0;$total_pay=0;$total_due=0;$total_profit=0?>
				@if($reports)
				   @foreach($reports as $invoice)
					   <tr>
							<td>{{++$i}}</td>
							<td>{{$invoice->sale_invoice_id}}</td>
							<td>{{$invoice->customer_name}}</td>
							<td>{{$invoice->amount + $invoice->discount + $invoice->point_use_taka}}<? $total_amount_taka=$total_amount_taka+$invoice->amount; ?></td>
							<td>{{$invoice->discount}}<? $total_discount=$total_discount+$invoice->discount; ?></td>
							<td>{{$invoice->pay+$invoice->due}}<? $total_payable=$total_payable+$invoice->pay+$invoice->due ?></td>
							<td>{{$invoice->pay}}<? $total_pay=$total_pay+$invoice->pay; ?></td>
							<td>{{$invoice->due}}<? $total_due=$total_due+$invoice->due; ?></td>
                                
                                @if(Session::get('role')==2)
                                <td>
                                {{$invoice->profit }}<? $total_profit += $invoice->profit; ?>
                                </td>
								@endif
                                
                                <td>{{$invoice->invoiced_employee}}</td>
							<td>{{$invoice->invoiced_datetime}}</td>
							<td width="80px"><a href="#saleDetailsModal" onclick="saleDetails({{$invoice->sale_invoice_id}})" class="btn btn-warning btn-small" role="button" data-toggle="modal"><i class="icon-zoom-in"></i> Details</a></td>
						</tr>				   
				   @endforeach              
                                 
				@else
					<tr>
						<td colspan="12" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
                               
			</tbody>
			<tfoot>
				  <tr style=" background:#DBEAF9; font-size: 15px;">
						   <td colspan="3"><strong style="font-size: 1.1em;">Total<strong></td>
						   <td>{{$total_amount_taka+$total_discount+$total_point_use_taka}}</td>
						   <td>{{$total_discount}}</td>
						   <td><strong>{{$total_payable}}</td>
                           <td><strong style="color: green;">{{$total_pay}}<strong></td>
						   <td><strong style="color: red;">{{$total_due}}<strong></td>   
                           <td><strong style="color: #5bc0de;">{{ round($total_profit,2)}}<strong></td>   
                           <td colspan="3"></td>  
                                                             
					   </tr>
			</tfoot>
		</table>
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
					<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
				</div>
			</div>	
		</div>	
		
		@endif
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
		
		
	
		/* function printDiv(printable) {
			 var printContents = document.getElementById(printable).innerHTML;			 
			 var originalContents = $('body').html();
			 document.body.innerHTML = printContents;
			 window.print();
			 document.body.innerHTML = originalContents;
			 //$("body").load();
		}  */
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
		$("#datatable").DataTable();
	</script>
@stop