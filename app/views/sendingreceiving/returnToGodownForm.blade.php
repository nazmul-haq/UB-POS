@extends('_layouts.default')

@section('content')
	<div class="row">
		<div class="span12">
			@include('_sessionMessage')
		</div>

		<div class="span8">
<div class='label label-info'><h4><i class='icon-shopping-cart'></i> Sending to Warehouse</h4></div>
			<div class="invoice-reg">
				{{ Form::open(array('route' => 'returnToGodown.itemAddForsending', 'id' => 'formItemLocation', 'class' => 'form-horizontal')) }}
					<div class="control-group">
						{{ Form::label('search_item', 'Find/Scan Item', ['class' => 'control-label', 'style' => 'font-weight: bold;']) }}
						<div class="controls">
							{{ Form::text('item_id', null, array('class' => 'span6', 'id'=>'auto_search_item', 'placeholder' => 'Start Typing item\'s name or scan barcode...')) }}
						</div> <!-- /controls -->
					</div> <!-- /control-group -->
				{{ Form::close() }}
			</div>

			<table class="table table-striped" width="100%">
				<thead class="table-head">
					<tr>
						<th>SL #</th>
						<th>Item Name</th>
						<th>Purchase Price</th>
						<th>Sale Price</th>
						<th>Available Quantity</th>
						<th>Now Send</th>
						<th>Total Price</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php $i=0; $total_amount=0; 
                                    if(Session::get('returnToGodown')){
                                    $reverse_items= array_reverse(Session::get('returnToGodown'));
                                    }
                                        ?>
					@if(Session::get('returnToGodown'))
					   @foreach($reverse_items as $item)
						 <? $total_amount = $total_amount + $item['total_price']; ?>
						 {{ Form::open(array('route' => 'returnToGodown.returnEditDeleteSending', 'class' => 'form-horizontal')) }}
							<tr>
								<td>{{++$i}}</td>
								<td>{{$item['item_name']}}<input type="hidden" name="stock_item_id" value="{{$item['stock_item_id']}}"><input type="hidden" name="item_id" value="{{$item['item_id']}}"><input type="hidden" name="item_name" value="{{$item['item_name']}}"><input type="hidden" name="price_id" value="{{$item['price_id']}}"></td>
								<td><input class="span1" type="text" name="purchase_price" readonly value="{{$item['purchase_price']}}"></td>
								<td><input class="span1" type="text" name="sale_price" readonly value="{{$item['sale_price']}}"></td>
								<td><input class="input-small" type="text" name="available_quantity" readonly value="{{$item['available_quantity']}}"></td>
								<td><input class="span1 send_qty" type="text" name="now_send" value="{{$item['now_send']}}"></td>
								<td><input type="text" class="span1 disabled" disabled="" value="{{$item['total_price']}}" /></td>
								<td class="span2">
									<button type="submit" class="edit btn btn-primary" name="edit_delete" value="edit"><i class="icon-edit"> Edit</i></button>
									<button type="submit" class="btn btn-warning" name="edit_delete" value="delete"><i class="icon-trash"></i></button>
								</td>
							</tr>
                            {{ Form::close() }}
                           @endforeach
						@else
							<tr>
								<td colspan="7" style="text-align:center; color:#E98203;"><strong>There are no sending items in the cart</strong><td>
							</tr>
					@endif
				</tbody>
			</table>
		</div>
		<!--Supplier--->
		<div class="span4">
			<div class="invoice-right">
				<div>
					{{ Form::open(array('route' => 'returnToGodown.saveReturnToGodown', 'class' => 'form-horizontal')) }}
					<table class="table table-striped">
						<tbody>
							<tr>
								<td>Date </td>
								<td>:</td>
								<td>
                                    <input id="dp3" name="sendingDateTime" data-date="<?= date("Y-m-d") ?>" data-date-format="yyyy-mm-dd" class="datepicker" type="text" value="<?= date("Y-m-d") ?>">
								</td>
							</tr>
							<tr>
								<td>Total Amount</td>
								<td>:</td>
								<td><strong id="total_amount" style="color: green; font-size: 1.4em;">{{$total_amount}}</strong></td>
							</tr>
						</tbody>
					</table>
					<div style="text-align: left; margin-left: 10px;">
						<button type="submit" class="btn btn-primary">Save Changes</button>
					</div>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>

	<script>
		$(function(){
			
			$('.send_qty').keyup(function(){
				this.value = Math.abs(this.value);
				var available_qty = $(this).parent().prev().children().val();
				var this_value	  = parseFloat(this.value);
				if(isNaN(this_value)){
					this.value = 0;
				}
				if(this_value > available_qty){
					this.value = available_qty;
					alert('Available quantity not available.');
				}

			});
		});
	</script>
	{{ HTML::script('js/jquery-ui.min.js') }}
<script>
		 $().ready(function(){
			 //Auto Complete for Item Search
			 $("#auto_search_item").autocomplete("{{route('returnToGodown.itemAutoSuggest')}}", {
				width: 260,
				matchContains: true,
				queryDelay : 0,
				formatItem: function(row) {
					return row[1];
				},
			});
			//Submit Search Item Form
			$("#auto_search_item").result(function(event, data, formatted) {
				$("#formItemLocation").submit();
			});



			});



	</script>


@stop