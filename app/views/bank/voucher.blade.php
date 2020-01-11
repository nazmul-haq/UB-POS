@extends('_layouts.default')

@section('content')
	<style>
		.head8{width: 212px}
	</style>
	<div class="row">
	  	<div class="span12">
			@include('_sessionMessage')
			<div class="employee-btn">	
				<a href="#addCustomer" role="button" data-toggle="modal"><button class="btn btn-info"><i class="icon-user"></i>&nbsp; Add New Bank Deposit Voucher</button></a>
			</div>			
	  	</div>
	  	<div class="span12">
			<div class="invoice-reg print_disable">
		        {{ Form::open(array('route' => 'admin.bank.deposit.getBankDeposit', 'class' => 'form-horizontal search-form')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary search-btn" type="button" value="Search">
					</div> <!-- /control-group -->
		        {{ Form::close() }}
	    	</div>
	    	{{ Datatable::table()
				->addColumn('Date','Bank Name','Branch Name', 'Voucher No', 'Amount Debit','Amount Credit','Action')
				->setUrl(route('admin.bank.deposit.datatable',[$from,$to]))   
				->render() }}
			@if(count($total_amount_credit) && count($total_amount_debit))
			<table class="table table-hover table-bordered">
				<tr>
					<td style="width: 625px; text-align: right; font-size: 18px;"> Total Amount:</td>
					<td style="width: 155px; font-size: 18px; color: green;">{{$total_amount_debit}}</td>
					<td style="width: 155px; font-size: 18px; color: red;">{{$total_amount_credit}}</td>
					<td> </td>
				</tr>
			</table>
			@endif
	  	</div>
	</div>

	<!--Add New Customer Modal-->
	<div id="addCustomer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addCusModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="addCusModalLabel"><i class="icon-user"></i>&nbsp; Add New Deposit Voucher</h3>
		</div>
			{{ Form::open(array('route' => 'admin.bank.deposit.post', 'class' => 'form-horizontal')) }}
			<div class="modal-body"> 
				<div class="control-group">	
					{{ Form::label('customerType', 'Select Bank', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::select('bank_id', $banks, 1, ['class' => 'span3']) }} *
					</div>							
				</div> <!-- /control-group -->
                                
				<div class="control-group">		
					{{ Form::label('fullName', 'Branch Name', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('bank_branch_name', null, array('class' => 'span3', 'id' => 'full_name', 'placeholder' => '')) }} *
					</div> <!-- /controls -->					
				</div> <!-- /control-group -->

                <div class="control-group">
					{{ Form::label('userName', 'Type', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::select('type',['1' => 'Debit', '2' => 'Credit'],1, ['class' => 'span3']) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
				<div class="control-group">
					{{ Form::label('userName', 'Voucher No', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('voucher_no', null, array('class' => 'span3', 'id' => 'user_name', 'placeholder' => '')) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
                                
                <div class="control-group">
					{{ Form::label('mobile', 'Date', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('date', null, array('class' => 'span3 datepicker', 'id' => 'mobile', 'placeholder' => ' ','data-date-format' => 'yyyy-mm-dd')) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                <div class="control-group">
					{{ Form::label('email', 'Deposit Amount', ['class' => 'control-label']) }} *
					<div class="controls">
						{{ Form::text('amount', null, array('class' => 'span3', 'id' => 'email', 'placeholder' => ' ')) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
			</div>
			{{ Form::close() }}
	</div>

	<div id="editCustomer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="editSupplierLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="editSupplierLabel"><i class="icon-user"></i>&nbsp;Edit Bank Deposit Voucher</h3>
		</div>
		<div class="modal-body" id="updateVoucher"></div>
	</div>

    <script>
    	$('.search-btn').on('click',function(e){
    		e.preventDefault();
    		$('.search-form').submit();
    	})
		function updateVoucher(voucher_id){
			$(function() {
				$("#updateVoucher").load("{{ URL::to('admin/bankDepositEdit') }}"+"/"+voucher_id);
			});
		}

		function deleteConfirm(cus_id){
			var con=confirm("Do you want to delete?");
			if(con){
				$().ready(function(){
					$.ajax({
						url: "bankDepositDelete/"+cus_id,
						success : function(data){
							$("#"+cus_id).prev().parent().parent().fadeOut("slow");
						}
					});
				});
				return true;
			}
			else{
				return false;
			}
		}
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
@if(Session::has('redTheme'))
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #71253a;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@else
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@endif
@stop