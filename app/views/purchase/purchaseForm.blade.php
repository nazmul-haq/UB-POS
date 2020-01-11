@extends('_layouts.default')

@section('content')

            <?php $amount=0;if(Session::get('items')){ foreach(Session::get('items') as $item)
                                $amount=$amount+$item['total'];
                                 }
             ?>
	<div class="row">
		<div class="span8">
	            @include('_sessionMessage')
				<div class='label label-info' style='height:24px'><h4 style='display:inline;'><i class='icon-shopping-cart'></i> Purchase Register</h4>
				<span style="color: orange; padding-left: 20px;"> Total items </span> 
				<span id='topTotalQuantity' class='label label-warning' style='font-size:22px; border-radius:90px;'>  </span>
				<a style="float:right; @if(!Session::get('items')) display: none; @endif"  class="btn btn-warning btn-small clearScreen" href="{{route('purchase.emptyCart')}}">Clear Screen</a>
				</div>
			<table class="table table-striped" width="100%">
				<thead class="table-head">
					<tr>
						<th>SL</th>
						<th>Item Name</th>
						<th>Purchase Price</th>
						<th>Sale Price</th>
						<th>Qty.</th>
						<!-- <th>Disc (Tk)</th> -->
						<th>Total</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody class="addTr">
				<?php $i=0; $invoice_total=0; 
                    if(Session::get('items')){
                    	$reverse_items = Session::get('items');
                    }
                    $totalQuantity = 0;
                ?>
					@if(Session::get('items'))						
						@foreach($reverse_items as $item)
							<? $invoice_total=$invoice_total+$item['total'];
								$totalQuantity += $item['quantity'];
							?>
							{{ Form::open(array('route' => 'purchase.editDeleteItem', 'class' => 'form-horizontal itemAddCartForm')) }}
							<tr>
								<td>{{++$i}}</td>
								<td>
									<span class="span3" id="itemName_{{$item['item_id']}}">
										{{$item['item_name']}}
									</span>
									<input type="hidden" name="item_id" value="{{$item['item_id']}}">
									<input type="hidden" name="price_id" id="priceId_{{$item['item_id']}}" value="{{$item['price_id']}}">
									<input type="hidden" name="item_name" value="{{$item['item_name']}}">
								</td>
								<td>
									<input class="span1 floatingCheck purchaseQuanty" type="text" name="purchase_price" id="purchasePrice_{{$item['item_id']}}" value="{{$item['purchase_price']}}" />
								</td>
								<td>
									<input class="span1 floatingCheck purchaseQuanty" type="text" id="salePrice_{{$item['item_id']}}" name="sale_price" value="{{$item['sale_price']}}" />
								</td>
								<td>
									<input class="span1 Quanty purchaseQuanty" type="text" maxlength="5" name="quantity" id="pcs_{{$item['item_id']}}" value="{{$item['quantity']}}" />
								</td>
								<!-- <td>
									<input class="span1 floatingCheck purchaseQuanty" type="text" maxlength="5" name="discount" value="{{$item['discount']}}" />
								</td> -->
								<td>
									<input type="text" name="total" id="total_{{$item['item_id']}}"  class="span1 disabled" disabled="" value="{{$item['total']}}" />
								</td>
								<td class="span1">
									<button type="submit" id="deleteItem_{{$item['item_id']}}" class="btn btn-warning btn-delete" name="edit_delete"><i class="icon-trash"></i></button>
								</td>
							</tr>
							{{ Form::close() }}
						@endforeach
					@endif
						<tfoot>
			                <tr>
			                    <td colspan="8" style="text-align:center; color:#E98203;">
			                    <strong>Total Item : <span id="cartAddedQty">{{count(Session::get('items'))}}</span></strong>
			                    <input type='hidden' id='toalQuantity' value='{{ $totalQuantity }}'>
			                    <td>
			                </tr>
			            </tfoot>
				</tbody>
			</table>
			<div class="invoice-reg">
				{{ Form::open(array('route' => 'purchase.addItemToChart', 'id' => 'formItemLocation', 'class' => 'form-horizontal itemAddCartForm')) }}
					<div class="control-group">		
						{{ Form::label('search_item', 'Find/Scan Item', ['class' => 'control-label', 'style' => 'font-weight: bold;font-weight:bold;margin-top:7px;font-size:18px; color : #022a65;']) }}
						<div class="controls">
							
							<input type="text" name="item_id" class='span6 item_id' autofocus='yes' id='auto_search_item' placeholder='Start Typing item name or scan barcode...' @if(Session::has('is_purchase_order')) disabled="" @endif style='height: 30px;border: 1px solid #022a65;'>
						</div> <!-- /controls -->					
					</div> <!-- /control-group -->	
				{{ Form::close() }}
			</div>
		</div>
		<!--Supplier--->
		<div class="span4">
			<div class="invoice-right">
				{{ Form::open(array('route' => 'purchase.selectDeleteSupplier', 'id' => 'supplierForm')) }}
					@if(Session::get('invoice_info.supp_or_comp_name'))
						<div class="control-group hr">											
							<label class="control-label" for="supplier_name"><strong style="font-size: 1.1em;"><u>Supplier Name</u></strong></label>
							<div class="controls">
								<strong style="color:#348100; font-size: 1.1em;">
									{{Session::get('invoice_info.supp_or_comp_name')}}
								</strong>
                                <input type="hidden" id="supplier" value="{{Session::get('invoice_info.supp_or_comp_name')}}">
								<p>
								<button type="submit" class="btn btn-warning" name="supplier" value="delete"><i class="icon-trash"></i> &nbsp;Remove</button>
							</div> <!-- /controls -->				
						</div>
					@else
						<div class="control-group hr">											
							<label class="control-label" for="supplier">Select Supplier (Optional)</label>
							<div class="controls">
								{{Form::text('supp_id', null, array('class' => 'span3', 'id' => 'supplierAutoSugg', 'placeholder' => 'Start Typing Supplier\'s name...'))}}
                                                                <input type="hidden" id="supplier" value="">
                                                        </div> <!-- /controls -->
						</div>
						<div class="control-group hr">											
							<label class="control-label" for="supplier">Purchase Saved For</label>
							<div class="controls">
								@if(Session::get('branch_id'))
									@if(Session::get('branch_id') == 1)
									{{Form::text('branch_id','MB Trade', array('class' => 'span3', 'readonly', 'id' => 'supplierAutoSugg', 'placeholder' => 'Start Typing Supplier\'s name...'))}}
									@elseif(Session::get('branch_id') == 2)
									{{Session::get('branch_id')}}
									{{Form::text('branch_id','MB Collection', array('class' => 'span3','readonly', 'id' => 'supplierAutoSugg', 'placeholder' => 'Start Typing Supplier\'s name...'))}}
									@elseif(Session::get('branch_id') == 3)
									{{Form::text('branch_id','MB Gulshan', array('class' => 'span3','readonly', 'id' => 'supplierAutoSugg', 'placeholder' => 'Start Typing Supplier\'s name...'))}}
									@else
									@endif
								@endif
                                                        </div> <!-- /controls -->
						</div>
					@endif  
				{{ Form::close() }}
				<div>
					{{ Form::open(array('route' => 'purchase.invoiceAndPurchase', 'autocomplete'=>'off', 'class' => 'form-horizontal')) }}
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
								<td>Memo No </td>
								<td>:</td>
								<td style="padding:0;">
                                    <input id="sup_momo_no" name="sup_momo_no" maxlength="14" class="span2" type="text" value="">
								</td>
							</tr>
							<tr>
								<td>Payment Type</td>
								<td>:</td>
								<td style="padding:0;">
									{{ Form::select('payment_type_id', $payment_type, 1, ['class' => 'span3', 'style' => 'margin-top: 3px;']) }}
								</td>
							</tr>
							<tr>
								<td>Sub Total</td>
								<td>:</td>
								<td><strong id="total_amount" style="color: green; font-size: 1.4em;">{{$invoice_total}}</strong></td>
							</tr>
							<tr>
								<td>Discount</td>
								<td>:</td>
								<td style="padding:0;">
									<input style="margin-top: 3px; width:95px;" maxlength="5" type="text" id="dis_percent" name="discount_percent" data-toggle="tooltip" title="Discount (%)"  placeholder="Discount(%)">&nbsp;&nbsp;
									<input style="margin-top: 3px; width:95px;" maxlength="10"  type="text" id="dis_taka" name="invoice_discount"  data-toggle="tooltip" title="Discount (Tk.)" placeholder="Discount(Taka)">
								</td>
							</tr>
							<tr>
								<td>Total Amount(Tk)</td>
								<td>:</td>
								<td><strong id="pay_amount" style="color: green; font-size: 1.5em;">{{$invoice_total-Session::get('invoice_info.invoice_discount')}}</strong></td>
							</tr>
							<tr>
								<td>Pay</td>
								<td>:</td>
								<td style="padding:0;">
									<div class="input-prepend input-append">
									  <span class="add-on">৳</span>
                                        <input type="text" maxlength="10" id="appendedPrependedInput" class="span2" name="pay" value="{{$amount}}">
									  <span class="add-on">.00</span>
									</div>
								</td>
							</tr>
							 <tr>
								<td>Due</td>
								<td>:</td>
								<td><strong id="due" style="color: green; font-size: 1.4em;">0.00</strong></td>
							</tr>
						</tbody>
					</table>
					<div style="text-align: left; margin-left: 10px;">	
					@if(Session::has('is_purchase_order'))				
						<button type="submit" class="btn btn-success" name="purchase_order"  onclick="return isSupplierAvailable();">Purchase Order</button>
					@else
						<button type="submit" class="btn btn-danger" name="purchase"  onclick="return isSupplierAvailable();">Complete Purchase</button>
					@endif
					</div>
					{{ Form::close() }}
				</div>				
				
			</div>
		</div>
	</div>
	{{ HTML::script('js/jquery-ui.min.js') }}
	@include('purchase.pruchaseFormAjaxRequest')

@stop
@section('stickyInfo')
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
		<li>P</li><li>u</li><li>r</li><li>c</li><li>h</li><li>a</li><li>s</li><li>e</li>
	</ul>       
</div>
@stop