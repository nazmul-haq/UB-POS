<?php
foreach ($empInfo as $emp) {
    echo $emp->f_name;
}
?>












@extends('_layouts/default')

@section('content')

<div class="row">
	<div class="span12">@include('_sessionMessage')</div>
	<div class="span12">
		<div class="widget-header setup-title"> <i class="icon-user"></i>
		  <h3>Permission For Employee</h3>
		</div>
		<div>	
			<?php
				//        echo '<pre>';
				//        dd($empInfo);
				//        exit();

				if(!isset($empInfo)){
					echo "Please select Employee";
					exit();
				}
				echo'<strong>User Name:</strong>&nbsp;'.$empInfo->user_name;
			?>

			<h4 class="magento_text">Module &amp; Sub Module List:</h4>
			{{ Form::open(array('route' => 'admin.savePermissionSubModule.post', 'class' => 'stepy-wizzard form-horizontal')) }}
				<div>
					<input type="hidden" name="emp_id" value="<?php echo $empInfo->emp_id; ?>"/>
					<?php
						$i = 0;
						$j = 0;
						foreach ($moduleInfo as $module) {
						++$j;
					?>
					<SCRIPT language="javascript">
						$(document).ready(function(){
							// add multiple select / deselect functionality
							$("#selectall<?php echo $j; ?>").click(function() {
								$(".case<?php echo $j; ?>").attr('checked', this.checked);
								});

								// if all checkbox are selected, check the selectall checkbox
								// and viceversa
								$(".case<?php echo $j; ?>").click(function() {
								if ($(".case<?php echo $j; ?>").length == $(".case<?php echo $j; ?>:checked").length) {
									alert($("#selectall<?php echo $j; ?>").attr("checked"));
								} else {
									$("#selectall<?php echo $j; ?>").removeAttr("checked");
									//alert($("#selectall<?php echo $j; ?>").attr("checked"));

								}
							});
						});
					</SCRIPT>

					<div>
						<?php
							$sub_module_q = DB::table('submodulenames')->where('module_id', $module->module_id)->get();

							if(!$sub_module_q){
							$is_permitted = DB::table('moduleemppermissions')->where('module_id', $module->module_id)->where('emp_id', $empInfo->emp_id)->get();

							$select=($is_permitted==TRUE)?"checked='checked'":"";
						?>
						<div class="span3">
							<input type="hidden" name="unsubmodule_module_all[]" value="<?php echo $module->module_id; ?>">
							<?php if($is_permitted){?>
								<input type="hidden" name="old_unsubmodule_module_ids[]" value="<?php echo $module->module_id; ?>"/>
							<?php } ?>
							<input type="checkbox" <?php echo $select;?> name="new_unsubmodule_module_ids[]" value="<?php echo $module->module_id; ?>">
							<label class="btn btn-warning" ><i class="fa <?php echo "fa-" . $module->icon; ?>"></i> <?php echo $module->module_name; ?></label>
						</div>
						<?php
							//            echo'<pre>';
							//            print_r($sub_module_q);
							//            exit();
							} else{
						?>

						<input type="checkbox" id="selectall<?php echo $j; ?>" value="">
						<label class="btn btn-warning" for='selectall<?php echo $j; ?>'><i class="fa <?php echo "fa-" . $module->icon; ?>"></i> <?php echo $module->module_name; ?></label>

						<div class="span3">
							<?php
								foreach ($sub_module_q as $sub_module) {
								++$i;
								$selected_sub_mod = DB::table('smemppermissions')->join('submodulenames', 'submodulenames.sub_module_id', '=', 'smemppermissions.sub_module_id')->where('smemppermissions.sub_module_id', $sub_module->sub_module_id)->where('smemppermissions.emp_id', $empInfo->emp_id)->first();
								// $selected_sub_mod = $this->dashboard->selectPermittedModuleById($emp_id,$sub_module->sub_module_id);
								//    echo '<pre>';
								//    print_r($selected_sub_mod);
								//    exit();

								$selected=($selected_sub_mod==TRUE)?"checked='checked'":"";
								if($selected_sub_mod==TRUE){

								// break;

							?>
							<input type="hidden" name="old_permission_ids[]" value="<?php echo $selected_sub_mod->sub_module_id; ?>">
							<?php }?>
							<div>

								<input type="hidden" name="permission_id_un[]" value="<?php echo  $sub_module->sub_module_id; ?>"/>
								<input id="<?php echo "test$i"; ?>" <?php echo $selected;?> type="checkbox" class="case<?php echo $j; ?>" name="new_permission_ids[]" value="<?php echo $sub_module->sub_module_id; ?>"/>&nbsp;&nbsp;&nbsp;<?php echo $sub_module->sub_module_name; ?>
								<label for="<?php echo "test$i"; ?>"></label>
							</div>
							<?php } ?>
						</div>
					</div>
					<?php }	
						  }
					  ?>
				</div>
				<div style="clear:both; margin-left: 17px; padding-top:15px;">
					<input type="hidden" name="total_sub_module" value="<?php echo $i; ?>"/>
					<button class="btn btn-primary" type="submit" id="submit">Save</button>
				</div>
			{{ Form::close()}}

		</div>	
	</div>
