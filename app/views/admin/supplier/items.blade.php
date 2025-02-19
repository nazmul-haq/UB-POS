@extends('_layouts.default')

@section('content')

	<div class="row">
		<div class="span12" id="print_preview">
			<div class="widget-header setup-title"> <i class="icon-list"></i>
              <h3>Supplier Wise Items View</h3>
			  <span style="float:right; margin:0 15px;"><button class='btn btn-info btn-small print-btn' onclick="window.print();">Print</button></span>
			</div>
			<div class="print">	
				<div class="payment-header">
					<strong>
						Supplier Name : <span style="font-weight:normal; color: green;">{{$supplierInfo->user_name}}</span>
					</strong>
					<strong style="float:right;">
						Report On: <span style="font-weight:normal;"><?php echo date("d.m.Y"); ?></span>
					</strong>
				</div>
				<table class="table table-striped" width="100%">
					<thead class="table-head">
						<tr>
							<th>#SL No.</th>
							<th>Item Name</th>
							<th>UPC Code</th>
							<th>Status</th>
							<th>Category Name</th>
						</tr>
					</thead>
					<tbody>
						@if(!empty($itemsInfo))
						<?php $i=0;?>
							@foreach($itemsInfo as $item)
							
							<tr>
								<td>{{++$i}}</td>
								<td>{{$item->item_name}}</td>
								<td>{{$item->upc_code}}</td>
								<td>{{$item->status}}</td>
								<td>{{$item->category_name}}</td>
							</tr>
							@endforeach	
							
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
							

	<script>
		$(function(){ 			
			$('#amount').keyup(function(){
				var total_due = parseFloat($("#due_amount").html());
				var now_pay = parseFloat($("#amount").val());
				this.value  = Math.abs(this.value);
				$this_value = parseFloat(this.value); 
				if($('#amount').val() == 0){
					this.value = '';
					return false;
				}
				if(isNaN($this_value)){
					this.value = '';
				}
				if(now_pay > total_due){
					alert("You can't pay more than Due");
					this.value = total_due;
				}
			});
			
			var formSupllierPayment	 = $('#formSupllierPayment');
			// validate form for Item Brand
			formSupllierPayment.validate({
			  rules: {
			   amount: {
				   number: true,
				   min : 1,
				   required: true
				}
			  }, messages: {
					//'brand_name'	: { required:  '<span class="error">Brand Name required.</span>' },					
				},
				ignore				: ':hidden'	
			});
		});
	</script>
@stop