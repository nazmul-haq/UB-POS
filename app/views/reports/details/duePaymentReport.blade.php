@extends('_layouts.default')
@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'viewDuePaymentReport.report', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>
		@if(count($reports) > 0)
		<table class="table table-bordered" id="datatable">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
                            <strong style="font-size: 2em;"><i class="icon-credit-card"></i> Due Payment Report :</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{Helper::onlyDMY($to)}}</span></strong>
			</article>
				<thead>
					<tr>
						<th>SL</th>
						<th>Customer Name</th>
						<th>Total Amount</th>
						<th>Discount (Tk)</th>
						<th>Received By</th>
						<th>Received Date</th>
						<th>Action</th>
					</tr>
				</thead>
			<tbody>
				@if(count($reports)>0)
				<?php $totalAmount = $totalDiscount = 0; ?>
				   @foreach($reports as $key => $report)
					   <tr>
							<td>{{++$key}}</td>
							<td>{{$report->full_name}}</td>
							<td>{{$report->amount}}</td>
							<td>{{$report->discount_amount}}</td>
							<td>{{$report->f_name}} {{$report->l_name}}</td>
							<td>{{date('d F, Y h:i a', strtotime($report->created_at))}}</td>
							<td><?php 
									$totalAmount += $report->amount;
									$totalDiscount += $report->discount_amount;
								?>
							</td>
						</tr>				   
				    @endforeach              
                	@else
						<tr>
							<td colspan="12" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
						</tr>
					@endif
			</tbody>
			<input type="hidden" name="index" value="{{++$key}}">
			<tfoot>
				<tr>
					<th>SL</th>
					<th>Customer Name</th>
					<th>Total Amount</th>
					<th>Discount (Tk)</th>
					<th>Received By</th>
					<th>Received At</th>
					<th>Action</th>
				</tr>
				<tr style="background-color: white !important;">
					<td></td>
					<td style="text-align: right;"> Total Amount</td>
					<td> {{$totalAmount}} TK</td>
					<td colspan="3">Total Discont : {{$totalDiscount}} TK</td>
					<td></td>
				</tr>
			</tfoot>
		</table>
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
		$("#checkAll").on('change', function(){
           $("input:checkbox").prop('checked', $(this).prop("checked"));
       	});
		// $("#deleteAll").on('click', function(){
  //     		$("#submitForm").submit();
  //   	});
	</script>
@stop