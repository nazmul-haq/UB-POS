<div id="message" style="display: none;"></div>
<table class="table table-striped" width="100%">
	<thead class="table-head">
		<tr>
			<th>#</th>
			<th>Company Name</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach($item_companys as $item_company)
			{{ Form::open(array('route' => 'admin.editItemCompany.post', 'id' => "itemCompany$item_company->company_id")) }}
			<tr>
				<td class="">{{ $item_company->company_id }}</td>
				<td class="">
					<span>{{ $item_company->company_name }}</span>
					<input class="input-small" id="company_name{{ $item_company->company_id }}" style="display: none;" type="text" name="company_name" value="{{$item_company->company_name}}" />
				</td>
				<td class="span3">
					<a href="javascript:;" class="edit btn btn-primary" id="editItemCompany_{{ $item_company->company_id }}" ><i class="icon-edit">&nbsp;Edit</i></a>
					<a href="javascript:;" style="display:none;" class="update btn btn-primary" id="updateItemCompany_{{ $item_company->company_id }}" ><i class="icon-ok">&nbsp;Save</i></a>
					&nbsp; | &nbsp;
					<a href="javascript:;" class="btn btn-warning" onclick="return deleteConfirm('{{ $item_company->company_id }}')" id="companyId{{ $item_company->company_id }}"><i class="icon-trash">&nbsp;Inactive</i></a>
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
			var companyId   = arr[1];			
			var companyName = $(this).parent().prev().children().html();
			//$(this).parent().prev().children().html($('#'+companyId)).show();
			$(this).parent().prev().children().next().show().val(companyName).prev().hide();// show input box and hide category name of <Span>//
			$(this).next().show(); //show update bottom
			$(this).hide();	//hide update bottom					
		});
		$('.update').click(function(){
			var data 	 	 = $(this).attr("id");
			var arr 	 	 =	data.split('_');
			var companyId      = arr[1];
			
			var update_btn	= $(this);	
			var edit_btn	= $(this).prev();
			
			var company_name	=  $("#company_name"+companyId).val();
			
			$.ajax({
				url  : "itemCompanyEdit/"+companyId,
				type : "GET",
				cache: false,
				dataType : 'json',
				data : {'company_name': company_name}, // serializes the form's elements.
				success : function(data){
					if(data.status == "success"){
						edit_btn.show();  //show Edit bottom
						update_btn.hide();//hide update bottom
						var updated_value = $('#company_name'+companyId).val();
						$('#company_name'+companyId).hide().prev().show().html(updated_value); // convert input field to text mode
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
	function deleteConfirm(companyId){
		var con = confirm("Do you want to delete?");
		if(con){
			$.ajax({
				url: "itemCompanyRemove/"+companyId,
				dataType:'json',
				success : function(data){
					if(data.status == 'success'){
						$("#companyId"+companyId).prev().parent().parent().fadeOut("slow");
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
