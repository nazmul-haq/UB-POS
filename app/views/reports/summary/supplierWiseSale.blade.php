@extends('_layouts.default')

@section('content')
<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'spplierWiseSale.report', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
					</div> <!-- /control-group -->
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Supplier :
						<select class="controls" name="supplier_id" style="margin-left:-2px !important">
						<option>Select Supplier</option>
						@if(isset($suppliers))
							@foreach($suppliers as $supplier)
							<option value="{{$supplier->supp_id}}">{{$supplier->supp_or_comp_name}} ({{$supplier->user_name}})</option>
							@endforeach
						@endif
						</select>
						&nbsp;&nbsp;&nbsp;
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>

            <article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 1.5em;"><i class="icon-credit-card"></i> Summary:: Supplier Wise Sale Report</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			@if(isset($totalValue[0]))
			<!-- <div class="row">
				<div class="offset3 span6"> 
					<div class="widget-content" id="divContent" style="padding:10px;">
						<table id="paymentStatus">
							<tbody>
								<tr>
									<td><strong><i class="icon-circle"></i> Discount </strong></td>
									<td>&nbsp;:&nbsp;</td>
									<td><span class='label label-success' style="font-size: 1em; line-height: 25px;">{{$totalValue[0]->totalDiscount}}</span></td>
									<td style="padding-left:40px"><strong><i class="icon-circle"></i> Amount </strong></td>
									<td>&nbsp;:&nbsp;</td>
									<td style="padding-right:20px"><span class='label label-primary'style="font-size: 1em; line-height: 25px;">{{$totalValue[0]->totalAmount}}</span></td>
									<td><strong><i class="icon-circle"></i> Paid </strong></td>
									<td>&nbsp;:&nbsp;</td>
									<td style="padding-right:20px"><span class='label label-success' style="font-size: 1em; line-height: 25px;">{{$totalValue[0]->totalPay}}</span></td>
									<td><strong><i class="icon-circle"></i> Due </strong></td>
									<td>&nbsp;:&nbsp;</td>
									<td><span class='label label-danger' style="font-size: 1em; line-height: 25px;">{{$totalValue[0]->totalDue}}</span></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div> -->
			@endif
            {{ Datatable::table()
				->addColumn('Invoice Id','Supplier Name','Date','Amount','Paid','Discount','Due')
				->setUrl(url("summary/getspplierWiseSaleData/$from/$to/$supplierId"))
				->render()
			}}
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
	function saleDetails(saleInvoiceId,supplierId){
			$(function(){									
				$("#saleDetailsBody").load("{{ URL::to('admin/sale/saleReportDetailsSuppWise') }}"+"/"+saleInvoiceId+"/"+supplierId);
			});
		}
</script>
@stop