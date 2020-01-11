@extends('_layouts.default')

@section('content')
	<div class="row">
	  <div class="span12">	
	  @include('_sessionMessage')
		<div class="employee-btn">	
			<a href="{{ URL::to('admin/godownItem') }}" class="btn btn-default active"><i class="icon-ok"></i>&nbsp; WareHouse Items</a>
			<a href="{{ URL::to('admin/viewRecentItem') }}" class="btn btn-info"><i class="icon-zoom-in"></i>&nbsp; Recent Add Items</a>
			<a href="#" id="barcodeQueue" class="btn btn-info"><i class="icon-print"></i>&nbsp; Barcode Queue</a>
            <a href="{{ URL::route('barcode') }}" class="btn btn-info"><i class="icon-barcode"></i>&nbsp; Barcode Print</a>
		</div>
                <h3>View WareHouse Items</h3>
                {{ Form::open(array('route' => 'barcode.queue.all','id'=>'itemForm')) }}
		{{ Datatable::table()
				->addColumn('#','Upc Code','Item Name','Company Name', 'Brand Name', 'Location', 'Purchase Price','Sale Price','Available Quantity', 'Diff<br>Price','Update')
				->setUrl(route('admin.getGodownItemData'))   
				->render() }}

                {{ Form::close()}}
		</div>	  
	</div>
  <!-- different Price Modal-->
	<div id="diffPrice" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="ModalLabel"><i class="icon-edit-sign"></i>&nbsp;Different Price's Item</h3>
		</div>
		<div class="modal-body" id="diffPricebody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>

		</div>
	</div>

	<div id="editItemPriceModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="ModalLabel"><i class="icon-edit-sign"></i>&nbsp;Different Price's Item</h3>
		</div>
		<div class="modal-body" id="editGodownItemPrice">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>

		</div>
	</div>

<div id="editItemModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemEditModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="itemEditModalLabel"><i class="icon-edit-sign"></i>&nbsp; Item Edit</h3>
		</div>
		<div class="modal-body" id="editItemBody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>

		</div>

</div>



	<script>
	$('.Items').addClass('active btn btn-fill');
		function ItemEdit(itemId){

			$(function() {
					$("#editItemBody").load("{{ URL::to('admin/itemEditForm') }}"+"/"+itemId);
			});
		}
		function ItemPriceEdit(itemId){
			$(function() {
				$("#editGodownItemPrice").load("{{ URL::to('updateGodownItemPrice') }}"+"/"+itemId);
			});
		}
		function gdDifferentPrice(itemId){
			$(function() {
				$("#diffPricebody").load("{{ URL::to('admin/godDifferentPriceItem') }}"+"/"+itemId);
			});
		}
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

function differentItemPriceEdit(itemId){
	$(function() {
				$("#diffPricebody").load("{{ URL::to('updateGodownItemDifferentPrice') }}"+"/"+itemId);
			});
}
			</script>
	
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