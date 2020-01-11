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
							{{ Form::open(array('route' => 'dbBackupRestore.post', 'id' => 'validate_wizard', 'class' => 'stepy-wizzard form-horizontal','files' => 'true')) }}
								<fieldset title="Personal info">
									<legend class="hide">Basic Information</legend>							
			
									<div class="formSep control-group">		
										{{ Form::label('f_name',Lang::get('common.first_name'), ['class' => 'control-label']) }}
										<div class="controls">
											<input id="f_name" type="file" name="dbFile" class="form-control">
										</div> <!-- /controls -->					
									</div> <!-- /control-group -->							
								</fieldset>
								
								<button type="submit" class="finish btn btn-primary"><i class="icon-ok"></i> Save Process</button>
							{{ Form::close()}}
						</div>
					</div>
				</div> <!-- /widget-content -->
						
			</div>
		</div>   
		
@stop