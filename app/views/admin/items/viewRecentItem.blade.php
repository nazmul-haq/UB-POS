@extends('_layouts.default')

@section('content')
	<style>
		.head9{width: 140px}
		.head10{width: 150px}
		col.con9{width: 150px}
	</style>
	<div class="row">
	  <div class="span12">	
		<div class="employee-btn">	
			<a href="{{ URL::to('admin/godownItem') }}" class="btn btn-primary"><i class="icon-zoom-in"></i>&nbsp; WareHouse  Items</a>
			<a href="{{ URL::to('admin/viewRecentItem') }}" class="btn btn-default active"><i class="icon-ok"></i>&nbsp; Recent Add Items</a>
		</div>
              <h3>View Recent Added Items</h3>
		@include('_sessionMessage')
		{{ Datatable::table()
				->addColumn('UPC Code','Item Name','Company Name', 'Brand ', 'Category', 'Location', 'Tax(%)','Offer', 'Entry Date', 'Action')      
				->setUrl(route('admin.recentItemsDatable'))   
				->render() }}	
	  </div>	  
	</div>
	<script>
		$('.Items').addClass('active btn btn-fill');
		function ItemEdit(itemId){
			
			$(function() {
				/* $('#loading').ajaxStart(function() {
					$(this).show();
				}).ajaxComplete(function() {
					$(this).hide();
				}); */
				//$('#editItem'+itemId).click(function(){
					$("#editItemBody").load("{{ URL::to('admin/itemEditForm') }}"+"/"+itemId);
				//});
				

			});
		}
		function deleteConfirm(itemId){
			var con=confirm("Do you want to delete?");
			if(con){
				$().ready(function(){
					$.ajax({
						url: "itemInactive/"+itemId,
						success : function(data){
							if(data.status == "success"){
								$("#"+itemId).parent().parent().parent().fadeOut("slow");
								$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> Delete Successfully </strong>');
								$('#message').css('display', 'block').fadeOut(10000);
							} else{								
								$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> Something Worng! </strong>');
								$('#message').css('display', 'block').fadeOut(10000);
							}
						}
					});
				});
				return true;
			}
			else{
				return false;
			}
		}
	
	</script>
	<!--Edit Item Modal-->
	<div id="editItemModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemEditModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="itemEditModalLabel"><i class="icon-edit-sign"></i>&nbsp; Item Edit</h3>
		</div>
		<div class="modal-body" id="editItemBody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>
			
		</div>	
	</div>	
@stop
@section('stickyInfo')
<?php
    $string = 'Recent Items';
    $li = '';
    for($j=0;$j<strlen($string);$j++){
        $li .= '<li>'.substr($string,$j,1).'</li>';
    }
?>
@if(Session::has('redTheme'))
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #71253a;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@else
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@endif
@stop