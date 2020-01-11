@extends('_layouts.default')

@section('content')
	<div class="row">
	  <div class="span12">
		<div class='label label-info' style="margin-bottom: 5px;">
			<h4><i class='icon-shopping-cart'></i> Item  receiving in Warehouse</h4>
		</div>
		@include('_sessionMessage')
		{{ Form::open(array('route' => 'returnReceive.savereturnReceiveItem', 'class' => 'form-horizontal', 'id' => 'form')) }}
		<table class="table table-striped">
			<thead class="table-head">
				<tr>
					<th><input type="checkbox" id="check_all" class=""> Check All</th>
					<th>Receiving ID</th>
					<th>SL</th>
					<th>Item Id</th>
					<th>Item Name</th>
					<th>Purchase Price</th>
					<th>Sale Price</th>
					<th>Quatity</th>
					<th>Total Price</th>
				</tr>
			</thead>
			<tbody>
				<? $i=0; ?>
				@if($items)
					<p>
						<button class="btn btn-primary" id="receiveSubmit" name="accept_cancel" value="Accept"><i class="icon-ok"> Accept</i></button>
						<button class="btn btn-warning" id="receiveSubmit" name="accept_cancel" value="Discard"><i class="icon-remove"> Discard</i></button>
					</p>
					@foreach($items as $item)
					 	<tr>
						   <td>
								<input class="check" type="checkbox" name="r_receiving_item_ids[]" value="{{$item->r_receiving_item_id}}" >
						   </td>

						   <td>{{$item->r_receiving_item_id}}</td>
						   <td>{{++$i}}</td>
						   <td>{{$item->item_id}}</td>
						   <td>{{$item->item_name}}</td>
						   <td>{{$item->purchase_price}}</td>
						   <td>{{$item->sale_price}}</td>
						   <td>{{$item->quantity}}</td>
						   <td>{{$item->sale_price*$item->quantity}}</td>

						   <input type="hidden"  value="{{$item->item_id}}" id="item{{$item->item_id}}" class='ItemId uncheck'>
						   <input type="hidden"  value="{{$item->price_id}}" class='PriceId uncheck'>
						   <input type="hidden" value="{{$item->quantity}}" class='Quantity uncheck'>
					   </tr>
					@endforeach
					@else
						<tr>
							<td colspan="8" style="text-align:center; color:#E98203;"><strong>There are no receiving items in the cart</strong><td>
						</tr>
				@endif
			</tbody>
		</table>
		{{ Form::close() }}
	  </div>
	</div>
	<script>
	  $(document).ready(function(){
			$("#check_all").on("click", function(){
				$(".check").prop('checked', $(this).prop('checked'));
			});
			$('#receiveSubmit').click(function(){
				$('#form').submit();
			});
		});
	</script>
@stop
