@extends('_layouts.default')

@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'summary.viewSalesReport', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>

		@if(isset($sales))
		<table class="table table-bordered">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 2em;"><i class="icon-credit-card"></i> Sales Report : Summary</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			<thead class="table-head">
				<tr>
					<th>SL</th>
					<th>Sub Total Amount</th>
					<th>Discount (Tk)</th>
					<th>Total Amount</th>
					<th>Paid</th>
					<th>Due</th>
                                        <th>Date</th>
				</tr>
			</thead>
			<tbody>
				
				@if($sales)
                                <? $i=0;
                                $sub_total=$total_discount=$total_point_tk=$total_amount=$total_paid=$total_due=0;
                                ?>
				   @foreach($sales as $sale)
                                   <?php
                                   //total summation 
                                    $sub_total+=$sale->sale_discount + $sale->sale_point_use_taka + $sale->sale_amount;
                                    $total_discount+=$sale->sale_discount;
                                    //$total_point_tk+=$sale->sale_point_use_taka;
                                    $total_amount+=$sale->sale_amount;
                                    $total_paid+=$sale->sale_pay;
                                    $total_due+=$sale->sale_due;
                                    
                                   ?>
					   <tr>
							<td>{{++$i}}</td>
							<td>{{$sale->sale_discount + $sale->sale_point_use_taka + $sale->sale_amount}}<?php //$total_amount_taka=$total_amount_taka+$invoice->amount; ?></td>
							<td>{{$sale->sale_discount}}<?php // $total_discount=$total_discount+$invoice->discount; ?></td>
							<td>{{$sale->sale_amount}}<?php //$total_payable=$total_payable+$invoice->pay+$invoice->due ?></td>
							<td>{{$sale->sale_pay}}<?php // $total_pay=$total_pay+$invoice->pay; ?></td>
							<td>{{$sale->sale_due}}<?php // $total_due=$total_due+$invoice->due; ?></td>
							<td width="80px">{{$sale->date}}</td>
						</tr>
				   @endforeach
                        
				@else
					<tr>
						<td colspan="6" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
                                @if($sales)
                                <tr bgcolor='#DBEAF9' style="font-size: 16px; font-weight: bold; color:#003454">
                                    <td>Total</td>
                                    <td><?php echo $sub_total ?></td>
                                    <td><?php echo $total_discount ?></td>
                                    <td style="color:green"><?php echo $total_amount ?></td>
                                    <td style="color:green"><?php echo $total_paid ?></td>
                                    <td colspan="2" style="color:red;"><?php echo $total_due ?></td>
                                    
 
                                </tr>
                                @endif
			</tbody>
		</table>
	  </div>
	  </div>
		@endif
	</div>
@stop