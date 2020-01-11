@extends('_layouts/default')

@section('content')
	<div class="row">
		<div class="span12">
			<div class="widget-header">
				<i class="icon-unlock"></i>
				<h3>URL Permission For Employee</h3>
			</div> <!-- /widget-header -->

			<div class="widget-content">
				<div class="row">
					<div class="span12">
						@include('_sessionMessage')
						{{ Form::open(array('route' => 'admin.viewEmpUrlPermission.post', 'class' => 'form-horizontal')) }}
							<div class="control-group">		
								<label for="username" class="control-label"><i class="icon-user"></i>&nbsp; User Name &nbsp;: &nbsp;</label>
								<div class="controls">
									{{ Form::select('emp_id', $getEmps, null,  array('class' => 'span3', 'required' => 'required')) }}
								</div> <!-- /controls -->					
							</div> <!-- /control-group -->	

							<div style="padding-left: 160px;">
								<button type="reset" class="btn btn-default"><i class="icon-undo"></i>&nbsp;Reset</button>&nbsp;
								<button type="submit" class="btn btn-info"><i class="icon-arrow-right "></i>&nbsp;Next</button>
							</div>
						{{ Form::close()}}
					</div>
				</div>
			</div> <!-- /widget-content -->

		</div>
	</div>

@stop