
{{ Form::open(array('route' => 'admin.addItemCompany.post', 'id' => 'formItemCompany', 'class' => 'form-horizontal')) }}
	<div class="control-group">		
		{{ Form::label('company_name', 'Company Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('company_name', null, array('class' => 'span3', 'placeholder' => 'Enter Item Company Name')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->
	
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
	</div>
{{ Form::close() }}

<script>
	$(function(){
		var formItemCompany	 = $('#formItemCompany');
		
		jQuery.validator.addMethod("alphaspace", function(value, element) {
		   return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
		}, "Only letters, Numbers & Space/underscore Allowed.");
		// validate form for Item Brand
		formItemCompany.validate({
		  rules: {
		   company_name: {
			   alphaspace: true,
			   required: true
			}
		  }, messages: {
				//'company_name'	: { required:  '<span class="error">Brand Name required.</span>' },					
			},
			ignore				: ':hidden'	
		});
		
	});
	
</script>