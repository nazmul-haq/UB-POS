@extends('_layouts.default')
<?php //echo'<pre>';print_r($purchasereturn);exit; ?>
@section('content')
<div class="row print_disable" style="margin-bottom: 20px;">
    <div class="span12">
        @include('_sessionMessage')
        <div class="invoice-reg print_disable">
            {{ Form::open(array('route' => 'summary.viewBackDateStockReports', 'class' => 'form-horizontal')) }}
            <div class="control-group" align="center">
                <i class="icon-calendar"></i>&nbsp; Select Date : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
                &nbsp;&nbsp;&nbsp;
                <input class="btn btn-primary" type="submit" value="Search">
            </div> <!-- /control-group -->
            {{ Form::close() }}
        </div>
    </div>
</div>
<article style="margin-bottom: 3px;; background: #EEEEEE; padding : 20px 0 7px; border-top: 1px solid #003454;">
    <div style="text-align: center; margin-bottom:1px">
        <img src="{{asset('img/logo-homeplus.png')}}" class="" style="padding-right: 15px;height: 70px;" alt="title">
    </div>
    
    <strong style="font-size: 2em;"><i class="icon-credit-card"></i> Summary : Back Date Stock Report </strong>
    <strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; Date : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> 
</article>
<div class="row" style="margin-bottom: 3px;">
    <div class="span6 full_print">
        <div class="widget-header setup-title"> <i class="icon-credit-card"></i>
            <h3>Sales from {{ Helper::onlyDMY($from) }} to {{ Helper::onlyDMY(date('Y-m-d')) }}</h3>
        </div>
        <table class="table table-bordered" style="margin: 0; padding:0;">
            <tbody>
                <tr>
                    <td><strong style="float:left;">Sub Total </strong></td>
                    <td class="span2"><strong style="color: #AF5400; font-size: 1.0em;">{{$sale->sale_amount+$sale->sale_point_use_taka+$sale->sale_discount}}</strong></td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Discount </strong></td>
                    <td>{{$sale->sale_discount}}</td>
                </tr>
                
                <tr>
                    <td><strong style="float:left;">Cash </strong></td>
                    <td>{{ $saleAmountCash->cash_pay }}</td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Credit/Debit Card </strong></td>
                    <td>
                        @if($saleAmountCard->card_pay > 0)
                            {{ $saleAmountCard->card_pay }}
                        @else
                            0
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong style="float:left;">BKash </strong></td>
                    <td>
                        @if($saleAmountBkash->bkash_pay > 0)
                            {{ $saleAmountBkash->bkash_pay }}
                        @else
                            0
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Rocket </strong></td>
                    <td>
                        @if($saleAmountRocket->rocket_pay > 0)
                            {{ $saleAmountRocket->rocket_pay }}
                        @else
                            0
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Total Amount </strong></td>
                    <td><strong style="color: green; font-size: 1.5em;">{{$sale->sale_amount}}</strong></td>
                </tr>
                
                 <tr>
                    <td><strong style="float:left; ">Sale Profit </strong></td>
                    <td><strong style="font-size: 1.5em; color: #AA80BB">{{$sale->total_sale_profit}}</strong></td>
                </tr>
            </tbody>
        </table>			
    </div>
    <div class="span5 full_print">				
        <div class="widget-header setup-title"> <i class="icon-arrow-left"></i>
            <h3>Sales Return from {{ Helper::onlyDMY($from) }} to {{ Helper::onlyDMY(date('Y-m-d')) }}</h3>
        </div>
        <table class="table table-bordered" style="margin: 0; padding:0;">
            <tbody>
                <tr>
                    <td><strong style="float:left;">Sub Total</strong></td>
                    <td class="span2"><strong style="color: #AF5400; font-size: 1em;">{{$salereturn->salereturn_less+$salereturn->salereturn_amount}}</strong></td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Less</strong></td>
                    <td><strong>{{$salereturn->salereturn_less}}</strong></td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Total Amount </strong></td>
                    <td><strong style="color: green; font-size: 1.5em;">{{$salereturn->salereturn_amount}}</strong> </td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Paid </strong></td>
                    <td><strong>{{$salereturn->salereturn_amount}}</strong></td>
                </tr>
                <tr>
                    <td><strong style="float:left; ">Due </strong></td>
                    <td>0.00</td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Loss Profit </strong></td>
                    <td><strong style="font-size: 1.5em; color: #AA80BB">{{$salereturn->total_loss_profit}}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>


