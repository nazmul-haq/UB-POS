@extends('_layouts/default')

@section('content')

		<div class="row">
			<div class="span12">
				<div class="widget-header">
					<i class="icon-unlock"></i>
					<h3>Permission For Employee</h3>
				</div> <!-- /widget-header -->

				<div class="widget-content">
					<div class="row">
						<div class="span12">
							@include('_sessionMessage')
							{{ Form::open(array('id' => 'perSubmit', 'class' => 'form-horizontal')) }}
								<div class="control-group">
									<label for="username" class="control-label"><i class="icon-user"></i>&nbsp; User Name : &nbsp;</label>
									<div class="controls">
										{{ Form::select('emp_id', $getEmps, null,  array('class' => 'span4', 'required' => 'required')) }}
									</div> <!-- /controls -->	
								</div> <!-- /control-group -->

								<div style="padding-left: 160px;">
									<button type="button" id="empUrlPermission" class="btn btn-primary"><i class="icon-arrow-right"></i>&nbsp;Url Permission</button>&nbsp;
									<button type="button" id="empPermission" class="btn btn-info"><i class="icon-arrow-right "></i>&nbsp;Module Permission</button>
								</div>
							{{ Form::close()}}
						</div>
					</div>
				</div> 
			</div>
		</div>
		<script>
			$(function(){
				$('#empPermission').click(function(e){
					e.preventDefault();
					var empPerAction = "{{ route('admin.viewPermission.post') }}"; 
					$('#perSubmit').attr('action', empPerAction).submit();					
				});
				$('#empUrlPermission').click(function(e){
					e.preventDefault();
					var empUrlPerAction = "{{ route('admin.viewEmpUrlPermission.post') }}"; 
					$('#perSubmit').attr('action', empUrlPerAction).submit();					
				});
			});
		</script>

@stop