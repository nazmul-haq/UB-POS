@extends('_layouts.default')

@section('content')
            
	<div class="row">
		<div class="span8">
			<div class="invoice-reg">
				{{ Form::open(array('route' => 'pointIncrease.PointIncreasingReport', 'id' => 'customerForm', 'class' => 'form-horizontal')) }}
						<div class="control-group">
                                                    {{ Form::label('serchCustomer', 'Find Customer', ['class' => 'control-label', 'style' => 'font-weight: bold;']) }}
							<div class="controls">
								{{ Form::text('cus_id', null, array('class' => 'span6', 'id'=>'customerAutoSugg', 'placeholder' => 'Start Typing customer name')) }}
                                                        </div> <!-- /controls -->
                                                </div>
						

				{{ Form::close() }}
			</div>
    @if($reports)
    @foreach($reports as $report)
    @endforeach
    <strong>Customer Name:</strong> &nbsp;{{$report->user_name}}
    @endif
    
    
			<table class="table table-striped" width="100%">
				<thead class="table-head">
					<tr>
						<th># SL No</th>
						<th>Invoice Id</th>
						<th>Invoice Amount</th>
						<th>Increased Point</th>
                        <th>Date</th>
                    </tr>
				</thead>
				<tbody>
					<?php $i=0; ?>
					@if($reports)
						@foreach($reports as $report)
							<tr>
								<td>{{++$i}}</td>
								<td>
									<span class="span3">
										{{$report->sale_invoice_id}}
                                    </span>
								</td>

								<td>
                                      <span class="span3">
										{{$report->amount}}
                                      </span>
                                  </td>
								<td>
                                                                        <span class="span3">
										{{$report->no_of_point}}
                                                                        </span>
                                                                </td>
                                                                <td>
                                                                        <span class="span3">
										{{$report->date}}
                                                                        </span>
                                                                </td>
							</tr>
							@endforeach
						@endif
                                                
				</tbody>
			</table>
		</div>
		<!--Supplier-->
		
	</div>
	





	
	{{ HTML::script('js/jquery-ui.min.js') }}
	<script>
		 $().ready(function(){
			 //Auto Complete for Item Search
			 $("#auto_search_item").autocomplete("{{route('sale.itemAutoSuggest')}}", {
				width: 260,
				matchContains: true,
				queryDelay : 0,
				formatItem: function(row) {
					return row[1];
				},
			});
			//Submit Search Item Form
			$("#auto_search_item").result(function(event, data, formatted) {
				$("#formItemLocation").submit();
			});

			//Customer auto suggest
			$("#customerAutoSugg").autocomplete("{{route('sale.autoCustomerSuggest')}}", {
				width: 260,
				matchContains: true,
				queryDelay : 0,
				formatItem: function(row) {
					return row[1];
				}
			});
			//Submit Supplier Form
			$("#customerAutoSugg").result(function(event, data, formatted) {
				$("#customerForm").submit();
			});


			//for input box tooltip
			$('input[type=text][name=discount_percent]').tooltip({
				placement: "right",
				trigger: "hover"
			});
		});
       

	</script>



@stop