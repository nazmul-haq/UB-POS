@extends('_layouts.default')

@section('content')
<style>.head6{ width: 130px}</style>
	<div class="row print_disable">
		<div class="span12">			
			<div class="widget-header setup-title"> <i class="icon-plus"></i>
			  <h3>Add Expense Reason</h3>
			</div>
			@include('_sessionMessage')
			<div class="content-bg">
			{{ Form::open(array('route' => 'saveOtherExpense.post', 'class' => 'form-horizontal', 'id' => 'addExpense')) }}
				<div class="control-group">
					{{ Form::label('expense_type_id', 'Expense Type', ['class' => 'control-label', 'style' => 'font-weight: bolder;']) }}
					<div class="controls">
						{{ Form::select('expense_type_id', $expenseReasons, array('class' => 'span3')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
				
				<div class="control-group">
					{{ Form::label('date', 'Date ', ['class' => 'control-label', 'style' => 'font-weight: bolder;']) }}
					<div class="controls">
						<input id="dp3" name="date" data-date="<?= date("Y-m-d") ?>" data-date-format="yyyy-mm-dd" class="datepicker span3" type="text" value="<?= date("Y-m-d") ?>">
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
				
				<div class="control-group">
					{{ Form::label('amount', 'Amount', ['class' => 'control-label', 'style' => 'font-weight: bolder;']) }}
					<div class="controls">
						{{ Form::text('amount', null, array('class' => 'span3', 'placeholder' => 'Enter Amount')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
				
				<div class="control-group">
					{{ Form::label('comment', 'Comment', ['class' => 'control-label', 'style' => 'font-weight: bolder;']) }}
					<div class="controls">
						{{ Form::textarea('comment', null, array('rows' =>'1', 'class' => 'span3', 'placeholder' => 'Enter Comment Here...')) }}
					</div> <!-- /controls -->
				</div> <!-- /control-group -->
				
				<div class="control-group">
				{{ Form::label('', '', ['class' => 'control-label', 'style' => 'margin-left: 17px;']) }}
					{{ Form::submit('Save Changes', array('class' => 'btn btn-primary')) }}
					<button type="reset" class="btn btn-default">Clear</button>
				</div> <!-- /control-group -->
				
			{{ Form::close() }}
			</div>
		</div>
	</div>
		
	<div class="row">
		<div class="span12" id="print_preview" style="margin-top:10px;">
			<div class="widget-header setup-title"> <i class="icon-list"></i>
			  <h3>View Other Expense History</h3>
			  <span style="float:right; margin:0 15px;"><button class='btn btn-info btn-small print-btn' onclick="window.print();">Print</button></span>
			</div>		
			<div class="print">						
				{{ Datatable::table()
					->addColumn('#','Expense Type','Amount','Comment','Date', 'Action')      
					->setUrl(route('others.getExpenseByDatable'))   
					->render() }}	
				<div style="float:right;"></div>
			</div>
		</div>
	</div>
	
	<!--Edit Other Income Modal-->
	<div id="editOtherExpenseModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="expenseModalLabel" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&#735;</button>
			<h3 id="expenseModalLabel"><i class="icon-edit-sign"></i>&nbsp; Edit Expense Reason</h3>
		</div>
		<div class="modal-body" id="editExpenseBody">
			<div id="loading">{{ HTML::image('img/loader.gif', 'Please Wait...')  }}</div>				
		</div>	
	</div>	
		
	<script>
		$(function(){
			var addExpense = $('#addExpense');

			jQuery.validator.addMethod("alphaspace", function(value, element) {
			   return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
			}, "Only letters, Numbers & Space/underscore Allowed.");

			jQuery.validator.addMethod("floatNumber", function(value, element) {
			   return this.optional(element) || value == value.match(/^-?(?:\d+|\d{1,3}(?:,\d{3})+)?(?:\.\d{1,2})?$/);
			}, "Value must be float or int after (.) only contain two decimal place.");
			// validate form for Item Category
			addExpense.validate({
			  rules: {
			   expense_reason: {
				   alphaspace: true,
				   required: true
				},
			   amount: {
				   floatNumber: true,
				   required: true
				},
			   date: {
				   required: true
				}
			  }, messages: {
					//'category_name'	: { required:  '<span class="error">Category Name required.</span>' },					
				},
				ignore				: ':hidden'	
			});
		});
	
	function otherExpenseEdit(expenseId){			
		$(function() {
			$('#loading').ajaxStart(function() {
				$(this).show();
			}).ajaxComplete(function() {
				$(this).hide();
			}); 
			$("#editExpenseBody").load("{{ URL::to('admin/otherExpenseEdit') }}"+"/"+expenseId);	
		});
	}	
		
	function deleteConfirm(expenseId){
		var con=confirm("Do you want to delete?");
		if(con){
			$(function(){
				$.ajax({
					url: "otherExpenseInactive/"+expenseId,
					success : function(data){
						if(data.status == "success"){
							$("#"+expenseId).parent().parent().fadeOut("slow");
							$('#message').html('<strong class="ajax-message-suc"><i class="icon-ok"></i> Delete Successfully </strong>');
							$('#message').css('display', 'block').fadeOut(7000);
						} else{								
							$('#message').html('<strong class="ajax-message-err"><i class="icon-warning-sign"></i> Something Worng! </strong>');
							$('#message').css('display', 'block').fadeOut(7000);
						}
					}
				});
			});
			return true;
		}
		else{
			return false;
		}
	}
	</script>
	<script>
		 /*$(function(){			 
			 //Auto Complete for Item Search
			 $("#auto_suggest_other").autocomplete("{{route('other.expenseAutoSuggest')}}", {
				width: 260,
				matchContains: true,
				queryDelay : 0,
				 formatItem: function(row) {
					return row[1];
				},	 		
			});
		 });*/	
	</script>
@stop