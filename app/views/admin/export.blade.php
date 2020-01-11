@extends('_layouts.default')
@section('content')
	<!--First column-->
	<div class="row">
		<div class="span12">
			@include('_sessionMessage')
		</div>
		<!--Item Category DIV-->
		<div class="span12">
            <div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>Export Panel</h3>
            </div>
			<div class="setup-items">	
				<div class="setup-item item-overlay">
					<i class="icon-list-alt setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">All Items</span>
					<div class="item-overlay-body">			
						<br>
						<p><a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('iteminfos'))}}/{{base64_encode(base64_encode('allItem'))}}" id="addItemCategory" class="btn btn-primary" role="button"><i class="icon-download"></i>&nbsp; Download</a></p>
					</div>
				</div>		
				<div class="setup-item item-overlay">
					<i class="icon-bar-chart setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Stock Items</span>
					<div class="item-overlay-body">
						<br>
						<p><a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('iteminfos'))}}/{{base64_encode(base64_encode('stockItem'))}}" id="addItemBrand" class="btn btn-primary" role="button"><i class="icon-download"></i>&nbsp; Download</a></p>
					</div>
				</div>
				<div class="setup-item item-overlay">
					<i class=" icon-road setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Category Wise Items</span>
					<div class="item-overlay-body">
						<br>
						<p><a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('iteminfos'))}}/{{base64_encode(base64_encode('catWiseItem'))}}" id="addItemLocationModal" class="btn btn-primary" role="button"><i class="icon-download"></i>&nbsp; Download</a></p>
					</div>
				</div>
				<div class="setup-item item-overlay">
					<i class="icon-tasks setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Employees</span>
					<div class="item-overlay-body">
						<br>
						<p><a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('empinfos'))}}" id="addItemCompany" class="btn btn-primary" role="button"><i class="icon-download"></i>&nbsp; Download</a></p>
					</div>
				</div>
				<div class="setup-item item-overlay">
					<i class="icon-tasks setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Supliers</span>
					<div class="item-overlay-body">
						<br>	
						<p><a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('supplierinfos'))}}" id="addItemCompany" class="btn btn-primary" role="button"><i class="icon-download"></i>&nbsp; Download</a></p>
					</div>
				</div>
				<div class="setup-item item-overlay">
					<i class="icon-tasks setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Customers</span>
					<div class="item-overlay-body">
						<br>
						<p><a href="{{ URL::to('exportToCsv')}}/{{base64_encode(base64_encode('customerinfos'))}}" id="addItemCompany" class="btn btn-primary" role="button"><i class="icon-download"></i>&nbsp; Download</a></p>
					</div>
				</div>
			</div>
        </div>
	<!--Second column-->
	@include('admin.setup.jQuery_function')
@stop