
@extends('_layouts/default')

@section('content')

<div class="row">
	<div class="span12">@include('_sessionMessage')</div>
	<div class="span12">
		<div class="widget-header setup-title"> <i class="icon-cogs"></i>
		  <h3>URL Permission For Employee</h3>
		</div>
		<p>
			<label style="margin-left: 10px;">
				<strong><i class="icon-user"></i>&nbsp; User Name :</strong> {{ $empInfo->user_name }}
			</label> 
		</p>
		<hr/>
		<div style="margin: -7px 10px 0 10px;"> 
			<h4><i class="icon-list"></i>&nbsp; URL Permission List : </h4><br/>
			
			{{ Form::open(array('route' => 'admin.saveEmpUrlPermission.post', 'class' => 'form-horizontal')) }}
				{{Form::hidden('emp_id', $empInfo->emp_id)}}
							
				@foreach ($urlInfos as $url)
					<?
						
						$getPermiteEmp = DB::table('urlemppermissions')->where('url_id', $url->url_id)->where('emp_id', $empInfo->emp_id)->get();
						
					?>
					<input type="hidden" name="un_url_all[]" value="<?php echo $url->url_id; ?>">
					<?
						if(!$getPermiteEmp){
					 ?>
					<div class="span2">						
						<input type="checkbox" name="new_un_url_ids[]" value="<?= $url->url_id ?>" style="float: left;">
						<strong style="margin-left: 10px;">{{{ $url->url_name }}}</strong>		
					</div>
					
					 <? } else{ ?>	
					<div class="span2">	
						<input type="hidden" name="old_un_url_ids[]" value="<?php echo $url->url_id; ?>"/>						
						<input align="left" type="checkbox" name="single_url_id[]" checked="checked" value="<?= $url->url_id ?>" style="float: left;">
						<strong style="margin-left: 10px;">{{{ $url->url_name }}}</strong>								 
					</div>
					<? } ?>
				@endforeach
				<div style="clear:both; margin-left: 17px; padding-top:15px;">
					<input type="hidden" name="total_sub_module" value="<?php //echo $i; ?>"/>
					<button class="btn btn-info" type="submit" id="submit"><i class="icon-ok"></i>&nbsp;Save Changes</button>
				</div>
				
				
			{{ Form::close() }}
		</div>
	</div>
</div>
@stop