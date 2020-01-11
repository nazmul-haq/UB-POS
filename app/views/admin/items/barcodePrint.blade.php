@extends('_layouts.default')

@section('content')
<div style='margin-top:15px;'>
<?php 
//echo'<pre>';
//print_r($itemInfo);exit;
//

$num_quantity_element=count($barcode_quantity);
$num_itemInfo_element=count($itemInfo);

/*this condition check for prevent hacking....
that means if anybody remove text button then this logic will get action
*/
if($num_quantity_element!=$num_itemInfo_element){

	echo "Something wrong";
}else{ $j=0;
	foreach($itemInfo as $items){
                $item=explode(",",$items);
               
             for($i=0;$i<$barcode_quantity[$j];$i++){
		?>
		<div style='float:left; width:120px; border:1px solid #d4d4d4; line-height:12px; text-align:center; margin:2px; font-weight:bold; padding:2px; min-height: 80px;'>
					<div>{{ Session::get('company_name') }}</div>
					<div style='width:120px; letter-spacing: 1px;' align="center">{{ DNS1D::getBarcodeHTML($item[0], "C128", 1, 25) }}
					<div style="font-size: 11px;">{{ $item[0] }}</div>
					</div>
						
					<div style="font-size: 10px;">{{ $item[2] }}</div>
					<div style="font-size: 10px; font-weight: bold;">M.R.P: {{ $item[1] }} Tk</div>
						
				
		</div>
		<?php
             }
             $j++;
	}
    }
    ?>
	</div>
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