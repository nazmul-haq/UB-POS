{{ Form::open(array('route' => 'admin.addItemCategory.post', 'id' => 'formItemCategory', 'class' => 'form-horizontal')) }}
	
	<div class="control-group">		
		{{ Form::label('itemCategory', 'Category Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('category_name', null, array('class' => 'span3', 'placeholder' => 'Enter Item Category Name')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->	
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		{{ Form::submit('Save', array('class' => 'btn btn-primary')) }}
	</div>
	
{{ Form::close() }}
<script>
	$(function(){
		var formItemCategory = $('#formItemCategory');

		jQuery.validator.addMethod("alphaspace", function(value, element) {
		   return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
		}, "Only letters, Numbers & Space/underscore Allowed.");
		// validate form for Item Category
		formItemCategory.validate({
		  rules: {
		   category_name: {
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