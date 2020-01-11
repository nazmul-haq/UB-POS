{{ Form::model($itemInfo,  array('route' => 'admin.saveQtyEdit.post', $itemInfo->stock_item_id, 'id' => 'editItemQty', 'class' => 'form-horizontal')) }}
<input type="hidden" name="stock_item_id" value="{{$itemInfo->stock_item_id}}">
	<div class="control-group">
		{{ Form::label('item_name', 'Item Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('item_name', null, array('class' => 'span3', 'readonly' => 'readonly')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
        <div class="control-group">
		{{ Form::label('available_quantity', 'Available Qty.', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('available_quantity', null, array('class' => 'span3', 'placeholder' => 'Enter Available Quantity')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->

	<div class="modal-footer">
		{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
		<button type="button" class="btn" data-dismiss="modal">Close</button>
	</div>
{{ Form::close() }}

<script>
	$(function(){
		var addItemform = $('#editItemQty');

		jQuery.validator.addMethod("floatNumber", function(value, element) {
		   return this.optional(element) || value == value.match(/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d{1,2})?$/);
		}, "Value must be float or int after (.) only contain two decimal place.");
		// validate form for Item Category
		addItemform.validate({
		  rules: {

		   available_quantity: {
			   floatNumber: true,
			   required: true
			}
		  }, messages: {
				//'category_name'	: { required:  '<span class="error">Category Name required.</span>' },
			},
			ignore				: ':hidden'
		});
	});
</script>