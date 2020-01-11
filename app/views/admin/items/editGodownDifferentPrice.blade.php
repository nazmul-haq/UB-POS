{{ Form::open(array('route' => 'godownDifferentPriceEdit.post','id'=>'itemFormDiff')) }}
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
    <? $total_quantity=0; $i=0; ?>
        @foreach($itemInfos as $item)
        <? $total_quantity+=$item->available_quantity; ?>
        <tr>
            <td>
                <input type="checkbox" name="price_id{{ $i++ }}" value="{{ $item->price_id }}">
            </td>
			<td><input  type='text' name='purchase_price[]' value='{{ $item->purchase_price }}' class='span1'></td>
			<td><input  type='text' name='sale_price[]' value='{{ $item->sale_price }}' class="span1"></td>
            <td>{{ $item->available_quantity }}</td>
            <td>{{	Helper::dateFormat($item->created_at)   }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
<input type='hidden' name='item_id' value='{{ $item->item_id }}'>
<input type='hidden' name='total_quantity' value='{{ $total_quantity }}'>
<input type='submit' value='Update'>
{{ Form::close()}}
