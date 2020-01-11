{{ Form::model($itemInfo,  array('route' => 'admin.saveGodownPriceEdit.post', $itemInfo->godown_item_id, 'id' => 'editItemPrice', 'class' => 'form-horizontal')) }}
<input type="hidden" name="item_id" value="{{$itemInfo->item_id}}">
<input type="hidden" name="godown_item_id" value="{{$itemInfo->godown_item_id}}">
	<div class="control-group">
		{{ Form::label('item_name', 'Item Name', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('item_name', null, array('class' => 'span3', 'readonly' => 'readonly')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
        <div class="control-group">
		{{ Form::label('purchase_price', 'Purchase Price', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('purchase_price', null, array('class' => 'span3')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->
        <div class="control-group">
		{{ Form::label('sale_price', 'Sale Price', ['class' => 'control-label']) }}
		<div class="controls">
			{{ Form::text('sale_price', null, array('class' => 'span3', 'placeholder' => 'Enter Sale Price')) }}
		</div> <!-- /controls -->
	</div> <!-- /control-group -->

	<div class="modal-footer">
		<input type='hidden' name='price_id' value='<? echo $itemInfo->price_id; ?>'>
		<input type='hidden' name='ex_purchase_price' value='<? echo $itemInfo->purchase_price; ?>'>
		<input type='hidden' name='item_id' value='<? echo $itemInfo->item_id; ?>'>
		{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
		<button type="button" class="btn" data-dismiss="modal">Close</button>
	</div>
{{ Form::close() }}

<script>
	$(function(){
		var addItemform = $('#editItemPrice');

		jQuery.validator.addMethod("floatNumber", function(value, element) {
		   return this.optional(element) || value == value.match(/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d{1,2})?$/);
		}, "Value must be float or int after (.) only contain two decimal place.");
		// validate form for Item Category
		addItemform.validate({
		  rules: {

		   sale_price: {
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