</div>
<div class="row">
    <div class="span6 full_print">				
        <div class="widget-header setup-title"> <i class="icon-shopping-cart"></i>
            <h3>Purchases from {{ Helper::onlyDMY($from) }} to {{ Helper::onlyDMY(date('Y-m-d')) }}</h3>
        </div>
        <table class="table table-bordered" style="margin: 0; padding:0;">
            <tbody>
                <tr>
                    <td><strong style="float:left;">Sub Total </strong></td>
                    <td class="span2"><strong style="color: #AF5400; font-size: 1.0em;">{{$purchase->purchase_discount+$purchase->purchase_amount}}</strong></td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Discount </strong></td>
                    <td><strong> {{$purchase->purchase_discount}}</strong></td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Total Amount </strong></td>
                    <td><strong style="color: green; font-size: 1.5em;">{{$purchase->purchase_amount}}</strong></td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Paid </strong></td>
                    <td>{{$purchase->purchase_pay}}</td>
                </tr>
                <tr>
                    <td><strong style="float:left; ">Due </strong></td>
                    <td>{{$purchase->purchase_due}}</td>
                </tr>
            </tbody>
        </table>
        <div class="span4 full_print">		
	        <div class="widget-header setup-title"> <i class="icon-money"></i>
	            <h3>Godown Amount</h3>
	        </div>
	        <p>
	            <strong>Total Amount :</strong>&nbsp;&nbsp;<strong style="color: green; font-size: 1.2em;">{{ $totalGodwonTk->total_amount }}</strong>
	        </p>
	        <div class="widget-header setup-title"> <i class="icon-money"></i>
	            <h3>Stock Amount</h3>
	        </div>
	        <p>
	        	<?php
	        		$finalStockAmount = $totalStockTk->total_amount + ($sale->sale_amount+$sale->sale_discount);
	        		$finalStockAmount = $finalStockAmount - $sale->total_sale_profit;
	        		$finalStockAmount = $finalStockAmount - ($salereturn->salereturn_less+$salereturn->salereturn_amount);
	        		
	        		$finalStockAmount = $finalStockAmount - ($purchase->purchase_discount+$purchase->purchase_amount);
	        		
	        		$finalStockAmount = $finalStockAmount + ($purchasereturn->purchasereturn_less+$purchasereturn->purchasereturn_amount);

	        	?>
	            <strong>Total Stock Amount Today :</strong>&nbsp;&nbsp;<strong style="color: green; font-size: 1.2em;">{{ $totalStockTk->total_amount }}</strong>
	        </p>
	        <p>
	            <strong>Total Stock Amount at  {{Helper::onlyDMY($from)}} :</strong>&nbsp;&nbsp;<strong style="color: green; font-size: 1.2em;">{{ $finalStockAmount }}</strong>
	        </p>
	    </div>
    </div>
    <div class="span5 full_print">				
        <div class="widget-header setup-title"> <i class="icon-undo"></i>
            <h3>Purchases Return from {{ Helper::onlyDMY($from) }} to {{ Helper::onlyDMY(date('Y-m-d')) }}</h3>
        </div>
        <table class="table table-bordered" style="margin: 0; padding:0;">
            <tbody>
                <tr>
                    <td><strong style="float:left;">Sub Total</strong></td>
                    <td class="span2"><strong style="color: #AF5400; font-size: 1.0em;">{{$purchasereturn->purchasereturn_less+$purchasereturn->purchasereturn_amount}}</strong></td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Less</strong></td>
                    <td>{{$purchasereturn->purchasereturn_less}}</td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Total Amount </strong></td>
                    <td><strong style="color: green; font-size: 1.5em;">{{$purchasereturn->purchasereturn_amount}}</strong></td>
                </tr>
                <tr>
                    <td><strong style="float:left;">Paid </strong></td>
                    <td>{{$purchasereturn->purchasereturn_amount}}</td>
                </tr>
                <tr>
                    <td><strong style="float:left; ">Due </strong></td>
                    <td>0.00</td>
                </tr>
            </tbody>
        </table>
    </div>

    

</div>
<div class="row" style="margin-top: 0px; padding-top: 0px;">
    
</div>
@stop