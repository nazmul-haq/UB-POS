@extends('_layouts/default')

@section('content')

		<div class="row">
			<div class="span12">
				<div class="widget-header">
					<i class="icon-user"></i>
					<h3>Your Account</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">	
					<div class="row">
						<div class="span12">
							@include('_sessionMessage')
							{{ Form::open(array('route' => 'admin.saveUser.post', 'id' => 'validate_wizard', 'class' => 'stepy-wizzard form-horizontal')) }}	
								<fieldset title="Personal info">
									<legend class="hide">Basic Information</legend>							
			
									<div class="formSep control-group">		
										{{ Form::label('f_name',Lang::get('common.first_name'), ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('f_name', null, array('class' => 'span8', 'id' => 'v_f_name', 'placeholder' => 'Enter First Name')) }}
											{{$errors->first('f_name','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->							
			
									<div class="formSep control-group">		
										{{ Form::label('l_name', Lang::get('common.last_name'), ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('l_name', null, array('class' => 'span8', 'id' => 'v_l_name', 'placeholder' => 'Enter Last Name')) }}
											{{$errors->first('l_name','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->							
			
									<div class="formSep control-group">		
										{{ Form::label('father_name',Lang::get('common.father_name'), ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('father_name', null, array('class' => 'span8', 'id' => 'v_father_name', 'placeholder' => 'Enter Father Name')) }}
											{{$errors->first('father_name','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->								
			
									<div class="formSep control-group">		
										{{ Form::label('mother_name', 'Mother Name', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('mother_name', null, array('class' => 'span8', 'id' => 'v_mother_name', 'placeholder' => 'Enter Mother Name')) }}
											{{$errors->first('mother_name','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->							
			
									<div class="formSep control-group">		
										{{ Form::label('user_name', 'Username', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('user_name', null, array('class' => 'span8', 'id' => 'v_username', 'placeholder' => 'Enter Username')) }}
											{{$errors->first('user_name','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->						
			
									<div class="formSep control-group">		
										{{ Form::label('password', 'password', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::password('password', array('class' => 'span8', 'id' => 'v_password', 'placeholder' => 'Enter Password')) }}
											{{$errors->first('password','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->						
			
									<div class="formSep control-group">		
										{{ Form::label('mobile', 'Mobile', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('mobile', null, array('class' => 'span8', 'id' => 'v_mobile', 'placeholder' => 'Enter Mobile')) }}
											{{$errors->first('mobile','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->						
			
									<div class="formSep control-group">		
										{{ Form::label('email', 'Email', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::email('email', null, array('class' => 'span8', 'id' => 'v_email', 'placeholder' => 'Enter Email')) }}
											{{$errors->first('email','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->
									
								</fieldset>
								
								<fieldset title="Contact info">
									<legend class="hide">Lorem ipsum dolor�</legend>
									
									<div class="formSep control-group">		
										{{ Form::label('permanent_address', 'Permanent Address', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::textarea('permanent_address', null, ['size' => '30x5'], array('class' => 'span8', 'id' => 'v_message', 'placeholder' => 'Enter Username')) }}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->	
									
									<div class="formSep control-group">		
										{{ Form::label('present_address', 'Present Address', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::textarea('present_address', null, ['size' => '30x5'], array('class' => 'span8', 'id' => 'v_message', 'placeholder' => 'Enter Username')) }}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->	
									
								</fieldset>
								<fieldset title="Additional info">
									<legend class="hide">Lorem ipsum dolor�</legend>
									
									<div class="formSep control-group">		
										{{ Form::label('national_id', 'National ID', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('national_id', null, array('class' => 'span8', 'id' => 'v_national_id', 'placeholder' => 'Enter Mobile')) }}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->	
									
									<div class="formSep control-group">		
										{{ Form::label('fixed_salary', 'Fixed Salary', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('fixed_salary', null, array('class' => 'span8', 'id' => 'v_fixed_salary', 'placeholder' => 'Enter Mobile')) }}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->	
									
									
								</fieldset>
								<button type="button" class="finish btn btn-primary"><i class="icon-ok"></i> Save Process</button>
							{{ Form::close()}}
						</div>
					</div>
				</div> <!-- /widget-content -->
						
			</div>
		</div>   
		
@stop