@extends('_layouts.default')
@section('content')
	<div class="row">
	 <div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'sale.viewSaleOrderReport', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', date('Y-m-d'), array('class' => 'span2 datepicker', 'id'=>'auto_search_item', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', date('Y-m-d'), array('class' => 'span2 datepicker', 'id'=>'auto_search_item', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>
		@if(isset($reports))
		<table class="table table-striped" id="tableOrder">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 2em;"><i class="icon-shopping-cart"></i> Sale Order Reports </strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{Helper::onlyDMY($to)}}</span></strong>
			</article>
			<thead class="table-head">
				<tr>
					<th>SL</th>
					<th>Invoice ID</th>
					<th>Customer Name</th>
					<th>Sub Total Amount</th>
                    <th>Total</th>
                    <th>Sold By</th>
                    <th>Sold At</th>
                    <th>Action</th>
				</tr>
			</thead>
			<tbody>
				<? $i=0;$total_amount_taka=0;$sub_total_amount_taka=0;$total_discount=0;$total_payable=0;$total_pay=0;$total_due=0; //echo'<pre>';print_r($items);exit;?>
				@if($reports)
				   @foreach($reports as $invoice)
					   <tr>
						   <td>{{++$i}}</td>
							<td>
								<a href="#saleDetailsModal" onclick="saleDetails({{ $invoice->sale_order_invoice_id }})" data-toggle="modal">{{ $invoice->sale_order_invoice_id }}</a>
							</td>
						   <td>{{$invoice->customer_name}}</td>
						   <td>{{$invoice->amount}}
						   		<?
						   		$total_amount_taka += $invoice->amount; 
						   		?>
						   </td>
                           <td>
                           		{{$invoice->amount}}
                       		</td>
						   <td>{{$invoice->invoice_by}}</td>
                           <td>{{$invoice->invoice_time}}</td>
                           <td>
	                           	<a href="{{URL::to('sale/deleteSaleOrder')}}/{{$invoice->sale_order_invoice_id}}" class="btn btn-danger btn-xs" onclick="return confirm('Do you want to delete ?')">
	                           		Delete
	                           	</a>
                           </td>
					   </tr>
				   @endforeach
                        <tr bgcolor="#DBEAF9">
						   <td colspan="3"><strong style="font-size: 1.3em;">Total<strong></td>
						   <td><strong><span class="badge-1">{{$total_amount_taka}}</span><strong></td>
						   <td><strong>{{$total_amount_taka}}</td>
						   <td colspan="2"></td>
					   </tr>
				@else
					<tr>
						<td colspan="10" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
			</tbody>
		</table>
		</div>
	  </div>
		<!--Purchase Details Model-->
		
		<div id="saleDetailsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="saleDetailsLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="saleDetailsLabel"><i class="icon-zoom-in"></i>&nbsp; 	Details</h3>
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
		function loadingImg(){
			$('#loading').ajaxStart(function() {
				$(this).show();
			}).ajaxComplete(function() {
				$(this).hide();
			});
		}
		function saleDetails(saleOrderInvoiceId){
			$(function(){									
				loadingImg();
				$("#saleDetailsBody").load("{{ URL::to('admin/sale/saleOrderDetailsReport') }}"+"/"+saleOrderInvoiceId);
			});
		}
	</script>
@stop