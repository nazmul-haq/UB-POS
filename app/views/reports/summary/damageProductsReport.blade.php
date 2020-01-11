@extends('_layouts.default')

@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'summary.damageProductReports', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>

		<table class="table table-bordered">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 2em;"><i class="icon-bolt"></i> Damage Report : Summary</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			<thead>
				<tr>
					<th>SL</th>
					<th>Total Amount</th>
                    <th>Date</th>
				</tr>
			</thead>
			<tbody>
				<? $i=0; $total_amount = 0;?>
				@if($damageReports)
				   @foreach($damageReports as $damageReport)
					   <tr>
							<td>{{++$i}}</td>
							<td>{{$damageReport->damage_amount}}<? $total_amount = $total_amount + $damageReport->damage_amount; ?></td>
							<td width="80px">{{ Helper::onlyDMY($damageReport->date) }}</td>
						</tr>
				   @endforeach          
						<tr bgcolor="#DBEAF9">
						   <td><strong style="font-size: 1.3em;">Total<strong></td>
						   <td colspan="2"><strong style="color: green;">{{$total_amount}}<strong></td>
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
	</div>
@stop