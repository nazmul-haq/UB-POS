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
			  <h3>Add Income / Expense</h3>
			</div>
			<div class="setup-items"> 				
				<a href="{{ route('others.getOtherIncome') }}" class="setup-item"> 
					<i class="setup-icon icon-plus"></i><span class="setup-label">Add Income</span>
				</a>
				<a href="{{ route('others.getOtherExpense') }}" class="setup-item">
					<i class="setup-icon icon-arrow-down"></i><span class="setup-label">Add Expense</span>
				</a>
			</div>
			<div class="setup-items"> 				
				<a href="{{ route('admin.empSalary') }}" class="setup-item">
					<i class="setup-icon icon-arrow-down"></i><span class="setup-label">Employee Payment</span>
				</a>
			</div>
			<div class="setup-items"> 
				<a href="javascript:;" class="setup-item">
					<i class="setup-icon icon-file"></i><span class="setup-label">Notes</span> 
				</a>			
			</div>
        </div>		
		
		<div class="span4">		
            <div class="widget-header setup-title"> <i class="icon-group"></i>
              <h3>Summary Report</h3>
            </div>
              <div class="setup-items"> 	
					<a href="javascript:;" class="setup-item">
						<i class="setup-icon icon-file"></i><span class="setup-label">Notes</span> 
					</a>
					<a href="javascript:;" class="setup-item">
						<i class="setup-icon icon-user"></i><span class="setup-label">Users</span> 
					</a>
					<a href="javascript:;" class="setup-item">
						<i class="setup-icon icon-file"></i><span class="setup-label">Notes</span> 
					</a>
			  </div>
        </div>
		
		<div class="span4">		
            <div class="widget-header setup-title"> <i class="icon-user"></i>
              <h3>Graphical Report</h3>
            </div>
              <div class="setup-items"> 				
					<a href="javascript:;" class="setup-item"> 
						<i class="setup-icon icon-comment"></i><span class="setup-label">Comments</span> 
					</a>

			  </div>
        </div>
	</div>
@stop