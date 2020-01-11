@extends('_layouts.default')

@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'categoryWiseSalesReport.report', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>

		@if(isset($items))
		<table class="table table-bordered">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<div style="text-align: center; margin-bottom:1px;margin-top:20px">s
			        <img src="{{asset('img/logo-homeplus.png')}}" class="" style="padding-right: 15px;height: 70px;" alt="title">
			    </div>
				<strong style="font-size: 1.5em;"><i class="icon-credit-card"></i> Summary:: Category Wise Sales Report</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			<thead class="table-head">
				<tr>
					<th>#SL No.</th>
					<th>Category Name</th>
					<th>Discount</th>
					<th>Sale Value</th>
					<th>Purchase Value</th>
					<th>Profit / Loss</th>
				</tr>
			</thead>
			<tbody>
				@if($items)
                    <? $i=0;
                    $total_discount = $total_amount= $total_purchase = $total_profit = 0;
                    ?>
				   @foreach($items as $sale)
                                   <?php
                                    $total_discount += $sale->total_discount;
                                    $total_amount 	+= $sale->total_sales;
                                    $total_purchase += $sale->purchase_price;
                                    $total_profit   += $sale->profit;
                                   ?>
					   <tr>
							<td>{{++$i}}</td>
							<td>{{ $sale->category_name}}</td>
							<td>{{ $sale->total_discount}}</td>
							<td>{{ $sale->total_sales}}</td>
							<td>{{ $sale->purchase_price}}</td>
							<td>{{ $sale->profit}}</td>
						</tr>
				   @endforeach
				@else
					<tr>
						<td colspan="3" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
				@if($items)
				<tr bgcolor='#DBEAF9' style="font-size: 16px; font-weight: bold; color:#003454">
					<td></td>
					<td>Total</td>
					<td><?php echo $total_discount ?></td>
					<td><?php echo $total_amount ?></td>	
					<td><?php echo $total_purchase ?></td>	
					<td><?php echo $total_profit ?></td>	
				</tr>
				@endif
			</tbody>
		</table>
	  </div>
	  </div>
		@endif
	</div>
@stop