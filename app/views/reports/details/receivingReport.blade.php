@extends('_layouts.default')

@section('content')
<div class="row">
	  <div class="span12">
		@include('_sessionMessage')	
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'receive.viewReceivingReport', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">		
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', date('Y-m-d'), array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', date('Y-m-d'), array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">						
					</div> <!-- /control-group -->	
					
                {{ Form::close() }}
            </div>        
		@if(isset($reports))
		<table class="table table-striped">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 2em;"><i class="icon-arrow-down"></i> Receiving Report</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
			</article>
			<thead class="table-head">
				<tr>
					<th>SL</th>
					<th>Item Name</th>
					<th>Purchase Price</th>
					<th>Sale Price</th>
					<th>Quantity</th>
					<th>Total Price</th>
					<th>Receive by</th>
					<th>Receive Date</th>
				</tr>
			</thead>
			<tbody>				
				<? $i=0; //echo'<pre>';print_r($items);exit;?>
				@if($reports)
				   @foreach($reports as $item)
						<tr>
							<td>{{++$i}}</td>
							<td>{{$item->item_name}}</td>
							<td>{{$item->purchase_price}}</td>
							<td>{{$item->sale_price}}</td>
							<td>{{$item->quantity}}</td>
							<td>{{$item->sale_price*$item->quantity}}</td>

							<input type="hidden" name="item_id" value="{{$item->item_id}}">
							<input type="hidden" name="price_id" value="{{$item->price_id}}">
							<input type="hidden" name="quantity" value="{{$item->quantity}}">

							<td>{{$item->receiving_by}}</td>
							<td>{{$item->receive_cancel_date}}</td>

						</tr>
				   @endforeach
				@else
					<tr>
						<td colspan="7" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>						
				@endif
			</tbody>
		</table>
		@endif
	  </div>
	</div>

@stop