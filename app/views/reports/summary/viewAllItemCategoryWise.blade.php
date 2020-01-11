@extends('_layouts.default')

@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			
		<table class="table table-bordered">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 2em;"><i class="icon-bolt"></i> Items Category Wise Report</strong>
			</article>
			<thead>
				<tr>
					<th>#SL No</th>
					<th>Category Name</th>
					<th>Stock Qty.</th>
                    <th>Stock Qty. &nbsp;Amount&nbsp;(Tk)</th>
					<th>Godown Qty.</th>
					<th>Godown Qty. &nbsp;Amount&nbsp;(Tk)</th>
					<th>Total Qty.</th>
					<th>Total Qty. &nbsp;Amount&nbsp;(Tk)</th>
				</tr>
			</thead>
			<tbody>
				@if($all_items)
					<? $i=0; $total_stock_qty=0;$total_stock_qty_amount=0;$total_godown_qty=0;$total_godown_qty_amount=0;$tatal_all_qty=0;$tatal_all_qty_amount=0;?>
				   @foreach($all_items as $item)
					   <tr>
							<td>{{++$i}}</td>
							<td>{{$item->category_name}}</td>
                                                        <td>{{$item->t_s_qty}}<? $total_stock_qty=$total_stock_qty+$item->t_s_qty; ?></td>
                                                        <td>{{$item->t_s_q_amount}}<?$total_stock_qty_amount=$total_stock_qty_amount+$item->t_s_q_amount; ?></td>
                                                        <td>{{$item->t_g_qty}}<?  $total_godown_qty=$total_godown_qty+$item->t_g_qty; ?></td>
                                                        <td>{{$item->t_g_q_amount}}<? $total_godown_qty_amount=$total_godown_qty_amount+$item->t_g_q_amount; ?></td>
                                                        <td>{{$item->total_qty}}<? $tatal_all_qty=$tatal_all_qty+$item->total_qty ?></td>
                                                        <td>{{$item->t_s_q_amount+$item->t_g_q_amount}}<? $tatal_all_qty_amount=$tatal_all_qty_amount+($item->t_s_q_amount+$item->t_g_q_amount) ?></td>
							
						</tr>				   
				   @endforeach
                                   <tr style=" background:#DBEAF9; font-size: 15px;">
						   <td colspan="2"><strong style="font-size: 1.1em;">Total<strong></td>
                                                    <td><strong style="color: blue;">{{$total_stock_qty}}<strong></td>
                                                    <td><strong style="color: blue;">{{$total_stock_qty_amount}}&nbsp;(Tk)<strong></td>           
						    <td><strong style="color: green;">{{$total_godown_qty}}<strong></td>
                                                    <td><strong style="color: green;">{{$total_godown_qty_amount}}&nbsp;(Tk)<strong></td>
						    <td><strong style="color: red;">{{$tatal_all_qty}}<strong></td>   
                                                    <td><strong style="color: red;">{{$tatal_all_qty_amount}}&nbsp;(Tk)<strong></td>   
                                                             
					   </tr>
                        
				@else
					<tr>
						<td colspan="8" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
                               
			</tbody>
		</table>
	  </div>	
	  </div>
			
	</div>

	
@stop