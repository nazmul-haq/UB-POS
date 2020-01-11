@extends('_layouts.default')
@section('content')
            <?php 
                $amount=0;
               
               // echo'<pre>';
               // print_r(Session::get('inventoryItems'));
               // exit;
            ?>
    <div class="row">
        <div class="span8">
                    @include('_sessionMessage')
                    <div style="height:24px;" class='label label-info'>
                    <h4><i class='icon-shopping-cart'></i> Inventory Dialog
                    @if(Session::get('inventoryItems'))
                        <span id='topTotalQuantity' class='label label-warning' style='font-size:22px; border-radius:90px;'></span>
                        <a style="float:right;"  class="btn btn-warning btn-small" href="{{route('admin.emptyCart')}}">Clear Screen</a>
                    @endif
                    </h4> 
                    </div>
            <div class="invoice-reg"><input type="hidden" id="max_dis_percent" value="{{Session::get('max_inv_dis_percent');}}">
                <!-- {{Session::get('max_inv_dis_percent');}}!-->
                {{ Form::open(array('route' => 'admin.inventoryItemToChart', 'id' => 'formItemLocation', 'class' => 'form-horizontal')) }}
                    <div class="control-group">
                        {{ Form::label('search_item', 'Find/Scan Item', ['class' => 'control-label', 'style' => 'font-weight: bold;']) }}
                        <div class="controls">
                            {{ Form::text('item_id', null, array('class' => 'span6','id'=>'auto_search_item', 'placeholder' => 'Start Typing item\'s name or scan barcode...')) }}
                        </div> <!-- /controls -->
                    </div> <!-- /control-group -->
                {{ Form::close() }}
            </div>
            <table class="table table-bordered" width="100%">
                <thead class="table-head">
                    <tr>
                        <th>SL</th>
                        <th>Item Name</th>
                        <th>UPC Code</th>
                        <th>Purchase Price</th>
                        <th>Sale Price</th>
                        <th>Available Qty.</th>
                        <th>New Qty.</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $i=0; $invoice_total=0; 
                        if(Session::get('inventoryItems')){
                            $reverse_items= array_reverse(Session::get('inventoryItems'));
                        }
                        $totalQuantityEx = $totalQuantityNew = 0;
                    ?>
                    @if(Session::get('inventoryItems'))
                        @foreach($reverse_items as $item)
                            <?php
                                // $invoice_total=$invoice_total+$item['total'];
                                $totalQuantityEx += $item['available_quantity'];
                                $totalQuantityNew += $item['new_quantity'];
                            ?>
                            {{ Form::open(array('route' => 'admin.editDeleteItem', 'class' => 'form-horizontal')) }}
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>
                                        <span class="span3">
                                            {{$item['item_name']}}
                                            <input type="hidden" name="key" value="{{$item['key']}}">
                                            <input type="hidden" name="item_id" value="{{$item['item_id']}}">
                                            <input type="hidden" name="item_name" value="{{$item['item_name']}}">
                                        </span>
                                    </td>
                                    <td>
                                        {{$item['upc_code']}}
                                    </td>
                                    <td>
                                        {{$item['purchase_price']}}
                                    </td>
                                    <td>
                                        {{$item['sale_price']}}
                                    </td>
                                    <td>
                                        <input class="span1 available_quantity"  type="text" readonly name="available_quantity" value="{{$item['available_quantity']}}" />
                                    </td>
                                    <td>
                                        <input class="span1 saleQuanty" type="text" name="new_quantity"  autocomplete="off" @if($item['new_quantity'] > 0)value="{{$item['new_quantity']}}" @else @endif autofocus='yes' />
                                    </td>
                                    <td class="span2">
                                        <button type="submit" class="edit btn btn-primary" name="edit_delete" value="edit"><i class="icon-edit"></i></button>
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
        <div class="span4">
            <div class="invoice-right">
                <div>
                    {{ Form::open(array('route' => 'admin.inventoryDialogSave','autocomplete'=>'off', 'class' => 'form-horizontal')) }}
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
                                <td>Total Quantity (Ex)</td>
                                <td>:</td>
                                <td><strong id="pay_amount" style="color: red; font-size: 2.4em; font-family:Times;">
                                	{{$totalQuantityEx}}
                                </strong></td>
                            </tr>
                            <tr>
                                <td>Total Quantity (New)</td>
                                <td>:</td>
                                <td><strong id="pay_amount" style="color: green; font-size: 2.4em; font-family:Times;">
                                	{{$totalQuantityNew}}
                                </strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="text-align: left; margin-left: 10px;">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to make this change ?')">Save Inventory</button>
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
             $("#auto_search_item").autocomplete("{{route('admin.autoInventoryItemSuggest')}}", {
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

           
            //Discount Calculate for percent
            var totalAmount = parseFloat($("#total_amount").html());
            if(totalAmount>=1000){
                autoDiscountCalculation(totalAmount);
            }


          $('.saleQuanty').blur(function(){
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
            saleQuanty.value = 22;
        }
    </script>
    <script>
    $(function(){
        var formAddInvoiceToQueue = $('#formAddInvoiceToQueue');

        formAddInvoiceToQueue.validate({
          rules: {
           customer: {
               required: true
            }
          }, messages: {
            },
            ignore: ':hidden'   
        });
    });
$(window).load(function(){
        salesInit();
        $('#topTotalQuantity').html($('#toalQuantity').val());
    });
    function salesInit() {
        shortcut.add("Alt+c", function() {
            window.location = "{{route('sale.emptyCart')}}";
        });
        shortcut.add("Alt+f", function() {
            $("#paynote").focus();
            //window.location = "{{route('sale.emptyCart')}}";
        });
    }
</script>
@stop

