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
					@if(Session::get('items'))
					<a style="float:right;"  class="btn btn-warning btn-small" href="{{route('purchase.emptyCart')}}">Clear Screen</a>
					@endif
					</div>
			<div class="invoice-reg">
				{{ Form::open(array('route' => 'purchase.addItemToChart', 'id' => 'formItemLocation', 'class' => 'form-horizontal')) }}
					<div class="control-group">		
						{{ Form::label('search_item', 'Find/Scan Item', ['class' => 'control-label', 'style' => 'font-weight: bold;']) }}
						<div class="controls">
							
							<input type="text" name="item_id" class='span6' autofocus='yes' id='auto_search_item' placeholder='Start Typing item name or scan barcode...' @if(Session::has('is_purchase_order')) disabled="" @endif>
						</div> <!-- /controls -->					
					</div> <!-- /control-group -->	
				{{ Form::close() }}
			</div>			
						
			<table class="table table-striped" width="100%">
				<thead class="table-head">
					<tr>
						<th>SL</th>
						<th># Item</th>
						<th>Item Name</th>
						<th>Purchase Price</th>
						<th>Sale Price</th>
						<th>Qty.</th>
						<th>Disc (Tk)</th>
						<th>Total</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				<?php $i=0; $invoice_total=0; 
                                    if(Session::get('items')){
                                    $reverse_items= array_reverse(Session::get('items'));
                                    }
                                ?>
					@if(Session::get('items'))						
						@foreach($reverse_items as $item)
							<? $invoice_total=$invoice_total+$item['total'];?>
							{{ Form::open(array('route' => 'purchase.editDeleteItem', 'class' => 'form-horizontal')) }}
							<tr>
								<td>{{++$i}}</td>
								<td>{{$item['item_id']}}</td>
								<td>
									<span class="span3">
										{{$item['item_name']}}<input type="hidden" name="item_id" value="{{$item['item_id']}}"><input type="hidden" name="item_name" value="{{$item['item_name']}}">
									</span>
								</td>
								<td>
									<input class="span1 floatingCheck" type="text" name="purchase_price" value="{{$item['purchase_price']}}" />
								</td>
								<td>
									<input class="span1 floatingCheck salePrice" type="text" name="sale_price" value="{{$item['sale_price']}}" />
								</td>
								<td>
									<input class="span1 Quanty" type="text" maxlength="5" name="quantity" value="{{$item['quantity']}}" />
								</td>
								<td>
									<input class="span1 floatingCheck" type="text" maxlength="5" name="discount" value="{{$item['discount']}}" />
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
								<td colspan="8" style="text-align:center; color:#E98203;"><strong>There are no items in the cart</strong><td>
							</tr>
					@endif
				</tbody>
			</table>
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
	<script>
		 $().ready(function(){
salesInit();			 
function salesInit() {
		shortcut.add("Alt+c", function() {
			window.location = "{{route('purchase.emptyCart')}}";
		});
}		
			 //Auto Complete for Item Search
			 $("#auto_search_item").autocomplete("{{route('purchase.itemAutoSuggest')}}", {
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
			
			//supplier auto suggest
			$("#supplierAutoSugg").autocomplete("{{route('purchase.autoSupplierSuggest')}}", {
				width: 260,
				matchContains: true,
				queryDelay : 0,
				formatItem: function(row) {
					return row[1];
				},	
			});
			//Submit Supplier Form
			$("#supplierAutoSugg").result(function(event, data, formatted) {
				$("#supplierForm").submit();
			});
			
			//Discount Calculate for percent
			$('#dis_percent').on('keyup', function() {
				//var discount_taka = parseFloat(this.value);
				var intRegex = /^\d+$/;
                                var floatRegex = /^((\d+\.(\.\d *)?)|((\d*\.)?\d+))$/;
                                var $total_amount = $('#total_amount').html();
                                
                                var str = $(this).val();
                                console.log(str);
                                if(this.value==''){
                                    this.value=null;
                                    $('#dis_taka').val(0);
				    $('#pay_amount').html($total_amount);
                                    $('#appendedPrependedInput').val($total_amount);
                                }
                               else if(intRegex.test(str) || floatRegex.test(str)) {
					var abs_value = Math.abs(parseFloat(this.value));
                                        
                                        var confirm_discount_percent=0;
					console.log(abs_value);
					$('#dis_taka').attr('readonly','readonly');
					

					if(isNaN(abs_value)||(abs_value>99)){
                                            if(isNaN(abs_value)){
						this.value = 0;
                                                confirm_discount_percent=0;
                                            }
                                            else{
                                                this.value = 100;
                                                confirm_discount_percent=100;
                                            }
					}
					else{
                                            confirm_discount_percent=this.value;
                                        }

					$cal_discount_taka = (confirm_discount_percent*$total_amount)/100;
					$payable_amount = $total_amount-$cal_discount_taka ;

					$('#dis_taka').val((isNaN($cal_discount_taka)) ? 0 : $cal_discount_taka.toFixed(2));
					$('#pay_amount').html((isNaN($payable_amount)) ? $total_amount : $payable_amount.toFixed(2));
                                        $('#appendedPrependedInput').val($payable_amount.toFixed(2));
				}
                                else {
                                     alert('Invalid Character! Please Check.');
                                     this.value = '';
                                     $('#dis_taka').val(0);
				     $('#pay_amount').html($total_amount);
                                     $('#appendedPrependedInput').val($total_amount);
                                     return false;
                                    }
                            });
			//Discount Calculate for Taka
			$('#dis_taka').on('keyup', function() {
				//var discount_taka = parseFloat(this.value);
				var intRegex = /^\d+$/;
                                var floatRegex = /^((\d+\.(\.\d *)?)|((\d*\.)?\d+))$/;

                                var str = $(this).val();
                                console.log(str);
                                if(this.value==''){
                                 this.value=null;
                                 $('#dis_percent').val(0);
                                 $('#pay_amount').html(parseInt($('#total_amount').html()));
                                 $('#appendedPrependedInput').val(parseInt($('#total_amount').html()));

                                 }
                              else if(intRegex.test(str) || floatRegex.test(str)) {
					var abs_value = Math.abs(parseFloat(this.value));
                                        var confirm_discount=0;
					console.log(abs_value);
					$('#dis_percent').attr('readonly','readonly');
					var $total_amount = $('#total_amount').html();
                                        if(isNaN(abs_value)||(abs_value>=$total_amount)){
                                            if(isNaN(abs_value)){
                                                    this.value = 0;
                                                    confirm_discount=0;
                                            }
                                            else{
                                                    this.value = $total_amount;
                                                    confirm_discount=$total_amount;
                                            }
                                        }
                                        else{
                                            confirm_discount=this.value;
                                        }
					$cal_amount_onDiscount = (confirm_discount*100)/$total_amount;
					$payable_amount = $total_amount-confirm_discount ;

					$('#dis_percent').val((isNaN($cal_amount_onDiscount)) ? 0 : $cal_amount_onDiscount.toFixed(2));
					$('#pay_amount').html((isNaN($payable_amount)) ? $total_amount : $payable_amount.toFixed(2));
                                        $('#appendedPrependedInput').val($payable_amount.toFixed(2));
				}
                                else {
                                     alert('Invalid Character! Please Check.');
                                     this.value = '';
                                     $('#dis_percent').val(0);
				     $('#pay_amount').html(parseInt($('#total_amount').html()));
                                     $('#appendedPrependedInput').val(parseInt($('#total_amount').html()));
                                     return false;
                                    }
                            });
                        $('#appendedPrependedInput').keyup(function(){
				//var discount_taka = parseFloat(this.value);
				var regex =  /^\d*(?:\.{1}\d+)?$/;

                                var pay =parseInt(this.value);
                                var $payable = parseInt($('#pay_amount').html());
                               if(this.value==''){
                                    this.value=null;
                                    $('#due').html($payable);
                               }
                               else{
				if (this.value.match(regex)) {


                                        if(pay>$payable){
                                            this.value=$payable;
                                            $('#due').html(0.00);
                                        }
                                        else{
                                        $('#due').html($payable-pay);
                                        }

				}

                               else{   this.value='';
                                       $('#due').html($payable);;
                                   }
                               }
			});


                      $('.Quanty').blur(function(){
                                var intRegex = /^\d+$/;
                                var floatRegex = /^((\d+\.(\.\d *)?)|((\d*\.)?\d+))$/;
                                var str = $(this).val();
                                if(this.value==''){
                                   this.value=1;
                                }
                                else if(this.value=='0') {
                                        this.value=1;
                                    }
                                else if(intRegex.test(str) || floatRegex.test(str)) {


                                    }
                                else{
                                        alert('Wrong Data');
					this.value = 1;
				}

                            });
                     $('.salePrice').blur(function(){                     			
                                var purchasePrice = parseFloat($(this).parent().prev().children().val());
                                var salePrice = parseFloat($(this).val());
                                if(purchasePrice>salePrice)  {
                                	alert("Sale Price can not be less then purchase price");
                                	this.value=purchasePrice;
                                }                          

                            });


                           
                            $('.floatingCheck').keyup(function(){




                                var intRegex = /^\d+$/;
                                var floatRegex = /^((\d+\.(\.\d *)?)|((\d*\.)?\d+))$/;
                                var str = $(this).val();
                                if(this.value==''){
                                   this.value='';
                                }
                                else if(intRegex.test(str) || floatRegex.test(str)) {

                                    }
                                else{
                                        alert('Wrong Data');
					this.value = 0;
				}
                            });
			
			//for input box tooltip
			$('input[type=text][name=discount_percent]').tooltip({
				placement: "right",
				trigger: "hover"
			});
		});
      function isSupplierAvailable() {

        var supplier = document.getElementById("supplier").value;
        if(!supplier){
            
             alert("Error !   Please select supplier");
            return false;
        }
        else{
            var confirmation=confirm("Are you sure to complete the purchase?");
            if(confirmation){
                return true;
            }
            return false;
        
        }
    }
	</script>
	
	<!--end purchase-->
	
@stop

@section('stickyInfo')
<?php
    $string = 'Purchase';
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