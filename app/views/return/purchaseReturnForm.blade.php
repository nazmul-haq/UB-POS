@extends('_layouts.default')

@section('content')
<?php   $amount=0;
if(Session::get('purchaseReturnItemInfo')) {
    foreach(Session::get('purchaseReturnItemInfo') as $item)
        $amount=$amount+$item['total'];
        $amount=$amount;
}
//echo'<pre>';print_r(Session::get('sale_return_invoice_info'));exit;
?>
<div class="row">
    <div class="span8">
        @include('_sessionMessage')
        <div class='label label-info'><h4><i class='icon-shopping-cart'></i> Purchase Return Register</h4></div>
        <div class="invoice-reg"><input type="hidden" id="max_dis_percent" value="{{Session::get('max_inv_dis_percent');}}">
            <!-- {{Session::get('max_inv_dis_percent');}}!-->
				{{ Form::open(array('route' => 'purchaseReturn.returnItemAddTochart', 'id' => 'formItemLocation', 'class' => 'form-horizontal')) }}
            <div class="control-group">
						{{ Form::label('search_item', 'Find/Scan Memo Id', ['class' => 'control-label', 'style' => 'font-weight: bold;']) }}
                <div class="controls">
							{{ Form::text('sup_memo_no', null, array('class' => 'span6','autofocus'=>'yes','id'=>'auto_search_invoice', 'placeholder' => 'Start Typing or Scan Sale Invoice Id . . . . . . . . .')) }}
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
                    <th>Quantity</th>
                    <th>Disc (Tk)</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i=0;
                $invoice_total=0; ?>
		@if(Session::get("purchaseReturnItemInfo"))
		   @foreach(Session::get('purchaseReturnItemInfo') as $item)
