@extends('_layouts.default')

@section('content')
	<div class="row">		
		<div class="span12">
			@include('_sessionMessage')	
		</div>			
		<div class="span4">
		  <div class="widget widget-nopad">
			<!-- /widget-header -->
			<div class="widget-content">
			  <div class="widget big-stats-container">
				<div class="widget-content">
				  <div id="clock" class="light">
						<div class="display">
							<div class="weekdays"></div>
							<div class="ampm"></div>
							<div class="alarm"></div>
							<div class="digits"></div>
						</div>
					</div>
				</div>
				<!-- /widget-content --> 			
			  </div>
			</div>
		  </div>
		  <!-- /widget -->
		  {{ HTML::image('img/company_logo.png','title', array('class' => 'company_logo','style'=>'padding-left: 60px;height: 180px;width: 220px;')) }}
		</div>
			<?

//only module, which has no sub Module
  // $modulesWithoutSub=DB::table('moduleemppermissions')
  //          ->join('modulenames', 'moduleemppermissions.module_id', '=', 'modulenames.module_id')
  //          ->select('modulenames.*')
  //          ->where('moduleemppermissions.status', '=',1)
  //          ->where('emp_id', '=', Session::get('emp_id'))
  //          ->orderBy('modulenames.sorting', 'asc')
  //          ->get();
  //          dd($modulesWithoutSub);
			?>
		<div class="span8">		
			  <div class="widget">
				<!-- /widget-header -->
				<div class="widget-content">
				  <div class="shortcuts"> 
				  
          @if (count($modulesWithoutSub) >1) 
          @foreach ($modulesWithoutSub as $moduleWithoutSub)
            			<a href="{{Route("$moduleWithoutSub->module_url")}}" class="shortcut">
							{{ HTML::image('img/nav_icon/'.$moduleWithoutSub->icon.'','title', array('class' => 'icon')) }} <span class="shortcut-label">{{$moduleWithoutSub->module_name}}</span> 
						</a>
            @endforeach
          @endif
					</div>
				  <!-- /shortcuts -->
					</div>
				<!-- /widget-content --> 
			  </div>
		</div>
	</div>
	
	<!-- Clock Js-->
	{{ HTML::script('js/clock.js') }}
	{{ HTML::script('js/moment.min.js') }}
@stop
@section('stickyInfo')
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
		<li>D</li><li>a</li><li>s</li><li>h</li><li>b</li><li>o</li><li>a</li><li>r</li><li>d</li>
	</ul>       
</div>
@stop