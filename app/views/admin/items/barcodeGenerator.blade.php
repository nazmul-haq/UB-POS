@extends('_layouts.default')

@section('content')
<script type='text/javascript'>
	function integerCheck(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
$('.Items').addClass('active btn btn-fill');

</script>
@include('_sessionMessage')
<div class="widget-header setup-title"> <i class="icon-list"></i><h3>Barcode Generate</h3></span>
			</div>
<a href="{{ URL::to("barcodeQueueEmpty") }}" class="btn btn-warning"><i class="icon-crop"></i>&nbsp; Empty the Queue</a>
{{ Form::open(array('route' => 'barcode.print','id'=>'itemForm')) }}
<table class='table striped'>
<thead>
	<tr>
		<th>Upc Code</th>
		<th>Item Name</th>
        <th>Sale Price</th>
		<th>Quantity</th>
		<th>Action</th>
	</tr>
</thead>


	@foreach($itemBarcodeInfos as $barcodeInfo)
	
	<tr>
		<td>{{ $barcodeInfo['upc_code'] }}</td>
		<td>{{ $barcodeInfo['item_name'] }}</td>
        <td>{{ $barcodeInfo['sale_price'] }}</td>
		<td>
			<input type='text' onkeypress="return integerCheck(event)" name="barcode_quantity[]">
            <input type="hidden" name="itemInfo[]" value="<?php echo $barcodeInfo['upc_code'].','.$barcodeInfo['sale_price'].','.$barcodeInfo['item_name'];?>">
		</td>
		<td>
			<?php $key=$barcodeInfo['key'];?>
		    <a href="{{ URL::to("barcodeQueueItemDelete/$key") }}" class="btn btn-danger">Delete</a>
		</td>
		
	</tr>
	
	@endforeach
	<tr>
		<td colspan='5'> <button type="submit" class="finish btn btn-primary"><i class="icon-ok"></i> Print Barcode</button></td>
	</tr>
</table>
{{ Form::close()}}

@stop
@section('stickyInfo')
<?php
    $string = 'Items';
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