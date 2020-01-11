@extends('_layouts.default')

@section('content')
	<style>.head10{ width: 151px}</style>
	<div class="row">
	  <div class="span12">
                 
		@include('_sessionMessage')
		<div class="employee-btn">
		@if (in_array('barcode.post', Session::get('project_url')))
			<a href="{{ URL::route('viewAllItem.report') }}" class="btn btn-info"><i class="icon-fullscreen"></i>&nbsp; View All </a>
		@endif
		<a href="#" id="barcodeQueue" class="btn btn-info"><i class="icon-print"></i>&nbsp; Barcode Queue</a>
		@if (in_array('admin.itemAddForm', Session::get('project_url')))
			<a href="#itemAddModal" id="addItemModal" class="btn btn-primary" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; Add New Item</a>
			<a href="{{URL::route('admin.multipleItemAdd')}}" class="btn btn-primary" ><i class="icon-plus-sign"></i> <i class="icon-plus-sign"></i>&nbsp; Add Multiple Item</a>
		@endif
		@if (in_array('barcode.post', Session::get('project_url')))
			<a href="{{ URL::route('barcode') }}" class="btn btn-info"><i class="icon-barcode"></i>&nbsp; Barcode Print</a>
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
				</ul>

				
			</div>

			<a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('iteminfos'))}}/{{base64_encode(base64_encode('stockItem'))}}" class="btn btn-info"><i class="icon-download"></i>&nbsp; Download / Export to Excel</a>
			
			<a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('iteminfos'))}}/{{base64_encode(base64_encode('catWiseItem'))}}" class="btn btn-info"><i class="icon-download"></i>&nbsp; Category Wise Item Export to Excel</a>

		</div>
		@endif 
                
                <h3>View Stock Items</h3>
		<div id="message" style="display: none;"></div>
                
		{{ Form::open(array('route' => 'barcode.queue.all','id'=>'itemForm')) }}
		{{ Datatable::table()
				->addColumn('#','Upc Code','Item Name','Company Name','Purchase Price','Sale Price','Category','Offer','Available Quantity', 'Action')
				->setUrl(route('admin.viewItem'))
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
					$("#barcodeQueue").click(function(){
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
		function ItemSuppliers(itemId){

			$(function() {
				/* $('#loading').ajaxStart(function() {
					$(this).show();
				}).ajaxComplete(function() {
					$(this).hide();
				}); */
				//$('#editItem'+itemId).click(function(){
					$("#viewItemSupplierBody").load("{{ URL::to('admin/viewItemSuppliers') }}"+"/"+itemId);
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
	<div id="viewItemSuppliers" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemSuppliersModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="itemSuppliersModalLabel"><i class="icon-edit-sign"></i>&nbsp; View Item Suppliers</h3>
		</div>
		<div class="modal-body" id="viewItemSupplierBody">
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
    $string = 'Stock Items';
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