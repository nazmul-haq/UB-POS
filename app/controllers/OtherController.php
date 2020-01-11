<?php

class OtherController extends \BaseController {
    public $timestamp;
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->timestamp = date('Y-m-d H:i:s');
    }
	public function otherIndex(){
		$title = ':: POSv2 :: - Others';
		return View::make('others.index', compact('title'));
	}
	
	public function getOtherIncome(){	
		$title = ':: POSv2 :: - Other Income';	
		$incomeReasons = array(
			'' => 'Please Select Income Reason') + DB::table('incomeexpensetype')
								->where('used_for', 1)
								->where('status', 1)
								->orderBy('type_name', 'asc')
								->lists('type_name', 'type_id');
		return View::make('others.addOtherIncome', compact('title', 'incomeReasons'));
	}
	/* public function autoIncomeSuggest(){	
		$term = Input::get('q');
		$search_incomes = DB::table('otherincomes')
				->where('income_reason', 'LIKE', '%'. $term .'%')
				->distinct()
				->get(array('income_reason'));
		//Goup by version.
		foreach($search_incomes as $income):
			echo  "$income->income_reason\n";
		endforeach;
	}  */
	public function saveOtherIncome(){		
		try{
			$data = Input::all();
			//echo '<pre>'; print_r($data);exit;
			$rules = array(
				'income_type_id' => 'required',
				'date'			=> 'required',
				'amount'		=> 'required|number_dot'
			);
			$validator = Validator::make($data, $rules);
			if($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			$otherIncome = array(
					'income_type_id' 	=>  Input::get('income_type_id'),
					'amount' 			=>  Input::get('amount'),
					'comment' 			=>  Input::get('comment'),
					'date' 				=>  Input::get('date'),
					'created_by' 		=>  Session::get('emp_id'),
					'created_at' 		=>  $this->timestamp
			);
			$insert = DB::table('otherincomes')->insert($otherIncome);
			if($insert) {
				return Redirect::to('admin/otherIncome')->with('message', 'Added Successfully');
			}
			return Redirect::to('admin/otherIncome')->with('errorMsg', 'Something must be wrong! Please check');
		 } catch(\Exception $e){
			Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Redirect::to('admin/otherIncome')->with('errorMsg', $err_msg)->withInput();
		} 
	}
			
    public function getIncomeByDatable() 	{        
        return Datatable::query(DB::table('otherincomes as oInc')	
				->select('oInc.other_income_id', 'incExpT.type_name', 'oInc.amount', 'oInc.comment', 'oInc.date')
				->leftJoin('incomeexpensetype as incExpT', 'incExpT.type_id', '=', 'oInc.income_type_id')
				->where('oInc.status', 1)
				->orderBy('oInc.created_at', 'DESC'))
				->showColumns('other_income_id', 'type_name', 'amount', 'comment', 'date')				
                ->addColumn('action', function($model) {
					$html = '<div class="span3">
							<a class="btn btn-primary btn-small" onclick="otherIncomeEdit('.$model->other_income_id.')" href="javascript:;"  role="button" data-toggle="modal" data-target="#editOtherIncomeModal"><i class="icon-edit"></i> Edit</a>
							<a class="btn btn-warning btn-small" href="javascript:;" title="Inactive" onclick="return deleteConfirm('.$model->other_income_id.')" id="'.$model->other_income_id.'"><i class="icon-remove"></i> Inactive</a>';
					return $html;	 
                })
                ->searchColumns('incExpT.type_name')
                ->setSearchWithAlias()
                ->orderColumns('oInc.other_income_id','incExpT.type_name')
                ->make(); 
    }
	
	public function getOtherIncomeForm($OtherIncomeId){
		$getIncome = DB::table('otherincomes')
				->where('other_income_id', $OtherIncomeId)
				->first();
		$incomeReasons = DB::table('incomeexpensetype')
								->where('used_for', 1)
								->where('status', 1)
								->orderBy('type_name', 'asc')
								->lists('type_name', 'type_id');
		return View::make('others.editOtherIncomeModal',compact('getIncome', 'incomeReasons'));
	}	
	
	public function editOtherIncomeSave(){
		try{
			$data = Input::all();
			$rules = array(
				'income_type_id' => 'required',
				'date'			=> 'required',
				'amount'		=> 'required|number_dot'
			);
			$validator = Validator::make($data, $rules);
			if($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			$OtherIncomeId = Input::get('other_income_id');
			$otheIncome_update = array(
					'income_type_id' 	=>  Input::get('income_type_id'),
					'amount' 			=>  Input::get('amount'),
					'comment' 			=>  Input::get('comment'),
					'date' 				=>  Input::get('date'),
					'updated_by' 		=>  Session::get('emp_id'),
					'updated_at' 		=>  $this->timestamp
			);
			DB::table('otherincomes')->where('other_income_id', $OtherIncomeId)->update($otheIncome_update);
					
			return Redirect::to('admin/otherIncome')->with('message', 'Updated Successfully');
		} catch(\Exception $e){
			Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg	= Lang::get("mysqlError.".$e->errorInfo[1]);
			return Redirect::to('admin/otherIncome')->with('errorMsg', $err_msg)->withInput();
		}
	}
	
	public function inactiveOtherIncome($OtherIncomeId){		
		$incomeRev = DB::table('otherincomes')			
			->where('other_income_id', $OtherIncomeId)
			->update(array('status' => 0));
		if($incomeRev){	
			return Response::json(['status' => 'success']);
		} 
		return Response::json(['status' => 'error']);
	}
	
  /*
   *  Other Expenses
  */
  
	public function getOtherExpense(){
		$title = ':: POSv2 :: - Other Expense';
		$expenseReasons = array(
			'' => 'Please Select Expense Reason') + DB::table('incomeexpensetype')
								->where('used_for', 2)
								->where('status', 1)
								->orderBy('type_name', 'asc')
								->lists('type_name', 'type_id');
		return View::make('others.addOtherExpense', compact('title', 'expenseReasons'));
	}
	/* public function autoExpenseSuggest(){	
		$term = Input::get('q');
		$search_expenses = DB::table('otherexpenses')
				->where('expense_reason', 'LIKE', '%'. $term .'%')				
				->distinct()
				->get(array('expense_reason'));
		foreach($search_expenses as $expense):
			echo  "$expense->expense_reason\n";
		endforeach;
	} */ 
	public function saveOtherExpense(){		
		try{
			$data = Input::all();
			//echo '<pre>'; print_r($data);exit;
			$rules = array(
				'expense_type_id' => 'required',
				'date'			=> 'required',
				'amount'		=> 'required|number_dot'
			);
			$validator = Validator::make($data, $rules);
			if($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			$otherExpense = array(
					'expense_type_id' 	=>  Input::get('expense_type_id'),
					'amount' 			=>  Input::get('amount'),
					'comment' 			=>  Input::get('comment'),
					'date' 				=>  Input::get('date'),
					'created_by' 		=>  Session::get('emp_id'),
					'created_at' 		=>  $this->timestamp
			);
			$insert = DB::table('otherexpenses')->insert($otherExpense);
			if($insert) {
				return Redirect::to('admin/otherExpense')->with('message', 'Added Successfully');
			}
			return Redirect::to('admin/otherExpense')->with('errorMsg', 'Something must be wrong! Please check');
		 } catch(\Exception $e){
			Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Redirect::to('admin/otherExpense')->with('errorMsg', $err_msg)->withInput();
		} 
	}
			
    public function getExpenseByDatable() 	{        
        return Datatable::query(DB::table('otherexpenses as oExp')
				->select('oExp.other_expense_id', 'incExpT.type_name', 'oExp.amount', 'oExp.comment', 'oExp.date')
				->leftJoin('incomeexpensetype as incExpT', 'incExpT.type_id', '=', 'oExp.expense_type_id')
				->where('oExp.status', 1)
				->orderBy('oExp.created_at', 'DESC'))
				->showColumns('other_expense_id', 'type_name', 'amount', 'comment', 'date')				
                ->addColumn('action', function($model) {
					$html = '
							<a class="btn btn-primary btn-small" onclick="otherExpenseEdit('.$model->other_expense_id.')" href="javascript:;"  role="button" data-toggle="modal" data-target="#editOtherExpenseModal"><i class="icon-edit"></i> Edit</a>
							<a class="btn btn-warning btn-small" href="javascript:;" title="Inactive" onclick="return deleteConfirm('.$model->other_expense_id.')" id="'.$model->other_expense_id.'"><i class="icon-remove"></i> Inactive</a>';
					return $html;	 
                })
                ->searchColumns('incExpT.type_name')
                ->setSearchWithAlias()
                ->orderColumns('oExp.other_expense_id','incExpT.type_name')
                ->make(); 
    }
	
	public function getOtherExpenseForm($OtherExpenseId){
		$getExpense = DB::table('otherexpenses')
				->where('other_expense_id', $OtherExpenseId)
				->first();
		$expenseReasons =  DB::table('incomeexpensetype')
								->where('used_for', 2)
								->where('status', 1)
								->orderBy('type_name', 'asc')
								->lists('type_name', 'type_id');
		return View::make('others.editOtherExpenseModal',compact('getExpense', 'expenseReasons'));
	}	
	
	public function editOtherExpenseSave(){
		try{
			$data = Input::all();
			$rules = array(
				'expense_type_id' => 'required',
				'date'			=> 'required',
				'amount'		=> 'required|number_dot'
			);
			$validator = Validator::make($data, $rules);
			if($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			$OtherExpenseId = Input::get('other_expense_id');
			$otheExpense_update = array(
					'expense_type_id' 	=>  Input::get('expense_type_id'),
					'amount' 			=>  Input::get('amount'),
					'comment' 			=>  Input::get('comment'),
					'date' 				=>  Input::get('date'),
					'updated_by' 		=>  Session::get('emp_id'),
					'updated_at' 		=>  $this->timestamp
			);
			DB::table('otherexpenses')->where('other_expense_id', $OtherExpenseId)->update($otheExpense_update);
					
			return Redirect::to('admin/otherExpense')->with('message', 'Updated Successfully');
		} catch(\Exception $e){
			Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg	= Lang::get("mysqlError.".$e->errorInfo[1]);
			return Redirect::to('admin/otherExpense')->with('errorMsg', $err_msg)->withInput();
		}
	}
	
	public function inactiveOtherExpense($OtherExpenseId){		
		$incomeRev = DB::table('otherexpenses')			
			->where('other_expense_id', $OtherExpenseId)
			->update(array('status' => 0));
		if($incomeRev){	
			return Response::json(['status' => 'success']);
		} 
		return Response::json(['status' => 'error']);
	}
}