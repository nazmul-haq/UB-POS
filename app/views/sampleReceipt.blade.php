@extends('_layouts.default')

@section('content')

<?

?>
	<!--Receipt-->	
	<div class="row">
		<div class="span12">    	
			<article class="head-receipt">
				<ul style="list-style-type:none; margin: 0;">
				  <li><strong class="company-name">Unitech Eng IT</strong></li>
				  <li>Panget, Dhaka-1200</li>
				  <li>01812615198</li>
				</ul>
			 <center>
			 <table>
				<tbody>
					<tr style="border-bottom: 1px solid #ccc; width:50%; margin: 3px auto;">
						<td colspan="3" style="text-align:center;"><b>Sales Receipt</b></td>
					</tr>
					<tr>
						<td align="right">Customer Name</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">Google.com</td>
					</tr>
					<tr>
						<td align="right">Transaction Date</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">
							15-05.2015
						</td>
					</tr>
					<tr>
						<td align="right">Invoice No</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">sdfsadff</td>
					</tr>
					<tr>
						<td align="right">Sold By</td>
						<td>&nbsp;&nbsp; : &nbsp;</td>
						<td align="left">Rimon</td>
					</tr>
				</tbody>
			 </table> 
			 </center>
			</article>	
			<table class="item-sales">
				<thead class="table-receipt-head">
					<tr>
						<th></th>
						<th>Item</th>
						<th>Price</th>
						<th>Qty.</th>
						<th>Disc (tk)</th>
						<th width="120">Tax (tk)</th>
						<th width="120">Total</th>
					</tr>
				</thead>
				<tbody>
					<tr class="tr-receipt">
						<td>1</td>
						<td>ABC</td>
						<td>4	</td>
						<td>45</td>
						<td>31</td>
						<td>2</td>
						<td>343</td>
					</tr>
					<tr class="tr-receipt">
						<td>1</td>
						<td>ABC</td>
						<td>4	</td>
						<td>45</td>
						<td>31</td>
						<td>2</td>
						<td>254</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td style="text-align: right;">Total</td>
						<td><b>(4)</b></td>
						<td></td>
						<td style="line-height: 5px; text-align: left;">Sub Total</td>
						<td style="line-height: 5px; text-align: right; padding-right: 12px;">120</td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td style="line-height: 8px; text-align: left;">Payment Type</td>
						<td style="line-height: 5px; text-align: right; padding-right: 12px;">Cash</td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td style="line-height: 5px; text-align: left;">Discount&nbsp;(Tk)</td>
						<td style="line-height: 5px; text-align: right; padding-right: 12px;">5</td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td style="line-height: 5px; text-align: left;">Use Point</td>
						<td style="line-height: 5px; text-align: right; padding-right: 12px;">10</td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td style="line-height: 5px; text-align: left;">Point Tk.</td>
						<td style="line-height: 5px; text-align: right; padding-right: 12px;">100</td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td style="line-height: 5px; text-align: left;">Total</td>
						<td style="line-height: 5px; text-align: right; padding-right: 12px;">500.50</td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td style="line-height: 5px; text-align: left;">Pay</td>
						<td style="line-height: 5px; text-align: right;  padding-right: 12px;">1450.00</td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td style="line-height: 5px; text-align: left;">Due</td>
						<td style="line-height: 5px; text-align: right;  padding-right: 12px;">100</td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td style="line-height: 5px; text-align: left;">Pay Note</td>
						<td style="line-height: 5px; text-align: right; padding-right: 12px;">1000</td>
					</tr>
					<tr>
						<td colspan="5"></td>
						<td style="line-height: 5px; text-align: left;">Return</td>
						<td style="line-height: 5px; text-align: right; padding-right: 12px;">50.50</td>
					</tr>
				</tbody>
			</table>
				
			<article style="clear:both; text-align: center;">
				<h6>Thanks for being with us</h6>
				<div align="center">
					{{ DNS1D::getBarcodeHTML(564646545, "C128", 1, 25) }}					
					<strong>654654646465</strong>				
				</div>
				<p style="float:right;">Developed By : <strong>Unitech IT</strong></p>
				
			</article>
		</div>		
	</div>
	
@stop

