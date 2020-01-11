@extends('_layouts.default')

@section('content')
	<style>.head5{width: 210px}</style>
	<div class="row">
	  <div class="span12">
		@include('_sessionMessage')
		<div class="employee-btn">
			<a href="{{ URL::to('admin/addEmployee') }}"><button type="submit" class="btn btn-primary"><i class="icon-plus-sign"></i>&nbsp;Add Employee</button></a>
			<a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('empinfos'))}}" class="btn btn-info"><i class="icon-download"></i>&nbsp; Download / Export to Excel</a>
		</div>
		{{ Datatable::table()
				->addColumn('id','Name','First Name', 'Last Name', 'Action')      
				->setUrl(route('admin.viewEmployee'))   
				->render() }}
	  </div>
	</div>
	
	<script>
		function deleteConfirm(empId){
		if(empId=={{ Session::get('emp_id')}}){
			alert("You can't delete yourself");
		}
		else{
			var con=confirm("Do you want to delete?");
			if(con){
				$().ready(function(){
					$.ajax({
						url: "deleteEmployee/"+empId,
						success : function(data){
							$("#"+empId).prev().parent().parent().fadeOut("slow");
						}
					});
				});
				return true;
			}
			else{
				return false;
			}
		}
		}
	</script>
@stop
@section('stickyInfo')
<?php
    $string = 'Employees';
    $li = '';
    for($j=0;$j<strlen($string);$j++){
        $li .= '<li>'.substr($string,$j,1).'</li>';
    }
?>
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@stop