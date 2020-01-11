@extends('_layouts.default')

@section('content')

	<div class="row">
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'summary.getEmpSaleReports', 'class' => 'form-horizontal')) }}
					<div class="control-group">
						<div class="controls">
							<strong style="text-align: center; margin-left: 158px;"><i class="icon-user"></i>&nbsp; Employee User Name : &nbsp;</strong>
							@if(Session::get('role')!=2)
							<select name='emp_id'>
								<option value="{{ Session::get('emp_id')}}">{{ Session::get('user_name') }}</option>
							</select>
							@else
							{{ Form::select('emp_id', $empList, null,  array('class' => 'span3', 'required' => 'required', 'id' => 'emp_name')) }}
							@endif
						</div> <!-- /controls -->
                                                
					</div> <!-- /control-group -->
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>
	<article style="margin-bottom: 10px; background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 2em;"><i class="icon-user"></i> @if($emp_name){{ ucwords($emp_name->user_name) }} @endif Summary : Employee Wise Full Report</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{ Helper::onlyDMY($to) }}</span></strong>
	    </article>
	<div class="row" style="margin-bottom: 10px;">
            
		<div class="span4 full_print">
			<div class="widget-header setup-title"> <i class="icon-credit-card"></i>
              <h3>Sales</h3>
            </div>
			<table class="table table-bordered" style="margin: 0; padding:0;">
				<tbody>
					 <tr>
						<td><strong style="float:left;">Sub Total </strong></td>
						<td class="span2"><strong style="color: red; font-size: 1.0em;">{{ $sales->sale_amount + ($sales->sale_discount + $sales->sale_point_use_taka) }}</strong></td>
					 </tr>
					 <tr>
						<td><strong style="float:left;">Discount </strong></td>
						<td>{{$sales->sale_discount}}</td>
					</tr>
					<tr>
						<td><strong style="float:left;">Point Use Taka </strong></td>
						<td>{{$sales->sale_point_use_taka}}</td>
					</tr>
					<tr>
						<td><strong style="float:left;">Total Amount </strong></td>
						<td><strong style="color: green; font-size: 1.5em;">{{ $sales->sale_amount }}</strong></td>
					</tr>
					<tr>
						<td><strong style="float:left;">Paid </strong></td>
						<td>{{ $sales->sale_pay }}</td>
					</tr>
					<tr>
						<td><strong style="float:left; ">Due </strong></td>
						<td>{{ $sales->sale_due }}</td>
					</tr>
				</tbody>
			</table>			
		</div>
		<div class="span4 full_print">				
			<div class="widget-header setup-title"> <i class="icon-arrow-left"></i>
				<h3>Sales Return</h3>
			</div>
			<table class="table table-bordered" style="margin: 0; padding:0;">
				<tbody>
					<tr>
						<td><strong style="float:left;">Sub Total</strong></td>
						<td class="span2"><strong style="color: #FF5400; font-size: 1em;">{{$salereturn->salereturn_less+$salereturn->salereturn_amount}}</strong></td>
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
				</tbody>
			</table>
		</div>
		<div class="span4 full_print">		
			<div class="widget-header setup-title"> <i class="icon-money"></i>
              <h3>Other Incomes</h3>
            </div>
			<p>
                <strong>Total Amount :</strong>&nbsp;&nbsp;<strong style="color: green; font-size: 1.2em;">{{$other_income}}</strong>
			</p>				
			<div class="widget-header setup-title" style="margin-top:60px;"> <i class="icon-money"></i>
              <h3>Other Expenses</h3>
            </div>
			<p>
               <strong>Total Amount :</strong>&nbsp;&nbsp;<strong style="color: green; font-size: 1.2em;">{{$other_expense}}</strong>
			</p>
		</div>
	</div>
	<div class="row">
		<div class="span4 full_print">				
			<div class="widget-header setup-title"> <i class="icon-shopping-cart"></i>
              <h3>Purchases</h3>
            </div>
			<table class="table table-bordered" style="margin: 0; padding:0;">
				<tbody>
					<tr>
						<td><strong style="float:left;">Sub Total </strong></td>
						<td class="span2"><strong style="color: red; font-size: 1.0em;">{{$purchase->purchase_discount+$purchase->purchase_amount}}</strong></td>
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
		</div>
		<div class="span4 full_print">				
			<div class="widget-header setup-title"> <i class="icon-undo"></i>
				<h3>Purchases Return</h3>
			</div>
			<table class="table table-bordered" style="margin: 0; padding:0;">
				<tbody>
					<tr>
						<td><strong style="float:left;">Sub Total</strong></td>
						<td class="span2"><strong style="color: red; font-size: 1.0em;">{{$purchasereturn->purchasereturn_less+$purchasereturn->purchasereturn_amount}}</strong></td>
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
		<div class="span4 full_print">	
			<div class="widget-header setup-title"> <i class="icon-money"></i>
              <h3>Customer Due Payment</h3>
            </div>
			<p>
				<strong>Total Amount :</strong>&nbsp;&nbsp;<strong style="color: green; font-size: 1.2em;">{{ $cusDuePayment->total_amount }}</strong>
			</p>
			<div class="widget-header setup-title"> <i class="icon-money"></i>
              <h3>Supplier Due Payment</h3>
            </div>
			<p>
				<strong>Total Amount :</strong>&nbsp;&nbsp;<strong style="color: green; font-size: 1.2em;">{{ $supDuePayment->total_amount }}</strong>
			</p>
			
			<div class="widget-header setup-title"> <i class="icon-money"></i>
              <h3>Damage Products</h3>
            </div>
			<p>
                            <strong>Total Amount :</strong>&nbsp;&nbsp;<strong style="color: green; font-size: 1.2em;">{{$damage->damage_amount}}</strong>
			</p>
		</div>
	</div>
	<div class="row" style="margin-top: 10px;">
		<div class="span12">
			<p>
				<strong style="font-size: 1.1em;">Total Debit Amount &nbsp;&nbsp;: &nbsp;</strong>
				<strong style="font-size: 1.0em; color: green">{{$purchasereturn->purchasereturn_amount+$sales->sale_amount+$other_income+$cusDuePayment->total_amount}}</strong>
			</p>
			<p>
				<strong style="font-size: 1.1em;">Total Credit Amount &nbsp;: &nbsp;</strong>
				<strong style="font-size: 1.0em; color: #2A80BB">{{$purchase->purchase_amount+$salereturn->salereturn_amount+$other_expense+$supDuePayment->total_amount}}</strong>
			</p>
                        <p>
				<strong style="font-size: 1.1em;">Cash Amount &nbsp;: &nbsp;</strong>
				<strong style="font-size: 1.0em; color: #FF5400">{{($purchasereturn->purchasereturn_amount+$sales->sale_amount+$other_income+$cusDuePayment->total_amount)-($purchase->purchase_amount+$salereturn->salereturn_amount+$other_expense+$supDuePayment->total_amount)}}</strong>
			</p>
		</div>
	</div>
	

@stop