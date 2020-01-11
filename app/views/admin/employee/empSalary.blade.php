@extends('_layouts.default')
@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'admin.empSalary', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
					 	Select Employee :
					 	<select name="employee_id" class="span2">
					 		@if(count($allEmployees)>0)
					 		@foreach($allEmployees as $employee)
					 		<option value="{{$employee->emp_id}}">
					 			{{$employee->f_name}}
					 		</option>
					 		@endforeach
					 		@endif
					 	</select>
					 	&nbsp;&nbsp;
					 	Month
					 	<select name="month" class="span2">
					 		<?php $monthNames = Helper::monthName(); ?>
					 		@foreach($monthNames as $key => $value)
					 			<option value="{{$key}}" @if($key == date('F')) selected @endif>{{$value}}</option>
					 		@endforeach
					 	</select>
					 	&nbsp;&nbsp;
					 	Amount
					 	<input type="number" name="amount" class="span2" />
					 	<input id="dp3" name="date" data-date="<?= date("Y-m-d") ?>" data-date-format="yyyy-mm-dd" class="datepicker span2" type="text" value="<?= date("Y-m-d") ?>">
						<input class="btn btn-primary" type="submit" value="Make Payment">
					</div> <!-- /control-group -->
                {{ Form::close() }}
            </div>
		@if(count($empSalarys)>0)
		<table class="table table-bordered" id="datatable">
				<thead>
				<tr>
					<th>SL</th>
					<th>Invoice ID</th>
					<th>Employee Name</th>
					<th>Amount</th>
					<th>Salary Month</th>
					<th>Given Date</th>
					<th>Given By</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<? $total_amount_taka=0;?>
				@if(count($empSalarys)>0)
				   @foreach($empSalarys as $key => $empSalary)
					   <tr>
							<td>{{++$key}}</td>
							<td>{{$empSalary->invoice_id}}</td>
							<td>{{$empSalary->emp_f_name}} {{$empSalary->emp_l_name}}</td>
							<td>{{$empSalary->amount}}</td>
							<td>{{$empSalary->salary_month}}</td>
							<td>{{$empSalary->date}}</td>
							<td>{{$empSalary->given_by}}</td>
							<td width="80px">
								<a href="{{URL::to('admin/empSalaryDetails/').'/'.$empSalary->invoice_id}}" class="btn btn-warning btn-small" role="button">
									<i class="icon-zoom-in"></i> Details
								</a>
							</td>
						</tr>				   
				    @endforeach              
                	@else
						<tr>
							<td colspan="12" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
						</tr>
					@endif
			</tbody>
			<tfoot>
				  <tr style=" background:#DBEAF9; font-size: 15px;">
					   
				   </tr>
			</tfoot>
		</table>
	  </div>	
	  </div>	
		<!--Sale Details Model-->
		<div id="saleDetailsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="saleDetailsLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="saleDetailsLabel"><i class="icon-zoom-in"></i>&nbsp; Sale Details</h3>
			</div>
			<div id="printable">
				<div class="modal-body print_modal" id="saleDetailsBody">
					<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
				</div>
			</div>	
		</div>	
		@endif
	</div>
	<script>	
		function printElement(elem, append, delimiter) {
			var domClone = elem.cloneNode(true);
			var $printSection = document.getElementById("printSection");
			if (!$printSection) {
				var $printSection = document.createElement("div");
				$printSection.id = "printSection";
				document.body.appendChild($printSection);
			}
			if (append !== true) {
				$printSection.innerHTML = "";
			}
			else if (append === true) {
				if (typeof(delimiter) === "string") {
					$printSection.innerHTML += delimiter;
				}
				else if (typeof(delimiter) === "object") {
					$printSection.appendChlid(delimiter);
				}
			}
			$printSection.appendChild(domClone);
		}
		function loadingImg(){
			$('#loading').ajaxStart(function() {
				$(this).show();
			}).ajaxComplete(function() {
				$(this).hide();
			});
		}
		function saleDetails(saleInvoiceId){
			$(function(){									
				loadingImg();
				$("#saleDetailsBody").load("{{ URL::to('admin/sale/saleReportDetails') }}"+"/"+saleInvoiceId);
			});
		}
		$("#datatable").DataTable();
		$("#checkAll").on('change', function(){
           $("input:checkbox").prop('checked', $(this).prop("checked"));
       	});
		// $("#deleteAll").on('click', function(){
  //     		$("#submitForm").submit();
  //   	});
	</script>
@stop