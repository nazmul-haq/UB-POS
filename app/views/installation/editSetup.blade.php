@extends('_layouts/default')

@section('content')
		<div class="row">
			<div class="span12">
				<div class="widget-header">
					<i class="icon-user"></i>
					<h3>Edit Sofware Settings</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">	
					<div class="row">
						<div class="span12">						
							@include('_sessionMessage')
							{{ Form::model($editInfo, array('route' => 'updateInstall.post', $editInfo->company_id, 'id' => 'validate_wizard', 'class' => 'stepy-wizzard form-horizontal')) }}	
								<fieldset title="Company info">
									<legend class="hide">Basic Information</legend>							
									{{ Form::hidden('company_id', $editInfo->company_id)}}
									<div class="formSep control-group">		
										{{ Form::label('id_company_name','Company Name', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('company_name', null, array('class' => 'span8', 'id' => 'id_company_name', 'placeholder' => 'Enter First Name')) }}
											{{$errors->first('company_name','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div>
									<div class="formSep control-group">		
										{{ Form::label('id_web_address','Web Address', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('web_address', null, array('class' => 'span8', 'id' => 'id_web_address', 'placeholder' => 'Enter Web Address')) }}
											{{$errors->first('web_address','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div>
									<div class="formSep control-group">		
										{{ Form::label('Address', 'Address', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('address', null, array('class' => 'span8', 'id' => 'Address', 'placeholder' => 'Enter address')) }}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->	
									<div class="formSep control-group">		
										{{ Form::label('id_mob_no','Mobile No', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('mobile', null, array('class' => 'span8', 'id' => 'id_mob_no', 'placeholder' => 'Enter Web Address')) }}
											{{$errors->first('mobile','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div>
								</fieldset>
								
								<fieldset title="Software Info">
									<legend class="hide">Software information</legend>
									
									<div class="formSep control-group">		
										{{ Form::label('id_discount','Maximum Discount(%)', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::text('max_inv_dis_percent', null, array('class' => 'span8', 'id' => 'id_discount', 'placeholder' => 'Enter Web Address')) }}
											{{$errors->first('max_inv_dis_percent','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div>
									<div class="formSep control-group">		
										{{ Form::label('back_date_entry','Back Date Entry', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::checkbox('back_date_sales',1,$editInfo->back_date_sales) }}
											  Sales 
											{{ Form::checkbox('back_date_purchase',1,$editInfo->back_date_purchase) }}
											  Purchase
											{{ Form::checkbox('back_date_return',1,$editInfo->back_date_return) }}
											 Product Return 
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->	
									<div class="formSep control-group">		
										{{ Form::label('id_language','Language', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::select('language', ['english' => 'English','bangla' => 'Bangla'],$editInfo->language, array('class' => 'span8')) }}
											{{$errors->first('language','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->	
									<div class="formSep control-group">		
										{{ Form::label('timezone','Time Zone', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::select('time_zone', ['asia/dhaka' => 'Bangladesh'],$editInfo->time_zone, array('class' => 'span8')) }}
											{{$errors->first('time_zone','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->	
									
									<div class="formSep control-group">		
										{{ Form::label('print_receipt','Print receipt', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::checkbox('print_recipt_a_sale',1,$editInfo->print_recipt_a_sale) }}
											Print Receipt after sale
											{{$errors->first('print_recipt_a_sale','<p class="error">:message</p>')}}
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->	
									<div class="formSep control-group">		
										{{ Form::label('theme','Software Theme', ['class' => 'control-label']) }}
										<div class="controls">
											{{ Form::select('theme', ['0' => 'Default','1' => 'Classic'],$editInfo->theme, array('class' => 'span8')) }}
											{{$errors->first('theme','<p class="error">:message</p>')}}
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
	

<!-- Jquery Validation -->
{{ HTML::script('js/wizard/jquery.stepy.min.js') }}
{{ HTML::script('js/wizard/gebo_wizard.js') }}	
@stop