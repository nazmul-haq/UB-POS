@extends('_layouts.default')

@section('content')
	<div class="row">
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'summary.viewOtherExpenseReports', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>
<?php //echo'<pre>';print_r($otherIncome);exit;?>
		<table class="table table-striped">
			<article style="background: #EEEEEE; padding : 4px 0 5px; border-top: 1px solid #003454;">
				<strong style="font-size: 1.5em;"><i class="icon-money"></i> Other Expense Reports : Summary</strong>
				<strong style="float: right; margin: 0 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			<thead class="table-head">
				<tr>
					<th>SL</th>
					<th>Amount</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				@if($otherExpense)
					<? $i = 0; $total_amount = 0; ?>
				   @foreach($otherExpense as $expense)
					   <tr>
						   <td>{{++$i}}</td>
						   <td>{{{ $expense->expenseTotal }}}</td><? $total_amount = $total_amount + $expense->expenseTotal; ?>
						   <td>{{{ $expense->date }}}</td>
					   </tr>
				   @endforeach
                        <tr bgcolor="#DBEAF9">
						   <td><strong style="font-size: 1.3em;">Total<strong></td>
						   <td colspan="2"><strong style="color: green;">{{ $total_amount }}<strong></td>
					   </tr>
				@else
					<tr>
						<td colspan="3" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
			</tbody>
		</table>
	  </div>
	</div>
@stop