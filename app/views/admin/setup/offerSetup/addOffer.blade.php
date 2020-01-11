@extends('_layouts.default')

@section('content')
<style>		
	.head6{width: 240px}		
	.head4{width: 200px}	
	.dataTable tbody tr td{
		margin: 0;
		padding:2px 10px 0 10px;
	}
</style>
<div class="row">
	<div class="span12">			
		<div class="widget-header setup-title"> <i class="icon-list"></i>
		  <h3>Offer Type</h3>
		</div>
		{{ Form::open(array('route' => 'admin.addOffer', 'class' => 'form-horizontal', 'method' => 'GET')) }}
			<div class="control-group">
				<label for="offer_type" style="font-weight: bolder;" class="control-label"><i class="icon-hand-right"></i>&nbsp;&nbsp;Offer Type &nbsp; : &nbsp;</label>
				{{ Form::select('OfferType', ['' => 'Please Select Offer Type', '1' => 'Item Wise', '2' => 'Category Wise', '3' => 'Brand Wise'], null, array('class' => 'span3', 'required' => 'required')) }}
				<button type="submit" class="btn btn-info"><i class="icon-arrow-right"></i>&nbsp;Next</button>
			</div> <!-- /control-group -->
		{{ Form::close()}}
	</div> <!-- /widget-content -->
	<? //echo '<pre>';print_r($data); ?>
	<? if(isset($data['OfferType'])) : ?>
	<div class="span12">
	  <!--Item Datatable-->
		<input type="hidden" id="offer_type" name="offer_type" value="<?= isset($data['OfferType']) ? $data['OfferType'] : null ?>" />
	 
		<div class="widget-header setup-title"> <i class="icon-th-list "></i>
		  <h3>Offer Type : @if($data['OfferType'] == 1) {{ 'Item Wise' }} @elseif($data['OfferType'] == 2) {{ 'Category Wise' }} @else {{ 'Brand Wise' }} @endif</h3>
		</div>
		<div id="message" style="display: none;"></div>
	 @if($data['OfferType']== 1)	
		{{ Datatable::table()
				->addColumn('UPC Code','Item Name', 'Brand Name', 'Category Name', 'Offer', 'Action')
				->setUrl(route('admin.getItemWiseData'))
				->render() }}
	  @endif
	  <!--Brand Datatable-->
	  @if($data['OfferType']== 3)	
		{{ Datatable::table()
				->addColumn('id','Brand Name', 'Offer', 'Action')
				->setUrl(route('admin.getBrandWiseData'))
				->render() }}
	  @endif
	  <!--Category Datatable-->
	  @if($data['OfferType']== 2)	
		{{ Datatable::table()
				->addColumn('id','Category Name', 'Offer', 'Action')
				->setUrl(route('admin.getCategoryWiseData'))
				->render() }}
	  @endif
	</div>
	<? endif; ?>
</div>
<script>	
$('.Setup').addClass('active btn btn-fill');
	 function itemOffer(itemId){
		$(function(){
			var offer = $('#offer'+itemId).val();
			var offer_type = $('#offer_type').val();
			$.ajax({
				url: "itemOfferCreate/"+itemId,
				type : "GET",
				cache: false,
				dataType : 'json',
				data : {'offer': offer, 'offer_type': offer_type}, // serializes the form's elements.
				success : function(data){
					if(data.status == 'success'){
						$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> ID '+itemId+' Offer Created Successfully.</strong>');
						$('#message').css('display', 'block').fadeOut(4000);				
					} else{
						$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> '+data.status+'</strong>');
						$('#message').css('display', 'block').fadeOut(5000);						
					}
				},
				error: function(){}
			});			
		}) ;
	 }
	 function resetItemOffer(itemId){
		$(function(){
			var resetOffer = $('#offer'+itemId).val(0);
			var offer = 0;
			var offer_type = $('#offer_type').val();
			$.ajax({
				url: "itemOfferReset/"+itemId,
				type : "GET",
				cache: false,
				dataType : 'json',
				data : {'offer': offer, 'offer_type': offer_type}, // serializes the form's elements.
				success : function(data){
					if(data.status == 'success'){
						$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> Reset Item Successfully.</strong>');
						$('#message').css('display', 'block').fadeOut(4000);				
					} else{
						$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> '+data.status+'</strong>');
						$('#message').css('display', 'block').fadeOut(5000);						
					}
				},
				error: function(){}
			});			
		}) ;
	 }
	 //Brand Offer
	 function brandOffer(brandId){
		$(function(){
			var offer = $('#offerBrand'+brandId).val();
			var offer_type = $('#offer_type').val();
			$.ajax({
				url: "itemBrandCreate/"+brandId,
				type : "GET",
				cache: false,
				dataType : 'json',
				data : {'offer': offer, 'offer_type': offer_type}, // serializes the form's elements.
				success : function(data){
					if(data.status == 'success'){
						$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> ID '+brandId+' Offer Created Successfully.</strong>');
						$('#message').css('display', 'block').fadeOut(4000);				
					} else{
						$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> '+data.status+'</strong>');
						$('#message').css('display', 'block').fadeOut(5000);						
					}
				},
				error: function(){}
			});			
		}) ;
	 }
	function resetBrandOffer(brandId){
		$(function(){
			var resetOffer = $('#offerBrand'+brandId).val(0);
			var offer = 0;
			var offer_type = $('#offer_type').val();
			$.ajax({
				url: "itemBrandReset/"+brandId,
				type : "GET",
				cache: false,
				dataType : 'json',
				data : {'offer': offer, 'offer_type': offer_type}, // serializes the form's elements.
				success : function(data){
					if(data.status == 'success'){
						$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> Reset Offer Successfully.</strong>');
						$('#message').css('display', 'block').fadeOut(4000);				
					} else{
						$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> '+data.status+'</strong>');
						$('#message').css('display', 'block').fadeOut(5000);						
					}
				},
				error: function(){}
			});			
		}) ;
	 } 
	 //Category Offer
	 function categoryOffer(categoryId){
		$(function(){
			var offer = $('#offerCategory'+categoryId).val();
			var offer_type = $('#offer_type').val();
			$.ajax({
				url: "itemCategoryCreate/"+categoryId,
				type : "GET",
				cache: false,
				dataType : 'json',
				data : {'offer': offer, 'offer_type': offer_type}, // serializes the form's elements.
				success : function(data){
					if(data.status == 'success'){
						$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> ID '+categoryId+' Offer Created Successfully.</strong>');
						$('#message').css('display', 'block').fadeOut(4000);				
					} else{					
						$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> '+data.status+'</strong>');
						$('#message').css('display', 'block').fadeOut(5000);						
					}
				},
				error: function(){}
			});			
		}) ;
	 }
	function resetCategoryOffer(categoryId){
		$(function(){
			var resetOffer = $('#offerCategory'+categoryId).val(0);
			var offer = 0;
			var offer_type = $('#offer_type').val();
			//alert(resetOffer);
			$.ajax({
				url: "itemCategoryReset/"+categoryId,
				type : "GET",
				cache: false,
				dataType : 'json',
				data : {'offer': offer, 'offer_type': offer_type}, // serializes the form's elements.
				success : function(data){
					if(data.status == 'success'){
						$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> Reset Offer Successfully.</strong>');
						$('#message').css('display', 'block').fadeOut(4000);				
					} else{					
						$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> '+data.status+'</strong>');
						$('#message').css('display', 'block').fadeOut(5000);						
					}
				},
				error: function(){}
			});		
		});		
	}
</script>
@stop
@section('stickyInfo')
<?php
    $string = 'Setup';
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