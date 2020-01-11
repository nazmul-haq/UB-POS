@extends('_layouts.default')

@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'itemWisePurchaseReport.report', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'id' => 'from_date', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'id' => 'to_date', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 1.5em;"><i class="icon-credit-card"></i> Summary:: Item Wise Purchase Report</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			<div class="row">
				<div class="offset3 span5"> 
					<div class="widget-content" style="padding:10px;">
						<table id="paymentStatus" align="center">
							<tbody>
								<tr>
									<td><strong><i class="icon-circle"></i> Sales </strong></td>
									<td>&nbsp;:&nbsp;</td>
									<td style="padding-right:20px"><span class='label label-danger' id="totalSale" style="font-size: 1em; line-height: 25px;"> 0.0 </span></td>
									<td><strong><i class="icon-circle"></i> Profit / Loss </strong></td>
									<td>&nbsp;:&nbsp;</td>
									<td><span class='label label-success' id="totalProfitOrLoss" style="font-size: 1em; line-height: 25px;"> 0.0 </span></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>	
			<table id="datatable" class="display table table-bordered" cellspacing="0" width="100%">
		        <thead>
		            <tr>
						<th>#SL No.</th>
						<th>Invoice ID</th>
						<th>Product Name</th>
						<th>Suplier Name</th>
						<th>Discount</th>
						<th>Total</th>
						<th>Purchase By</th>
		            </tr>
		        </thead>
		        <tfoot>
					<tr>
						<td colspan="4" align="right">
							<strong class='label label-info pull-right' style="background-color:white;font-size: 1em; color: black;line-height:22px">Total</strong>
						</td>
						<td>
							<strong class='label' style="background-color:white;margin-left:-10px;font-size: 1em; color: black;" id="offsetDiscount"> 0.0 </strong>
						</td>
						<td>
							<strong class='label' style="background-color:white;margin-left:-10px;font-size: 1em; color: black;" id="offsetTotalAmount"> 0.0 </strong>
						</td>
						<td></td>
					</tr>
				</tfoot>
		    </table>
	  </div>
	  </div>
	</div>

	<script type="text/javascript">
	$(document).ready(function() {
		var from_date = $("#from_date").val();
		var to_date = $("#to_date").val();
		loadDatatable(from_date , to_date)
	});

	function loadDatatable(from_date , to_date){
		var t = $('#datatable').DataTable( {
	        "ajax": "{{URL::to('summary/itemWisePurchaseReportJsonFormat')}}/"+from_date+"/"+to_date,
	        "columnDefs": [ {
	            "searchable": false,
	            "orderable": false,
	            "targets": 0
	        } ],
	        "order": [[ 1, 'asc' ]],
	        "columns": [
	            { "data": "sup_invoice_id" },
	            { "data": "sup_invoice_id" },
	            { "data": "item_name" },
	            { "data": "supp_or_comp_name" },
	            { "data": "discount" },
	            { "data": "amount" },
	            { "data": "user_name" }
	        ],
	        "footerCallback": function (row, data, start, end) {
		        	$('body').append('<div id="dataTableCal"></div>');
		        	$("#dataTableCal").empty();
		        	var api = this.api(), data;
		            columnTotalValue(row, data, start, end, api,4,"offsetDiscount");
		            columnTotalValue(row, data, start, end, api,5,"offsetTotalAmount");
		        }
		    });
		 	t.on( 'order.dt search.dt', function () {
		        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
		            cell.innerHTML = i+1;
		            $("#totalSale").html(parseFloat($("#offsetDiscountTotal").html()).toFixed(2));
		    		$("#totalProfitOrLoss").html(parseFloat($("#offsetTotalAmountTotal").html()).toFixed(2));
		        } );
	    	} ).draw();

		function columnTotalValue(row, data, start, end, api,columnNumber,placementId){
	    // Remove the formatting to get integer data for summation
	    var intVal = function ( i ) {
	        return typeof i === 'string' ?
	            i.replace(/[\$,]/g, '')*1 :
	            typeof i === 'number' ?
	                i : 0;
	    };
	    // Total over all pages
	    total = api
	        .column( columnNumber )
	        .data()
	        .reduce( function (a, b) {
	            return intVal(a) + intVal(b);
	        }, 0 );

	    // Total over this page
	    pageTotal = api
	        .column( columnNumber, { page: 'current'} )
	        .data()
	        .reduce( function (a, b) {
	            return intVal(a) + intVal(b);
	        }, 0 );
		$("#"+placementId).html(pageTotal.toFixed(2));
	    $("#dataTableCal").append('<span style="display:none" id="'+placementId+'Total">'+total+'</span>');
		}
	}
	</script>
@stop