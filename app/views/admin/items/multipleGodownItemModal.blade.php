<p>
<a href="#" id="barcodeQueueDiff" class="btn btn-info"><i class="icon-print"></i>&nbsp; Barcode Queue</a>
</p>
{{ Form::open(array('route' => 'barcode.queue.all','id'=>'itemFormDiff')) }}
<table class="table table-bordered">

    <thead class="table-head">
        <tr>
             <th></th>
            <th>Purchase Price</th>
            <th>Sales Price</th>
            <th>Available Quantity</th>
            <th>Entry Date</th>
        </tr>
    </thead>
    <tbody>		
        @foreach($itemInfos as $item)
        <tr>
            <td>
                <input type="checkbox" name="barcodeInfo[]" value="{{ $item->item_id }}-{{$item->sale_price}}">
            
            </td>
			<td>{{ $item->purchase_price }}</td>
            <td>
				<span>{{ $item->sale_price }}</span>
			</td>
            <td>{{ $item->available_quantity }}</td>
            <td>{{	Helper::dateFormat($item->created_at)   }} </td>

        </tr>
        @endforeach
    </tbody>
</table>
{{ Form::close()}}



<script>
        $(function(){   

            $("#barcodeQueueDiff").click(function(){
                $("#itemFormDiff").submit();
            });             
                 
        });
    </script>