@extends('_layouts.default')

@section('content')
	<style>
		.head6{width: 265px}
	</style>
	<div class="row">
	  <div class="span12">
		@include('_sessionMessage')

		<div class="employee-btn">
			<a href="#addSupplier" role="button" data-toggle="modal"><button class="btn btn-info"><i class="icon-user"></i>&nbsp; Add New Supplier</button></a>
			<a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('supplierinfos'))}}" class="btn btn-info"><i class="icon-download"></i>&nbsp; Download / Export to Excel</a>
		</div>
		{{ Datatable::table()
				->addColumn('id','Supplier / Company Name', 'Mobile', 'Due', 'Action')
				->setUrl(route('admin.viewSupplierAll'))
				->render() }}
	  </div>
	</div>
	<!--Customer Type Modal-->

	<!--Add New Supplier Modal-->
	<div id="addSupplier" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addCusModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="addSupplierLabel"><i class="icon-user"></i>&nbsp; Add New Supplier</h3>
		</div>
			<div class="modal-body">
                           
			{{ Form::open(array('route' => 'admin.saveSupplier.post', 'class' => 'form-horizontal')) }}
				

				<div class="control-group">
					{{ Form::label('supplierOrCompanyName', 'Supplier Name', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('supp_or_comp_name', null, array('class' => 'span3', 'id' => 'supplier_company_name', 'placeholder' => 'supplier or company name')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('userName', 'User Name', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('user_name', null, array('class' => 'span3', 'id' => 'user_name', 'placeholder' => 'Enter user name')) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->


                                <div class="control-group">
					{{ Form::label('mobile', 'Mobile No', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('mobile', null, array('class' => 'span3', 'id' => 'mobile', 'placeholder' => 'Enter mobile no')) }} *
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('email', 'Email', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::email('email', null, array('class' => 'span3', 'id' => 'email', 'placeholder' => 'Enter email')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->


                                <div class="control-group">
					{{ Form::label('permanentAddress', 'Permanent Address', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::textarea('permanent_address', null, array('rows' =>'1', 'class' => 'span3', 'id' => 'permanent_address', 'placeholder' => '')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('presentAddress', 'Present Address', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::textarea('present_address', null, array('rows' =>'1', 'class' => 'span3', 'id' => 'present_address', 'placeholder' => '')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->


			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
			</div>
			{{ Form::close() }}
	</div>


        <div id="viewSupplier" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addCusModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="addSupplierLabel"><i class="icon-user"></i>&nbsp;Supplier Details</h3>
		</div>
            <div class="modal-body" id="supplierDetails">

            </div>
     


			
	</div>

       <div id="editSupplier" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="editSupplierLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="editSupplierLabel"><i class="icon-user"></i>&nbsp;Edit Supplier</h3>
		</div>
            <div class="modal-body" id="updateSupplier">


            </div>




	</div>
       
    <script>
    $('.Suppliers').addClass('active btn btn-fill');

function supplierDetails(supp_id){
              $(function() {
              //$("#testVal").html(MyApp.empId);
            // alert(subject_setup_id);
          //  alert(supp_id);
              $("#supplierDetails").load("{{ URL::to('admin/suppliers') }}"+"/"+supp_id);

        });
}
function updateSupplier(supp_id){
              $(function() {
              //$("#testVal").html(MyApp.empId);
            // alert(subject_setup_id);
              $("#updateSupplier").load("{{ URL::to('admin/suppliers/update') }}"+"/"+supp_id);

        });
}


      </script>


        <script>
		function deleteConfirm(supp_id){
			var con=confirm("Do you want to delete?");
			if(con){
				$().ready(function(){
					$.ajax({
						url: "suppliers/destroy/"+supp_id,
						success : function(data){
							$("#"+supp_id).prev().parent().parent().fadeOut("slow");
						}
					});
				});
				return true;
			}
			else{
				return false;
			}
		}
	</script>

@stop
@section('stickyInfo')
<?php
    $string = 'Suppliers';
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