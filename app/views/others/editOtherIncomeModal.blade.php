{{ Form::model($getIncome, array('route' => 'editOtherIncome.post', $getIncome->other_income_id,  'id' => 'editIncome', 'class' => 'form-horizontal')) }}
	{{ Form::hidden('other_income_id', $getIncome->other_income_id)}}
	<div class="control-group">
		{{ Form::label('income_type_id', 'Income Type', ['class' => 'control-label', 'style' => 'font-weight: bolder;']) }}
		<div class="controls">
			{{ Form::select('income_type_id', $incomeReasons, $getIncome->income_type_id, array('class' => 'span3')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
	
	<div class="control-group">
		{{ Form::label('date', 'Date ', ['class' => 'control-label', 'style' => 'font-weight: bolder;']) }}
		<div class="controls">
			<input id="dp3" name="date" data-date="<?= date("Y-m-d") ?>" data-date-format="yyyy-mm-dd" class="datepicker span3" type="text" value="<?= $getIncome->date ?>">
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
	
	<div class="control-group">
		{{ Form::label('amount', 'Amount', ['class' => 'control-label', 'style' => 'font-weight: bolder;']) }}
		<div class="controls">
			{{ Form::text('amount', null, array('class' => 'span3', 'placeholder' => 'Enter Amount')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
	
	<div class="control-group">
		{{ Form::label('comment', 'Comment', ['class' => 'control-label', 'style' => 'font-weight: bolder;']) }}
		<div class="controls">
			{{ Form::textarea('comment', null, array('rows' =>'1', 'class' => 'span3', 'placeholder' => 'Enter Comment Here...')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
	
	<div class="modal-footer">
		{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
		<button type="button" class="btn" data-dismiss="modal">Close</button>
	</div>
{{ Form::close() }}

	<script>
		$(function(){
			var editIncome = $('#editIncome');

			jQuery.validator.addMethod("alphaspace", function(value, element) {
			   return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
			}, "Only letters, Numbers & Space/underscore Allowed.");

			jQuery.validator.addMethod("floatNumber", function(value, element) {
			   return this.optional(element) || value == value.match(/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d{1,2})?$/);
			}, "Value must be float or int after (.) only contain two decimal place.");
			// validate form for Item Category
			editIncome.validate({
			  rules: {
			   income_reason: {
				   alphaspace: true,
				   required: true
				},
			   amount: {
				   floatNumber: true,
				   required: true
				},
			   date: {
				   required: true
				}
			  }, messages: {
					//'category_name'	: { required:  '<span class="error">Category Name required.</span>' },					
				},
				ignore				: ':hidden'	
			});
		});
	</script>