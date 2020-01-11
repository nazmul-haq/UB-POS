@extends('_layouts.default')

@section('content')
	
<div class="row">
	<div class="span12">			
		<div class="widget-header setup-title"> <i class="icon-th-list "></i>
		  <h3>View Offer</h3>
		</div>
		
		{{ Form::open(array('route' => 'admin.showOffer.post', 'class' => 'form-horizontal')) }}
			<div class="control-group">
				<label for="offer_type" style="font-weight: bolder; margin-left: 20px;" class="control-label"><i class="icon-hand-right"></i>&nbsp;&nbsp;Select Offer Type: &nbsp;</label>
				{{ Form::select('OfferType', ['' => 'Please Select Offer Type', '1' => 'Item Wise', '2' => 'Category Wise', '3' => 'Brand Wise'], null, array('class' => 'span3', 'required' => 'required')) }}
				<button type="submit" class="btn btn-info"><i class="icon-arrow-right"></i>&nbsp;Next</button>
			</div> <!-- /control-group -->
		{{ Form::close()}}
		<? if(isset($data['OfferType'])) : ?>
		<div class="widget-header setup-title"> <i class="icon-th-list "></i>
		  <h3>Offer View : @if($data['OfferType'] == 1) {{ 'Item Wise' }} @elseif($data['OfferType'] == 2) {{ 'Category Wise' }} @else {{ 'Brand Wise' }} @endif</h3>
		</div>
		<table class="table table-striped" width="100%">
			<thead class="table-head">
				<tr>
					<th>#</th>
					<th>@if($data['OfferType'] == 1) {{ 'Item Name' }} @elseif($data['OfferType'] == 2) {{ 'Category Name' }} @else {{ 'Brand Name' }} @endif</th>
					<th>Offer(%)</th>
				</tr>
			</thead>
			<tbody>			
				<? $i = 0; ?>
				@if($data['OfferType'] == 1)
					@foreach($getOfferType['itemOffers'] as $itemOffer)
						<tr>
							<? $i++; ?>
							<td>{{ $i }}</td>
							<td>{{ $itemOffer->item_name }}</td>
							<td>{{ $itemOffer->offer }}</td>
						</tr>
					@endforeach
				@endif
				
				@if($data['OfferType'] == 2)
					@foreach($getOfferType['categoryOffers'] as $categoryOffer)
						<tr>
							<? $i++; ?>
							<td>{{ $i }}</td>
							<td>{{ $categoryOffer->category_name }}</td>
							<td>{{ $categoryOffer->offer }}</td>
						</tr>
					@endforeach
				@endif
				
				@if($data['OfferType'] == 3)
					@foreach($getOfferType['brandOffers'] as $brandOffer)
						<tr>
							<? $i++; ?>
							<td>{{ $i }}</td>
							<td>{{ $brandOffer->brand_name }}</td>
							<td>{{ $brandOffer->offer }}</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
		<? endif; ?>
	</div> 
</div>

<script>
    $('.Setup').addClass('active btn btn-fill');
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