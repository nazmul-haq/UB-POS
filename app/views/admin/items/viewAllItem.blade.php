@extends('_layouts.default')

@section('content')
	<style>.head10{ width: 151px}</style>
	<div class="row">
	  <div class="span12">
                 
		@include('_sessionMessage')
		<div class="employee-btn">

		
		@if (in_array('admin.itemAddForm', Session::get('project_url')))
			<a href="#itemAddModal" id="addItemModal" class="btn btn-primary" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; Add New Item</a>
		@endif
		@if (in_array('barcode.post', Session::get('project_url')))
			<a href="#" id="barcodePrint" class="btn btn-info"><i class="icon-print"></i>&nbsp; Barcode Print</a>
		@endif
		@if (in_array('admin.itemView', Session::get('project_url')))
			<a href="{{ URL::route('admin.itemView') }}" class="btn btn-primary"><i class="icon-zoom-in"></i>&nbsp; Stock Items</a>
		@endif
                @if (in_array('admin.godownItem', Session::get('project_url')))
			<a href="{{ URL::route('admin.godownItem') }}" class="btn btn-primary"><i class="icon-zoom-in"></i>&nbsp; WareHouse Items</a>
		@endif
                @if (in_array('admin.getRecentItems', Session::get('project_url')))
			<a href="{{ URL::route('admin.getRecentItems') }}" class="btn btn-info"><i class="icon-zoom-in"></i>&nbsp; Recently Added Items</a>
		@endif 
                @if (in_array('admin.getRecentItems', Session::get('project_url')))
                <div class="btn-group" style="margin-right:3px;">
				<a  class="btn btn-primary" href="javascript:;" data-toggle="dropdown"><i class="icon-user icon-white"></i>&nbsp;View Low Inventory</a>
				<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="javascript:;"><span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="{{ URL::route('admin.godownLowInventory') }}"><i class="icon-zoom-in"></i>&nbsp; Godown Low Inventory</a></li>
					<li><a href="{{ URL::route('admin.stockLowInventory') }}"><i class="icon-zoom-in"></i>&nbsp; Stock Low Inventory</a></li>
				<li><a href="{{ URL::route('admin.getAllItemData') }}"><i class="icon-zoom-in"></i>&nbsp; View all data</a></li>
				</ul>
			</div>
		</div>
		@endif 
                
                <h3>View All Godown & Stock Items</h3>
		<div id="message" style="display: none;"></div>
                
		{{ Form::open(array('route' => 'barcode.post','id'=>'itemForm')) }}
		{{ Datatable::table()
				->addColumn('# ID','Upc Code','Item Name','Godown Quantity','Stock Quantity','Total Quantity')
				->setUrl(route('admin.getAllItemData'))
				->render() }}
		{{ Form::close()}}
	  </div>
	</div>
	<!--Add New Item Modal-->
	<div id="itemAddModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="newItemModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="newItemModalLabel"><i class="icon-plus-sign"></i>&nbsp; Add New Item</h3>
		</div>
		<div class="modal-body" id="addNewItemBody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>
			<script>
				$().ready(function(){
					$("#barcodePrint").click(function(){
					$("#itemForm").submit();

					});
					$("#addItemModal").click(function(){
						$('#loading').ajaxStart(function() {
							$(this).show();
						}).ajaxComplete(function() {
							$(this).hide();
						});
						$("#addNewItemBody").load('{{ route("admin.itemAddForm") }}');
					});
				});
			</script>
		</div>
	</div>
	<!--close Item -->
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
                function editPrice(stockItemId){
                   // alert(stockItemId);

			$(function() {
				/* $('#loading').ajaxStart(function() {
					$(this).show();
				}).ajaxComplete(function() {
					$(this).hide();
				}); */
				//$('#editItem'+itemId).click(function(){
					$("#editPriceBody").load("{{ URL::to('admin/itemPriceEdit') }}"+"/"+stockItemId);
				//});
			});
		}
                function editQuantity(stockItemId){
                   // alert(stockItemId);

			$(function() {
				/* $('#loading').ajaxStart(function() {
					$(this).show();
				}).ajaxComplete(function() {
					$(this).hide();
				}); */
				//$('#editItem'+itemId).click(function(){
					$("#editQtyBody").load("{{ URL::to('admin/itemQtyEdit') }}"+"/"+stockItemId);
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
            function differentPrice(itemId){
				$(function() {
						$("#diffPricebody").load("{{ URL::to('admin/differentPriceItem') }}"+"/"+itemId);
				});
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
        <div id="editPriceModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemEditModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="itemEditModalLabel"><i class="icon-edit-sign"></i>&nbsp; Item Price Edit</h3>
		</div>
		<div class="modal-body" id="editPriceBody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>

		</div>

	</div>
        <div id="editQuantityModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemEditModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="itemEditModalLabel"><i class="icon-edit-sign"></i>&nbsp; Item Quantity Edit</h3>
		</div>
		<div class="modal-body" id="editQtyBody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>

		</div>

	</div>
<!-- different Price Modal-->
	<div id="diffPrice" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemEditModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="itemEditModalLabel"><i class="icon-edit-sign"></i>&nbsp;Different Price's Item</h3>
		</div>
		<div class="modal-body" id="diffPricebody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>

		</div>
	</div>
	
@stop
@section('stickyInfo')
<?php
    $string = 'Items';
    $li = '';
    for($j=0;$j<strlen($string);$j++){
        $li .= '<li>'.substr($string,$j,1).'</li>';
    }
?>
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@stop