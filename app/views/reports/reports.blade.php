@extends('_layouts.default')
@section('content')
	<!--Receipt-->
	<!--First column-->
	<div class="row">
		<div class="span12">
			@include('_sessionMessage')
		</div>
		<!--Item Category DIV-->
		@if (Session::get('role')==2)
		<div class="span4">		
			<div class="widget-header setup-title"> <i class="icon-list"></i>
			  <h3>Details Report</h3>
			</div>
			<div class="setup-items">
                @if (Session::get('role')==1 ||Session::get('role')==2)
				<a href="{{ route('sale.report') }}" class="setup-item">
					<i class="setup-icon icon-zoom-out"></i><span class="setup-label">Sales</span>
				</a>
            	@endif
        @if (Session::get('role')==2)
        		<a href="{{ route('saleReturn.report') }}" class="setup-item">
					<i class="setup-icon icon-undo"></i><span class="setup-label">Sale Return</span>
				</a>
				<a href="{{ route('purchase.report') }}" class="setup-item">
					<i class="setup-icon icon-zoom-in"></i><span class="setup-label">Purchase</span>
				</a>
				<a href="{{ route('purchaseOrder.report') }}" class="setup-item">
					<i class="setup-icon icon-zoom-in"></i><span class="setup-label">Purchase Order</span>
				</a>
				<a href="{{ route('saleOrder.report') }}" class="setup-item">
					<i class="setup-icon icon-zoom-in"></i><span class="setup-label">Sale Order</span>
				</a>
				<a href="{{ route('purchasereturn.report') }}" class="setup-item">
					<i class="setup-icon icon-plane"></i><span class="setup-label">Purchase Return</span>
				</a>
				<a href="{{ route('send.report') }}" class="setup-item"> 
					<i class="setup-icon icon-arrow-up"></i><span class="setup-label">Sending</span>
				</a>
				<a href="{{ route('receive.report') }}" class="setup-item">
					<i class="setup-icon icon-arrow-down"></i><span class="setup-label">Receiving</span>
				</a>
				<a href="{{ route('income.report') }}" class="setup-item">
					<i class="setup-icon icon-money"></i><span class="setup-label">Other Income</span>
				</a>
				<a href="{{ route('expense.report') }}" class="setup-item">
					<i class="setup-icon icon-plane"></i><span class="setup-label">Other Expense</span>
				</a>
				<a href="{{ route('damage.report') }}" class="setup-item">
					<i class="setup-icon icon-bolt"></i><span class="setup-label">Damage Items</span>
				</a>
				<a href="{{ route('returntogodowon.report') }}" class="setup-item">
					<i class="setup-icon icon-upload-alt"></i><span class="setup-label">Return To Godown</span>
				</a>
				<a href="{{ route('returnreceiving.report') }}" class="setup-item">
					<i class="setup-icon icon-arrow-left"></i><span class="setup-label">Return Receiving</span>
				</a>
				<a href="{{ route('duepaymentreport.report') }}" class="setup-item">
					<i class="setup-icon icon-arrow-left"></i><span class="setup-label">Due Payment Report</span>
				</a>
                <a href="{{ route('viewAllItem.report') }}" class="setup-item">
					<i class="setup-icon icon-zoom-in"></i><span class="setup-label">View All Item Report</span>
				</a>
        @endif
			</div>
        </div>		
		<div class="span4">		
            <div class="widget-header setup-title"> <i class="icon-group"></i>
              <h3>Summary Report</h3>
            </div>
			<div class="setup-items"> 				
        @if (Session::get('role')==1 ||Session::get('role')==2)
				<a href="{{ route('summary.sales') }}" class="setup-item">
					<i class="setup-icon icon-zoom-out"></i><span class="setup-label">Sales</span>
				</a>
        @endif
        @if (Session::get('role')==2)
				
                <a href="{{ route('summary.salesReturn') }}" class="setup-item">
					<i class="setup-icon icon-undo"></i><span class="setup-label">Sales Return</span>
				</a>

				<a href="{{ route('summary.purchaseReports') }}" class="setup-item">
					<i class="setup-icon icon-zoom-in"></i><span class="setup-label">Purchases</span>
				</a>
				<a href="{{ route('summary.purchaseReturnReports') }}" class="setup-item">
					<i class="setup-icon icon-plane"></i><span class="setup-label">Purchases Return</span>
				</a>

				<a href="{{ route('summary.otherIncomeReports') }}" class="setup-item">
					<i class="setup-icon icon-money"></i><span class="setup-label">Other Income</span>
				</a>
				<a href="{{ route('summary.otherExpenseReports') }}" class="setup-item">
					<i class="setup-icon icon-plane"></i><span class="setup-label">Other Expense</span>
				</a>       
				<a href="{{ route('dailyledger.report') }}" class="setup-item">
					<i class="setup-icon icon-arrow-left"></i><span class="setup-label">Daily Ledger</span>
				</a>                   
				<a href="{{ route('summary.damageProductReports') }}" class="setup-item">
					<i class="setup-icon icon-bolt"></i><span class="setup-label">Damage Items</span>
				</a>

                <a href="{{ route('viewAllItemCategoryWise.report') }}" class="setup-item">
					<i class="setup-icon icon-bolt"></i><span class="setup-label"> Category Wise Item View</span>
				</a>

                <a href="{{ route('itemWiseSalesReport.report') }}" class="setup-item">
					<i class="setup-icon icon-bolt"></i><span class="setup-label"> Item Wise Sales report</span>
				</a>
				
                <a href="{{ route('categoryWiseSalesReport.report') }}" class="setup-item">
					<i class="setup-icon icon-bolt"></i><span class="setup-label"> Category Wise Sales Report</span>
				</a>
				@if (Session::get('role')==0 ||Session::get('role')==2)
				<a href="{{ route('summary.spplierWisePurchase') }}" class="setup-item">
					<i class="setup-icon icon-zoom-out"></i><span class="setup-label">Supplier Wise Purchase</span>
				</a>
				<a href="{{ route('summary.spplierWiseSale') }}" class="setup-item">
					<i class="setup-icon icon-zoom-out"></i><span class="setup-label">Supplier Wise Sale</span>
				</a>
				<a href="{{ route('summary.itemWisePurchaseReport') }}" class="setup-item">
					<i class="setup-icon icon-zoom-out"></i><span class="setup-label"> Item Wise Purchase </span>
				</a>
        		@endif
        @endif
			</div>
        </div>
        @endif
        @if(Session::get('role')==1)
        <div class="span4">		
			<div class="widget-header setup-title"> <i class="icon-list"></i>
			  <h3>Details Report</h3>
			</div>

			<div class="setup-items">
				<a href="{{ route('sale.report') }}" class="setup-item">
					<i class="setup-icon icon-zoom-out"></i><span class="setup-label">Sales</span>
				</a>
		        <a href="{{ route('send.report') }}" class="setup-item"> 
					<i class="setup-icon icon-arrow-up"></i><span class="setup-label">Sending</span>
				</a>
				<a href="{{ route('receive.report') }}" class="setup-item">
					<i class="setup-icon icon-arrow-down"></i><span class="setup-label">Receiving</span>
				</a>
		        <a href="{{ route('saleOrder.report') }}" class="setup-item">
					<i class="setup-icon icon-zoom-in"></i><span class="setup-label">Sale Order</span>
				</a>
				<a href="{{ route('damage.report') }}" class="setup-item">
					<i class="setup-icon icon-bolt"></i><span class="setup-label">Damage Items</span>
				</a>
                   
				<a href="{{ route('returntogodowon.report') }}" class="setup-item">
					<i class="setup-icon icon-upload-alt"></i><span class="setup-label">Return To Godown</span>
				</a>
				<a href="{{ route('returnreceiving.report') }}" class="setup-item">
					<i class="setup-icon icon-arrow-left"></i><span class="setup-label">Return Receiving</span>
				</a>
				<a href="{{ route('dailyledger.report') }}" class="setup-item">
					<i class="setup-icon icon-arrow-left"></i><span class="setup-label">Daily Ledger</span>
				</a>
			</div>
		</div>
		@endif
		<div class="span4">		
            <div class="widget-header setup-title"> <i class="icon-user"></i>
              <h3>Full Report</h3>
            </div>
              <div class="setup-items"> 
              	 @if (Session::get('role')==2)
	              	<a href="{{ route('summary.fullReports') }}" class="setup-item"> 
						<i class="setup-icon icon-th-large"></i><span class="setup-label">Full Report</span> 
					</a>
				 @endif
	              	<a href="{{ route('summary.EmpSalesReports') }}" class="setup-item">
						<i class="setup-icon icon-group"></i><span class="setup-label">Employees Sales Report</span>
					</a>
					<a href="{{ route('summary.EmpDetailSaleReport') }}" class="setup-item">
						<i class="setup-icon icon-group"></i><span class="setup-label">Employees Sales Report(Details)</span>
					</a>	



			  </div>
        </div>
	</div>
	
	<style>
	</style>
	
	<!--Second column-->
	
	
	@include('admin.setup.jQuery_function')
@stop
@section('stickyInfo')
<?php
    $string = 'Reports';
    $li = '';
    for($j=0;$j<strlen($string);$j++){
        $li .= '<li>'.substr($string,$j,1).'</li>';
    }
?>
<div id="sticky" style="text-align: center;">        
	<ul id="example-3" class="sticklr" style="margin-left:5px;color:#ffffff;background-color: #053a64;font-size:18px;font-family:monospace;">
	    {{$li}}
	</ul>       
</div>
@stop