</div>
@stop





















//second 

@extends('_layouts/default')

@section('content')

<div class="row">
	<div class="span12">@include('_sessionMessage')</div>
	<div class="span12">
		<div class="widget-header setup-title"> <i class="icon-setup"></i>
		  <h3>Permission For Employee</h3>
		</div>
		<p>
			@if(!isset($empInfo))
				{{ 'Please Select Employee' }}
				{{ exit() }}
			@endif
			<label style="margin-left: 10px;">
				<strong><i class="icon-user"></i>&nbsp; User Name :</strong> {{ $empInfo->user_name }}
			</label> 
		</p>
		<hr/>
		<div style="margin: -7px 10px 0 10px;"> 
			<h4><i class="icon-list"></i>&nbsp; Module &amp; Sub Module List:</h4><br/>
			
			{{ Form::open(array('route' => 'admin.savePermissionSubModule.post', 'class' => 'form-horizontal')) }}
				{{Form::hidden('emp_id', $empInfo->emp_id)}}
				<? $i = 0; $j = 0; ?>
			 @foreach ($moduleInfo as $module)
				<?	++$j; ?>
				<script language="javascript">
					$(document).ready(function(){
						// add multiple select / deselect functionality
						$("#selectall<?php echo $j; ?>").click(function() {
							$(".case<?php echo $j; ?>").attr('checked', this.checked);
							});
							// if all checkbox are selected, check the selectall checkbox
							// and viceversa
							$(".case<?php echo $j; ?>").click(function() {
							if ($(".case<?php echo $j; ?>").length == $(".case<?php echo $j; ?>:checked").length) {
								alert($("#selectall<?php echo $j; ?>").attr("checked"));
							} else {
								$("#selectall<?php echo $j; ?>").removeAttr("checked");
								//alert($("#selectall<?php echo $j; ?>").attr("checked"));
							}
						});
					});
				</script>
				 <?php
					$sub_module_q = DB::table('submodulenames')->where('module_id', $module->module_id)->get();
					if(!$sub_module_q){
						$is_permitted = DB::table('moduleemppermissions')->where('module_id', $module->module_id)->where('emp_id', $empInfo->emp_id)->get();
						$select=($is_permitted==TRUE) ? "checked='checked'":"";
				 ?>			
				<div class="span2">
					<input type="hidden" name="unsubmodule_module_all[]" value="<?php echo $module->module_id; ?>">
					<?php if($is_permitted){ ?>
						<input type="hidden" name="old_unsubmodule_module_ids[]" value="<?php echo $module->module_id; ?>"/>
					<?php } ?>	
					
					<input type="checkbox" <?php echo $select;?> name="new_unsubmodule_module_ids[]" value="<?php echo $module->module_id; ?>">&nbsp;
					<label class="btn btn-warning"><i class="fa <?php echo "fa-" . $module->icon; ?>"></i> <?php echo $module->module_name; ?></label>
				 
				 <? } else{ ?>	
					
					<input type="checkbox" id="selectall<?php echo $j; ?>" value="">
					<label class="btn btn-warning" for='selectall<?php echo $j; ?>'><i class="fa <?php echo "fa-" . $module->icon; ?>"></i> <?php echo $module->module_name; ?></label>
				 
				 @foreach($sub_module_q as $sub_module) 
				 <?
					++$i;
					$selected_sub_mod = DB::table('smemppermissions')->join('submodulenames', 'submodulenames.sub_module_id', '=', 'smemppermissions.sub_module_id')->where('smemppermissions.sub_module_id', $sub_module->sub_module_id)->where('smemppermissions.emp_id', $empInfo->emp_id)->first();
					$selected=($selected_sub_mod==TRUE)?"checked='checked'":"";
					if($selected_sub_mod==TRUE){
				 ?>
					<input type="hidden" name="old_permission_ids[]" value="<?php echo $selected_sub_mod->sub_module_id; ?>">
				 <? } ?>				
				 
					<div class="checkbox">
					   <label for="<?php echo "test$i"; ?>">
							<input type="hidden" name="permission_id_un[]" value="<?php echo  $sub_module->sub_module_id; ?>"/>
							<input id="<?php echo "test$i"; ?>" <?php echo $selected;?> type="checkbox" class="case<?php echo $j; ?>" name="new_permission_ids[]" value="<?php echo $sub_module->sub_module_id; ?>"/>&nbsp;&nbsp;&nbsp;<?php echo $sub_module->sub_module_name; ?>
					   </label>
					</div>
				 @endforeach	
				</div>
				<? } ?>
			 @endforeach
			{{ Form::close() }}
		</div>
	</div>
</div>
@stop