<?php // $invoice_total=$invoice_total+$item['total'];?>
		  {{ Form::open(array('route' => 'purchaseReturn.editDeleteItem', 'class' => 'form-horizontal')) }}
                <tr>
                    <td>{{++$i}}</td>
                    <td>
                        <span class="span3">
			{{$item['item_name']}}<input type="hidden" name="i_purchase_id" value="{{$item['i_purchase_id']}}"><input type="hidden" name="item_id" value="{{$item['item_id']}}"><input type="hidden" name="price_id" value="{{$item['price_id']}}"><input type="hidden" name="item_name" value="{{$item['item_name']}}">
                        </span>
                    </td>

                    <td>
                        <input class="span1" type="text" readonly name="purchase_price" value="{{$item['purchase_price']}}" />
                    </td>

                    <td>
                        <input class="span1 quantity" type="text" name="quantity"  autocomplete="off" value="{{$item['quantity']}}" />
                    </td>
                    <td>
                        <input class="span1" type="text" readonly="readonly" name="discount" autocomplete="off" value="{{$item['discount']}}" />
                    </td>

                    
                    <td>
                        <input class="span1" type="text" readonly name="total" value="{{$item['total']}}" />
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


            <div class="control-group hr">
                <label class="control-label" for="supplier_name"><strong style="font-size: 1.1em;"><u>Supplier</u></strong></label>
                <div class="controls">
                    <table class="table table-striped" style="margin: 0; padding:0;">
                        <tbody>
			@if(Session::has('purchase_return_invoice_info'))
                            <tr>
                                <td width="112px">Supplier Name</td>
                                <td>:</td>
                                <td><strong id="supplier_name" style="color: green; font-size: 1.4em;">

                                        {{Session::get('purchase_return_invoice_info.supp_or_comp_name')}}
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td width="112px">Previous Due</td>
                                <td>:</td>
                                <td style="padding:0;"><strong style="color: red; font-size: 1.2em;">&nbsp;&nbsp;{{Session::get('purchase_return_invoice_info.supplier_due')}}&nbsp;Tk</strong></td>
                            </tr>
                            
                            @endif
                        </tbody>
                    </table>
                </div> <!-- /controls -->
            </div>
            <div>
		{{ Form::open(array('route' => 'purchaseReturn.invoiceAndPurchaseReturn','autocomplete'=>'off', 'class' => 'form-horizontal')) }}
                <table class="table table-striped" style="margin: 0; padding:0;">
                    <tbody>
                        <tr>
                            <td>Date </td>
                            <td>:</td>
                            <td style="padding:0;">
                                <input id="dp3" name="date" data-date="<?= date("Y-m-d") ?>" data-date-format="yyyy-mm-dd" class="datepicker span2" type="text" value="<?= date("Y-m-d") ?>">
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
                            <td>Invoiced Discount</td>
                            <td>:</td>
                            <td><strong id="invoiced_discount"  font-size: 1.4em;">{{Session::get('purchase_return_invoice_info.invoice_discount')}}</strong></td>
                        </tr>
                        <tr>
                            <td>Sub Total Amount</td>
                            <td>:</td>
                            <td><strong id="sub_total_amount" style="color: green; font-size: 1.4em;">{{$amount}}</strong></td>
                        </tr>
                        <tr>
                            <td>Less</td>
                            <td>:</td>
                            <td style="padding:0;">
                                <input style="margin-top: 3px; width:95px;" readonly type="text" maxlength="5" id="less_percent" name="less_percent" data-toggle="tooltip" title="Less Amount (%)"  placeholder="Less (%)">&nbsp;&nbsp;
                                <input style="margin-top: 3px; width:95px;"  type="text" maxlength="10" id="less_taka" name="less_taka"  data-toggle="tooltip" title="Less Amount (Tk.)" placeholder="Less (Taka)">
                            </td>
                        </tr>

                    <tr>
                        <td>Total Amount (Tk)</td>
                        <td>:</td>
                        <td><strong id="total_amount" style="color: red; font-size: 2.4em; font-family:Times;">{{$amount}}</strong></td>
                    </tr>
                    <tr>
                        <td>Pay</td>
                        <td>:</td>
                        <td style="padding:0;">
                            <div class="input-prepend input-append">
                                <input class="span2" type="text" readonly="readonly" id="pay_amount" maxlength="10" class="span2" name="pay_amount" value="{{$amount}}">
                            </div>
                        </td>
                    </tr>

                    </tbody>
                </table>
                <div style="text-align: left; margin-left: 10px; margin-top: 7px;">
                    <button type="submit" class="btn btn-danger" onclick="return isUnRegisteredCustomer();">Complete Sale Return</button>
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
        $("#auto_search_invoice").autocomplete("{{route('purchaseReturn.invoiceAutoSuggest')}}", {
            width: 260,
            matchContains: true,
            queryDelay : 0
        });
        //Submit Search Item Form
        $("#auto_search_invoice").result(function(event, data, formatted) {
            $("#formItemLocation").submit();
        });

        //Customer auto suggest
        $("#customerAutoSugg").autocomplete("{{route('sale.autoCustomerSuggest')}}", {
            width: 260,
            matchContains: true,
            queryDelay : 0,
            formatItem: function(row) {
                return row[1];
            }
        });
        //Submit Supplier Form
        $("#customerAutoSugg").result(function(event, data, formatted) {
            $("#customerForm").submit();
        });


        //Discount Calculate for Taka
        $('#less_taka').keyup(function(){

            var $sub_total_amount = parseFloat($('#sub_total_amount').html());
            var less_taka=parseFloat(this.value);
            var confirm_less_taka=0;
            var less_taka_percent=0;
            if(isNaN(less_taka)){
                less_taka = 0;

            }
            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+\.(\.\d *)?)|((\d*\.)?\d+))$/;
            var str = $(this).val();


            if(this.value==''){
                this.value=null;
                $('#less_percent').val(0);
                $('#total_amount').html($sub_total_amount);
                $('#pay_amount').val($sub_total_amount.toFixed(2));
            }
            else if(intRegex.test(str) || floatRegex.test(str)) {

               if(less_taka>$sub_total_amount){
                   confirm_less_taka=$sub_total_amount;
               }
               else{
                    confirm_less_taka=less_taka;
                }
             less_taka_percent=(confirm_less_taka*100)/$sub_total_amount;
            $('#less_percent').val(less_taka_percent.toFixed(2));
            this.value=confirm_less_taka;
            $('#total_amount').html(($sub_total_amount-confirm_less_taka).toFixed(2));
            $('#pay_amount').val(($sub_total_amount-confirm_less_taka).toFixed(2));


            }

            else{
                alert('Wrong Data');
		this.value = '';
                $('#less_percent').val(0);
                $('#total_amount').html($sub_total_amount);
                $('#pay_amount').val($sub_total_amount.toFixed(2));
                return false;
            }
        });



        $('.floatingCheck').keyup(function(){

            var intRegex = /^\d+$/;
            var floatRegex = /^((\d+\.(\.\d *)?)|((\d*\.)?\d+))$/;
            var str = $(this).val();
            if(this.value==''){
                this.value=0;
            }
            else if(intRegex.test(str) || floatRegex.test(str)) {

            }
            else{
                alert('Wrong Data');
                this.value = 0;
            }
        });

        $('.quantity').blur(function(){
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

        //for input box tooltip
        $('input[type=text][name=discount_percent]').tooltip({
            placement: "right",
            trigger: "hover"
        });
    });

    function saleQuantyCheck() {
        saleQuanty.value=22;
    }

    function isUnRegisteredCustomer() {

            var confirmation=confirm("Are you sure to complete the sale?");
            if(confirmation){
                return true;
            }
            return false;
    }

</script>

<!--end purchase-->

@stop