@extends('_layouts.default')

@section('content')
	<div class="row">
	<div>
	  <div class="span12">
		@include('_sessionMessage')
			<div class="invoice-reg print_disable">
                {{ Form::open(array('route' => 'damage.report', 'class' => 'form-horizontal')) }}
					<div class="control-group" align="center">
						<i class="icon-calendar"></i>&nbsp; Form : {{ Form::text('from', $from, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'Form date')) }}
						&nbsp;&nbsp;&nbsp;
						<i class="icon-calendar"></i>&nbsp; To 	 : {{ Form::text('to', $to, array('class' => 'span2 datepicker', 'data-date-format'=> 'yyyy-mm-dd', 'placeholder' => 'To date')) }}
						<input class="btn btn-primary" type="submit" value="Search">
					</div> <!-- /control-group -->

                {{ Form::close() }}
            </div>
		<table class="table table-bordered">
			<article style="background: #EEEEEE; padding : 5px 0 7px; border-top: 1px solid #003454;">
				<strong style="font-size: 2em;"><i class="icon-bolt"></i> Damage Products Report</strong>
				<strong style="float: right; margin: 6px 5px"><i class="icon-calendar"></i>&nbsp; From : <span style="font-weight: normal;">{{ Helper::onlyDMY($from) }}</span> &nbsp;&nbsp;&nbsp;<i class="icon-calendar"></i>&nbsp;&nbsp;To : <span style="font-weight: normal;">{{Helper::onlyDMY($to)}}</span></strong>
			</article>
			<thead>
				<tr>
					<th>SL</th>
					<th>Damage Invoice ID</th>
					<th>Amount</th>
					<th>Date</th>
					<th>Sold By</th>
				</tr>
			</thead>
			<tbody>
				@if($reports)
					<? $i=0; $total_amount = 0;?>
				   @foreach($reports as $damageItem)
					   <tr>
							<td>{{++$i}}</td>
							<td><a href="#damageModel" onclick="damageDetails({{$damageItem->damage_invoice_id}})" data-toggle="modal">{{$damageItem->damage_invoice_id}}</a></td>
							<td>{{$damageItem->amount}} <? $total_amount = $total_amount + $damageItem->amount; ?></td>
							<td>{{$damageItem->date}}</td>
							<td>{{$damageItem->user_name}}</td>
						</tr>				   
				   @endforeach              
                        <tr bgcolor="#DBEAF9">
						   <td colspan="2"><strong style="font-size: 1.3em;">Total<strong></td>
						   <td><strong style="color: green;">{{$total_amount}}<strong></td>
							<td colspan="2"></td>
						</tr>
				@else
					<tr>
						<td colspan="4" style="text-align:center; color:#E98203;"><strong>There are no record available.</strong><td>
					</tr>
				@endif
                               
			</tbody>
		</table>
	  </div>	
	  </div>	
		<!--Sale Details Model-->
		
		<div id="damageModel" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="damageItemLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
				<h3 id="damageItemLabel"><i class="icon-zoom-in"></i>&nbsp; Damage Product Details</h3>
			</div>
			
			<div id="printable">
				<div class="modal-body print_modal" id="damageDetailsBody">
					<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>			
				</div>
			</div>	
		</div>			
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
		function damageDetails(damageInvoiceId){
			$(function(){									
				loadingImg();
				$("#damageDetailsBody").load("{{ URL::to('admin/damageReportDetails') }}"+"/"+damageInvoiceId);
			});
		}
	</script>
@stop