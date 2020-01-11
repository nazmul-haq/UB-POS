
			{{ Form::open(array('route' => 'admin.updateSupplier.post', 'class' => 'form-horizontal')) }}

                            {{ Form::hidden('supp_id', $supplier_info->supp_id) }}

				<div class="control-group">
					{{ Form::label('supplierOrCompanyName', 'Supplier Name', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('supp_or_comp_name', $supplier_info->supp_or_comp_name, array('class' => 'span3', 'id' => 'supplier_company_name', 'placeholder' => 'supplier or company name')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('userName', 'User Name', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('user_name', $supplier_info->user_name, array('class' => 'span3', 'id' => 'user_name', 'placeholder' => 'Enter user name')) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->


                                <div class="control-group">
					{{ Form::label('mobile', 'Mobile No', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('mobile', $supplier_info->mobile, array('class' => 'span3', 'id' => 'mobile', 'placeholder' => 'Enter mobile no')) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('email', 'Email', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::email('email', $supplier_info->email, array('class' => 'span3', 'id' => 'email', 'placeholder' => 'Enter email')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->


                                <div class="control-group">
					{{ Form::label('permanentAddress', 'Permanent Address', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::textarea('permanent_address', $supplier_info->permanent_address, array('rows' =>'1', 'class' => 'span3', 'id' => 'permanent_address', 'placeholder' => '')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('presentAddress', 'Present Address', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::textarea('present_address', $supplier_info->present_address, array('rows' =>'1', 'class' => 'span3', 'id' => 'present_address', 'placeholder' => '')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                        <div class='modal-footer'>

                                 <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
                        </div>
                                {{ Form::close() }}

  