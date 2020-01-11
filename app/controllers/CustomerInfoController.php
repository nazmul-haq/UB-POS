<?php

class CustomerInfoController extends \BaseController {

    public $timestamp;
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->timestamp = date('Y-m-d H:i:s');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $customer_types = DB::table('customertypes')
							->where('status', 1)
							->get();
        $cus_type=array();
        foreach ($customer_types as $value)
            $cus_type[$value->cus_type_id]=$value->cus_type_name;

        return View::make('admin.customer.viewCustomer',compact('cus_type','customer_types'));
    }

    public function addCustomerType() {
        try {
//			$data = Input::all();
            $data = array(
                    'cus_type_name' 			=> Input::get('customerTypeName'),
                    'discount_percent' 			=> Input::get('discountPercent'),
                    'point_unit'                            => Input::get('takaForPerPoint'),
                    'created_at'                            => $this->timestamp,
                    'created_by'                            => Session::get('emp_id')
            );
            $validator = Validator::make($data, Customerinfo::$cus_rules);

            if($validator->fails()) {

                return Redirect::back()->withErrors($validator)->withInput();
            }
            $insert = DB::table('customertypes')->insert($data);

            if($insert) {
                return Redirect::to('admin/customers')->with('message', 'Added Customer Type Successfully');
            }
            return Redirect::to('admin/customers')->with('errorMsg', 'Something must be wrong! Please check');
        } catch(Exception $e) {
            return Redirect::to('admin/customers')->with('errorMsg', 'Duplicate entry found.')->withInput();
        }
    }

	public function cusTypeEdit($cus_type_id) {
		try{
			$data = Input::all();
			$validator = Validator::make($data, Customerinfo::$cusType_rules);
			if($validator->fails()) {
				return Response::json(['status' =>'Validation Error occurred']);
			}			
			$updateSql = DB::table('customertypes')			
				->where('cus_type_id', $cus_type_id)
				->update(array(
						'cus_type_name' 	=> Input::get('cus_type_name'),
						'discount_percent' 	=> Input::get('discount_percent'),
						'point_unit' 		=> Input::get('point_unit'),
						'updated_by' 		=> Session::get('emp_id'),
						'updated_at' 		=> $this->timestamp
					));
			if($updateSql){	
				return Response::json(['status' => 'success']);
			} 
			return Response::json(['status' => 'No operation occurred! Please Check.']);
		} catch(\Exception $e){
			Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Response::json(['status' => $err_msg]);
		}
	}
	
    public function deleteCusType($cus_type_id) {
        $categoryItemDelete = DB::table('customertypes')
                ->where('cus_type_id', $cus_type_id)
                ->update(array('status' => 0));
        if($categoryItemDelete) {
            return Response::json(['status' => 'success']);
        }
        return Response::json(['status' => 'error']);
    }


    public function getCustomerData() {
        return Datatable::query(DB::table('customerinfos')->where('status', 1))
                ->addColumn('national_id', function($model) {
                    $html='<a href="'.URL::to("admin/customer/transactionDetails/$model->cus_id").'">'.$model->national_id.'</a>';
                    return $html;
                })

                ->showColumns('full_name','user_name', 'mobile', 'due','created_at')

                ->addColumn('action', function($model) {
                    $html = '<a class="btn btn-success btn-small" href="'.URL::to("admin/customer/paymentCus/$model->cus_id").'">Payment</a>'.' | '.
                            '<a class="btn btn-primary btn-small" href="#" title="View" onclick="customerDetails('.$model->cus_id.')" data-toggle="modal" data-target="#viewCustomer"><i class="icon-zoom-in"></i></a>' .' | '.
                            '<a class="btn btn-info btn-small" title="Edit" href="#" onclick="updateCustomer('.$model->cus_id.')" data-toggle="modal" data-target="#editCustomer"><i class="icon-edit"></i></a>' .' | '.
                            '<a class="btn btn-warning btn-small" title="Inactive" href="#" onclick="return deleteConfirm('.$model->cus_id.')" id="'.$model->cus_id.'"><i class="icon-remove"></i></a>';
                    return $html;
                })

                ->searchColumns('national_id','full_name','user_name', 'mobile')
                ->setSearchWithAlias()
                ->orderColumns('national_id','full_name','due','created_at')
                ->make();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {

        $vdata = Input::all();

        try {
            $validator = Validator::make($vdata, Customerinfo::$customer_rules);

            if($validator->fails()) {

                return Redirect::back()->withErrors($validator)->withInput();
            }

            $cus_type_id 		= Input::get('cus_type_id');
            $full_name 			= Input::get('full_name');
            $user_name 			= Input::get('user_name');
            $mobile 			= Input::get('mobile');
            $email 				= Input::get('email');
            $national_id 		= Input::get('national_id');
            $permanent_address 	= Input::get('permanent_address');
            $present_address 	= Input::get('present_address');
            $created_at         = $this->timestamp;
            $created_by         = Session::get('emp_id');

            $insert = DB::select("insert into customerinfos (cus_card_id,cus_type_id,full_name,user_name,mobile,email,national_id,permanent_address,present_address,created_at,created_by) values
					  ( ifnull(1+(
						SELECT right(sale_inv.cus_card_id, 7) AS LAST7 FROM customerinfos as sale_inv
						order by LAST7 desc limit 1),'1000000'),'$cus_type_id','$full_name','$user_name','$mobile','$email','$national_id','$permanent_address','$present_address','$created_at','$created_by')");
            return Redirect::to('admin/customers')->with('message', 'Added Customer Successfully');
        } catch(Exception $e) {
            return $e;
            return Redirect::to('admin/customers')->with('errorMsg', 'Duplicated Occured');
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($cus_id) {
        $customer_info = DB::table('customerinfos')
                ->join('customertypes','customertypes.cus_type_id','=','customerinfos.cus_type_id')
                ->where('cus_id', $cus_id)->first();

        return View::make('admin.customer.viewCustomerModal',compact('customer_info'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($cus_id) {
        $customer_types = DB::table('customertypes')->get();
        $cus_type=array();
        foreach ($customer_types as $value)
            $cus_type[$value->cus_type_id]=$value->cus_type_name;

        $customer_info = DB::table('customerinfos')
                ->join('customertypes','customertypes.cus_type_id','=','customerinfos.cus_type_id')
                ->where('cus_id', $cus_id)->first();


        return View::make('admin.customer.editCustomerModal',compact('customer_info','cus_type'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update() {


        $vdata = Input::all();
//            echo '<pre>';
//            print_r($vdata);
//            exit;
        try {
            $validator = Validator::make($vdata, Customerinfo::$customer_rules);

            if($validator->fails()) {

                return Redirect::back()->withErrors($validator)->withInput();
            }


            $data = array(
                    'cus_id' 			=> Input::get('cus_id'),
                    'cus_type_id' 			=> Input::get('cus_type_id'),
                    'full_name' 			=> Input::get('full_name'),
                    'user_name' 			=> Input::get('user_name'),
                    'mobile' 			=> Input::get('mobile'),
                    'email' 			=> Input::get('email'),
                    'national_id' 			=> Input::get('national_id'),
                    'permanent_address' 		=> Input::get('permanent_address'),
                    'present_address' 		=> Input::get('present_address'),
                    'created_at'                    => $this->timestamp,
                    'created_by'                    =>  Session::get('emp_id')
            );

            $update=DB::table('customerinfos')
                    ->where('cus_id', $data['cus_id'])
                    ->update($data);


            if($update) {
                return Redirect::to('admin/customers')->with('message', 'Update Customer Successfully');
            }
            return Redirect::to('admin/customers')->with('errorMsg', 'Something must be wrong! Please check.');
        } catch(Exception $e) {

            return Redirect::to('admin/customers')->with('errorMsg', 'Duplicated Occured');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($cus_id) {
        $suppDelete = DB::table('customerinfos')
                ->where('cus_id', $cus_id)
                ->update(array('status' => 0, 'updated_by' => Session::get('emp_id'), 'updated_at' => $this->timestamp));
        if($suppDelete) {
            return Response::json(['status' => 'success']);
        }
        return Response::json(['status' => 'error']);
    }

    /*
	*	Customer Due Payment
    */
    public function paymentCus($cus_id) {
        $get_customer = DB::table('customerinfos')
                ->select('cus_id','full_name', 'due')
                ->where('cus_id', $cus_id)
                ->first();

        $payment_type = array(
                '' => 'Please Select Payment Type') + DB::table('paymenttypes')
                ->where('status', 1)
                ->orderBy('payment_type_name', 'asc')
                ->lists('payment_type_name', 'payment_type_id');

        $get_company_infos = DB::table('companyprofiles')
                ->select('company_name')
                ->where('install_complete', 1)
                ->where('company_id', 1)
                ->first();

        $pre_date 	= date('Y-m-d');
        $sub_date	= strtotime($pre_date. '-10 days');
        $cal_date	= date('Y-m-d', $sub_date);
        $from_date 	= empty(Input::get('from')) ? $cal_date : Input::get('from');
        $to 		= empty(Input::get('to')) ? $pre_date : Input::get('to');
        $display_date = $from_date.' '.$to;
        $date_exp	= explode(' ', $display_date);
        //echo $date_exp[1];

        $get_transaction_infos = DB::table('cusduepayments AS cusDuePay')
                ->select('cusDuePay.date as pay_date','cusDuePay.amount','empInfo.user_name','payType.payment_type_name')
                ->join('paymenttypes AS payType', 'cusDuePay.payment_type_id', '=', 'payType.payment_type_id')
                ->join('empinfos AS empInfo', 'cusDuePay.created_by', '=', 'empInfo.emp_id')
                ->where('cusDuePay.cus_id', $cus_id)
                ->whereBetween('cusDuePay.date', array($from_date, $to))
                ->where('cusDuePay.status', 1)
                ->orderBy('cusDuePay.c_due_payment_id', 'DESC')
                ->paginate(25);

        return View::make('admin.customer.paymentCustomer',compact('get_customer', 'payment_type', 'get_company_infos', 'get_transaction_infos', 'date_exp'));
    }

    public function paymentSaveCustomer() {
        DB::beginTransaction();
        try {
            $data = Input::all();
            //print_r($data); exit;
            $validator = Validator::make($data, Customerinfo::$rules_payment);
            if($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $cus_id = Input::get('cus_id');
            $get_cus_due =  DB::table('customerinfos')
                    ->select('due')
                    ->where('cus_id', $cus_id)
                    ->first();
            $due = $get_cus_due->due;
            $cal_due = $due - Input::get('amount');
            $cus_due_update = array(
                    'due' 			=>  $cal_due,
                    'updated_by' 	=>  Session::get('emp_id'),
                    'updated_at' 	=>  $this->timestamp
            );
            DB::table('customerinfos')
                    ->where('cus_id', $cus_id)
                    ->update($cus_due_update);

            $payment_customer = array(
                    'cus_id' 			=>  $cus_id,
                    'payment_type_id' 	=>  Input::get('payment_type_id'),
                    'amount'			=>  Input::get('amount'),
                    'date'				=>  date('Y-m-d'),
                    'created_by' 		=>  Session::get('emp_id'),
                    'created_at' 		=>  $this->timestamp
            );
            $insert = DB::table('cusduepayments')->insert($payment_customer);
            DB::commit();
            return Redirect::to('admin/customer/paymentCus/'.$cus_id.'')->with('message', 'Added Successfully');

        } catch(\Exception $e) {
            DB::rollback();
            Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError.".$e->errorInfo[1]);
            return Redirect::to('admin/customer/paymentCus/'.$cus_id.'')->with('errorMsg', $err_msg)->withInput();
        }
    }

    function transactionDetails($cus_id) {
        $get_customer = DB::table('customerinfos')
                ->select('customerinfos.*','cusType.cus_type_name')
                ->join('customertypes AS cusType', 'cusType.cus_type_id', '=', 'customerinfos.cus_type_id')
                ->where('cus_id', $cus_id)
                ->first();
        $calculate1 = DB::select(DB::raw("
		select (select  sum(salereturninvoices.amount-salereturninvoices.less_amount) from salereturninvoices where salereturninvoices.cus_id='$cus_id')as return_amount
,sum(saleinvoices.amount)as total_purchase,sum(saleinvoices.pay) as total_paid,sum(saleinvoices.due)as total_due, sum(saleinvoices.discount) as discount from saleinvoices where saleinvoices.cus_id='$cus_id' LIMIT 1
                "));
        $calculate=$calculate1[0];
        $pre_date 	= date('Y-m-d');
        $sub_date	= strtotime($pre_date. '-10 days');
        $cal_date	= date('Y-m-d', $sub_date);
        $from_date 	= empty(Input::get('from')) ? $cal_date : Input::get('from');
        $to 		= empty(Input::get('to')) ? $pre_date : Input::get('to');
        $display_date = $from_date.' '.$to;
        $date_exp	= explode(' ', $display_date);
        $report_type=Input::get('report_type');
        if($report_type==2) {
            $reports= DB::table('pointincreasingrecords')
                    ->leftjoin('saleinvoices', 'saleinvoices.sale_invoice_id', '=', 'pointincreasingrecords.sale_invoice_id')
                    ->leftjoin('customerinfos', 'customerinfos.cus_id', '=', 'pointincreasingrecords.cus_id')
                    ->where('pointincreasingrecords.cus_id', '=', $cus_id)
                    ->where('pointincreasingrecords.status', '=', 1)
                    ->whereBetween('saleinvoices.date', array($from_date, $to))
                    ->get();
        }
        elseif($report_type==1) {
            $reports= DB::table('pointusingrecords')
                    ->leftjoin('saleinvoices', 'saleinvoices.sale_invoice_id', '=', 'pointusingrecords.sale_invoice_id')
                    ->leftjoin('customerinfos', 'customerinfos.cus_id', '=', 'pointusingrecords.cus_id')
                    ->where('pointusingrecords.cus_id', '=', $cus_id)
                    ->where('pointusingrecords.status', '=', 1)
                    ->whereBetween('saleinvoices.date', array($from_date, $to))
                    ->get();
        }else {
            $reports= DB::table('saleinvoices')
                    ->leftjoin('empinfos', 'empinfos.emp_id', '=', 'saleinvoices.created_by')
                    ->leftjoin('customerinfos', 'customerinfos.cus_id', '=', 'saleinvoices.cus_id')
                    ->leftjoin('paymenttypes', 'paymenttypes.payment_type_id', '=', 'saleinvoices.payment_type_id')
                    ->select('saleinvoices.sale_invoice_id','saleinvoices.cus_id','customerinfos.user_name as customer_name','saleinvoices.payment_type_id','paymenttypes.payment_type_name','saleinvoices.discount','saleinvoices.point_use_taka','saleinvoices.amount','saleinvoices.pay','saleinvoices.due','saleinvoices.date','saleinvoices.status','empinfos.user_name as invoiced_employee','saleinvoices.created_at as invoiced_datetime')
                    ->where('saleinvoices.status', '=', 1)
                    ->where('saleinvoices.cus_id', '=', $cus_id)
                    ->whereBetween('saleinvoices.date', array($from_date, $to))
                    ->orderBy('saleinvoices.date', 'desc')
                    ->get();
        }
        $get_transaction_infos = DB::table('cusduepayments AS cusDuePay')
                ->select('cusDuePay.date as pay_date','cusDuePay.amount','empInfo.user_name','payType.payment_type_name')
                ->join('paymenttypes AS payType', 'cusDuePay.payment_type_id', '=', 'payType.payment_type_id')
                ->join('empinfos AS empInfo', 'cusDuePay.created_by', '=', 'empInfo.emp_id')
                ->where('cusDuePay.cus_id', $cus_id)
                ->whereBetween('cusDuePay.date', array($from_date, $to))
                ->where('cusDuePay.status', 1)
                ->orderBy('cusDuePay.c_due_payment_id', 'DESC')
                ->paginate(25);
        return View::make('admin.customer.transactionDetails',compact('get_customer','date_exp','get_transaction_infos','calculate','reports','report_type'));
    }

    public function getMembership() {



        $customer_types = DB::table('customertypes')->get();
        $cus_type=array();
        foreach ($customer_types as $value)
            $cus_type[$value->cus_type_id]=$value->cus_type_name;
        return View::make('admin.customer.getMembership',compact('cus_type','customer_types'));
    }

    public function getMemberShipData() {

        return   Datatable::query(DB::table('saleinvoices')
                ->leftjoin('customerinfos', 'customerinfos.cus_id', '=', 'saleinvoices.cus_id')
                ->select(DB::raw('customerinfos.cus_id,
                             customerinfos.cus_card_id,
                             customerinfos.full_name,
                             customerinfos.user_name,
                             customerinfos.mobile,
                             customerinfos.point,
                             customerinfos.due,
                             customerinfos.created_at,
                             sum(saleinvoices.amount) as purchaseAmount'))
                ->where('saleinvoices.status', 1)
                ->where('saleinvoices.cus_id', '!=', 0)
                ->where('customerinfos.cus_type_id', '=', 1)
                ->groupBy('saleinvoices.cus_id')
                ->having(DB::raw('sum(saleinvoices.amount)'), '>', 2000)
                )
                ->addColumn('cus_card_id', function($model) {
                    $html='<a href="'.URL::to("admin/customer/transactionDetails/$model->cus_id").'">'.$model->cus_card_id.'</a>';
                    return $html;
                })

                ->showColumns('full_name','user_name', 'mobile', 'purchaseAmount','point', 'due','created_at')

                ->addColumn('action', function($model) {
                    $customer_types = DB::table('customertypes')
                                        ->where('customertypes.cus_type_id', '!=', 1)
                                        ->get();
                    
                    $html = '<div class="btn-group">';
                    $html .= '<a  class="btn btn-info" href="javascript:;" data-toggle="dropdown">Membership Type</a>';
                    $html .= '<a  class="btn btn-warning dropdown-toggle" data-toggle="dropdown" href="javascript:;"><span class="caret"></span></a>';
                    $html .= '<ul class="dropdown-menu">';
                    foreach ($customer_types as $value) :
                    $html .= '<li><a  href="#" class="'.$model->cus_id.'" onclick="getMembership('.$model->cus_id. ','.$value->cus_type_id.')" role="button" data-toggle="modal"><i class="icon-plus-sign"></i>&nbsp; '.$value->cus_type_name.'</a></li>';
                    endforeach;
                    $html .= '</ul>';
                    $html .= '</div>';
                    return $html;
                })

                ->searchColumns('cus_card_id','full_name','user_name', 'mobile')
                ->setSearchWithAlias()
                ->orderColumns('cus_card_id','full_name','due','created_at')
                ->make();
    }

    public function confirmMembership($cus_id,$cus_type_id) {
//        echo $cus_id.'&nbsp;&nbsp;&nbsp;'.$cus_type_id;
        $getMembershipConfirm = DB::table('customerinfos')
                ->where('cus_id', $cus_id)
                ->update(array('cus_type_id' => $cus_type_id, 'updated_by' => Session::get('emp_id'), 'updated_at' => $this->timestamp));
        if($getMembershipConfirm) {
            return Response::json(['status' => 'success']);
        }
        return Response::json(['status' => 'error']);
    }




}
