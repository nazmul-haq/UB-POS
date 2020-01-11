<?php 

class BankDepositController extends  BaseController
{
	public $timestamp;

    public function __construct() 
    {
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->timestamp = date('Y-m-d H:i:s');
    }

	public function bankDeposit()
	{
		$data = Input::all();
		if (isset($data['from']) && isset($data['to'])) {
			$from = $data['from'];
			$to   = $data['to'];
		}else{
			$from = date('Y-m-d');
			$to   = date('Y-m-d');
		}
		$total_amount_debit = DB::table('bank_deposit')
			->where('type',1)
			->whereBetween('date',[$from,$to])
			->sum('amount');
		$total_amount_credit = DB::table('bank_deposit')
			->where('type',2)
			->whereBetween('date',[$from,$to])
			->sum('amount');
		$banks = [''=>' ']+DB::table('banks')->lists('name','id');
		return View::make('bank.voucher',compact('banks','from','to','total_amount_credit','total_amount_debit'));
	}

	public function getBankDeposit($from = null,$to = null)
	{
		return Datatable::query(DB::table('bank_deposit as bd')
			->join('banks','bd.bank_id','=','banks.id')
			->select('bd.*','banks.name')
			->whereBetween('date',[$from,$to])
			)
            ->showColumns('date', 'name','bank_branch_name', 'voucher_no')
            ->addColumn('amount_debit', function($model) {
            	if($model->type == 1){
            		return $model->amount;
            	}
            })
            ->addColumn('amount_credit', function($model) {
                if($model->type == 2){
            		return $model->amount;
            	}
            })
            ->addColumn('action', function($model) {
                $html = '<a class="btn btn-info btn-small" title="Edit" href="#" onclick="updateVoucher('.$model->id.')" data-toggle="modal" data-target="#editCustomer"><i class="icon-edit"></i></a>' .' | '.
                        '<a class="btn btn-warning btn-small" title="Delete" href="#" onclick="return deleteConfirm('.$model->id.')" id="'.$model->id.'"><i class="icon-remove"></i> Delete</a>';
                return $html;
            })
            ->searchColumns('name','bank_branch_name', 'voucher_no', 'date')
            ->setSearchWithAlias()
            ->orderColumns('name','bank_branch_name', 'voucher_no', 'date')
            ->make();
	}

	public function saveBankDeposit()
	{
		$data = Input::all();
		try{
			DB::table('bank_deposit')
				->insert([
					'bank_id' => $data['bank_id'],
					'bank_branch_name' => $data['bank_branch_name'],
					'voucher_no' => $data['voucher_no'],
					'date' => $data['date'],
					'type' => $data['type'],
					'amount' => $data['amount'],
					'created_by' => Session::get('emp_id'),
				]);
			return Redirect::to('admin/bankDeposit')->with('message', 'Voucher Insert Success');
		}catch(\Exception $e){
			return Redirect::to('admin/bankDeposit')->with('errorMsg', 'Voucher Insert Fail...!!')->withInput();
		}
	}

	public function bankDepositEdit($id)
	{
		$banks = [''=>' ']+DB::table('banks')->lists('name','id');
		$voucher = DB::table('bank_deposit as bd')
			->where('id',$id)
			->first();
		return View::make('bank.editVoucher', compact('banks','voucher'));
	}

	public function saveEditBankDeposit()
	{
		$data = Input::all();
		try{
			DB::table('bank_deposit')
				->where('id',$data['bank_deposit_id'])
				->update([
					'bank_id' => $data['bank_id'],
					'bank_branch_name' => $data['bank_branch_name'],
					'voucher_no' => $data['voucher_no'],
					'date' => $data['date'],
					'amount' => $data['amount'],
					'created_by' => Session::get('emp_id'),
				]);
			return Redirect::to('admin/bankDeposit')->with('message', 'Voucher Update Success');
		}catch(\Exception $e){
			return Redirect::to('admin/bankDeposit')->with('errorMsg', 'Voucher Update Fail...!!')->withInput();
		}
	}

	public function bankDepositDelete($id)
	{
		try{
			DB::table('bank_deposit')
				->where('id',$id)
				->delete();
			return Redirect::to('admin/bankDeposit')->with('message', 'Voucher Delete Success');
		}catch(\Exception $e){
			return Redirect::to('admin/bankDeposit')->with('errorMsg', 'Voucher Delete Fail...!!')->withInput();
		}
	}

}