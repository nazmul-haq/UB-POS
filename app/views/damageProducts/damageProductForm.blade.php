@extends('_layouts.default')

@section('content')

<? 
	$amount = 0;
	if(Session::get('damage_items')){ 
		foreach(Session::get('damage_items') as $item)
			$amount = $amount + $item['total'];
	}
?>
	<div class="row">
		<div class="span8">
			@include('_sessionMessage')
			<div class='label label-info'><h4><i class='icon-shopping-cart'></i> Damage Products Register</h4></div>
			<div class="invoice-reg">
				{{ Form::open(array('route' => 'damage.addItemToChart', 'id' => 'formValidation', 'class' => 'form-horizontal')) }}
					<div class="control-group">		
						{{ Form::label('search_item', 'Find/Scan Item', ['class' => 'control-label', 'style' => 'font-weight: bold;']) }}
						<div class="controls">
							{{ Form::text('item_id', null, array('class' => 'span6', 'autofocus'=>'yes', 'id'=>'auto_search_item', 'placeholder' => 'Start Typing item\'s name or scan barcode...')) }}
						</div> <!-- /controls -->					
					</div> <!-- /control-group -->	
				{{ Form::close() }}
			</div>			
						
			<table class="table table-striped" width="100%">
				<thead class="table-head">
					<tr>
						<th>SL</th>
						<th>Item Name</th>
						<th>Purchase Price</th>
						<th>Available Qty.</th>
						<th>Damage Qty.</th>
						<th>Total</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php $i=0; $invoice_total=0; ?>
					@if(Session::get('damage_items'))						
						@foreach(Session::get('damage_items') as $item)
							<? $invoice_total=$invoice_total+$item['total'];?>
							{{ Form::open(array('route' => 'damage.editDeleteItem', 'class' => 'form-horizontal')) }}
							<tr>
								<td>{{++$i}}</td>
								<td>
									<span class="span3">
										{{$item['item_name']}}<input type="hidden" name="item_id" value="{{$item['item_id']}}">
										<input type="hidden" name="stock_item_id" value="{{$item['stock_item_id']}}">
									</span>
								</td>
								<td>
									<input class="span1 floatingCheck" readonly="" type="text" name="purchase_price" value="{{$item['purchase_price']}}" />
								</td>
								<td>
									<input class="span1" readonly="" type="text" name="available_qty" value="{{$item['available_quantity']}}" />
								</td>
								<td>
									<input class="span1 Quanty" type="text" maxlength="5" name="damage_quantity" value="{{$item['damage_quantity']}}" />
								</td>
								<td>
									<input type="text" name="total"  class="span1 disabled" disabled="" value="{{$item['total']}}" />
								</td>
								<td class="span2">
									<button type="submit" class="edit btn btn-primary" name="edit_delete" value="edit"><i class="icon-edit"> Edit</i></button>
									<button type="submit" class="btn btn-warning" name="edit_delete"><i class="icon-trash"></i></button>
								</td>
							</tr>
							{{ Form::close() }}
						@endforeach
						@else
							<tr>
								<td colspan="6" style="text-align:center; color:#E98203;"><strong>There are no items in the cart</strong><td>
							</tr>
					@endif
				</tbody>
			</table>
		</div>
		<!--Supplier--->
		<div class="span4">
			<div class="invoice-right">
				<div>
					<h3>Calculation</h3>
					{{ Form::open(array('route' => 'damage.invoiceAndDamaged', 'autocomplete'=>'off', 'class' => 'form-horizontal')) }}
					<table class="table table-striped" style="margin: 0; padding:0;">
						<tbody>
							@if(Session::get('backdate_purchase')==0)
							<input name="date" type="hidden" value="<?= date("Y-m-d") ?>">
							@else
							<tr>
								<td>Date </td>
								<td>:</td>
								<td style="padding:0;">
                                    <input id="dp3" name="date" data-date="<?= date("Y-m-d") ?>" data-date-format="yyyy-mm-dd" class="datepicker span2" type="text" value="<?= date("Y-m-d") ?>">
								</td>
							</tr>
							@endif
							<tr>
								<td>Total Amount(Tk)</td>
								<td>:</td>
								<td><strong id="pay_amount" style="color: green; font-size: 1.5em;">{{$invoice_total-Session::get('invoice_info.invoice_discount')}}</strong>
								</td>
							</tr>
						</tbody>
					</table>
					<div style="text-align: left; margin-left: 10px;">					
						<button type="submit" class="btn btn-danger">Save Changes</button>
					</div>
					{{ Form::close() }}
				</div>				
				
			</div>
		</div>
	</div>
	{{ HTML::script('js/jquery-ui.min.js') }} 
	<script>
		 $().ready(function(){			 
			 //Auto Complete for Item Search
			 $("#auto_search_item").autocomplete("{{route('damage.itemAutoSuggest')}}", {
				width: 260,
				matchContains: true,
				queryDelay : 0,
				formatItem: function(row) {
					return row[1];
				},				
			});	
			//Submit Search Item Form
			$("#auto_search_item").result(function(event, data, formatted) {
				$("#formValidation").submit();
			});
					
		$('.Quanty').blur(function(){
			var intRegex = /^\d+$/;
			var floatRegex = /^((\d+\.(\.\d *)?)|((\d*\.)?\d+))$/;
			var str = $(this).val();
			if(this.value==''){
			   this.value=1;
			} else if(this.value=='0') {
					this.value=1;
			} else if(intRegex.test(str) || floatRegex.test(str)) {
			} else{
				alert('Wrong Data');
				this.value = 1;
			}

        });
                           
		$('.floatingCheck').keyup(function(){
			var intRegex = /^\d+$/;
			var floatRegex = /^((\d+\.(\.\d *)?)|((\d*\.)?\d+))$/;
			var str = $(this).val();
			if(this.value==''){
			   this.value='';
			} else if(intRegex.test(str) || floatRegex.test(str)) {
			} else{
				alert('Wrong Data');
				this.value = 0;
			}
		});
			
	});
	
	</script>
	
	<!--end purchase-->
	
	
	


        

       
    

@stop