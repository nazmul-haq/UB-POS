@extends('_layouts.default')

@section('content')
	<!--First column-->
	<div class="row">
		<div class="span12">
			@include('_sessionMessage')
		</div>
		<!--Item Category DIV-->
		<div class="span4">		
            <div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>Item Setup</h3>
            </div>
			<div class="setup-items"> 					
				<div class="setup-item item-overlay">
					<i class="icon-list-alt setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Item Category</span>
					<div class="item-overlay-body">							
						<p><a href="#itemCategoryModal" id="addItemCategory" class="btn btn-primary" role="button" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; Add</a></p>
						<p><a href="#viewItemCategoryModal" id="viewitemCategory" class="btn btn-default" role="button" data-toggle="modal"><i class="icon-zoom-in"></i> View</a></p>
					</div>	
				</div>			
				<div class="setup-item item-overlay">
					<i class="icon-bar-chart setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Item Brand</span>
					<div class="item-overlay-body">							
						<p><a href="#itemBrandModal" id="addItemBrand" class="btn btn-primary" role="button" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; Add</a></p>
						<p><a href="#viewItemBrandModal" id="viewItemBrand" class="btn btn-default" role="button" data-toggle="modal"><i class="icon-zoom-in"></i> View</a></p>
					</div>	
				</div>	
				<div class="setup-item item-overlay">
					<i class=" icon-road setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Item Location</span>
					<div class="item-overlay-body">							
						<p><a href="#itemLocationModal" id="addItemLocationModal" class="btn btn-primary" role="button" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; Add</a></p>
						<p><a href="#viewItemLocationModal" id="viewItemLocation" class="btn btn-default" role="button" data-toggle="modal"><i class="icon-zoom-in"></i> View</a></p>
					</div>	
				</div>
				<div class="setup-item item-overlay">
					<i class="icon-tasks setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Item Company</span>
					<div class="item-overlay-body">							
						<p><a href="#itemCompanyModal" id="addItemCompany" class="btn btn-primary" role="button" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; Add</a></p>
						<p><a href="#viewItemCompanyModal" id="viewItemCompany" class="btn btn-default" role="button" data-toggle="modal"><i class="icon-zoom-in"></i> View</a></p>
					</div>	
				</div>
                                
              <!--   <a href="{{ route('admin.returnQtyFromStock') }}">
                  <div class="setup-item item-overlay">               
					<i class="icon-edit setup-icon overlay-bg"></i>
					<span class="setup-label overlay-bg">Return Quantity from Stock</span>
				  </div>
                </a> -->
                                

			</div>
        </div>
		<!--Add New Item Category Modal-->
		<div id="itemCategoryModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemCategoryModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="itemCategoryModalLabel"><i class="icon-plus-sign"></i>&nbsp; Add Item Category</h3>
			</div>
			<div class="modal-body" id="AdditemCategorybody">
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
			</div> 
		</div>	
		<!--View Item Category Modal-->	
		<div id="viewItemCategoryModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="viewItemCatModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="viewItemCatModalLabel"><i class="icon-zoom-in"></i>&nbsp; View Item Category</h3>
			</div>
			<div class="modal-body" id="viewItemCategorybody">
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
			</div>
		</div>
		<!---close Item Category----->
		<!---Add New Item Brand-->
		<div id="itemBrandModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemBrandModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="itemBrandModalLabel"><i class="icon-plus-sign"></i>&nbsp; Add New Item Brand</h3>
			</div>
			<div class="modal-body" id="addItemBrandBody">
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>				
			</div>
		</div>
		<!--View Item Brand Modal-->	
		<div id="viewItemBrandModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="viewItemBrandLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="viewItemBrandLabel"><i class="icon-zoom-in"></i>&nbsp; View Item Brand</h3>
			</div>
			<div class="modal-body" id="viewItemBrandBody">
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
			</div>
		</div>
		<!--close Item Brand-->
		<!--Add New Item Location Modal-->
		<div id="itemLocationModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemLocationLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="itemLocationLabel"><i class="icon-plus-sign"></i>&nbsp; Add Item Location</h3>
			</div>
			<div class="modal-body" id="AdditemLocationbody">
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
			</div> 
		</div>	
		<!--View Item Location Modal-->	
		<div id="viewItemLocationModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="viewItemLocationLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="viewItemLocationLabel"><i class="icon-zoom-in"></i>&nbsp; View Item Location</h3>
			</div>
			<div class="modal-body" id="viewItemLocationbody">fdsfsdfsdfsdf
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
			</div>
		</div>
		<!---close Item Location----->
		<!---Add New Item Company-->
		<div id="itemCompanyModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="itemCompanyLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="itemCompanyLabel"><i class="icon-plus-sign"></i>&nbsp; Add New Item Company</h3>
			</div>
			<div class="modal-body" id="addItemCompanyBody">
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>				
			</div>
		</div>
		<!--View Item Company Modal-->	
		<div id="viewItemCompanyModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="viewItemCompanyLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="viewItemCompanyLabel"><i class="icon-zoom-in"></i>&nbsp; View Item Company</h3>
			</div>
			<div class="modal-body" id="viewItemCompanyBody">
				<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
			</div>
		</div>
		<!--close Item Brand-->
	<!--Second column-->
	<div class="row">
		
		<div class="span4">		
            <div class="widget-header setup-title"> <i class="icon-cog"></i>
              <h3>Offer Setup</h3>
            </div>
			<div class="setup-items"> 		
				<a href="{{ route('admin.addOffer') }}" class="setup-item">
					<i class="setup-icon icon-plus"></i><span class="setup-label">Add New Offer</span> 
				</a>	
				<a href="{{ route('admin.offerView') }}" class="setup-item">
					<i class="setup-icon icon-zoom-in"></i><span class="setup-label">View All Offer</span> 
				</a>
			</div>
        </div>
		
		<div class="span4">		
            <div class="widget-header setup-title"> <i class="icon-wrench"></i>
              <h3>Others Setup</h3>
            </div>
              <div class="setup-items"> 				
					<a href="{{ route('admin.getMembership') }}" class="setup-item">
						<i class="setup-icon icon-retweet"></i><span class="setup-label">Get Membership</span>
					</a>
					<div class="setup-item item-overlay">
						<i class="icon-signal setup-icon overlay-bg"></i>
						<span class="setup-label overlay-bg">Income/Exp Type</span>
						<div class="item-overlay-body">							
							<p><a href="#incExpModal" id="addIncExpModal" class="btn btn-primary" role="button" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; Add</a></p>
							<p><a href="#viewIncExpModal" id="viewIncExp" class="btn btn-default" role="button" data-toggle="modal"><i class="icon-zoom-in"></i> View</a></p>
						</div>	
					</div>
			  </div>
        </div>		
	</div>
	<!--Add New Income Expense Modal-->
	<div id="incExpModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="incExpLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="incExpLabel"><i class="icon-plus-sign"></i>&nbsp; Add Income/Expense Type</h3>
		</div>
		<div class="modal-body" id="AddIncExpbody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
		</div> 
	</div>	
	<!--View Income Expense Modal-->	
	<div id="viewIncExpModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="viewIncExpLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="viewIncExpLabel"><i class="icon-zoom-in"></i>&nbsp; View Income/Expense Type</h3>
		</div>
		<div class="modal-body" id="viewIncExpbody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
		</div>
	</div>
	<!---close Income/Expense----->
	
	@include('admin.setup.jQuery_function')
@stop
@section('stickyInfo')
<?php
    $string = 'Setup';
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