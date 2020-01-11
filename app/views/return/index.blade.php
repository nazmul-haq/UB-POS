@extends('_layouts.default')

@section('content')
	<!--Receipt-->
	
	
	<!--First column-->
	<div class="row">
		<div class="span12">
			@include('_sessionMessage')
		</div>
		<!--Item Category DIV-->
		<div class="span6">	
			<div class="widget-header setup-title"> <i class="icon-list"></i>
			  <h3>Product's Return</h3>
			</div>
			<div class="setup-items"> 				
                @if (in_array('saleReturn.index', Session::get('project_url')))
					<a href="{{ route('saleReturn.index') }}" class="setup-item"> 
						<i class="setup-icon icon-arrow-up"></i><span class="setup-label">Sales Return from Customer<br>(Invoice Wise)</span>
					</a>
                @endif

				<!--@if (in_array('saleReturn.index', Session::get('project_url')))-->
    <!--                <a href="{{ route('admin.returnQtyFromCustomer') }}" class="setup-item">-->
				<!--		<i class="setup-icon icon-zoom-out"></i><span class="setup-label overlay-bg">Sales Return from Customer<br>(Item Wise)</span>-->
    <!--                </a>-->
				<!--@endif-->

                            
                @if (in_array('returnReceive', Session::get('project_url')))
                    <a href="{{ route('purchase.returnToSupplier') }}" class="setup-item">
						<i class="setup-icon icon-arrow-up"></i><span class="setup-label">Return to Supplier<br>(Invoice Wise)</span>
					</a>
                @endif
				@if (in_array('returnReceive', Session::get('project_url')))
                    <a href="{{ route('admin.returnQtyFromGodown') }}" class="setup-item">
						<i class="setup-icon icon-zoom-out"></i><span class="setup-label overlay-bg">Return to Supplier <br>(Item Wise)</span>
                    </a>
				@endif
							
			</div>
        </div>	
<div class="span6">	
			<div class="widget-header setup-title"> <i class="icon-list"></i>
			  <h3>Return Products Sending Receiving</h3>
			</div>
			<div class="setup-items">

				<!--if (in_array('send.returnToGodown', Session::get('project_url')))-->
					<a href="{{ route('send.returnToGodown') }}" class="setup-item">
						<i class="setup-icon icon-arrow-up"></i><span class="setup-label">Return to WareHouse</span>
					</a>
                <!--endif 				-->
				@if (in_array('returnReceive', Session::get('project_url')))
					<a href="{{ route('returnReceive') }}" class="setup-item">
						<i class="setup-icon icon-arrow-down"></i><span class="setup-label">WareHouse Receiving</span>
					</a>
                @endif
                @if (in_array('damage.index', Session::get('project_url')))
					<a href="{{ route('damage.index') }}" class="setup-item">
						<i class="setup-icon icon-arrow-down"></i><span class="setup-label">Damage Products</span>
					</a>
                @endif
			</div>		
				

        </div>		

	</div>
	
	<style>
	</style>
	
	<!--Second column-->
	
	
	@include('admin.setup.jQuery_function')
@stop