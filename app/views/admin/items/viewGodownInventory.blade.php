@extends('_layouts.default')

@section('content')
	<style>.head9{ width: 151px}</style>
	<div class="row">
	  <div class="span12">
<h3>View Item Low inventory</h3>
		@include('_sessionMessage')
		<div class="employee-btn">
		@if (in_array('barcode.post', Session::get('project_url')))
			<a href="#" id="barcodeQueue" class="btn btn-info"><i class="icon-print"></i>&nbsp; Barcode Queue</a>
			<a href="{{ URL::route('barcode') }}" class="btn btn-info"><i class="icon-barcode"></i>&nbsp; Barcode Print</a>
		@endif
		
		<div class="btn-group">
				<a  class="btn btn-primary" href="javascript:;" data-toggle="dropdown"><i class="icon-user icon-white"></i>&nbsp;View Low Inventory</a>
				<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="javascript:;"><span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href="{{ URL::route('admin.godownLowInventory') }}"><i class="icon-zoom-in"></i>&nbsp; Godown Low Inventory</a></li>
					<li><a href="{{ URL::route('admin.stockLowInventory') }}"><i class="icon-zoom-in"></i>&nbsp; Stock Low Inventory</a></li>
				</ul>
			</div>
		</div>

		<div id="message" style="display: none;"></div>
		{{ Form::open(array('route' => 'barcode.queue.all','id'=>'itemForm')) }}
		{{ Datatable::table()
				->addColumn('#','Upc Code','Item Name','Company Name','Purchase Price','Sale Price','Offer','Available Quantity')
				->setUrl(route('admin.getGLInventory'))
				->render() }}
		{{ Form::close()}}
	  </div>
	</div>
	
  <script>
  $('.Items').addClass('active btn btn-fill');
	$().ready(function(){
		$("#barcodeQueue").click(function(){
		$("#itemForm").submit();

		});
		
	});
</script>
	
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