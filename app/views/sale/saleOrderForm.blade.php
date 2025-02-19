@extends('_layouts.default')
@section('content')
    <?php 
    $amount=0;
    if(Session::get('saleOrderItems')){
        foreach(Session::get('saleOrderItems') as $item)
        $amount=$amount+$item['total'];
    }
    ?>
        <div class="row">
        <div class="span8">
            <div style="height:24px;" class='label label-info'>
            <h4><i class='icon-shopping-cart'></i> Sales Order Register 
                <a style="float:right;"  class="btn btn-warning btn-small" href="{{route('sale.emptySaleOrderCart')}}">Clear Screen</a>
            <span id='topTotalQuantity' class='label label-warning' style='font-size:22px; border-radius:90px;'></span>
            </h4>
            </div>
            <div class="" style=" text-align: center; font-size: 24px; font-family: monospace; color: white; background-color: #2d6694;">
                Sales Order Form
            </div>
            <table class="table table-bordered" width="100%">
                <thead class="table-head">
                    <tr>
                        <th>SL</th>
                        <th>Item Name</th>
                        <th colspan="2" style="width: 300px;">Sale Qty.</th>      
                        <th>Available Qty.</th>
                        <th>Sale Price</th>
                        <th style="width: 50px;">Total</th>
                        <th style="width: 90px;">Action</th>
                    </tr>
                </thead>
                <tbody class="addTr">
                    <?php 
                        $i=0; $invoice_total=0; 
                        if(Session::get('saleOrderItems')){
                            $reverse_items= Session::get('saleOrderItems');
                        }
                        $totalQuantity = 0;
                        $totalPoint = 0;
                    ?>
                    @if(Session::get('saleOrderItems'))
                        @foreach($reverse_items as $key => $item)
                            <? $invoice_total=$invoice_total+$item['total'];
                            $totalQuantity+=$item['sale_quantity'];
                            //$totalPoint = $totalPoint + ($item['item_point'] * $item['sale_quantity']);
                            ?>
                            {{ Form::open(array('route' => 'sale.editDeleteItemForSaleOrder', 'class' => 'form-horizontal')) }}
                                <tr>                            
                                    <td id="sl_{{$key+1}}">{{++$i}}</td>
                                    <td>
                                        <span class="span3">
                                            {{$item['item_name']}}
                                            <input type="hidden" name="key" value="{{$item['key']}}">
                                            <input type="hidden" name="item_id" value="{{$item['item_id']}}">
                                            <input type="hidden" name="price_id" value="{{$item['price_id']}}">
                                            <input type="hidden" name="item_name" value="{{$item['item_name']}}">
                                            <input type="hidden" id="pcsPerCarton_{{$key}}" name="pcs_per_cartoon" value="{{$item['pcs_per_carton']}}">
                                            <input type="hidden" id="availableQuantity_{{$key}}" name="available_quantity" value="{{$item['available_quantity']}}">
                                        </span>
                                    </td>
                                    <td colspan="2">
                                        <div class="span3">
                                            <div class="span1" style="float: left; margin-right: 20px;"><label>PCS</label>
                                                <input style="width: 100%;" class="span1 saleQuanty" id="pcs_{{$key}}" type="text" name="sale_quantity"  autocomplete="off" value="{{$item['sale_quantity']}}"/></div>
                                            <div @if($item['unit'] == 1 || $item['unit'] == 3) style="float: left; margin-right: 20px; display: none;" @elseif($item['unit'] == 2) style="float: left; margin-right: 20px;" @endif class="span1"><label>Dozz</label><input style="width: 100%;" class="span1 saleQuanty" id="saleQuantityDozz_{{$key}}" type="text" name="sale_quantity_dozz"  autocomplete="off" value="{{round($item['dozz'],4)}}" /></div>
                                            <div @if($item['unit'] == 1 || $item['unit'] == 2) style="float: left; margin-right: 20px; display: none;" @elseif($item['unit'] == 3) style="float: left; margin-right: 20px;" @endif  class="span1"><label>Set</label><input style="width: 100%;" class="span1 saleQuanty" type="text" id="saleQuantitySet_{{$key}}" name="sale_quantity_set"  autocomplete="off" value="{{round($item['set'],4)}}" /></div>
                                            <div style="float: left;" class="span1"><label>Carton</label><input style="width: 100%;" class="span1 saleQuanty" type="text" name="sale_quantity_carton" id="saleQuantityCarton_{{$key}}" autocomplete="off" value="{{round($item['carton'],4)}}" /></div>
                                        </div>
                                            <input class="span1 floatingCheck" type="text" name="discount" id="discount_{{$key}}" autocomplete="off" value="{{$item['discount']}}" readonly="" style="display: none;" />
                                    </td>
                                    <td>
                                    @if($item['sale_quantity']==0)
                                            <input class="span1 available_quantity" style="background-color:red; color:#fff;"  type="text" readonly name="available_quantity" value="{{$item['available_quantity']}}" />
                                        @else
                                            <input class="span1 available_quantity"  type="text" readonly name="available_quantity" value="{{$item['available_quantity']}}" />
                                    @endif
                                    </td>
                                    
                                    <td>
                                        <input class="span1 price_change saleQuanty" type="text" name="sale_price" id="price_{{$key}}" value="{{$item['sale_price']}}" />
                                        <input type="hidden" name="purchase_price" id="purchasePrice_{{$key}}" value="{{$item['purchase_price']}}" />
                                    </td>
                                    <td>
                                        <input type="text" name="total" id="total_{{$key}}"  class="span1 disabled" disabled="" value="{{$item['total']}}" />
                                    </td>
                                    <td class="span2" style="width: 90px;">
                                        <button style="display:none;" type="submit" class="edit btn btn-primary" name="edit_delete" value="edit"><i class="icon-edit"></i></button>
                                        <button type="submit" class="btn btn-warning btn-delete" id="delete_{{$key}}" name="edit_delete"><i class="icon-trash"></i></button>
                                    </td>
                                </tr>
                            {{ Form::close() }}
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" style="text-align:center; color:#E98203;">
                        <strong>Total Item : <span id="cartAddedQty">{{count(Session::get('saleOrderItems'))}}</span></strong>
                        <input type='hidden' id='toalQuantity' value='{{ $totalQuantity }}'>
                        <td>
                    </tr>
                </tfoot>
            </table>
            <div class="invoice-reg"><input type="hidden" id="max_dis_percent" value="{{Session::get('max_inv_dis_percent');}}">
                <!-- {{Session::get('max_inv_dis_percent');}}!-->
                @include('_sessionMessage')
                {{ Form::open(array('route' => 'sale.addItemToOrderChart', 'id' => 'formItemLocation', 'class' => 'form-horizontal itemAddCartForm')) }}
                    <div class="control-group">
                        {{ Form::label('search_item', 'Find/Scan Item', ['class' => 'control-label', 'style' => 'font-weight:bold;margin-top:7px;font-size:18px;']) }}
                        <div class="controls">
                            {{ Form::text('item_id', null, array('class' => 'span6 item_id','autofocus'=>'yes','id'=>'auto_search_item', 'placeholder' => 'Start Typing item\'s name or scan barcode...','style' => 'height: 30px;border: 1px solid #125f0d;')) }}
                        </div> <!-- /controls -->
                    </div> <!-- /control-group -->
                {{ Form::close() }}
            </div>
        </div>
        <div class="span4" style="position: fixed; left: 858px;">
            <div class="invoice-right">
                {{ Form::open(array('route' => 'sale.selectDeleteCustomerForOrder','autocomplete'=>'off', 'id' => 'customerForm', 'style' => 'margin : 0 0 9px !important;')) }}
                    @if(Session::get('sale_order_invoice_info.user_name'))
                        <div class="control-group hr">
                            <label class="control-label" for="supplier_name"><strong style="font-size: 1.1em;"><u>Customer</u></strong></label>
                            <div class="controls">
                                <table class="table table-striped" style="margin: 0; padding:0;">
                                    <tbody> 
                                        <tr>
                                            <td>Customer Name</td>
                                            <td>:</td>
                                            <td><strong id="customer_name" style="color: green; font-size: 1.4em;">
                                                {{Session::get('sale_order_invoice_info.full_name')}}
                                                <input type="hidden" id="customer" value="{{Session::get('sale_order_invoice_info.user_name')}}">
                                                <input type="hidden" id="cust_type_discount_percent" value="0">
                                            </strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="submit" style="margin: 3px 0 3px" class="btn btn-warning" name="customer" value="delete"><i class="icon-trash"></i> &nbsp;Remove</button>
                            </div> <!-- /controls -->
                        </div>
                    @else
                        <div class="control-group hr">
                            <label class="control-label" for="supplier"><b>Select Customer (Optional)</b></label>
                            <div class="controls">
                                {{Form::text('national_id', null, array('class' => 'span3', 'id' => 'customerAutoSugg', 'placeholder' => 'Start Typing Customer\'s name...'))}}
                                <input type="hidden" id="customer" value="">
                            </div> <!-- /controls -->
                        </div>
                    @endif
                {{ Form::close() }}
                <div>
                    @if(Session::has('oldOrderInvoiceId'))
                    {{ Form::open(array('route' => 'sale.editInvoiceAndSaleOrder','autocomplete'=>'off', 'class' => 'form-horizontal', 'style' => 'margin : 0 0 1px !important;')) }}
                    @else
                    {{ Form::open(array('route' => 'sale.invoiceAndSaleOrder','autocomplete'=>'off', 'class' => 'form-horizontal', 'style' => 'margin : 0 0 1px !important;')) }}
                    @endif
                    <table class="table table-striped" style="margin: 0; padding:0;">
                        <tbody>
                        <tr>
                            <td>Branch </td>
                            <td>:</td>
                            <td style="padding:0;">
                                <span> {{Helper::getBranchName()}}</span>
                            </td>
                        </tr>
                        @if(Session::get('backdate_sales')==0)
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
                            <tr style="display: none;">
                                <td>Payment Type</td>
                                <td>:</td>
                                <td style="padding:0;">
                                    {{ Form::select('payment_type_id', $payment_type, 1, ['class' => 'span3', 'style' => 'margin-top: 3px;']) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Sub Total Amount</td>
                                <td>:</td>
                                <td><strong id="total_amount" style="color: green; font-size: 1.4em;">{{$invoice_total}}</strong></td>
                            </tr>
                            <tr style="display: none;">
                                <td>Discount</td>
                                <td>:</td>
                                <td style="padding:0;">
                                    <input style="margin-top: 3px; width:95px;" type="text" maxlength="5" readonly id="dis_percent" name="discount_percent" data-toggle="tooltip" title="Discount (%)" value="%"  placeholder="Discount(%)">&nbsp;&nbsp;
                                    <input style="margin-top: 3px; width:95px;"  type="text" maxlength="10" id="dis_taka" name="invoice_discount"  data-toggle="tooltip" title="Discount (Tk.)" placeholder="Discount(Taka)" value="0">
                                </td>
                            </tr>
                            <tr>
                                <td>Total Amount (Tk)</td>
                                <td>:</td>
                                <td><strong id="pay_amount" style="color: red; font-size: 2.4em; font-family:Times;">
                                @if(Session::get('sale_order_invoice_info.user_name'))
                                <?php
                                    $amountTaka = $invoice_total-Session::get('invoice_info.invoice_discount');
                                    echo $amountTaka;
                                ?>
                                @else
                                {{$invoice_total-Session::get('invoice_info.invoice_discount')}}
                                @endif
                                </strong></td>
                            </tr>
                            <tr>
                                <td>Pay</td>
                                <td>:</td>
                                <td style="padding:0;">
                                    <div class="input-prepend input-append">
                                        <input readonly="" class="span2" type="text" id="appendedPrependedInput" maxlength="10" class="span2" name="pay" value="0">
                                    </div>
                                </td>
                            </tr>
                            <tr style="display: none;">
                                <td>Pay Note</td>
                                <td>:</td>
                                <td style="padding:0;">
                                    <input type="text" class="span2" id="paynote" autocomplete="off" maxlength="10" name="paynote" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>Due</td>
                                <td>:</td>
                                <td><strong id="due" style="color: green; font-size: 1.4em;">{{$amount}}</strong></td>
                            </tr>
                            @if(Session::has('sale_order_invoice_info.customer_due'))
                            @if(Session::get('sale_order_invoice_info.customer_due') > 0)
                            <tr>
                                <td>Previous Due</td>
                                <td>:</td>
                                <td><strong id="due" style="color: green; font-size: 1.4em;">{{Session::get('sale_order_invoice_info.customer_due')}}</strong></td>
                            </tr>
                            @endif
                            @endif
                        </tbody>
                    </table>
                    <div style="text-align: left; margin-left: 10px;">
                        <button type="submit" class="btn btn-danger" onclick="return isUnRegisteredCustomer();">Complete Sale Order</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    {{ HTML::script('js/jquery-ui.min.js') }}
    @include('sale.saleOrderFormAjaxRequest')
    <div id="addInvoiceToQueueModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="addInvoiceToQueueModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
            <h3 id="itemEditModalLabel"><i class="icon-edit-sign"></i>&nbsp; Add Invoice To Queue</h3>
        </div>
    </div>
    <script>
$(window).load(function(){
        $('#topTotalQuantity').html($('#toalQuantity').val());
    });

</script>
@stop
@section('stickyInfo')
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
		<li>S</li><li>a</li><li>l</li><li>e</li><li>s</li><li></li><li>O</li><li>r</li><li>d</li><li>e</li><li>r</li>
	</ul>       
</div>
@stop