<?php 
//dd($customer_info);
//exit;
?>



                           
			{{ Form::open(array('route' => 'admin.updateCustomer.post', 'class' => 'form-horizontal')) }}
				

                                {{ Form::hidden('cus_id', $customer_info->cus_id) }}
                                <div class="control-group">
					{{ Form::label('customerType', 'Select Customre Type', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::select('cus_type_id', $cus_type, $customer_info->cus_type_id, ['class' => 'span3']) }}
					</div>
				</div> <!-- /control-group -->

				<div class="control-group">
					{{ Form::label('fullName', 'Full Name', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('full_name', $customer_info->full_name, array('class' => 'span3', 'id' => 'full_name', 'placeholder' => 'Enter full name')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('userName', 'User Name', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('user_name', $customer_info->user_name, array('class' => 'span3', 'id' => 'user_name', 'placeholder' => 'Enter user name')) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->


                                <div class="control-group">
					{{ Form::label('mobile', 'Mobile No', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('mobile', $customer_info->mobile, array('class' => 'span3', 'id' => 'mobile', 'placeholder' => 'Enter mobile no')) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('email', 'Email', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::email('email', $customer_info->email, array('class' => 'span3', 'id' => 'email', 'placeholder' => 'Enter email')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('nationalId', 'Barcode Id', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('national_id', $customer_info->national_id, array('class' => 'span3', 'id' => 'national_id', 'placeholder' => '')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('permanentAddress', 'Permanent Address', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::textarea('permanent_address', $customer_info->permanent_address, array('rows' =>'1','class' => 'span3', 'id' => 'permanent_address', 'placeholder' => '')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('presentAddress', 'Present Address', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::textarea('present_address', $customer_info->present_address,  array('rows' =>'1','class' => 'span3', 'id' => 'present_address', 'placeholder' => '')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
			
			<div class="modal-footer">
				
				{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
			</div>
			{{ Form::close() }}
