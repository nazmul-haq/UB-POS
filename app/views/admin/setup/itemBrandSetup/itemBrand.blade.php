<div id="message" style="display: none;"></div>
<table class="table table-striped" width="100%">
	<thead class="table-head">
		<tr>
			<th>#</th>
			<th>Brand Name</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach($item_brands as $item_brand)
			{{ Form::open(array('route' => 'admin.editItemBrand.post', 'id' => "itemBrand$item_brand->brand_id")) }}
			<tr>
				<td class="">{{ $item_brand->brand_id }}</td>
				<td class="">
					<span>{{ $item_brand->brand_name }}</span>
					<input id="brand_name{{ $item_brand->brand_id }}" style="display: none;" type="text" name="brand_name" value="{{$item_brand->brand_name}}" />
				</td>
				<td class="span3">
					<a href="javascript:;" class="edit btn btn-primary" id="editItemBrand_{{ $item_brand->brand_id }}" ><i class="icon-edit">&nbsp;Edit</i></a>
					<a href="javascript:;" style="display:none;" class="update btn btn-primary" id="updateItemBrand_{{ $item_brand->brand_id }}" ><i class="icon-edit">&nbsp;update</i></a>
					&nbsp; | &nbsp;
					<a href="javascript:;" class="btn btn-warning" onclick="return deleteConfirm('{{ $item_brand->brand_id }}')" id="brandId{{ $item_brand->brand_id }}"><i class="icon-trash">&nbsp;Inactive</i></a>
				</td>
			</tr>
			{{ Form::close() }}
		@endforeach
	</tbody>
</table>
<script>
	$().ready(function(){
		$('.edit').click(function(){
			var data 	 	 = $(this).attr("id");
			var arr 	 	 = data.split('_');
			var brandId   = arr[1];			
			var brandName = $(this).parent().prev().children().html();
			//$(this).parent().prev().children().html($('#'+brandId)).show();
			$(this).parent().prev().children().next().show().val(brandName).prev().hide();// show input box and hide category name of <Span>//
			$(this).next().show(); //show update bottom
			$(this).hide();	//hide update bottom					
		});
		$('.update').click(function(){
			var data 	 	 = $(this).attr("id");
			var arr 	 	 =	data.split('_');
			var brandId      = arr[1];
			
			var update_btn	= $(this);	
			var edit_btn	= $(this).prev();
			
			var brand_name	=  $("#brand_name"+brandId).val();
			
			$.ajax({
				url  : "itemBrandEdit/"+brandId,
				type : "GET",
				cache: false,
				dataType : 'json',
				data : "brand_name="+brand_name, // serializes the form's elements.
				success : function(data){
					if(data.status == "success"){
						edit_btn.show();  //show Edit bottom
						update_btn.hide();//hide update bottom
						var updated_value = $('#brand_name'+brandId).val();
						$('#brand_name'+brandId).hide().prev().show().html(updated_value); // convert input field to text mode
						$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> Update Successfully </strong>');
						$('#message').css('display', 'block').fadeOut(10000);
					} else{
						$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> '+data.status+' </strong>');
						$('#message').css('display', 'block').fadeOut(10000);
					}
				}
			}); 
		});
	});
	
	//Delete Operation
	function deleteConfirm(brandId){
		var con = confirm("Do you want to delete?");
		if(con){
			$.ajax({
				url: "itemBrandRemove/"+brandId,
				dataType:'json',
				success : function(data){
					if(data.status == 'success'){
						$("#brandId"+brandId).prev().parent().parent().fadeOut("slow");
						$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> Delete Successfully</strong>');
						$('#message').css('display', 'block').fadeOut(5000);				
					} else{
						$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> Something worng!</strong>');
						$('#message').css('display', 'block').fadeOut(5000);						
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
</script>
