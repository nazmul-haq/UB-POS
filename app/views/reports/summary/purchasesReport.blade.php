@extends('_layouts.default')

@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'summary.viewPurchaseReports', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>

		<table class="table table-striped">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 2em;"><i class="icon-credit-card"></i> Purchase Report : Summary</strong>
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
				<? $i=0;$total_sub_total=$total_discount=$total_amount=$total_pay=$total_due=0;?>
				@if($purchases)
				   @foreach($purchases as $purchase)
					   <tr>
							<td>{{++$i}}</td>
							<td>{{$purchase->purchase_discount +$purchase->purchase_amount}}<?php $total_sub_total=$total_sub_total+($purchase->purchase_discount +$purchase->purchase_amount); ?></td>
							<td>{{$purchase->purchase_discount}}<?php $total_discount=$total_discount+$purchase->purchase_discount; ?></td>
							<td>{{$purchase->purchase_amount}}<?php $total_amount=$total_amount+$purchase->purchase_amount?></td>
							<td>{{$purchase->purchase_pay}}<?php  $total_pay=$total_pay+$purchase->purchase_pay; ?></td>
							<td>{{$purchase->purchase_due}}<?php  $total_due=$total_due+$purchase->purchase_due; ?></td>
							<td width="80px">{{$purchase->date}}</td>
					</tr>
				   @endforeach
                                        <tr bgcolor="#DBEAF9">
                                                       <td><strong style="font-size: 1.3em;">Total<strong></td>
                                                       <td><strong>{{$total_sub_total}}<strong></td>
                                                       <td>{{$total_discount}}</td>
                                                       <td><strong>{{$total_amount}}</td>
                                                       <td><strong style="color: green;">{{$total_pay}}<strong></td>
                                                       <td colspan="3"><strong style="color: red;">{{$total_due}}<strong></td>
                                       </tr>

				@else
					<tr>
						<td colspan="6" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif

			</tbody>
		</table>
	  </div>
	  </div>

	</div>

	
@stop