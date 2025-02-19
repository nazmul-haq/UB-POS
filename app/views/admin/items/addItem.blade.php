<style>
	.modal-body {max-height: 500px;}
	.itemAddModal{margin-top: 100px}
</style>
{{ Form::open(array('route' => 'admin.itemAddFormSave.post', 'id' => 'addItemform', 'class' => 'form-horizontal')) }}
	<div class="control-group">
		{{ Form::label('company', 'Company Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::select('company_id', $company, null,  array('class' => 'span3')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
	<div class="control-group">
		{{ Form::label('item_name', 'Item Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('item_name', null, array('class' => 'span3', 'placeholder' => 'Enter Item Name')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->
	<div class="control-group">
		{{ Form::label('item_point', 'Item Point', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('item_point', 0, array('class' => 'span3', 'placeholder' => 'Enter Item Point')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->
	<div class="control-group">		
		{{ Form::label('upc_code', 'UPC Code', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('upc_code', null, array('class' => 'span3', 'placeholder' => 'Enter UPC Code')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->	
	<div class="control-group">
		{{ Form::label('supplier_id', 'Supplier Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::select('supplier_id', $suppliers, null, array('class' => 'span3')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
	<div class="control-group">		
		{{ Form::label('item_company_id', 'Item Company', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::select('item_company_id', $item_company, null, array('class' => 'span3')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->	
	<div class="control-group">		
		{{ Form::label('category_id', 'Item Category', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::select('category_id', $item_categorys, null, array('class' => 'span3', 'required' => 'required')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->	
	<div class="control-group">		
		{{ Form::label('brand_id', 'Brand Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::select('brand_id', $item_brands, null,  array('class' => 'span3')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->	
	<div class="control-group">		
		{{ Form::label('location_id', 'Location Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::select('location_id', $item_locations, null,  array('class' => 'span3')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->	
	<div class="control-group">		
		{{ Form::label('tax_amount', 'Tax (%)', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('tax_amount', 0, array('class' => 'span3', 'placeholder' => 'Enter Tax Amount')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->		
	<div class="control-group">		
		{{ Form::label('description', 'Description', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::textarea('description', null, array('rows'=>'1', 'class' => 'span3', 'placeholder' => 'Enter Description')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->	

	<div class="modal-footer">
		{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
		<button type="button" class="btn" data-dismiss="modal">Close</button>
	</div>
{{ Form::close() }}

<script>
	$(function(){
		var addItemform = $('#addItemform');

		jQuery.validator.addMethod("alphaspace", function(value, element) {
		   return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
		}, "Only letters, Numbers & Space/underscore Allowed.");

		jQuery.validator.addMethod("floatNumber", function(value, element) {
		   return this.optional(element) || value == value.match(/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d{1,2})?$/);
		}, "Value must be float or int after (.) only contain two decimal place.");
		// validate form for Item Category
		addItemform.validate({
		  rules: {
		   company: {
			   required: true
			},item_name: {
			   required: true
			},
		   tax_amount: {
			   floatNumber: true,
			   required: true
			},
		   location: {
			   alphaspace: true,
			   required: true
			}
		  }, messages: {
				//'category_name'	: { required:  '<span class="error">Category Name required.</span>' },					
			},
			ignore				: ':hidden'	
		});
	});
</script>