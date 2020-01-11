@extends('_layouts/default')

@section('content')
		<div class="row">
			<div class="span12">
				<div class="employee-btn">					
					<a href="{{ URL::to('admin/employee') }}"><button class="btn btn-info"><i class="icon-user"></i>&nbsp; View Employee</button></a>		
				</div>
				<div class="widget-header">
					<i class="icon-user"></i>
					<h3>Add Employee</h3>
				</div> <!-- /widget-header -->
				
				<div class="widget-content">	
					<div class="row">
						<div class="span12">
							@include('_sessionMessage')
							{{ Form::open(array('route' => 'admin.saveMultipleItem.post', 'id' => 'validate_wizard', 'class' => 'stepy-wizzard form-horizontal')) }}	
							<div class="control-group">
								{{ Form::label('company', 'Company Name', ['class' => 'control-label']) }}
								<div class="controls">
									{{ Form::select('company_id', $company, null,  array('class' => 'span5','id' => 'company_name')) }}
								</div> <!-- /controls -->
							</div> <!-- /control-group -->
							
							<div class="control-group">
								{{ Form::label('supplier_id', 'Supplier Name', ['class' => 'control-label']) }}
								<div class="controls">
									{{ Form::select('supplier_id', $suppliers, null, array('class' => 'span5')) }}
								</div> <!-- /controls -->
							</div> <!-- /control-group -->
							<div class="control-group">		
								{{ Form::label('item_company_id', 'Item Company', ['class' => 'control-label']) }}
								<div class="controls">
									{{ Form::select('item_company_id', $item_company, null, array('class' => 'span5')) }}
								</div> <!-- /controls -->					
							</div> <!-- /control-group -->	
							<div class="control-group">		
								{{ Form::label('category_id', 'Item Category', ['class' => 'control-label']) }}
								<div class="controls">
									{{ Form::select('category_id', $item_categorys, null, array('class' => 'span5', 'required' => 'required')) }}
								</div> <!-- /controls -->					
							</div> <!-- /control-group -->	
							<div class="control-group">		
								{{ Form::label('brand_id', 'Brand Name', ['class' => 'control-label']) }}
								<div class="controls">
									{{ Form::select('brand_id', $item_brands, null,  array('class' => 'span5')) }}
								</div> <!-- /controls -->					
							</div> <!-- /control-group -->	
							<div class="control-group">		
								{{ Form::label('location_id', 'Location Name', ['class' => 'control-label']) }}
								<div class="controls">
									{{ Form::select('location_id', $item_locations, null,  array('class' => 'span5')) }}
								</div> <!-- /controls -->					
							</div> <!-- /control-group -->

							<div class="row">
								<div class="control-group">
									<label class="span1" style="margin-left: 90px;">Item Name</label>											
									<div class="span2">
										<input type="text" name="item_name[]">
									</div>
									<label class="span1" style="margin-left: 70px;">Item Point</label>											
									<div class="span2">
										<input type="text" name="item_point[]">
									</div>
									<label class="span1" style="margin-left: 70px;">UPC Code</label>											
									<div class="span2">
										<input type="text" name="upc_code[]" class="upc-code">
									</div>
									<div class="span1" style="margin-left: 70px;">
										<button type="button" class="btn btn-xs btn-info btn-add-more" title="Add More"><span class="icon-plus" style=""></span></button>
									</div>					
								</div> 
							</div>


							<div class="row load-dynamic-content">

            				</div>

							<div class="modal-footer">
								<button class="btn btn-primary btn-form-submit" type="button">Save</button>
							</div>
						{{ Form::close() }}
						</div>
					</div>
				</div> <!-- /widget-content -->
						
			</div>
		</div>   
		

		<script type="text/javascript">
		$('.Items').addClass('active btn btn-fill');
			$(".btn-add-more").on('click',function(e){
		        var content = '';
		        content +='<div class="control-group">'+
						'<label class="span1" style="margin-left: 90px;">Item Name</label>'+
						'<div class="span2">'+
							'<input type="text" name="item_name[]">'+
						'</div>'+
						'<label class="span1" style="margin-left: 70px;">Item Point</label>'+
						'<div class="span2">'+
							'<input type="text" name="item_point[]">'+
						'</div>'+
						'<label class="span1" style="margin-left: 70px;">UPC Code</label>'+
						'<div class="span2">'+
							'<input type="text" name="upc_code[]" class="upc-code">'+
						'</div>'+
						'<div class="span1" style="margin-left: 70px;">'+
							'<button type="button" class="btn btn-xs btn-danger btn-remove" title="Add More"><span class="icon-minus" style=""></span></button>'+
						'</div>'+					
					'</div>';

		        $(".load-dynamic-content").append(content);
		    });

		    $(document).on('click', '.btn-remove', function(){
		        $(this).parent().parent().remove();
		    });

		    $('.btn-form-submit').on('click',function(){
		    	$('#validate_wizard').submit();
		    });
		</script>
@stop
@section('stickyInfo')
<?php
    $string = 'Items';
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