<a href="#myAnchor" id="scroll_top" class="go-top"><i class="icon-chevron-up"></i></a>
<div class="navbar navbar-fixed-top" id="myAnchor">
  <div class="navbar-inner">
    <div class="container"> <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span
                    class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span> </a><a class="brand" href="index.html">
                      <img style="height: 30px; width: 40px;" class="pos_logo" src="{{asset('img/unitech.png')}}" alt="" />
                      <img style="height: 30px; width: 100px;" class="pos_logo" src="{{asset('img/posNewLogo.png')}}" alt="" />
                    </a>
      
	  <div class="nav-collapse">
        <ul class="nav pull-right">
            
          <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="icon-flag"></i> Branch Name :  <b>{{Helper::getBranchName()}} </b> </a>
            <ul class="dropdown-menu">
              <li><a href="javascript:;">Help</a></li>
            </ul>
          </li>
		  
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
                            class="icon-user"></i> 
                            @if (Session::has('emp_id')) 
                                {{ Session::get('full_name') }} 
                            @endif 
                    <b class="caret"></b></a>
                    
            <ul class="dropdown-menu">
			<? if(Session::get('role')==2){ ?>
              <li><a href="{{URL::to('editInstallation')}}">Settings</a></li>
              <li><a href="{{URL::to('admin/dbBackup')}}">DB Backup</a></li>
			  <? } ?>
              <li><a href="{{ URL::to('logout') }}">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
      <!--/.nav-collapse --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /navbar-inner --> 
</div>
<!-- /navbar -->
<?php
// these arrays are used for store permitted  module and sub module url//
$module_url=array(); 
$sub_module_url=array();

//only module, which has no sub Module
  $modulesWithoutSub=DB::table('moduleemppermissions')
           ->join('modulenames', 'moduleemppermissions.module_id', '=', 'modulenames.module_id')
           ->select('modulenames.*')
           ->where('moduleemppermissions.status', '=',1)
           ->where('emp_id', '=', Session::get('emp_id'))
           ->orderBy('modulenames.sorting', 'asc')
           ->get();
 
//module retreive by submoduleId from empSubmodulePermission Table
$modulesWithSub=DB::table('submodulenames')
            ->join('smemppermissions', 'submodulenames.sub_module_id', '=', 'smemppermissions.sub_module_id')
            ->join('modulenames', 'modulenames.module_id', '=', 'submodulenames.module_id')
            ->select('submodulenames.module_id','modulenames.*')
            ->where('smemppermissions.status', '=',1)
           ->where('smemppermissions.emp_id', '=', Session::get('emp_id'))
           ->groupBy('submodulenames.module_id')
           ->get();

?>

<div class="subnavbar">
  <div class="subnavbar-inner">
    <div class="container">
      <ul class="mainnav">
        
          @if (count($modulesWithoutSub) >1)
		  <? $i=0; ?>
          @foreach ($modulesWithoutSub as $moduleWithoutSub)
		  <? ++$i;?>
				@if($i<13)
            <li class="{{$moduleWithoutSub->module_name}}">
					<a href="{{Route("$moduleWithoutSub->module_url")}}">{{ HTML::image('img/nav_icon/'.$moduleWithoutSub->icon.'','title', array('class' => 'icon')) }}<span>{{$moduleWithoutSub->module_name}}</span> </a> 
			</li>
				@endif
          <? $module_url[]=$moduleWithoutSub->module_url; ?>
            @endforeach
          @endif
          
          @foreach ($modulesWithSub as $moduleWithSub)
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ HTML::image('img/nav_icon/'.$moduleWithSub->icon.'', 'title', array('class' => 'icon')) }} <span>{{$moduleWithSub->module_name}}</span> <b class="caret"></b></a>
          <ul class="dropdown-menu">
          <?
          //this query runs for select sub modules(which has Main module) according to module id which manipulated by moduleWithSub Query
           $subModules=DB::table('submodulenames')
             ->join('smemppermissions', 'submodulenames.sub_module_id', '=', 'smemppermissions.sub_module_id')
                ->select('submodulenames.*')
                    ->where('smemppermissions.status', '=',1)
                    ->where('submodulenames.module_id', '=',$moduleWithSub->module_id)
                    ->where('smemppermissions.emp_id', '=', Session::get('emp_id'))
                    ->where('smemppermissions.status', '=',1)
                    ->orderBy('submodulenames.sorting', 'asc')
                    ->get();
          ?>
         @foreach ($subModules as $subModule)
         <? $sub_module_url[]=$subModule->sub_module_url ?>
         
         <li><a href="{{$subModule->sub_module_url }}"><i class="shortcut-icon {{$subModule->sub_module_icon}}"></i> &nbsp;<span>{{$subModule->sub_module_name}}</span> </a> </li>         
         @endforeach
        </ul>
        </li>
        @endforeach
        
          <? Session::put(array('module_url'    =>$module_url));
             Session::put(array('submodule_url'    =>$sub_module_url )) 
          ?>
      </ul>
    </div>
    <!-- /container --> 
  </div>
  <!-- /subnavbar-inner --> 
</div>
<!-- /subnavbar -->
