{{ Form::open(array('route' => 'admin.addIncExpType.post', 'id' => 'formIncExp', 'class' => 'form-horizontal')) }}
	
	<div class="control-group">		
		{{ Form::label('Use For', 'Use For', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::select('used_for', ['1' => 'Income', '2' => 'Expense'], null, array('class' => 'span3')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->	
	<div class="control-group">		
		{{ Form::label('type_name', 'Type Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('type_name', null, array('class' => 'span3', 'placeholder' => 'Enter Income/Expense Name')) }}
		</div> <!-- /controls -->					
	</div> <!-- /control-group -->	
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
	</div>
	
{{ Form::close() }}
<script>
	$(function(){
		var formIncExp = $('#formIncExp');

		jQuery.validator.addMethod("alphaspace", function(value, element) {
		   return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
		}, "Only letters, Numbers & Space/underscore Allowed.");
		// validate form for Item Category
		formIncExp.validate({
		  rules: {
		   type_name: {
			   alphaspace: true,
			   required: true
			}
		  }, messages: {
				//'location_name'	: { required:  '<span class="error">Category Name required.</span>' },					
			},
			ignore				: ':hidden'	
		});
	});
</script>