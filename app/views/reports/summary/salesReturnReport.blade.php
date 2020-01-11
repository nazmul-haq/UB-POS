@extends('_layouts.default')

@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'summary.viewSalesReturnReport', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from , array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>

		@if(isset($saleReturns))
		<table class="table table-striped">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 2em;"><i class="icon-credit-card"></i> Sales Return Report : Summary</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			<thead class="table-head">
				<tr>
					<th>SL</th>
					<th>Sub Total Amount</th>
					<th>Less Amount (Tk)</th>
					<th>Total Amount</th>
					<th>Loss Profit</th>
                    <th>Date</th>
				</tr>
			</thead>
			<tbody>
				<? $i=0; 
                                $sub_total=$less_total=$total_amount=$total_paid=$paid=$due=$total_loss_profit=0;
                                ?>
				@if($saleReturns)
				   @foreach($saleReturns as $saleRe)
                                   <?
                                  // echo '<pre>';print_r($saleRe);exit;
                                   $sub_total+=$saleRe->salereturn_amount + $saleRe->salereturn_less;
                                   $less_total+=$saleRe->salereturn_less;
                                   $total_amount+=$saleRe->salereturn_amount;
                                   $total_loss_profit+=$saleRe->total_loss_profit;
                                   
                                   ?>
					   <tr>
							<td>{{++$i}}</td>
							<td>{{$saleRe->salereturn_amount + $saleRe->salereturn_less}}</td>
							<td>{{$saleRe->salereturn_less}}</td>
							<td>{{$saleRe->salereturn_amount}}</td>
							<td>{{$saleRe->total_loss_profit}}</td>
							<td width="80px">{{$saleRe->date}}</td>
						</tr>
				   @endforeach

				@else
					<tr>
						<td colspan="6" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
                                @if($saleReturns)
                                <tr bgcolor='#DBEAF9' style="font-size: 16px; font-weight: bold; color:#003454">
                                    <td>Total</td>
                                    <td><?php echo $sub_total; ?></td>
                                    <td><?php echo $less_total; ?></td>
                                    <td style="color:green"><?php echo $total_amount ?></td>
                                    <td style="color:red"><?php echo $total_loss_profit ?></td>
                                    <td></td>
                                </tr>
                                @endif
			</tbody>
		</table>
	  </div>
	  </div>

		@endif
	</div>

@stop