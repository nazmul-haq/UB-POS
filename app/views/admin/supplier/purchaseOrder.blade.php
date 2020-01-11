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
				<table class="table table-striped table-responsive" width="100%">
					<thead class="table-head">
						<tr>
							<th>#SL No.</th>
							<th>Item Name</th>
							<th>UPC Code</th>
							<th>Category Name</th>
							<th>Purchase Price</th>
							<th>Sale Price</th>
							<th>Disc.</th>
							<th>Available Qty.</th>
							<th>Qty.</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@if(!empty($itemsInfo))
						<?php $i=0;?>
							@foreach($itemsInfo as $item)
							{{ Form::open(array('route' => 'admin.supplier.addItemToPurchaseOrder', 'class' => 'form-horizontal')) }}
							<tr>
								<td>{{++$i}}</td>
								<td>{{$item->item_name}}</td>
								<td>{{$item->upc_code}}</td>
								<td>{{$item->category_name}}</td>
								<td>
									<input type="text" name="purchase_price" value="{{$item->purchase_price}}" style="width: 80px;" />
									<input type="hidden" name="item_id" value="{{$item->item_id}}">
									<input type="hidden" name="supp_id" value="{{$supplierInfo->supp_id}}">
									<input type="hidden" name="supp_or_comp_name" value="{{$supplierInfo->supp_or_comp_name}}">
									<input type="hidden" name="item_name" value="{{$item->item_name}}">
								</td>
								
								<td>
									<input type="text" name="sale_price" value="{{$item->sale_price}}"  style="width: 80px;"/>
								</td>
								<td>
									<input type="text" name="discount" value="{{$item->offer}}"  style="width: 80px;"/>
								</td>
								<td>
									<input type="text" name="available_qty" disabled="" value="{{$item->available_s_qty + $item->available_g_qty}}" style="width: 80px;" />
								</td>
								<td>
									<input type="text" name="quantity" value="1" style="width: 80px;" />
								</td>
								<td>
									<button name="addToPurchase" type="submit" class="btn btn-info btn-small"><i class="icon-shopping-cart"></i>Add To Purchase</button>
								</td>
							</tr>
							{{ Form::close() }}
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

			// $(".purchaseOrderTable").DataTable(); 			
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

        $('.Suppliers').addClass('active btn btn-fill');
	</script>
@stop
@section('stickyInfo')
<?php
    $string = 'Purchase Order';
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