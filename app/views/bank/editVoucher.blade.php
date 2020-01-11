{{ Form::open(array('route' => 'admin.bank.deposit.edit.post', 'class' => 'form-horizontal')) }}
	{{ Form::hidden('bank_deposit_id', $voucher->id) }}
	<div class="control-group">	
		{{ Form::label('customerType', 'Select Bank', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::select('bank_id', $banks, $voucher->bank_id, ['class' => 'span3']) }}  *
		</div>							
	</div> <!-- /control-group -->
                    
	<div class="control-group">		
		{{ Form::label('fullName', 'Branch Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('bank_branch_name', $voucher->bank_branch_name, array('class' => 'span3', 'id' => 'full_name', 'placeholder' => '')) }}  *
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->

                    <div class="control-group">
		{{ Form::label('userName', 'Voucher No', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('voucher_no', $voucher->voucher_no, array('class' => 'span3', 'id' => 'user_name', 'placeholder' => '')) }} *
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
                    

                    <div class="control-group">
		{{ Form::label('mobile', 'Date', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('date', $voucher->date, array('class' => 'span3 datepicker', 'id' => 'mobile', 'placeholder' => ' ','data-date-format' => 'yyyy-mm-dd')) }} *
		</div> <!-- /controls -->
	</div> <!-- /control-group -->

    <div class="control-group">
			{{ Form::label('email', 'Deposit Amount', ['class' => 'control-label']) }}
			<div class="controls">
				{{ Form::text('amount', $voucher->amount, array('class' => 'span3', 'id' => 'email', 'placeholder' => ' ')) }}  *
			</div> <!-- /controls -->
		</div> <!-- /control-group -->
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
	</div>
{{ Form::close() }}
