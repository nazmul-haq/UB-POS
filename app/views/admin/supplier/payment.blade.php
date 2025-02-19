@extends('_layouts.default')

@section('content')
	<div class="row print_disable">
		<div class="span12">@include('_sessionMessage')</div>
		<div class="span8">				
			<div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>Payment</h3>
            </div>
			{{ Form::open(array('route' => 'supplier.saveSupplierPayment.post', 'id' => 'formSupllierPayment', 'class' => 'form-horizontal')) }}
					{{ Form::hidden('supp_id', $get_supplier->supp_id)}}
					<table class="table table-striped" style="margin: 0; padding:0;">
						<tbody>
							 <tr>
								<td><strong style="float:right; margin-right:50px;">Supplier Name </strong></td>
								<td style="padding:0;"><strong style="color: green; font-size: 1.0em;">{{$get_supplier->supp_or_comp_name}}</strong></td>
							</tr>
							 <tr>
								<td>
									<strong style="float:right; margin-right:50px;">
										Balance @if($get_supplier->due >= 0) {{ ' Due' }} @else {{ 'Advanced' }} @endif 
									</strong>
								</td>
								<td style="padding:0;"><strong style="color: red; font-size: 1.0em;" id='due_amount'>{{ abs($get_supplier->due) }}</strong></td>
							</tr>
							<tr>
								<td><strong style="float:right; margin-right:50px;">Payment Type </strong></td>
								<td style="padding:0;">
									{{ Form::select('payment_type_id', $payment_type, 1, ['class' => 'span3']) }}
								</td>
							</tr>
							<tr>
								<td><strong style="float:right; margin-right:50px;">Now Payment </strong></td>
								<td style="padding:0;">
									<input type="text" id="amount" class="span3" name="amount">
								</td>
							</tr>
							<tr>
								<td></td>
								<td style="padding: 10px 0;">
									<button type="submit" class="btn btn-info">Save Changes</button>
								</td>
							</tr>
						</tbody>
					</table>
					{{ Form::close() }}
			
		</div>
		<!--Supplier--->
		<div class="span4">
			<div class="search-box">
				<div class="widget-header setup-title"> <i class="icon-search"></i>
				  <h3>Search Transaction Payment </h3>
				</div>	
				{{ Form::open(array('url' => "admin/supplier/searchPaymentTransaction/$get_supplier->supp_id", 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">		
						<i class="icon-calendar"></i> Form : {{ Form::text('from', null, array('class' => 'input-small datepicker', 'id'=>'auto_search_item', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i> To 	 : {{ Form::text('to', null, array('class' => 'input-small datepicker', 'id'=>'auto_search_item', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
					</div> <!-- /control-group -->	
					<center><input class="btn btn-info" type="submit" value="Search"></center>
				{{ Form::close() }}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span12" id="print_preview">
			<div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>View Transaction History</h3>
			  <span style="float:right; margin:0 15px;"><button class='btn btn-info btn-small print-btn' onclick="window.print();">Print</button></span>
			</div>
			<div class="print">	
				<div class="payment-header">
					<strong>
						Company Name : <span style="font-weight:normal; color: green;">{{$get_company_infos->company_name}}</span>
					</strong>
					<strong style="float:right;">
						Report : <span style="font-weight:normal;">{{ $date_exp[0] }} <b>To</b> {{ $date_exp[1] }}</span>
					</strong>
				</div>
				<table class="table table-striped" width="100%">
					<thead class="table-head">
						<tr>
							<th>Payment Date</th>
							<th>Amount</th>
							<th>Received by</th>
							<th>Payment Way</th>
						</tr>
					</thead>
					<tbody>
						@if(!empty($get_transaction_infos))
							@foreach($get_transaction_infos as $transaction_info)
							<? 
								$date = $transaction_info->pay_date; 
								$pay_date = date('d-m-Y', strtotime($date));
							?>
							<tr>
								<td>{{$pay_date}}</td>
								<td>{{$transaction_info->amount}}</td>
								<td>{{$transaction_info->user_name}}</td>
								<td>{{$transaction_info->payment_type_name}}</td>
							</tr>
							@endforeach	
							
						@else
							<tr>
								<td colspan="3" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
							</tr>						
						@endif
					</tbody>
				</table>
				<div style="float:right;">{{$get_transaction_infos->links()}}</div>
			</div>	
		</div>
	</div>
							

	<script>
		$(function(){ 			
			$('#amount').keyup(function(){
				var total_due = parseFloat($("#due_amount").html());
				var now_pay = parseFloat($("#amount").val());
				this.value  = Math.abs(this.value);
				$this_value = parseFloat(this.value); 
				if($('#amount').val() == 0){
					this.value = '';
					return false;
				}
				if(isNaN($this_value)){
					this.value = '';
				}
				if(now_pay > total_due){
					alert("You can't pay more than Due");
					this.value = total_due;
				}
			});
			
			var formSupllierPayment	 = $('#formSupllierPayment');
			// validate form for Item Brand
			formSupllierPayment.validate({
			  rules: {
			   amount: {
				   number: true,
				   min : 1,
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