@extends('_layouts.default')

@section('content')
	<style>
		.head8{width: 212px}
	</style>
	<div class="row">
	  <div class="span12">
		@include('_sessionMessage')
		<div class="employee-btn">	
		
			<div class="btn-group">
				<a  class="btn btn-primary" href="javascript:;" data-toggle="dropdown"><i class="icon-user icon-white"></i> Customer Type</a>
				<a 	class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="javascript:;"><span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="#customerTypeModal" role="button" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; Add Customer Type</a></li>
					<li><a href="#viewCustomerTypeModal" role="button" data-toggle="modal"><i class="icon-zoom-in"></i> View Customer Type</a></li>
				</ul>

			</div>
			&nbsp;&nbsp;
			<a href="#addCustomer" role="button" data-toggle="modal"><button class="btn btn-info"><i class="icon-user"></i>&nbsp; Add New Customer</button></a>
			<a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('customerinfos'))}}" class="btn btn-info"><i class="icon-download"></i>&nbsp; Download / Export to Excel</a>
			
		</div>
		{{ Datatable::table()
				->addColumn('Customer ID','Full Name', 'Username', 'Mobile', 'Due','Register Date', 'Action')
				->setUrl(route('admin.viewCustomer'))   
				->render() }}
	  </div>
	</div>
	<!--Customer Type Modal-->
	<div id="customerTypeModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="cusTypeModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="cusTypeModalLabel">Customer Type</h3>
		</div>
			<div class="modal-body">
			{{ Form::open(array('route' => 'admin.addCustomerType.post', 'class' => 'form-horizontal')) }}
					
				<div class="control-group">		
					{{ Form::label('customerTypeName', 'Customer Type Name', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('customerTypeName', null, array('class' => 'span3', 'id' => 'customerTypeName', 'placeholder' => 'Enter customer type name')) }}
					</div> <!-- /controls -->					
				</div> <!-- /control-group -->
                                <div class="control-group">
					{{ Form::label('discountPercent', 'Discount Percent', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('discountPercent', null, array('class' => 'span3', 'id' => 'discountPercent', 'placeholder' => '10')) }}%
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
                                <div class="control-group">
					{{ Form::label('takaForPerPoint', 'Taka For Per Point', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('takaForPerPoint', null, array('class' => 'span3', 'id' => 'takaForPerPoint', 'placeholder' => '2')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
			</div>
			{{ Form::close() }}
	</div>

        <div id="viewCustomerTypeModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="viewModalLabel"><i class="icon-plus-sign"></i>&nbsp; View Customer Type</h3>
		</div>
		<div class="modal-body">
			<div id="message" style="display: none;"></div>
			<table class="table table-striped" width="100%">
			<thead class="table-head">
				<tr>
					<th>SL No</th>
					<th>Customer Type Name</th>
					<th>Discount (%)</th>
					<th>Taka Per Point</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($customer_types as $customer_type)
				<tr>
					<td>{{ $customer_type->cus_type_id }}</td>
					<td>
						<span>{{ $customer_type->cus_type_name }}</span>
						<input id="cusTypeId{{ $customer_type->cus_type_id }}" class="input-small" style="display: none;" type="text" name="cus_type_name" value="{{ $customer_type->cus_type_name }}" />	
					</td>
					<td>
						<span>{{ $customer_type->discount_percent }}</span>
						<input id="cusDisPer{{ $customer_type->cus_type_id }}" class="input-small" style="display: none;" type="text" name="discount_percent" value="{{ $customer_type->discount_percent }}" />
					</td>
					<td>
						<span>{{ $customer_type->point_unit }}</span>						
						<input id="cusPoint{{ $customer_type->cus_type_id }}" class="input-small" style="display: none;" type="text" name="point_unit" value="{{ $customer_type->point_unit }}" />	
					</td>
					<td class="span2">
						<a href="javascript:;" class="edit btn btn-info btn-small" id="cusType_{{ $customer_type->cus_type_id }}" ><i class="icon-edit">&nbsp;Edit</i></a>
						<a href="javascript:;" style="display:none;" class="update btn btn-default btn-small" id="cusSave_{{ $customer_type->cus_type_id }}" ><i class="icon-ok">&nbsp;Save</i></a>
						|
						<a href="javascript:;" class="btn btn-warning btn-small" onclick="return deleteCusTypeConfirm('{{ $customer_type->cus_type_id }}')" id="cusTypeId{{ $customer_type->cus_type_id }}"><i class="icon-trash"></i></a>
					</td>
				</tr>
				@endforeach
			</tbody>
			</table>
		</div>
                <div class="modal-footer">
        		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
	</div>
	
	<!--Add New Customer Modal-->
	<div id="addCustomer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addCusModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="addCusModalLabel"><i class="icon-user"></i>&nbsp; Add New Customer</h3>
		</div>
			<div class="modal-body">
 
			{{ Form::open(array('route' => 'admin.saveCustomer.post', 'class' => 'form-horizontal')) }}
				<div class="control-group">	
					{{ Form::label('customerType', 'Select Customre Type', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::select('cus_type_id', $cus_type, 1, ['class' => 'span3']) }}
					</div>							
				</div> <!-- /control-group -->
                                
				<div class="control-group">		
					{{ Form::label('fullName', 'Full Name', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('full_name', null, array('class' => 'span3', 'id' => 'full_name', 'placeholder' => 'Enter full name')) }}
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
					{{ Form::label('nationalId', 'Customer Barcode', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::text('national_id', null, array('class' => 'span3', 'id' => 'national_id', 'placeholder' => '')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->

                                <div class="control-group">
					{{ Form::label('permanentAddress', 'Permanent Address', ['class' => 'control-label']) }}
					<div class="controls">
						{{ Form::textarea('permanent_address', null, array( 'rows' =>'1', 'class' => 'span3', 'id' => 'permanent_address', 'placeholder' => '')) }}
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

	<div id="viewCustomer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addCusModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="addSupplierLabel"><i class="icon-user"></i>&nbsp;Customer Details</h3>
		</div>
		<div class="modal-body" id="customerDetails">  </div>
	</div>

	<div id="editCustomer" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="editSupplierLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="editSupplierLabel"><i class="icon-user"></i>&nbsp;Edit Customer</h3>
		</div>
		<div class="modal-body" id="updateCustomer"></div>
	</div>

    <script>
		function customerDetails(cus_id){
			$(function() {
				//$("#testVal").html(MyApp.empId);
				// alert(subject_setup_id);
				//  alert(supp_id);
				$("#customerDetails").load("{{ URL::to('admin/customers') }}"+"/"+cus_id);
			});
		}
		function updateCustomer(cus_id){
			$(function() {
				//$("#testVal").html(MyApp.empId);
				// alert(subject_setup_id);
				$("#updateCustomer").load("{{ URL::to('admin/customers/update') }}"+"/"+cus_id);
			});
		}
		function deleteConfirm(cus_id){
			var con=confirm("Do you want to delete?");
			if(con){
				$().ready(function(){
					$.ajax({
						url: "customer/destroy/"+cus_id,
						success : function(data){
							$("#"+cus_id).prev().parent().parent().fadeOut("slow");
						}
					});
				});
				return true;
			}
			else{
				return false;
			}
		}
		//Inactive Customer Type
		function deleteCusTypeConfirm(cus_id){
			var con = confirm("Do you want to delete?");
			if(con){
				$.ajax({
					url: "customers/TypeRemove/"+cus_id,
					dataType:'json',
					success : function(data){
						if(data.status == 'success'){
							$("#cusTypeId"+cus_id).prev().parent().parent().fadeOut("slow");
							$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> Delete Successfully</strong>');
							$('#message').css('display', 'block');
							$('#message').fadeOut(3000);
						} else{
							$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> Something worng!</strong>');
							$('#message').css('display', 'block');
							$('#message').fadeOut(3000);
						}
					},
					error: function(){}
				});
				return true;
			}
			else{
				return false;
			}
		}
		//Edit Customer Type
		$('.edit').click(function(){
			var data 	  = $(this).attr("id");
			var arr 	  = data.split('_');
			var cusTypeId = arr[1];			
			var cusTypeName = $(this).parent().prev().prev().prev().children().html();
			var discountPer = $(this).parent().prev().prev().children().html();
			var pointUnit 	= $(this).parent().prev().children().html();
			$(this).parent().prev().prev().prev().children().next().show().val(cusTypeName).prev().hide();// show input box and hide category name of <Span>//
			$(this).parent().prev().prev().children().next().show().val(discountPer).prev().hide();// show input box and hide category name of <Span>//
			$(this).parent().prev().children().next().show().val(pointUnit).prev().hide();// show input box and hide category name of <Span>//
			$(this).next().show(); //show update bottom
			$(this).hide();	//hide update bottom					
		});
		$('.update').click(function(){
			var data 	 	 = $(this).attr("id");
			var arr 	 	 =	data.split('_');
			var cusTypeId    = arr[1];
			
			var update_btn	= $(this);	
			var edit_btn	= $(this).prev();
			
			var cusTypeName	=  $("#cusTypeId"+cusTypeId).val();
			var discountPer	=  $("#cusDisPer"+cusTypeId).val();
			var pointUnit	=  $("#cusPoint"+cusTypeId).val();
			//alert(discountPer);
			
			$.ajax({
				url  : "customers/"+ cusTypeId +"/edit",
				type : "GET",
				cache: false,
				dataType : 'json',
				data : {'cus_type_name': cusTypeName, 'discount_percent': discountPer, 'point_unit': pointUnit }, // serializes the form's elements.
				success : function(data){
					if(data.status == "success"){
						edit_btn.show();  //show Edit bottom
						update_btn.hide();//hide update bottom
						var updated_value = $('#cusTypeId'+cusTypeId).val();
						$('#cusTypeId'+cusTypeId).hide().prev().show().html(updated_value); // convert input field to text mode
						$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> Update Successfully </strong>');
						$('#message').css('display', 'block').fadeOut(4000);
					} else{
						$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> '+data.status+' </strong>');
						$('#message').css('display', 'block').fadeOut(6000);
					}
				}
			}); 
		});

      </script>

@stop
@section('stickyInfo')
<?php
    $string = 'Customers';
    $li = '';
    for($j=0;$j<strlen($string);$j++){
        $li .= '<li>'.substr($string,$j,1).'</li>';
    }
?>
@if(Session::has('redTheme'))
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #71253a;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@else
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@endif
@stop