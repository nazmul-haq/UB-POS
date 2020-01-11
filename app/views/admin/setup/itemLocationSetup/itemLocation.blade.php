@if(!empty($item_locations))
<div id="message" style="display: none;"></div>
<table class="table table-striped" width="100%">
	<thead class="table-head">
		<tr>
			<th>#</th>
			<th>Location Name</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach($item_locations as $item_location)
		{{ Form::open(array('route' => 'admin.editItemLocation.post', 'id' => "itemCategory$item_location->location_id")) }}
		<tr>
			<td>{{ $item_location->location_id }}</td>
			<td>
				<span>{{ $item_location->location_name }}</span>
				<input id="location_name{{ $item_location->location_id }}" style="display: none;" type="text" name="location_name" value="{{$item_location->location_name}}" />
			</td>
			<td class="span3">
				<a href="javascript:;" class="edit btn btn-primary" id="editItemLocation_{{ $item_location->location_id }}" ><i class="icon-edit">&nbsp;Edit</i></a>
				<a href="javascript:;" style="display:none;" class="update btn btn-primary" id="updateItemLocation_{{ $item_location->location_id }}" ><i class="icon-edit">&nbsp;update</i></a>
				&nbsp; | &nbsp;
				<a href="javascript:;" class="btn btn-warning" onclick="return deleteConfirm('{{ $item_location->location_id }}')" id="locationId{{ $item_location->location_id }}"><i class="icon-trash">&nbsp;Inactive</i></a>
			</td>
		</tr>
		{{ Form::close() }}
		@endforeach
	</tbody>
</table>
@else
    <div class="empty-msg alert-block">
	  <button type="button" class="close" data-dismiss="alert">&#735;</button>
	  <h4>Nothing found!</h4>
		Thank You
	</div>
@endif
<script>
	$().ready(function(){
		$('.edit').click(function(){
			var data 	 	 = $(this).attr("id");
			var arr 	 	 = data.split('_');
			var locationId   = arr[1];			
			var locationName = $(this).parent().prev().children().html();
			//$(this).parent().prev().children().html($('#'+locationId)).show();
			$(this).parent().prev().children().next().show().val(locationName).prev().hide();// show input box and hide category name of <Span>//
			$(this).next().show(); //show update bottom
			$(this).hide();	//hide update bottom					
		});
		$('.update').click(function(){
			var data 	 	 = $(this).attr("id");
			var arr 	 	 =	data.split('_');
			var locationId   = arr[1];
			
			var update_btn	= $(this);	
			var edit_btn	= $(this).prev();
			
			var cat_name	=  $("#location_name"+locationId).val();
			
			$.ajax({
				url  : "itemLocationEdit/"+locationId,
				type : "GET",
				cache: false,
				dataType : 'json',
				data : "location_name="+cat_name, // serializes the form's elements.
				success : function(data){
					if(data.status == "success"){
						edit_btn.show();  //show Edit bottom
						update_btn.hide();//hide update bottom
						var updated_value = $('#location_name'+locationId).val();
						$('#location_name'+locationId).hide().prev().show().html(updated_value); // convert input field to text mode
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
	function deleteConfirm(locationId){
		var con = confirm("Do you want to delete?");
		if(con){
			$.ajax({
				url: "itemLocationRemove/"+locationId,
				dataType:'json',
				success : function(data){
					if(data.status == 'success'){
						$("#locationId"+locationId).prev().parent().parent().fadeOut("slow");
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
