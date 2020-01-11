
{{ Form::open(array('route' => 'admin.addItemBrand.post', 'id' => 'formItemBrand', 'class' => 'form-horizontal')) }}
	<div class="control-group">		
		{{ Form::label('brand_name', 'Brand Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('brand_name', null, array('class' => 'span3', 'placeholder' => 'Enter Item Brand Name')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->
	
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
	</div>
{{ Form::close() }}

<script>
	$(function(){
		var formItemBrand	 = $('#formItemBrand');
		
		jQuery.validator.addMethod("alphaspace", function(value, element) {
		   return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
		}, "Only letters, Numbers & Space/underscore Allowed.");
		// validate form for Item Brand
		formItemBrand.validate({
		  rules: {
		   brand_name: {
			   alphaspace: true,
			   required: true
			}
		  }, messages: {
				//'brand_name'	: { required:  '<span class="error">Brand Name required.</span>' },					
			},
			ignore				: ':hidden'	
		});
		
	});
	
</script>