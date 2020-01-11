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
            // print "<pre>";
            // $cusDue = Helper::customerDue();
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
//          $data = Input::all();
            $data = array(
                    'cus_type_name'     => Input::get('customerTypeName'),
                    'discount_percent'  => Input::get('discountPercent'),
                    'point_unit'        => Input::get('takaForPerPoint'),
                    'created_at'        => $this->timestamp,
                    'created_by'        => Session::get('emp_id')
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
                        'cus_type_name'     => Input::get('cus_type_name'),
                        'discount_percent'  => Input::get('discount_percent'),
                        'point_unit'        => Input::get('point_unit'),
                        'updated_by'        => Session::get('emp_id'),
                        'updated_at'        => $this->timestamp
                    ));
            if($updateSql){ 
                return Response::json(['status' => 'success']);
            }
            return Response::json(['status' => 'No operation occurred! Please Check.']);
        }catch(\Exception $e){
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
        return Datatable::query(DB::table('customerinfos')->where('status', 1)
            ->where('branch_id',Session::get('branch_id')))
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
            $cus_type_id        = Input::get('cus_type_id');
            $branch_id          = Session::get('branch_id');
            $full_name          = Input::get('full_name');
            $user_name = implode('_',explode(' ', strtolower($full_name)));
            $i=1;
            while($duplicate=DB::table('customerinfos')->where('user_name',$user_name)->first()){
                $user_name = $user_name.'_'.$i;
                $i++;
            }
            // $user_name          = Input::get('user_name');
            $mobile             = Input::get('mobile');
            $email              = '';
            $national_id = DB::table('customerinfos')
                ->max('national_id');
            $national_id++;
            $cus_card_id = DB::table('customerinfos')
                ->max('cus_card_id');
            $cus_card_id++;
            $permanent_address  = Input::get('permanent_address');
            $present_address    = Input::get('present_address');
            $created_at         = $this->timestamp;
            $created_by         = Session::get('emp_id');
            $insert = DB::select("insert into customerinfos (cus_card_id,cus_type_id,branch_id,full_name,user_name,mobile,email,national_id,permanent_address,present_address,created_at,created_by) values
                      ('$cus_card_id','$cus_type_id','$branch_id','$full_name','$user_name','$mobile','$email','$national_id','$permanent_address','$present_address','$created_at','$created_by')");
            return Redirect::to('admin/customers')->with('message', 'Added Customer Successfully');
        } catch(Exception $e) {
            return $e;
            return Redirect::to('admin/customers')->with('errorMsg', 'Duplicated Occured');
        }
    }
    public function rawCustAdd()
    {
        $array = $this->custArray();
        print "<pre>";
        // print_r($array);
        $cus_card_id_array = DB::table('customerinfos')
            ->select(DB::raw('MAX(cus_card_id) as cus_card_id'))
            ->first();
        $cus_card_id = $cus_card_id_array->cus_card_id + 1;
        foreach($array as $key => $value){
            $stdClass = new stdClass;
            $stdClass = (object) $value;
            // print_r($stdClass);
            $cus_card_id++;
            $full_name          = $stdClass->customer_name;
            $nameArray = explode(" ", $stdClass->customer_name);
            $user_name = '';
            for($j=0;$j<count($nameArray);$j++){
                $user_name .= strtolower($nameArray[$j]).'_';
            }
            $user_name = rtrim($user_name,'_');
            $mobile             = $stdClass->mobile;
            $present_address    = $stdClass->address;
            $shipping_address    = $stdClass->shipping_address;
            $insert = DB::table('customerinfos')
                ->insert([
                    'cus_card_id'       => $cus_card_id,
                    'branch_id'         => 1,
                    'cus_type_id'       => 1,
                    'full_name'         => $full_name,
                    'user_name'         => $user_name,
                    'mobile'            => $mobile,
                    'present_address'   => $present_address,
                    'shipping_address'  => $shipping_address,
                    'created_at'        => $this->timestamp,
                    'created_by'        => Session::get('emp_id')
                ]);
        }
        exit;
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
                    'cus_id'            => Input::get('cus_id'),
                    'cus_type_id'           => Input::get('cus_type_id'),
                    'full_name'             => Input::get('full_name'),
                    'user_name'             => Input::get('user_name'),
                    'mobile'            => Input::get('mobile'),
                    'email'             => Input::get('email'),
                    'national_id'           => Input::get('national_id'),
                    'permanent_address'         => Input::get('permanent_address'),
                    'present_address'       => Input::get('present_address'),
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
    *   Customer Due Payment
    */
    public function paymentCus($cus_id) {
        $get_customer = DB::table('customerinfos')
                ->select('cus_id','full_name', 'due')
                ->where('cus_id', $cus_id)
                ->first();
        $payment_type = DB::table('paymenttypes')
                ->where('status', 1)
                ->orderBy('payment_type_name', 'asc')
                ->lists('payment_type_name', 'payment_type_id');
        $get_company_infos = DB::table('companyprofiles')
                ->select('company_name')
                ->where('install_complete', 1)
                ->where('company_id', 1)
                ->first();
        $pre_date   = date('Y-m-d');
        $sub_date   = strtotime($pre_date. '-10 days');
        $cal_date   = date('Y-m-d', $sub_date);
        $from_date  = empty(Input::get('from')) ? $cal_date : Input::get('from');
        $to         = empty(Input::get('to')) ? $pre_date : Input::get('to');
        $display_date = $from_date.' '.$to;
        $date_exp   = explode(' ', $display_date);
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
            $cal_due = $due - (Input::get('amount') + Input::get('due_discount'));
            $cus_due_update = array(
                'due'           =>  $cal_due,
                'updated_by'    =>  Session::get('emp_id'),
                'updated_at'    =>  $this->timestamp
            );
            DB::table('customerinfos')
                ->where('cus_id', $cus_id)
                ->update($cus_due_update);
            $insert = 0;
            if(Input::get('amount') > 0){
                $payment_customer = array(
                    'cus_id'            =>  $cus_id,
                    'payment_type_id'   =>  Input::get('payment_type_id'),
                    'amount'            =>  Input::get('amount'),
                    'date'              =>  (Input::get('payment_date')) ? Input::get('payment_date') : date('Y-m-d'),
                    'created_by'        =>  Session::get('emp_id'),
                    'created_at'        =>  $this->timestamp
                );
                $insert = DB::table('cusduepayments')->insertGetId($payment_customer);
            }
            if(Input::get('due_discount') > 0){
                if($insert <= 0){
                    $payment_customer = array(
                        'cus_id'            =>  $cus_id,
                        'payment_type_id'   =>  Input::get('payment_type_id'),
                        'amount'            =>  0,
                        'date'              =>  (Input::get('payment_date')) ? Input::get('payment_date') : date('Y-m-d'),
                        'created_by'        =>  Session::get('emp_id'),
                        'created_at'        =>  $this->timestamp
                    );
                    $insert = DB::table('cusduepayments')->insertGetId($payment_customer);
                }
                $due_discount_customer = array(
                    'cus_id'            =>  $cus_id,
                    'c_due_payment_id'  =>  $insert,
                    'amount'            =>  Input::get('due_discount'),
                    'date'              =>  (Input::get('payment_date')) ? Input::get('payment_date') : date('Y-m-d'),
                    'created_by'        =>  Session::get('emp_id'),
                    'created_at'        =>  $this->timestamp
                );
                $insert = DB::table('cusduediscounts')->insert($due_discount_customer);
            }
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
        $pre_date   = date('Y-m-d');
        $sub_date   = strtotime($pre_date. '-10 days');
        $cal_date   = date('Y-m-d', $sub_date);
        $from_date  = empty(Input::get('from')) ? $cal_date : Input::get('from');
        $to         = empty(Input::get('to')) ? $pre_date : Input::get('to');
        $display_date = $from_date.' '.$to;
        $date_exp   = explode(' ', $display_date);
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
        return  Datatable::query(DB::table('saleinvoices')
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
    public function custArray()
    {
        return array(
            // 0 => array('customer_name' => 'Retail Customer', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1 => array('customer_name' => 'Dalim Traders', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 2 => array('customer_name' => 'Kamal Vai', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 3 => array('customer_name' => 'Sobuj Vai', 'address' => 'CHakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 4 => array('customer_name' => 'Mofiz Mia', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 5 => array('customer_name' => 'Red Rose', 'address' => 'Savar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 6 => array('customer_name' => 'Marfat Ullah', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 7 => array('customer_name' => 'Bismillah Trading', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 8 => array('customer_name' => 'Rabbani Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 9 => array('customer_name' => 'D U', 'address' => 'Student', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 10 => array('customer_name' => 'Razzak Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 11 => array('customer_name' => 'Kodom Rasul', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 12 => array('customer_name' => 'Nirvana Trading', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 13 => array('customer_name' => 'Rally Brothers', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 14 => array('customer_name' => 'Rubel', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 15 => array('customer_name' => 'Belal Vai', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 16 => array('customer_name' => 'Sadia Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 17 => array('customer_name' => 'Hossain', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 18 => array('customer_name' => 'Iqbal Vai', 'address' => 'Khilgaon', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 19 => array('customer_name' => 'Mamun Vai', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 20 => array('customer_name' => 'Noor Uddin Vai', 'address' => 'Khilgaon', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 21 => array('customer_name' => 'Monir Vai', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 22 => array('customer_name' => 'Gift Lane (Jia Vai)', 'address' => 'Khilgaon', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 23 => array('customer_name' => 'M R Trading', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 24 => array('customer_name' => 'Khaja Beuty Concept', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 25 => array('customer_name' => 'Ammar Plaza', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 26 => array('customer_name' => 'Rubel Brothers', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 27 => array('customer_name' => 'Star Library', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 28 => array('customer_name' => 'Badsha Store', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 29 => array('customer_name' => 'Fair Price', 'address' => 'Khilgaon', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 30 => array('customer_name' => 'Zinnah Enterprize', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 31 => array('customer_name' => 'Rajdhani Cosmetics', 'address' => 'Sanir Akhra', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 32 => array('customer_name' => 'Razib Vai', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 33 => array('customer_name' => 'Arif Vai', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 34 => array('customer_name' => 'Shiblu Vai', 'address' => 'Gulshan-01', 'mobile' => '01732537745', 'shipping_address' => 'NULL'),
            // 35 => array('customer_name' => 'Chaity Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 36 => array('customer_name' => 'Delwar Vai', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 37 => array('customer_name' => 'Nabila Trade', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 38 => array('customer_name' => 'Eashana Cosmetics', 'address' => 'Narshingdi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 39 => array('customer_name' => 'Abul Mia', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 40 => array('customer_name' => 'Razib', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 41 => array('customer_name' => 'Shohag Enterprise', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 42 => array('customer_name' => 'Shawon Vai', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 43 => array('customer_name' => 'Progati Store', 'address' => 'Chittagong', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 44 => array('customer_name' => 'Bichitra', 'address' => 'Gawchia', 'mobile' => '01718752885', 'shipping_address' => 'NULL'),
            // 45 => array('customer_name' => 'Howlader Trading', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 46 => array('customer_name' => 'Sajhoni', 'address' => 'Mymansingh', 'mobile' => '01913323600', 'shipping_address' => 'NULL'),
            // 47 => array('customer_name' => 'Faruk & Brothers', 'address' => 'New Market', 'mobile' => '01717343620', 'shipping_address' => 'NULL'),
            // 48 => array('customer_name' => 'Selim', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 49 => array('customer_name' => 'Laboni Super Shop', 'address' => 'Elephant Road', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 50 => array('customer_name' => 'Afsar & Brothers', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 51 => array('customer_name' => 'Shahin Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 52 => array('customer_name' => 'Tareq Enterprize', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 53 => array('customer_name' => 'Uttara Saj Bitan', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 54 => array('customer_name' => 'Nasim Vai', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 55 => array('customer_name' => 'Chadpur Store', 'address' => 'Chandpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 56 => array('customer_name' => 'Family Center', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 57 => array('customer_name' => 'BP Store', 'address' => 'Jessore', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 58 => array('customer_name' => 'Sarfa', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 59 => array('customer_name' => 'Jordana', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 60 => array('customer_name' => 'Amity', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 61 => array('customer_name' => 'Abul Khayer Group', 'address' => 'Green Road', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 62 => array('customer_name' => 'Orient Snakes', 'address' => 'Jessore', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 63 => array('customer_name' => 'Sumon', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 64 => array('customer_name' => 'Alamgir', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 65 => array('customer_name' => 'Rupmahal', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 66 => array('customer_name' => 'M/S Fazlu Cor. Under Ground', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 67 => array('customer_name' => 'Aisarja', 'address' => 'Kakrail', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 68 => array('customer_name' => 'Nahar Enterprise', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 69 => array('customer_name' => 'Jannat Trading', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 70 => array('customer_name' => 'Nasiba', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 71 => array('customer_name' => 'J S Trading', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 72 => array('customer_name' => 'Prince Bazar Mirpur', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 73 => array('customer_name' => 'Lovely Cosmetics', 'address' => 'Nardda', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 74 => array('customer_name' => 'Anabia', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 75 => array('customer_name' => 'Baby Fair', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 76 => array('customer_name' => 'Almas Super Shop', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 77 => array('customer_name' => 'Shaibal', 'address' => 'Dinajpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 78 => array('customer_name' => 'Ma Cosmetics', 'address' => 'Gandaria', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 79 => array('customer_name' => 'Micro Shine', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 80 => array('customer_name' => 'Jonys Collection', 'address' => 'Khulna', 'mobile' => '01922864707', 'shipping_address' => 'NULL'),
            // 81 => array('customer_name' => 'Salauddin', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 82 => array('customer_name' => 'Famous', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 83 => array('customer_name' => 'Forse BC', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 84 => array('customer_name' => 'Korean Cosmetics', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 85 => array('customer_name' => 'Al Madina', 'address' => 'Tikatuli', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 86 => array('customer_name' => 'Sahjadi Cos.', 'address' => 'Gazipur', 'mobile' => '01924850084', 'shipping_address' => 'NULL'),
            // 87 => array('customer_name' => 'Nehar Store', 'address' => 'Rajshahi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 88 => array('customer_name' => 'Shuvorna Enterprise', 'address' => 'Kachukhet', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 89 => array('customer_name' => 'Mithila Enterprise', 'address' => 'Gulshan-01', 'mobile' => '01726178431', 'shipping_address' => 'NULL'),
            // 90 => array('customer_name' => 'Kazi Store', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 91 => array('customer_name' => 'Allardan Trading', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 92 => array('customer_name' => 'Ring Stone', 'address' => 'B,Baria', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 93 => array('customer_name' => 'Hasan Drugs', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 94 => array('customer_name' => 'Pria Cos.', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 95 => array('customer_name' => 'Kabir Cos.', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 96 => array('customer_name' => 'Arif & Brothers', 'address' => 'R Bazar', 'mobile' => '01817700517', 'shipping_address' => 'NULL'),
            // 97 => array('customer_name' => 'SS Collection', 'address' => 'Tongi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 98 => array('customer_name' => 'Masud Store', 'address' => 'Sayedpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 99 => array('customer_name' => 'Emarat', 'address' => 'R Bazar', 'mobile' => '01819830613', 'shipping_address' => 'NULL'),
            // 100 => array('customer_name' => 'Mamun Store', 'address' => 'Rajshahi', 'mobile' => '01929231940', 'shipping_address' => 'NULL'),
            // 101 => array('customer_name' => 'Matching Corner', 'address' => 'Eastern Plus, Shantinagar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 102 => array('customer_name' => 'Pink Ladies', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 103 => array('customer_name' => 'Ananna', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 104 => array('customer_name' => 'Raju Parlur', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 105 => array('customer_name' => 'Anjan', 'address' => 'Eastern Plaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 106 => array('customer_name' => 'Razib & Brothers', 'address' => 'Narsingdi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 107 => array('customer_name' => 'Tanha BC', 'address' => 'Noakhali', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 108 => array('customer_name' => 'Bismillah Trade', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 109 => array('customer_name' => 'Sweet Int.', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 110 => array('customer_name' => 'Rubel Enterprise', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 111 => array('customer_name' => 'Hossain Store', 'address' => 'R Bazar', 'mobile' => '01716392278', 'shipping_address' => 'NULL'),
            // 112 => array('customer_name' => 'Harun & Brothers', 'address' => 'R Bazar', 'mobile' => '01817770457', 'shipping_address' => 'NULL'),
            // 113 => array('customer_name' => 'Kamal Store', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 114 => array('customer_name' => 'Liakat Brothers', 'address' => 'Chittagong', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 115 => array('customer_name' => 'FR Enterprise', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 116 => array('customer_name' => 'Mitaly Store', 'address' => 'Khulna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 117 => array('customer_name' => 'Green Bond', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 118 => array('customer_name' => 'Park Avenue', 'address' => 'Narsighdi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 119 => array('customer_name' => 'Shafin Enterprize', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 120 => array('customer_name' => 'Alam Store', 'address' => 'Kaptan Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 121 => array('customer_name' => 'Ujjal & Brothers', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 122 => array('customer_name' => 'Mahi Enterprise', 'address' => 'R Bazar', 'mobile' => '01819334227', 'shipping_address' => 'NULL'),
            // 123 => array('customer_name' => 'Makka Traders', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 124 => array('customer_name' => 'Khomini', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 125 => array('customer_name' => 'Alifa Enterprise', 'address' => 'R Bazar', 'mobile' => '01830181011', 'shipping_address' => 'NULL'),
            // 126 => array('customer_name' => 'Rasel Vai', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 127 => array('customer_name' => 'Alauddin Store', 'address' => 'Sirajganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 128 => array('customer_name' => 'Nitto Ponno', 'address' => 'Lalbagh', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 129 => array('customer_name' => 'Al Mamun', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 130 => array('customer_name' => 'Sayed Enterprise', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 131 => array('customer_name' => 'Taj COs.', 'address' => 'Bagura', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 132 => array('customer_name' => 'Brothers Tech.', 'address' => 'Dhaka', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 133 => array('customer_name' => 'Kerani Ganj Super Shop', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 134 => array('customer_name' => 'M K Enterprise', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 135 => array('customer_name' => 'Tajul', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 136 => array('customer_name' => 'Sanam Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 137 => array('customer_name' => 'Rohan Enterprise', 'address' => 'Faridpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 138 => array('customer_name' => 'Shujon', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 139 => array('customer_name' => 'B Baria Store', 'address' => 'B Baria', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 140 => array('customer_name' => 'Karim & Brothers', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 141 => array('customer_name' => 'Bandhar Gift', 'address' => 'Sonir AKhra', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 142 => array('customer_name' => 'BY Store', 'address' => 'Farmgate', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 143 => array('customer_name' => 'Jony Vai', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 144 => array('customer_name' => 'Shuveccha Cos.', 'address' => 'Jessor', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 145 => array('customer_name' => 'TBC', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 146 => array('customer_name' => 'J S', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 147 => array('customer_name' => 'Shuvroto Cos.', 'address' => 'Faridpur', 'mobile' => '01913973815', 'shipping_address' => 'NULL'),
            // 148 => array('customer_name' => 'S A', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 149 => array('customer_name' => 'Shahid Vai', 'address' => 'CHakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 150 => array('customer_name' => 'Green City', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 151 => array('customer_name' => 'Baby Club', 'address' => 'New Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 152 => array('customer_name' => 'Samiya Enterprise', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 153 => array('customer_name' => 'Polton', 'address' => 'Polton', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 154 => array('customer_name' => 'Rijya Gallary', 'address' => 'Bashundhara CIty', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 155 => array('customer_name' => 'Babu Mia', 'address' => 'Lalbagh', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 156 => array('customer_name' => 'S S Enterprise', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 157 => array('customer_name' => 'Tanmoy Enterprise', 'address' => 'Pabna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 158 => array('customer_name' => 'Shakil & Brothers', 'address' => 'Midford', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 159 => array('customer_name' => 'Moushumi Store', 'address' => 'Bogura', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 160 => array('customer_name' => 'Apurbo', 'address' => 'Savar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 161 => array('customer_name' => 'Obaydul vai (Esmam Marketing)', 'address' => 'Chakbazar', 'mobile' => '01913893178', 'shipping_address' => 'NULL'),
            // 162 => array('customer_name' => 'Julhas Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 163 => array('customer_name' => 'Liton Cosmetics', 'address' => 'Sirajgong', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 164 => array('customer_name' => 'B B C', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 165 => array('customer_name' => 'Warisha Trading', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 166 => array('customer_name' => 'M A Malek', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 167 => array('customer_name' => 'Alamgir Gulshan', 'address' => 'Gulshan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 168 => array('customer_name' => 'Sonya Cor.', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 169 => array('customer_name' => 'J K Store', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 170 => array('customer_name' => 'Farid Vai', 'address' => 'New Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 171 => array('customer_name' => 'Janata Ent.', 'address' => 'Gulistan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 172 => array('customer_name' => 'Anna Plaza', 'address' => 'Gulshan-01', 'mobile' => '01934215737', 'shipping_address' => 'NULL'),
            // 173 => array('customer_name' => 'M S Trading', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 174 => array('customer_name' => 'Kayes Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 175 => array('customer_name' => 'Karim Store', 'address' => 'Gulistan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 176 => array('customer_name' => 'Mostafa', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 177 => array('customer_name' => 'P N C', 'address' => 'Laxmi Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 178 => array('customer_name' => 'Jahangir', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 179 => array('customer_name' => 'Nobrupa', 'address' => 'Rayer Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 180 => array('customer_name' => 'Sharif Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 181 => array('customer_name' => 'Mukta Cosmetics', 'address' => 'Chittagong', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 182 => array('customer_name' => 'Trust Family', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 183 => array('customer_name' => 'Faruk Vai', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 184 => array('customer_name' => 'A R Cosmetics', 'address' => 'Chakbazar', 'mobile' => '01812255992', 'shipping_address' => 'NULL'),
            // 185 => array('customer_name' => 'Bushra Enterprise', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 186 => array('customer_name' => 'Ripon', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 187 => array('customer_name' => 'Chanchal', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 188 => array('customer_name' => 'Raz', 'address' => 'Shamoli', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 189 => array('customer_name' => 'Mukti Store', 'address' => 'Jatrabari', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 190 => array('customer_name' => 'Safayad Enterprise', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 191 => array('customer_name' => 'Chandan Beuty Concept', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 192 => array('customer_name' => 'Torikul Islam', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 193 => array('customer_name' => 'Momota Cos.', 'address' => 'Eastern Mollika', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 194 => array('customer_name' => 'Raju Enterprise', 'address' => 'Chakbazar', 'mobile' => '01727389503', 'shipping_address' => 'NULL'),
            // 195 => array('customer_name' => 'MST', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 196 => array('customer_name' => 'Apu', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 197 => array('customer_name' => 'Nirala', 'address' => 'Savar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 198 => array('customer_name' => 'Safa Enterprise', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 199 => array('customer_name' => 'Insan Vai', 'address' => 'Midford', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 200 => array('customer_name' => 'S R Enterprise', 'address' => 'New Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 201 => array('customer_name' => 'Milton Cosmetics', 'address' => 'Gopalpur, Tangail', 'mobile' => '01714633384', 'shipping_address' => 'NULL'),
            // 202 => array('customer_name' => 'Shuvo', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 203 => array('customer_name' => 'Icon', 'address' => 'Savar', 'mobile' => '01684247459', 'shipping_address' => 'NULL'),
            // 204 => array('customer_name' => 'Suift Enterprise', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 205 => array('customer_name' => 'Tanha Traders', 'address' => 'DCC, Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 206 => array('customer_name' => 'D S Cosmetics', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 207 => array('customer_name' => 'Sharif Cosmetics', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 208 => array('customer_name' => 'Safely', 'address' => 'Basundhara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 209 => array('customer_name' => 'Khayrul', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 210 => array('customer_name' => 'Mosarof', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 211 => array('customer_name' => 'Mahfuz', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 212 => array('customer_name' => 'Habib Vai', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 213 => array('customer_name' => 'Hasmat Enterprise', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 214 => array('customer_name' => 'Ahmed Trading', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 215 => array('customer_name' => 'Hair & Care Store', 'address' => 'Moghbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 216 => array('customer_name' => 'Rubel Chakbazar', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 217 => array('customer_name' => 'Best Buy', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 218 => array('customer_name' => 'Ziku Enterprise', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 219 => array('customer_name' => 'Khans Corporation', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 220 => array('customer_name' => 'Abu Bakar', 'address' => 'Razbari', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 221 => array('customer_name' => 'Nazim Store', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 222 => array('customer_name' => 'HM Trading', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 223 => array('customer_name' => 'Jubaer', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 224 => array('customer_name' => 'N Islam Store', 'address' => 'R Bazar', 'mobile' => '01815683060', 'shipping_address' => 'NULL'),
            // 225 => array('customer_name' => 'Popy Store', 'address' => 'Raypur', 'mobile' => '01711458594', 'shipping_address' => 'NULL'),
            // 226 => array('customer_name' => 'TS Enterprise', 'address' => 'B Baria', 'mobile' => '01688872144', 'shipping_address' => 'NULL'),
            // 227 => array('customer_name' => 'B Baria Store Khilgaon', 'address' => 'Khilgaon', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 228 => array('customer_name' => 'B T', 'address' => 'Farmgate', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 229 => array('customer_name' => 'Fancy', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 230 => array('customer_name' => 'Sumon Store', 'address' => 'Rajhsahi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 231 => array('customer_name' => 'Alim', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 232 => array('customer_name' => 'Ripon Gulshan', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 233 => array('customer_name' => 'Shamim', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 234 => array('customer_name' => 'Mayer doya Trading', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 235 => array('customer_name' => 'Jayma Store', 'address' => 'Kaptan Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 236 => array('customer_name' => 'Soyef', 'address' => 'Pirozpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 237 => array('customer_name' => 'Taj Cosmetics', 'address' => 'Bagura', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 238 => array('customer_name' => 'Jubaer Enterprise', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 239 => array('customer_name' => 'Ibrahim Trading', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 240 => array('customer_name' => 'Dubai Collection', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 241 => array('customer_name' => 'Suny', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 242 => array('customer_name' => 'Top Haat', 'address' => 'Shewrapara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 243 => array('customer_name' => 'Faysal', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 244 => array('customer_name' => 'Hossain Enterprise', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 245 => array('customer_name' => 'Mohammadia Cosmetics', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 246 => array('customer_name' => 'Smart Family', 'address' => 'Gazipur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 247 => array('customer_name' => 'Madina Matching Corner', 'address' => 'Khulna', 'mobile' => '01917099207', 'shipping_address' => 'NULL'),
            // 248 => array('customer_name' => 'Rahim Enterprise', 'address' => 'Jatrabari', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 249 => array('customer_name' => 'Abbas Enterprise', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 250 => array('customer_name' => 'Olympia Cosmetics', 'address' => 'Khilgaon', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 251 => array('customer_name' => 'Magic Touch', 'address' => 'Uttara', 'mobile' => '01988165850', 'shipping_address' => 'NULL'),
            // 252 => array('customer_name' => 'Enamul', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 253 => array('customer_name' => 'Ohab Enterprise', 'address' => 'R Bazar', 'mobile' => '01818825685', 'shipping_address' => 'NULL'),
            // 254 => array('customer_name' => 'Angona', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 255 => array('customer_name' => 'M/S Fazlu Cor. 1st Floor', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 256 => array('customer_name' => 'Rahat', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 257 => array('customer_name' => 'Sanjida Cosmetics', 'address' => 'New Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 258 => array('customer_name' => 'Kazi Store-2', 'address' => 'R Bazar', 'mobile' => '01815115298', 'shipping_address' => 'NULL'),
            // 259 => array('customer_name' => 'Sohag Traders', 'address' => 'Kaptan Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 260 => array('customer_name' => 'Masud 2', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 261 => array('customer_name' => 'S Alam', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 262 => array('customer_name' => 'J S B Baria', 'address' => 'B Baria', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 263 => array('customer_name' => 'Sumon & Brothers', 'address' => 'Rajshahi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 264 => array('customer_name' => 'Rahamat Brothers', 'address' => 'Gulshan-01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 265 => array('customer_name' => 'Jannat Cosmetics', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 266 => array('customer_name' => 'Paragon', 'address' => 'Gazipur', 'mobile' => '01716946487', 'shipping_address' => '12. Nwe Tangail : Mosharof Tower'),
            // 267 => array('customer_name' => 'Arif 2', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 268 => array('customer_name' => 'Thai Gift Gallary', 'address' => 'Twin Tower', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 269 => array('customer_name' => 'Treasured', 'address' => 'Mohammadpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 270 => array('customer_name' => 'Emon Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 271 => array('customer_name' => 'Al Madina Cos.', 'address' => 'Sirajgonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 272 => array('customer_name' => 'Putul Cosmetics', 'address' => 'Manikdee', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 273 => array('customer_name' => 'Shahin 2', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 274 => array('customer_name' => 'Dubai Plaza', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 275 => array('customer_name' => 'Shoronika Gen. Store', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 276 => array('customer_name' => 'Alam Store 2', 'address' => 'Bagura', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 277 => array('customer_name' => 'Apra Cosmetics', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 278 => array('customer_name' => 'Ladies Corner', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 279 => array('customer_name' => 'Samrat Shoes', 'address' => 'Jessore', 'mobile' => '01777144922', 'shipping_address' => 'NULL'),
            // 280 => array('customer_name' => 'Sumon Store 2', 'address' => 'CoxBazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 281 => array('customer_name' => 'Lotus Fashion', 'address' => 'Barishal', 'mobile' => '01849232696', 'shipping_address' => 'NULL'),
            // 282 => array('customer_name' => 'Cosmetics Gallery', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 283 => array('customer_name' => 'Lake City Bazar', 'address' => 'KishorGanj', 'mobile' => '01971435772', 'shipping_address' => 'Faruq Transport'),
            // 284 => array('customer_name' => 'Bhai Bhai Enterprise', 'address' => 'Kishorganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 285 => array('customer_name' => 'Isme Cosmetics', 'address' => 'Pabna', 'mobile' => '01712981336', 'shipping_address' => 'NULL'),
            // 286 => array('customer_name' => 'Binimoy Store', 'address' => 'Tangail', 'mobile' => '01734156669', 'shipping_address' => 'NULL'),
            // 287 => array('customer_name' => 'Quaz Store', 'address' => 'R Bazar', 'mobile' => '01824460463', 'shipping_address' => 'NULL'),
            // 288 => array('customer_name' => 'Fahim Cosmetics', 'address' => 'Narayanganj', 'mobile' => '01716242597', 'shipping_address' => 'NULL'),
            // 289 => array('customer_name' => 'Noor Trading', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 290 => array('customer_name' => 'A S R Trading', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 291 => array('customer_name' => 'Needs International', 'address' => 'West Kazipara', 'mobile' => '01676164110', 'shipping_address' => 'NULL'),
            // 292 => array('customer_name' => 'Prince Bazar Mohammadpur', 'address' => 'Mohammadpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 293 => array('customer_name' => 'Prince Bazar Pallabi', 'address' => 'Pallabi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 294 => array('customer_name' => 'Prince Bazar Shamoly', 'address' => 'Shamoly', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 295 => array('customer_name' => 'Decent Collection', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 296 => array('customer_name' => 'Masum', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 297 => array('customer_name' => 'Alfy', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 298 => array('customer_name' => 'Fatema Cosmetics', 'address' => 'Farmgate', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 299 => array('customer_name' => 'Apon', 'address' => 'Bashundhara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 300 => array('customer_name' => 'Md. Ali', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 301 => array('customer_name' => 'Rafiq', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 302 => array('customer_name' => 'Sayed', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 303 => array('customer_name' => 'Al Taufiq Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 304 => array('customer_name' => 'Kanchan', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 305 => array('customer_name' => 'Surma Collection', 'address' => 'Eastern Plus', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 306 => array('customer_name' => 'Minu Trading', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 307 => array('customer_name' => 'Saha Marketing', 'address' => 'Khulna', 'mobile' => '01725174344', 'shipping_address' => 'NULL'),
            // 308 => array('customer_name' => 'Rafa Super Shop', 'address' => 'Mirpur-2', 'mobile' => '01882061907', 'shipping_address' => 'NULL'),
            // 309 => array('customer_name' => 'Sah Amanat Enterprise', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 310 => array('customer_name' => 'Sah Sufi', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 311 => array('customer_name' => 'Anita Collection', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 312 => array('customer_name' => 'Azad Enterprise', 'address' => 'Barishal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 313 => array('customer_name' => 'SOSY', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 314 => array('customer_name' => 'Angel Baby', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 315 => array('customer_name' => 'Shaj Ghor', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 316 => array('customer_name' => 'Wintex', 'address' => 'Feni', 'mobile' => '01819360984', 'shipping_address' => 'NULL'),
            // 317 => array('customer_name' => 'Saiful', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 318 => array('customer_name' => 'Sarem Sindicate', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 319 => array('customer_name' => 'M K Trading', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 320 => array('customer_name' => 'Nahid Enterprise', 'address' => 'Begum Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 321 => array('customer_name' => 'Vai Vai Cosmetics', 'address' => 'Jinaidah', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 322 => array('customer_name' => 'Asif', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 323 => array('customer_name' => 'Muslim Traders', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 324 => array('customer_name' => 'Raj Store', 'address' => 'Shamoly', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 325 => array('customer_name' => 'Utsab', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 326 => array('customer_name' => 'Fashion Prosadhoni', 'address' => 'Jinaidah', 'mobile' => '01711398911', 'shipping_address' => 'NULL'),
            // 327 => array('customer_name' => 'Sabbir Enterprise', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 328 => array('customer_name' => 'Munia Cosmetics', 'address' => 'Kapasia, Gazipur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 329 => array('customer_name' => 'Rimi Enterprise', 'address' => 'Srinagar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 330 => array('customer_name' => 'Mahi Cosmetics', 'address' => 'Dhohar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 331 => array('customer_name' => 'Noyon Cosmetics', 'address' => 'Laxmipur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 332 => array('customer_name' => 'Chena Ochena', 'address' => 'Mirpur-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 333 => array('customer_name' => 'Stone Gallery', 'address' => 'Shantinagar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 334 => array('customer_name' => 'Cosmetics Corner', 'address' => 'Rajshahi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 335 => array('customer_name' => 'Royal Cosmetics', 'address' => 'Rajshahi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 336 => array('customer_name' => 'Aboni', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 337 => array('customer_name' => 'Nijhum Cosmetics', 'address' => 'Gazipur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 338 => array('customer_name' => 'Generator', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 339 => array('customer_name' => 'Somir', 'address' => 'DCC', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 340 => array('customer_name' => 'Jahid', 'address' => 'Dhanmondi', 'mobile' => '01720237218', 'shipping_address' => 'NULL'),
            // 341 => array('customer_name' => 'Shovona Cosmetics', 'address' => 'Dhanmondi-7', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 342 => array('customer_name' => 'Hassan', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 343 => array('customer_name' => 'Humayun Traders', 'address' => 'Jatrabari', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 344 => array('customer_name' => 'Hossain Traders', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 345 => array('customer_name' => 'Micro Mart', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 346 => array('customer_name' => 'Progati Enterprise', 'address' => 'Nayapaltan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 347 => array('customer_name' => 'SM Mart', 'address' => '24/b, Majid Saroni, Sonadanga, Khulna', 'mobile' => '01820545200', 'shipping_address' => 'NULL'),
            // 348 => array('customer_name' => 'Azim', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 349 => array('customer_name' => 'Tanvir', 'address' => 'Barishal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 350 => array('customer_name' => 'Antorango Prosadhoni', 'address' => 'Kushtia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 351 => array('customer_name' => 'Masum Cosmetics', 'address' => 'Savar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 352 => array('customer_name' => 'Neha Enterprise', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 353 => array('customer_name' => 'Jobaer', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 354 => array('customer_name' => 'Sahjalal Trading', 'address' => 'Sylhet', 'mobile' => '01712958147', 'shipping_address' => 'NULL'),
            // 355 => array('customer_name' => 'Janata Pharmacy', 'address' => 'Farmgate', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 356 => array('customer_name' => 'Babu Cosmetics', 'address' => 'Satkhira', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 357 => array('customer_name' => 'Royel Aroma', 'address' => 'Farmgate', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 358 => array('customer_name' => 'Arju Cosmetics', 'address' => 'Mohammadpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 359 => array('customer_name' => 'Milon Vai', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 360 => array('customer_name' => 'Neetu Fasion Enterprise', 'address' => 'Bachila', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 361 => array('customer_name' => 'Sharna', 'address' => 'Barguna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 362 => array('customer_name' => 'Rahim Beuty Concept', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 363 => array('customer_name' => 'Touch Beauty Concept', 'address' => 'R Bazar', 'mobile' => '01919132337', 'shipping_address' => 'NULL'),
            // 364 => array('customer_name' => 'Progati Store', 'address' => 'Sirajganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 365 => array('customer_name' => 'Dream Touch', 'address' => 'Savar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 366 => array('customer_name' => 'Sonali Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 367 => array('customer_name' => 'Rabbi Store', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 368 => array('customer_name' => 'Samsul Islam', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 369 => array('customer_name' => 'R K Collection', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 370 => array('customer_name' => 'Alam & Sons', 'address' => 'Sylhet', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 371 => array('customer_name' => 'Nancy', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 372 => array('customer_name' => 'Mim Collection', 'address' => 'Suvastu', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 373 => array('customer_name' => 'Hisham International', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 374 => array('customer_name' => 'Mahbub Trading', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 375 => array('customer_name' => 'Imran Trading', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 376 => array('customer_name' => 'Nazrul Islam', 'address' => 'mirpur 01', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 377 => array('customer_name' => 'Tarek', 'address' => 'Newmarket', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 378 => array('customer_name' => 'Shajeda Traders', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 379 => array('customer_name' => 'Modina Maching Corner', 'address' => 'Khulna Fulbari gate', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 380 => array('customer_name' => 'MB Trade Gift', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 381 => array('customer_name' => 'Almas General Store', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 382 => array('customer_name' => 'Rafiq Store', 'address' => 'Fulbari Gate, Khulna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 383 => array('customer_name' => 'Anwar Traders', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 384 => array('customer_name' => 'Moon Mahin', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 385 => array('customer_name' => 'Somahar', 'address' => 'Narinda', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 386 => array('customer_name' => 'Titu Vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 387 => array('customer_name' => 'Shughandha Dep. Store', 'address' => 'Kishorganj', 'mobile' => '01711664012', 'shipping_address' => 'Al-modina : swari ghat'),
            // 388 => array('customer_name' => 'Babul & Sons', 'address' => 'Gazipur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 389 => array('customer_name' => 'Farhana Pharmacy', 'address' => 'Mirpur Kazi Para', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 390 => array('customer_name' => 'Butto', 'address' => 'Rajshahi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 391 => array('customer_name' => 'Hasmat', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 392 => array('customer_name' => 'Sattar & Brothers', 'address' => 'Jessor', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 393 => array('customer_name' => 'Elias Medical Hall', 'address' => 'Mohammadpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 394 => array('customer_name' => 'Suma Cosmetic', 'address' => 'Saver City center', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 395 => array('customer_name' => 'Lal Mia', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 396 => array('customer_name' => 'Bismillah Store', 'address' => 'jassore/Nazmul vai', 'mobile' => '01719819076', 'shipping_address' => 'NULL'),
            // 397 => array('customer_name' => 'Mohabbot Store', 'address' => 'Bosundhara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 398 => array('customer_name' => 'Kalam', 'address' => 'New Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 399 => array('customer_name' => 'Sikder Trade', 'address' => 'Askona Dokhin Khna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 401 => array('customer_name' => 'Arab Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 402 => array('customer_name' => 'SL Trade International', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 403 => array('customer_name' => 'Joy', 'address' => 'Keraniganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 404 => array('customer_name' => 'Eshamam', 'address' => 'Tajmahal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 405 => array('customer_name' => 'Prio Cosmetics', 'address' => 'Narayanganj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 406 => array('customer_name' => 'Siam Cosmatic', 'address' => 'Bogura', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 407 => array('customer_name' => 'Farcy', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 408 => array('customer_name' => 'Mim Enterprise', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 409 => array('customer_name' => 'Jordana Karnafuly', 'address' => 'Kakrail', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 410 => array('customer_name' => 'Athoy Cosmetics', 'address' => 'Kustia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 411 => array('customer_name' => 'Mamun vai uttara', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 412 => array('customer_name' => 'Shorna Varieties Store', 'address' => 'Barguna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 413 => array('customer_name' => 'Arad Store', 'address' => 'Lakshmipur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 414 => array('customer_name' => 'Bikrompur Store', 'address' => 'Bikrompur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 415 => array('customer_name' => 'Mohananda', 'address' => 'ChapainababGanj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 416 => array('customer_name' => 'M Hossain', 'address' => 'Kustia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 417 => array('customer_name' => 'Sumon 3', 'address' => 'new market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 418 => array('customer_name' => 'Satorupa', 'address' => 'saver', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 419 => array('customer_name' => 'Anondo', 'address' => 'Midfort', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 420 => array('customer_name' => 'Karim Casmetic', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 421 => array('customer_name' => 'Jinnath Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 422 => array('customer_name' => 'Star City', 'address' => 'Khilgaon', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 423 => array('customer_name' => 'Emon Beauty Concept', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 424 => array('customer_name' => 'Robi Store', 'address' => 'Khulna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 425 => array('customer_name' => 'R K Cosmetics', 'address' => 'Konapara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 426 => array('customer_name' => 'Konica', 'address' => 'Jatrabari', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 427 => array('customer_name' => 'Robiul', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 428 => array('customer_name' => 'Yunus', 'address' => 'Khilkhet', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 429 => array('customer_name' => 'Tayba', 'address' => 'Gausea', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 430 => array('customer_name' => 'Sujon', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 431 => array('customer_name' => 'Shajadi Cosmetic', 'address' => 'Gazipur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 432 => array('customer_name' => 'Star One', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 433 => array('customer_name' => 'Rupashi', 'address' => 'New market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 434 => array('customer_name' => 'Alpi', 'address' => 'b.c gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 435 => array('customer_name' => 'Kings', 'address' => 'Newmarket', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 436 => array('customer_name' => 'Riyad Dokan', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 437 => array('customer_name' => 'Tuhin Vai', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 438 => array('customer_name' => 'Abir', 'address' => 'Midford', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 439 => array('customer_name' => 'Tacita', 'address' => 'Nwemarket', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 440 => array('customer_name' => 'Aayn', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 441 => array('customer_name' => 'Saiful Islam', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 442 => array('customer_name' => 'Mehedi Hassan', 'address' => 'SIBL', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 443 => array('customer_name' => 'Ghohona', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 444 => array('customer_name' => 'Emdad Store', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 445 => array('customer_name' => 'Momtaj', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 446 => array('customer_name' => 'Hammad Enterprise', 'address' => 'Coxbazar', 'mobile' => '01815141522', 'shipping_address' => 'NULL'),
            // 447 => array('customer_name' => 'Nasir Uddin Nannu', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 448 => array('customer_name' => 'Khaja Enterprise', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 449 => array('customer_name' => 'Arong Sweets', 'address' => 'Kodomtali', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 450 => array('customer_name' => 'Oishi Drug House', 'address' => 'Karani Gong (bikash barmon ) Proprietor', 'mobile' => '01720536694', 'shipping_address' => 'NULL'),
            // 451 => array('customer_name' => 'Wabes', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 452 => array('customer_name' => 'Ali Enterprise', 'address' => 'Khulna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 453 => array('customer_name' => 'VIP Cosmetic', 'address' => 'Daudkandi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 454 => array('customer_name' => 'Zakir Hossain', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 455 => array('customer_name' => 'Gift 2', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 456 => array('customer_name' => 'Kalam 2', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 457 => array('customer_name' => 'Sorkar Pharmacy', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 458 => array('customer_name' => 'Cameliya Trading', 'address' => 'City heart, Polton', 'mobile' => '01712776608', 'shipping_address' => 'NULL'),
            // 459 => array('customer_name' => 'Brothers Enterprise', 'address' => 'Mirpur 2', 'mobile' => '01920022982', 'shipping_address' => 'NULL'),
            // 460 => array('customer_name' => 'Best Quality', 'address' => 'Uttara', 'mobile' => '01926760162', 'shipping_address' => 'NULL'),
            // 461 => array('customer_name' => 'Mohoshin vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 462 => array('customer_name' => 'Saud Ahmed', 'address' => '', 'mobile' => '01975733467', 'shipping_address' => 'NULL'),
            // 463 => array('customer_name' => 'ICD', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 464 => array('customer_name' => 'Jashim vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 465 => array('customer_name' => 'Zaia vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 466 => array('customer_name' => 'Cosmetics Center', 'address' => 'R-bazar', 'mobile' => '01790338838', 'shipping_address' => 'NULL'),
            // 467 => array('customer_name' => 'Robin Beauty con.', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 468 => array('customer_name' => 'Razib Savar', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 469 => array('customer_name' => 'Kashem Store', 'address' => 'Fani', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 470 => array('customer_name' => 'Tanzila', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 471 => array('customer_name' => 'Sajoni', 'address' => 'Tangil', 'mobile' => '01712085858', 'shipping_address' => 'NULL'),
            // 472 => array('customer_name' => 'Faizan', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 473 => array('customer_name' => 'Suvo', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 474 => array('customer_name' => 'Bondhon Cosmatic', 'address' => 'Modonpur', 'mobile' => '01722534062', 'shipping_address' => 'NULL'),
            // 475 => array('customer_name' => 'Hanif Enterprise', 'address' => 'Chakbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 476 => array('customer_name' => 'Shafiq Vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 477 => array('customer_name' => 'Afra', 'address' => 'Gusia', 'mobile' => '01716909409', 'shipping_address' => 'NULL'),
            // 478 => array('customer_name' => 'Chowdhury Traders', 'address' => 'Narayanganj', 'mobile' => '01777322225', 'shipping_address' => 'NULL'),
            // 479 => array('customer_name' => 'Amanat Trading', 'address' => 'R- Bazar', 'mobile' => '01819648993', 'shipping_address' => 'NULL'),
            // 480 => array('customer_name' => 'Babu', 'address' => 'Kolabagan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 481 => array('customer_name' => 'Priya Beauty Con.', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 482 => array('customer_name' => 'MAMUN 4', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 483 => array('customer_name' => 'Mohini', 'address' => 'Barishal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 484 => array('customer_name' => 'Zumka Store', 'address' => 'Comilla', 'mobile' => '01727402535', 'shipping_address' => 'NULL'),
            // 485 => array('customer_name' => 'Azan Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 486 => array('customer_name' => 'Khan Enterprise', 'address' => 'Brahmanbaria', 'mobile' => '01715828282', 'shipping_address' => 'Nabinagar Brahmanbaria / Raza Ghat Zolil majhi'),
            // 487 => array('customer_name' => 'Prime Collection', 'address' => 'Simanto Squire Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 488 => array('customer_name' => 'Reza Lalbagh', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 489 => array('customer_name' => 'Fair Beauty', 'address' => 'NewMerket Khulna', 'mobile' => '01919497692', 'shipping_address' => 'Rainbow'),
            // 490 => array('customer_name' => 'Babul Store', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 491 => array('customer_name' => 'Mastafa Uttara', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 492 => array('customer_name' => 'FM Cosmetics', 'address' => 'Momotaz Plaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 493 => array('customer_name' => 'Monir Hossain', 'address' => 'Bikrampur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 494 => array('customer_name' => 'Maruf online shop', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 495 => array('customer_name' => 'Dighnity Int.', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 496 => array('customer_name' => 'Bismillah Trading', 'address' => 'CTG', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 497 => array('customer_name' => 'Rayhan', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 498 => array('customer_name' => 'shopno', 'address' => '270/B Tejgaon I/A 1208', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),

            // 500 => array('customer_name' => 'SSR Int', 'address' => 'Tongi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 501 => array('customer_name' => 'Monir 2', 'address' => 'DCC Market Gulshan -1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 502 => array('customer_name' => 'Fashion Jewelry & Cos.', 'address' => 'Comila', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 503 => array('customer_name' => 'Monir Hossain 2', 'address' => 'Bikrampur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 504 => array('customer_name' => 'Monerekho Saj Ghor', 'address' => 'Nowga', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 505 => array('customer_name' => 'Jannat Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 506 => array('customer_name' => 'Shohag 2', 'address' => 'Chawk Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 507 => array('customer_name' => 'Tonmoy Beauty Con.', 'address' => 'Badda', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 508 => array('customer_name' => 'Shojib', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 509 => array('customer_name' => 'Hira vhai', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 510 => array('customer_name' => 'mazada', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 511 => array('customer_name' => 'Salsabil', 'address' => 'Chawkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 512 => array('customer_name' => 'Atlast', 'address' => 'Ekram ullah Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 513 => array('customer_name' => 'Nirala Beauty Con.', 'address' => 'Saver', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 514 => array('customer_name' => 'Maya Cos.', 'address' => 'Jossore', 'mobile' => '01917668365', 'shipping_address' => 'NULL'),
            // 515 => array('customer_name' => 'Salam Store', 'address' => 'Rajshahi', 'mobile' => '01712678547', 'shipping_address' => 'NULL'),
            // 516 => array('customer_name' => 'Qohinoor', 'address' => 'Mulltiplan Center', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 517 => array('customer_name' => 'Belal 2', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 518 => array('customer_name' => 'Amin Trading', 'address' => 'Chawkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 519 => array('customer_name' => 'Nafiz Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 520 => array('customer_name' => 'Mahedi Hasan', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 521 => array('customer_name' => 'Raju Enterprise 2', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 522 => array('customer_name' => 'Rupmahal 2', 'address' => 'Bandura', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 523 => array('customer_name' => 'Rupali', 'address' => 'Kaptan Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 524 => array('customer_name' => 'Raj Beauty Con', 'address' => 'Tongi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 525 => array('customer_name' => 'Ankur', 'address' => 'Sankor Plaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 526 => array('customer_name' => 'Takwa', 'address' => 'Khan Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 527 => array('customer_name' => 'Sumona Traders', 'address' => 'Naraiangong', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 528 => array('customer_name' => 'Chowa Cos', 'address' => 'Goforgo', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 529 => array('customer_name' => 'A K Enterprise', 'address' => 'Munsur Khan Plaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 530 => array('customer_name' => 'Alamin', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 531 => array('customer_name' => 'Singapore Corner', 'address' => 'Konapara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 532 => array('customer_name' => 'Top Collection', 'address' => 'Eastern Plus', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 533 => array('customer_name' => 'olimpya', 'address' => 'Khilgaw', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 534 => array('customer_name' => 'Farhan Int.', 'address' => 'Jossore', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 535 => array('customer_name' => 'Sohath Cosmetic', 'address' => 'Ctg', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 536 => array('customer_name' => 'Almas 2', 'address' => 'Chawkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 537 => array('customer_name' => 'Choyonika', 'address' => '', 'mobile' => '01747370476', 'shipping_address' => 'NULL'),
            // 538 => array('customer_name' => 'Sayed Traders', 'address' => 'Dhanmondi', 'mobile' => '01726244888', 'shipping_address' => 'NULL'),
            // 539 => array('customer_name' => 'M C Trade', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 540 => array('customer_name' => 'ishmam International', 'address' => 'Chawkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 541 => array('customer_name' => 'NN Enterprise', 'address' => 'Narsingdi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 542 => array('customer_name' => 'Khaja Store', 'address' => 'Kolabagan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 543 => array('customer_name' => 'Sayma Cos.', 'address' => 'Kajpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 544 => array('customer_name' => 'Joshim vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 545 => array('customer_name' => 'Maa rexine', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 546 => array('customer_name' => 'KDM', 'address' => 'R- Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 547 => array('customer_name' => 'Protima Cosmetics', 'address' => 'Mirjapur, Taingail', 'mobile' => '01819191512', 'shipping_address' => 'NULL'),
            // 548 => array('customer_name' => 'Neaz Medicine Corner', 'address' => 'Nageswari', 'mobile' => '01717677640', 'shipping_address' => 'NULL'),
            // 549 => array('customer_name' => 'Cosmetics World', 'address' => 'Kakrile', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 550 => array('customer_name' => 'Sneha Collection', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 551 => array('customer_name' => 'Rabbi Int', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 552 => array('customer_name' => 'Fatema Chopping Center', 'address' => 'Jurain', 'mobile' => '01761792241', 'shipping_address' => 'NULL'),
            // 553 => array('customer_name' => 'Md. Mamun Hasan', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 554 => array('customer_name' => 'Jonopriya', 'address' => 'Jurine Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 555 => array('customer_name' => 'Lucky Beauty Con.', 'address' => 'F.Rahman AC market Fani', 'mobile' => '01818957680', 'shipping_address' => 'NULL'),
            // 556 => array('customer_name' => 'Lokman Vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 557 => array('customer_name' => 'Monia Cos.', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 558 => array('customer_name' => 'Hasan Cos.', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 559 => array('customer_name' => 'Al- Rafi', 'address' => 'Chadkhil', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 560 => array('customer_name' => 'Nolak', 'address' => 'Faramgat', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 561 => array('customer_name' => 'Bashir & Brothers', 'address' => 'Ekramullah Plaza', 'mobile' => '01670244341', 'shipping_address' => 'NULL'),
            // 562 => array('customer_name' => 'Moure', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 563 => array('customer_name' => 'Mahedi Collection', 'address' => 'Cantonment, Kochukheet', 'mobile' => '01768009002', 'shipping_address' => 'NULL'),
            // 564 => array('customer_name' => 'Shughandha Dep Store Exclusive', 'address' => 'Kishoregonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 565 => array('customer_name' => 'Alap Ghor', 'address' => 'Mirpur-2', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 566 => array('customer_name' => 'ICT Dhaka', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 567 => array('customer_name' => 'Priya Cosmetic', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 568 => array('customer_name' => 'Alauddin Vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 569 => array('customer_name' => 'Alamin 3', 'address' => 'Saver', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 570 => array('customer_name' => 'Mimi 2', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 571 => array('customer_name' => 'Baba Int.', 'address' => 'Kaptanbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 572 => array('customer_name' => 'Salim 2', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 573 => array('customer_name' => 'HK', 'address' => 'Chawk Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 574 => array('customer_name' => 'Ashian Sky Shop', 'address' => 'Eastarn Plaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 575 => array('customer_name' => 'Abdulla', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 576 => array('customer_name' => 'Shibnath', 'address' => 'chawkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 577 => array('customer_name' => 'Abid Store', 'address' => 'Dinajpur', 'mobile' => '01842542830', 'shipping_address' => 'NULL'),
            // 578 => array('customer_name' => 'Kana.kata', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 579 => array('customer_name' => 'Cozmerina', 'address' => 'Mohammadpur', 'mobile' => '01712399244', 'shipping_address' => 'NULL'),
            // 580 => array('customer_name' => 'Razu Bogora', 'address' => 'Bogora', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 581 => array('customer_name' => 'Haque General Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 582 => array('customer_name' => 'Sajahan', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 583 => array('customer_name' => 'ST Trade', 'address' => 'Faridpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 584 => array('customer_name' => 'Al Modina Munsigonj', 'address' => 'Munsigonj', 'mobile' => '01985793627', 'shipping_address' => 'NULL'),
            // 585 => array('customer_name' => 'Safallo Store', 'address' => 'Gobindogong', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 586 => array('customer_name' => 'Rinta', 'address' => 'Gibandha', 'mobile' => '01712535099', 'shipping_address' => 'NULL'),
            // 587 => array('customer_name' => 'Baby Care', 'address' => '', 'mobile' => '01853457763', 'shipping_address' => 'NULL'),
            // 588 => array('customer_name' => 'Rupanton', 'address' => 'RDA MARKET', 'mobile' => '01716560580', 'shipping_address' => 'NULL'),
            // 589 => array('customer_name' => 'Suad', 'address' => 'Mohammadpur', 'mobile' => '01911821031', 'shipping_address' => 'NULL'),
            // 590 => array('customer_name' => 'Butto Khan Varitage', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 591 => array('customer_name' => 'Butto Khan Variegate Store', 'address' => 'RDA', 'mobile' => '01728756950', 'shipping_address' => 'NULL'),
            // 592 => array('customer_name' => 'Siyam Cos.', 'address' => 'Shirpur Bogora', 'mobile' => '01730857550', 'shipping_address' => 'Chanda transport'),
            // 593 => array('customer_name' => 'Arafat Cosmetic', 'address' => 'Gousia', 'mobile' => '01817112792', 'shipping_address' => 'NULL'),
            // 594 => array('customer_name' => 'Three Store', 'address' => 'Savar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 595 => array('customer_name' => 'Buiya Cos', 'address' => 'Faramgate', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 596 => array('customer_name' => 'KPS', 'address' => 'Subahan Bagh', 'mobile' => '01777753425', 'shipping_address' => 'NULL'),
            // 597 => array('customer_name' => 'Center Point', 'address' => 'Narayangonj', 'mobile' => '01915493223', 'shipping_address' => 'NULL'),
            // 598 => array('customer_name' => 'Sumona Traders 2', 'address' => 'Narayangonj', 'mobile' => '01964671665', 'shipping_address' => 'NULL'),
            // 599 => array('customer_name' => 'Sonya Cor. 2', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 600 => array('customer_name' => 'Trade Link', 'address' => 'maejdi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 601 => array('customer_name' => 'Smart Collection', 'address' => 'Mirpur-2', 'mobile' => '01927643853', 'shipping_address' => 'NULL'),
            // 602 => array('customer_name' => 'Shahajahan', 'address' => 'Mohammad Pur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 603 => array('customer_name' => 'Sornali', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 604 => array('customer_name' => 'Chumki', 'address' => 'Gausia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 605 => array('customer_name' => 'Bismillah Store Jossore', 'address' => 'Jossore', 'mobile' => '01714441858', 'shipping_address' => 'NULL'),
            // 606 => array('customer_name' => 'Saraj Store', 'address' => 'Dinajpur', 'mobile' => '01715181921', 'shipping_address' => 'NULL'),
            // 607 => array('customer_name' => 'Narayon Store', 'address' => 'Notun Bazar Chadpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 608 => array('customer_name' => 'Tonni Enterprise', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 609 => array('customer_name' => 'Now and Wow', 'address' => 'Faridpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 610 => array('customer_name' => 'Qushik Vai', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 611 => array('customer_name' => 'Islam Cosmetic', 'address' => 'Narayongong', 'mobile' => '01920798502', 'shipping_address' => 'NULL'),
            // 612 => array('customer_name' => 'Jumka Store', 'address' => 'Comilla', 'mobile' => '01727402535', 'shipping_address' => 'NULL'),
            // 613 => array('customer_name' => 'Projapoti Store', 'address' => 'Kishorgong', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 614 => array('customer_name' => 'Kashem Trading', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 615 => array('customer_name' => 'Mitaly Store Sylhet', 'address' => 'Sylhet', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 616 => array('customer_name' => 'MIlton Cosmetic', 'address' => 'Gopalgonj', 'mobile' => '01714633324', 'shipping_address' => 'NULL'),
            // 617 => array('customer_name' => 'Sumon Mohakhali', 'address' => '', 'mobile' => '01838152326', 'shipping_address' => 'NULL'),
            // 618 => array('customer_name' => 'Kazi Sajahan', 'address' => 'R-Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 619 => array('customer_name' => 'Maa Ittadi Corner', 'address' => 'Gandaria', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 620 => array('customer_name' => 'Fariwala', 'address' => 'Khulna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 621 => array('customer_name' => 'Ismile', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 622 => array('customer_name' => 'Anmol Collection', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 623 => array('customer_name' => 'Sajuguju', 'address' => 'Basundhara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 624 => array('customer_name' => 'Sohel Trading', 'address' => 'C Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 625 => array('customer_name' => 'Harun Collection', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 626 => array('customer_name' => 'Jeni Trading', 'address' => 'Jassor', 'mobile' => '01711237071', 'shipping_address' => 'NULL'),
            // 627 => array('customer_name' => 'Fashion Cosmatics', 'address' => 'Khulna', 'mobile' => '01814350360', 'shipping_address' => 'NULL'),
            // 628 => array('customer_name' => 'Mahija Cosmetics', 'address' => '01917961338', 'mobile' => '01917961338', 'shipping_address' => 'NULL'),
            // 629 => array('customer_name' => 'Sweep Enterprise', 'address' => 'Badda', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 630 => array('customer_name' => 'Siyam Corporation', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 631 => array('customer_name' => 'Safe & Save', 'address' => 'Khulna', 'mobile' => '01717007124', 'shipping_address' => 'NULL'),
            // 632 => array('customer_name' => 'Mahedi Collection', 'address' => 'Cantonment', 'mobile' => '01768009002', 'shipping_address' => 'NULL'),
            // 633 => array('customer_name' => 'Saba Beauty Con 2', 'address' => 'Syllet-Jindabazar', 'mobile' => '01737178470', 'shipping_address' => 'NULL'),
            // 634 => array('customer_name' => 'Arian Cosmetic Zone', 'address' => 'Coxbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 635 => array('customer_name' => 'M/S Modina Store', 'address' => 'Rajbari', 'mobile' => '01712701041', 'shipping_address' => 'NULL'),
            // 636 => array('customer_name' => 'Sinha Cosmatic', 'address' => 'Eastern Mollika', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 637 => array('customer_name' => 'Smart Lady', 'address' => 'Narayongonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 638 => array('customer_name' => 'Skin Food', 'address' => 'BaddA', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 639 => array('customer_name' => 'Plus Point', 'address' => 'Raypur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 640 => array('customer_name' => 'Moyori', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 641 => array('customer_name' => 'Sorna', 'address' => 'Bogura', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 642 => array('customer_name' => 'Modern Saj Ghor', 'address' => 'Khilkhet', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 643 => array('customer_name' => 'Anik Cosmetic', 'address' => 'Boshundhara', 'mobile' => '01753660057', 'shipping_address' => 'NULL'),
            // 644 => array('customer_name' => 'Tacita 2', 'address' => 'Newmarket', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 645 => array('customer_name' => 'Sompa Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 646 => array('customer_name' => 'S D Enterprise', 'address' => 'Sondip', 'mobile' => '01815024605', 'shipping_address' => 'NULL'),
            // 647 => array('customer_name' => 'Arefin vai', 'address' => 'Chowkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 648 => array('customer_name' => 'Saykot', 'address' => 'Serajgonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 649 => array('customer_name' => 'Perfect Choice', 'address' => 'Eastern Plus', 'mobile' => '01911250592', 'shipping_address' => 'NULL'),
            // 650 => array('customer_name' => 'Neat Super', 'address' => 'Karanigonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 651 => array('customer_name' => 'Hashy Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 652 => array('customer_name' => 'Shila Moni', 'address' => 'B.Bariya', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 653 => array('customer_name' => 'Nazim & Brothers', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 654 => array('customer_name' => 'Paragon 2', 'address' => 'Gazipur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 655 => array('customer_name' => 'Fancy Collection', 'address' => 'Thakurghon', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 656 => array('customer_name' => 'Hamim', 'address' => 'City Heart', 'mobile' => '01716603998', 'shipping_address' => 'NULL'),
            // 657 => array('customer_name' => 'Hamid & Brothers', 'address' => 'Nowga', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 658 => array('customer_name' => 'Fuad Enterprise', 'address' => 'Candpur', 'mobile' => '01919002365', 'shipping_address' => 'NULL'),
            // 659 => array('customer_name' => 'Adni Store', 'address' => 'Serajgonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 660 => array('customer_name' => 'Toyhid Store', 'address' => 'Goalgonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 661 => array('customer_name' => 'Nahar Trade', 'address' => 'Nahakali', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 662 => array('customer_name' => 'Sonali Cosmetic', 'address' => 'Khulna', 'mobile' => '01951907883', 'shipping_address' => 'NULL'),
            // 663 => array('customer_name' => 'Monir Beauty concept', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 664 => array('customer_name' => 'Jhil Cosmetic', 'address' => 'Comilla', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 665 => array('customer_name' => 'A to Z Family Bazar', 'address' => 'Madobdi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 666 => array('customer_name' => 'afifa Cosmatics', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 667 => array('customer_name' => 'Asma Store', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 668 => array('customer_name' => 'Fatema Cosmatic', 'address' => 'Kajpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 669 => array('customer_name' => 'Ridoy Cosmetic', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 670 => array('customer_name' => 'Hafazur Rahman', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 671 => array('customer_name' => 'Jashmin Store', 'address' => 'Soriyotpur', 'mobile' => '01712189245', 'shipping_address' => 'NULL'),
            // 672 => array('customer_name' => 'Jewel', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 673 => array('customer_name' => 'Ava Cosmatics', 'address' => 'Zinzira', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 674 => array('customer_name' => 'Pabna Store', 'address' => 'Hili', 'mobile' => '01717798725', 'shipping_address' => 'NULL'),
            // 675 => array('customer_name' => 'Thi Cosmetics', 'address' => 'R.B', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 676 => array('customer_name' => 'AM', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 677 => array('customer_name' => 'Rahman Store', 'address' => 'Nowpara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 678 => array('customer_name' => 'Modhu vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 679 => array('customer_name' => 'Avenue Supper Shop', 'address' => 'Savar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 680 => array('customer_name' => 'Prince 2', 'address' => 'Eastern plaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 681 => array('customer_name' => 'Rezia Cosmetic', 'address' => '', 'mobile' => '00000000001', 'shipping_address' => 'NULL'),
            // 682 => array('customer_name' => 'Adil Store', 'address' => '', 'mobile' => '00000000002', 'shipping_address' => 'NULL'),
            // 683 => array('customer_name' => 'Mahin Enterprise', 'address' => 'C Bazar', 'mobile' => '00000000003', 'shipping_address' => 'NULL'),
            // 684 => array('customer_name' => 'Pratima', 'address' => 'Mirpure', 'mobile' => '00000000004', 'shipping_address' => 'NULL'),
            // 685 => array('customer_name' => 'Join', 'address' => '', 'mobile' => '00000000005', 'shipping_address' => 'NULL'),
            // 686 => array('customer_name' => 'Shorif Cosmetic', 'address' => 'Budda Gausia', 'mobile' => '00000000006', 'shipping_address' => 'NULL'),
            // 687 => array('customer_name' => 'She', 'address' => '', 'mobile' => '00000000007', 'shipping_address' => 'NULL'),
            // 688 => array('customer_name' => 'Bhai Bhai Cosmatic', 'address' => 'Ctg', 'mobile' => '00000000008', 'shipping_address' => 'NULL'),
            // 689 => array('customer_name' => 'Annona Cosmatic', 'address' => 'Rajshahi', 'mobile' => '00000000009', 'shipping_address' => 'NULL'),
            // 690 => array('customer_name' => 'Tarak Enterprise', 'address' => 'R-Bazar', 'mobile' => '00000000010', 'shipping_address' => 'NULL'),
            // 691 => array('customer_name' => 'B Tech Expart', 'address' => 'Dhanmondi', 'mobile' => '00000000011', 'shipping_address' => 'NULL'),
            // 692 => array('customer_name' => 'Classic collection', 'address' => '', 'mobile' => '00000000012', 'shipping_address' => 'NULL'),
            // 693 => array('customer_name' => 'Rupa Cosmetic', 'address' => 'Gopalgonj', 'mobile' => '01535806561', 'shipping_address' => 'NULL'),
            // 694 => array('customer_name' => 'M/S Swift Enterprise', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 695 => array('customer_name' => 'Akhi Enterprise', 'address' => 'DCC Gulshan 20 D.C.C Super Market 1st Floor', 'mobile' => '01986303236', 'shipping_address' => 'NULL'),
            // 696 => array('customer_name' => 'Tanij Beauty Concept', 'address' => '', 'mobile' => '01717237248', 'shipping_address' => 'NULL'),
            // 697 => array('customer_name' => 'Romana Dep Store', 'address' => 'Coxbazar', 'mobile' => '01815861469', 'shipping_address' => 'NULL'),
            // 698 => array('customer_name' => 'Thai Cosmatic', 'address' => 'Anjuman Market RB', 'mobile' => '01815622044', 'shipping_address' => 'NULL'),
            // 699 => array('customer_name' => 'Glamar World', 'address' => 'Rafin Plaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 700 => array('customer_name' => 'ahad Cosmetic', 'address' => 'Bogora', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 701 => array('customer_name' => 'Cosmetic Gallery', 'address' => 'R-Bazar', 'mobile' => '01874834180', 'shipping_address' => 'NULL'),
            // 702 => array('customer_name' => 'Sopnil Varaytise Sore', 'address' => 'Borguna', 'mobile' => '01712115611', 'shipping_address' => 'NULL'),
            // 703 => array('customer_name' => 'Maa Enterprise', 'address' => 'Jayduppur', 'mobile' => '01939343003', 'shipping_address' => 'NULL'),
            // 704 => array('customer_name' => 'Tayob', 'address' => 'Chawkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 705 => array('customer_name' => 'Mahi Gloxy', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 706 => array('customer_name' => 'Makka Store', 'address' => 'Mohammadia Market R Bazar', 'mobile' => '01817719559', 'shipping_address' => 'Daimond'),
            // 707 => array('customer_name' => 'Santa Cosmatic', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 708 => array('customer_name' => 'Mahabub', 'address' => 'Sibl', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 709 => array('customer_name' => 'Jibon Cosmatics', 'address' => 'Jossore', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 710 => array('customer_name' => 'MHM', 'address' => 'Nowga', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 711 => array('customer_name' => 'Fatema Trading', 'address' => 'Chawkbazar', 'mobile' => '01745932046', 'shipping_address' => 'NULL'),
            // 712 => array('customer_name' => 'Archis', 'address' => 'Kallanpur', 'mobile' => '01711548313', 'shipping_address' => 'NULL'),
            // 713 => array('customer_name' => 'Nurol Islam', 'address' => '', 'mobile' => '01728510893', 'shipping_address' => 'NULL'),
            // 714 => array('customer_name' => 'Rajdhani Cosmetics 2', 'address' => 'Rajdhani Super Market', 'mobile' => '01711365422', 'shipping_address' => 'NULL'),
            // 715 => array('customer_name' => 'Dhaka Lion', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 716 => array('customer_name' => 'Glmar', 'address' => 'Wari', 'mobile' => '01749988442', 'shipping_address' => 'NULL'),
            // 717 => array('customer_name' => 'Haven Tuch', 'address' => 'Tokeyo Squre', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 718 => array('customer_name' => 'Dhaka Trading', 'address' => 'Ekramullah Plaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 719 => array('customer_name' => 'Kader Store', 'address' => 'Chawkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 720 => array('customer_name' => 'Shopping Park', 'address' => 'Takurgaw', 'mobile' => '01738417878', 'shipping_address' => 'Romna Transport'),
            // 721 => array('customer_name' => 'Brand Mania', 'address' => 'Rahman Mansion R-Bazar Ctg', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 722 => array('customer_name' => 'Nava Enterprise', 'address' => 'Ekramullah Plaza', 'mobile' => '00000000001', 'shipping_address' => 'NULL'),
            // 723 => array('customer_name' => 'Sunsayn Cosmetics', 'address' => 'Damray', 'mobile' => '00000000002', 'shipping_address' => 'NULL'),
            // 724 => array('customer_name' => 'Kids Am', 'address' => 'Mirpur', 'mobile' => '00000000003', 'shipping_address' => 'NULL'),
            // 725 => array('customer_name' => 'Belmon Mart', 'address' => 'Gazipur', 'mobile' => '00000000004', 'shipping_address' => 'NULL'),
            // 726 => array('customer_name' => 'Mahammud', 'address' => 'Chawkbazar', 'mobile' => '00000000005', 'shipping_address' => 'NULL'),
            // 727 => array('customer_name' => 'J.R', 'address' => 'Feni', 'mobile' => '00000000006', 'shipping_address' => 'NULL'),
            // 728 => array('customer_name' => 'Sodia Store', 'address' => 'Eastern plaza', 'mobile' => '01715034687', 'shipping_address' => 'NULL'),
            // 729 => array('customer_name' => 'Mahin Telecom', 'address' => 'Saiyadpur', 'mobile' => '01740184224', 'shipping_address' => 'Romna Transport'),
            // 730 => array('customer_name' => 'Sajon Gojon', 'address' => 'Nazar Valley Shopping Mall', 'mobile' => '01715618827', 'shipping_address' => 'NULL'),
            // 731 => array('customer_name' => 'Civil General Store', 'address' => 'Gulshan _1', 'mobile' => '01711378774', 'shipping_address' => 'NULL'),
            // 732 => array('customer_name' => 'Babys Gallery', 'address' => 'Mirpur Pollobi', 'mobile' => '01913023215', 'shipping_address' => 'NULL'),
            // 733 => array('customer_name' => 'Intemate DW', 'address' => 'New Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 734 => array('customer_name' => 'Sporsho bipone', 'address' => 'Jinaidaho', 'mobile' => '01717726273', 'shipping_address' => 'NULL'),
            // 735 => array('customer_name' => 'Mithila Cosmatic', 'address' => 'Mohammad pur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 736 => array('customer_name' => 'Khadija', 'address' => '', 'mobile' => '00000000001', 'shipping_address' => 'NULL'),
            // 737 => array('customer_name' => 'Touch & touch', 'address' => '', 'mobile' => '00000000002', 'shipping_address' => 'NULL'),
            // 738 => array('customer_name' => 'Rangela Cosmetics', 'address' => 'Gazipur', 'mobile' => '00000000003', 'shipping_address' => 'NULL'),
            // 739 => array('customer_name' => 'Amzad vai', 'address' => '', 'mobile' => '00000000004', 'shipping_address' => 'NULL'),
            // 740 => array('customer_name' => 'Shompa', 'address' => '', 'mobile' => '00000000005', 'shipping_address' => 'NULL'),
            // 741 => array('customer_name' => 'Business World', 'address' => 'Comilla', 'mobile' => '00000000006', 'shipping_address' => 'NULL'),
            // 742 => array('customer_name' => 'Rks Enterprise', 'address' => 'Mirpur-1', 'mobile' => '00000000007', 'shipping_address' => 'NULL'),
            // 743 => array('customer_name' => 'Jondo', 'address' => 'BDR', 'mobile' => '00000000008', 'shipping_address' => 'NULL'),
            // 744 => array('customer_name' => 'SK Trading', 'address' => '', 'mobile' => '00000000009', 'shipping_address' => 'NULL'),
            // 745 => array('customer_name' => 'The City Point', 'address' => 'Comilla', 'mobile' => '01921455484', 'shipping_address' => 'NULL'),
            // 746 => array('customer_name' => 'Sultan Store', 'address' => 'Kaptan Bazar', 'mobile' => '01911855193', 'shipping_address' => 'NULL'),
            // 747 => array('customer_name' => 'New Family Center', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 748 => array('customer_name' => 'Shashi B/C', 'address' => 'Murpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 749 => array('customer_name' => 'H K I Cosmetics', 'address' => 'Samoly', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 750 => array('customer_name' => 'Saj Gallery', 'address' => 'Gazipur Kaliyapur', 'mobile' => '01754143420', 'shipping_address' => 'NULL'),
            // 751 => array('customer_name' => 'Abdul Khalek', 'address' => 'Razbari', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 752 => array('customer_name' => 'JR Corporation', 'address' => 'Fani', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 753 => array('customer_name' => 'Kolpona Store', 'address' => 'Nowga', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 754 => array('customer_name' => 'M R', 'address' => 'Rbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 755 => array('customer_name' => 'Prety Cosmetics', 'address' => 'Savar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 756 => array('customer_name' => 'Bloo Ming', 'address' => 'Rafin Plaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 757 => array('customer_name' => 'White House', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 758 => array('customer_name' => 'Aisha Cosmetics', 'address' => 'comilla', 'mobile' => '01819695593', 'shipping_address' => 'NULL'),
            // 759 => array('customer_name' => 'M N Enterprise', 'address' => 'Norsindi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 760 => array('customer_name' => 'Randhuno Bazar', 'address' => 'Razshahi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 761 => array('customer_name' => 'The Super-shop', 'address' => 'Tongi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 762 => array('customer_name' => 'Silpi', 'address' => 'rajdhani super market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 763 => array('customer_name' => 'All Well', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 764 => array('customer_name' => 'Shamol vai', 'address' => '', 'mobile' => '01718323020', 'shipping_address' => 'NULL'),
            // 765 => array('customer_name' => 'Zashion', 'address' => 'Lalbagh', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 766 => array('customer_name' => 'Billal Trading', 'address' => 'Tongi', 'mobile' => '01682560980', 'shipping_address' => 'NULL'),
            // 767 => array('customer_name' => 'Mazi Store', 'address' => 'Mirpur 10', 'mobile' => '01911713355', 'shipping_address' => 'NULL'),
            // 768 => array('customer_name' => 'Piyangon', 'address' => 'Mirpur-10', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 769 => array('customer_name' => 'Kashem Vai', 'address' => 'Gulshan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 770 => array('customer_name' => 'Sohel', 'address' => 'Hazaribag', 'mobile' => '01671136334', 'shipping_address' => 'NULL'),
            // 771 => array('customer_name' => 'Touch & Tech', 'address' => 'Belly Road', 'mobile' => '01722121272', 'shipping_address' => 'NULL'),
            // 772 => array('customer_name' => 'Reyad', 'address' => 'Mowchak', 'mobile' => '01717064238', 'shipping_address' => 'NULL'),
            // 773 => array('customer_name' => 'HS Cosmetics', 'address' => 'Damra', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 774 => array('customer_name' => 'Girls Zone', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 775 => array('customer_name' => 'Rafsan', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 776 => array('customer_name' => 'Hetashe', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 777 => array('customer_name' => 'Vintage World Bogra', 'address' => 'Bogra', 'mobile' => '01711142302', 'shipping_address' => 'NULL'),
            // 778 => array('customer_name' => 'Vintage World Rangpur', 'address' => 'Rangpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 779 => array('customer_name' => 'Riya Cosmetics', 'address' => 'Jinaidha', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 780 => array('customer_name' => 'M RR Corporation', 'address' => 'Gulshan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 781 => array('customer_name' => 'Choice24', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 782 => array('customer_name' => 'Zara Enterprise', 'address' => 'HMM Road Jassore', 'mobile' => '01835248709', 'shipping_address' => 'NULL'),
            // 783 => array('customer_name' => 'Keya cos', 'address' => 'Fullbari', 'mobile' => '01743922197', 'shipping_address' => 'NULL'),
            // 784 => array('customer_name' => 'Tajmohol Store', 'address' => 'Kustia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 785 => array('customer_name' => 'Ali Confectionery', 'address' => 'kolanpur', 'mobile' => '00000000001', 'shipping_address' => 'NULL'),
            // 786 => array('customer_name' => 'Munia', 'address' => 'Online', 'mobile' => '00000000002', 'shipping_address' => 'NULL'),
            // 787 => array('customer_name' => 'Zaman Trade', 'address' => 'hasan1111', 'mobile' => '00000000003', 'shipping_address' => 'NULL'),
            // 788 => array('customer_name' => 'Suptara', 'address' => '', 'mobile' => '00000000004', 'shipping_address' => 'NULL'),
            // 789 => array('customer_name' => 'Rasal', 'address' => '', 'mobile' => '01971119100', 'shipping_address' => 'NULL'),
            // 790 => array('customer_name' => 'Rokomari', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 791 => array('customer_name' => 'Khondokar cosmatics', 'address' => 'Rdr', 'mobile' => '01778527796', 'shipping_address' => 'NULL'),
            // 792 => array('customer_name' => 'Sharif Vai', 'address' => 'Dhanmondi', 'mobile' => '01911707744', 'shipping_address' => 'NULL'),
            // 793 => array('customer_name' => 'Nazir vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 794 => array('customer_name' => 'New Amanat Enterprise', 'address' => 'R-Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 795 => array('customer_name' => 'M/s Keya Cosmetics & Gift Corner', 'address' => 'Dinajpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 796 => array('customer_name' => 'Kholil', 'address' => 'Gulshan1212', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 797 => array('customer_name' => 'Noor Cosmetics & Lather House', 'address' => 'Hazigonj', 'mobile' => '01686118078', 'shipping_address' => 'Khaja Mainuddin transport'),
            // 798 => array('customer_name' => 'Gulshan Ara Traders', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 799 => array('customer_name' => 'Ripon vai', 'address' => 'tajmohol', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 800 => array('customer_name' => 'Monihari Store', 'address' => 'Rongpur', 'mobile' => '01737578346', 'shipping_address' => 'NULL'),
            // 801 => array('customer_name' => 'Suborna Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 802 => array('customer_name' => 'Nazmul Cosmatics', 'address' => 'Estarn Mollika', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 803 => array('customer_name' => 'Banani Proshadhoni', 'address' => 'Mymansingh', 'mobile' => '01718072757', 'shipping_address' => 'NULL'),
            // 804 => array('customer_name' => 'Takbir', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 805 => array('customer_name' => 'Mukta Cosmatics', 'address' => 'Bogura', 'mobile' => '01767466222', 'shipping_address' => 'NULL'),
            // 806 => array('customer_name' => 'Indian Beauty Corner', 'address' => 'Savar New Market', 'mobile' => '01681505820', 'shipping_address' => 'NULL'),
            // 807 => array('customer_name' => 'Niyaj Matching Corner', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 808 => array('customer_name' => 'R.P', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 809 => array('customer_name' => 'Aaiyan collection', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 810 => array('customer_name' => 'Lazz Pharma', 'address' => 'Dhanmondi', 'mobile' => '01730944867', 'shipping_address' => 'NULL'),
            // 811 => array('customer_name' => 'Micro Shine -2', 'address' => 'R-bazar', 'mobile' => '01814370323', 'shipping_address' => 'NULL'),
            // 812 => array('customer_name' => 'Megla Cosmatics', 'address' => 'Saver City Center', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 813 => array('customer_name' => 'Dev Enterprise', 'address' => 'Mirzapur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 814 => array('customer_name' => 'Al Haz Cosmetic', 'address' => 'Mirzapur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 815 => array('customer_name' => 'MM Trading', 'address' => 'Faramgate', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 816 => array('customer_name' => 'Chokoriya Store', 'address' => 'Chokoriya ctg', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 817 => array('customer_name' => 'bomby Collection', 'address' => 'Jessore', 'mobile' => '01716952848', 'shipping_address' => 'NULL'),
            // 818 => array('customer_name' => 'Saudi Enterprise', 'address' => 'Estarn Plus Commila', 'mobile' => '01715034687', 'shipping_address' => 'NULL'),
            // 819 => array('customer_name' => 'Shakil', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 820 => array('customer_name' => 'Achol Cosmetics', 'address' => 'Bhanga', 'mobile' => '01712982300', 'shipping_address' => 'NULL'),
            // 821 => array('customer_name' => 'Alin Cosmetics', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 822 => array('customer_name' => 'Topor', 'address' => 'Port Ctg', 'mobile' => '00000000001', 'shipping_address' => 'NULL'),
            // 823 => array('customer_name' => 'Speed Life', 'address' => 'Bosundhara city', 'mobile' => '00000000002', 'shipping_address' => 'NULL'),
            // 824 => array('customer_name' => 'Victoria Gift', 'address' => 'Noril', 'mobile' => '00000000003', 'shipping_address' => 'NULL'),
            // 825 => array('customer_name' => 'Tamanna', 'address' => 'Uttara', 'mobile' => '00000000004', 'shipping_address' => 'NULL'),
            // 826 => array('customer_name' => 'Matre Shaya', 'address' => 'Barishal', 'mobile' => '00000000005', 'shipping_address' => 'NULL'),
            // 827 => array('customer_name' => 'Shahadat vai', 'address' => '', 'mobile' => '00000000006', 'shipping_address' => 'NULL'),
            // 828 => array('customer_name' => 'Raj Super Shop', 'address' => 'tongi', 'mobile' => '01614561111', 'shipping_address' => 'NULL'),
            // 829 => array('customer_name' => 'Humayra', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 830 => array('customer_name' => 'Bankok Corner', 'address' => 'Saver', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 831 => array('customer_name' => 'Priyojon Sotre', 'address' => 'Serajgong', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 832 => array('customer_name' => 'Al Kamy', 'address' => 'Shabag', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 833 => array('customer_name' => 'Fashion +', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 834 => array('customer_name' => 'Jui Emporium', 'address' => 'Picture Plase Khulna', 'mobile' => '01916697057', 'shipping_address' => 'Jedda Transport'),
            // 835 => array('customer_name' => 'ZE Shop', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 836 => array('customer_name' => 'Aisha Cosmetics 2', 'address' => 'Abdullah pur', 'mobile' => '01936108175', 'shipping_address' => 'NULL'),
            // 837 => array('customer_name' => 'Kintuki', 'address' => 'Narayangonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 838 => array('customer_name' => 'MB PLAZA', 'address' => 'Tajmohal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 839 => array('customer_name' => 'M.A Beauty Concept', 'address' => 'Hazisalim Tower', 'mobile' => '01957063111', 'shipping_address' => 'NULL'),
            // 840 => array('customer_name' => 'Wahab Enterprise', 'address' => 'R-Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 841 => array('customer_name' => 'Amir & Sons', 'address' => 'Gulshan', 'mobile' => '00000000001', 'shipping_address' => 'NULL'),
            // 842 => array('customer_name' => 'Star World', 'address' => 'Dhanmondi', 'mobile' => '00000000002', 'shipping_address' => 'NULL'),
            // 843 => array('customer_name' => 'Faisal Vai', 'address' => 'faysal', 'mobile' => '00000000003', 'shipping_address' => 'NULL'),
            // 844 => array('customer_name' => 'Greho Sojja', 'address' => '', 'mobile' => '00000000004', 'shipping_address' => 'NULL'),
            // 845 => array('customer_name' => 'Pabna Cosmatics', 'address' => 'Pabna', 'mobile' => '01729511101', 'shipping_address' => 'NULL'),
            // 846 => array('customer_name' => 'Dewen Cosmetics', 'address' => 'Tangail', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 847 => array('customer_name' => 'Sakura', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 848 => array('customer_name' => 'Sonya Tailors & Cosmetics', 'address' => 'Potuwakhali', 'mobile' => '01711287140', 'shipping_address' => 'NULL'),
            // 849 => array('customer_name' => 'Swim Beauty Concept', 'address' => 'Gawsia', 'mobile' => '01715420475', 'shipping_address' => 'NULL'),
            // 850 => array('customer_name' => 'Farha Cosmetics', 'address' => 'Kochukhet', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 851 => array('customer_name' => 'Sumaya Cosmetics', 'address' => 'Borishal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 852 => array('customer_name' => 'Tipu Vai', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 853 => array('customer_name' => 'Mayer Doa Color Cosmetics', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 854 => array('customer_name' => 'Akter Trading', 'address' => 'Rahman Mention', 'mobile' => '017818574145', 'shipping_address' => 'NULL'),
            // 855 => array('customer_name' => 'Tasniya Enterprise', 'address' => 'R Bazar', 'mobile' => '01866692100', 'shipping_address' => 'NULL'),
            // 856 => array('customer_name' => 'Alpona', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 857 => array('customer_name' => 'Maximum', 'address' => 'Dhanmondi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 858 => array('customer_name' => 'Gold Nova Cosmetics', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 859 => array('customer_name' => 'SI', 'address' => 'R Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 860 => array('customer_name' => 'Persona', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 861 => array('customer_name' => 'Rupushi Cosmetics', 'address' => 'New market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 862 => array('customer_name' => 'Manik Store', 'address' => 'Tangail', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 863 => array('customer_name' => 'Almas Cosmatics', 'address' => 'B.baria', 'mobile' => '01611395370', 'shipping_address' => 'NULL'),
            // 864 => array('customer_name' => 'Color World', 'address' => 'Gulshan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 865 => array('customer_name' => 'Sahajada (Opodartho)', 'address' => 'Chawkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 866 => array('customer_name' => 'Electric Sumon', 'address' => 'Tazmahal Tower', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 867 => array('customer_name' => 'Tareq Enterprize R', 'address' => 'R-Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 868 => array('customer_name' => 'Ranasa', 'address' => 'Chowkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 869 => array('customer_name' => 'Switch Pharma', 'address' => 'Kolabagan', 'mobile' => '01760748674', 'shipping_address' => 'NULL'),
            // 870 => array('customer_name' => 'Dhonia Trade Int', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 871 => array('customer_name' => 'Moon Cosmatics', 'address' => 'Dinajpur', 'mobile' => '01715803815', 'shipping_address' => 'Romna transport'),
            // 872 => array('customer_name' => 'Saj Rong', 'address' => '', 'mobile' => '01738886949', 'shipping_address' => 'NULL'),
            // 873 => array('customer_name' => 'New Gift Shop', 'address' => 'Gulshan-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 874 => array('customer_name' => 'Modina Traders', 'address' => 'R-bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 875 => array('customer_name' => 'MB Collection (Gulshan)', 'address' => 'DCC Market, Gulshan-1, Dhaka-1212', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 876 => array('customer_name' => 'Asha Cosmetics', 'address' => 'Razzak Plaza Savar', 'mobile' => '01717328528', 'shipping_address' => 'NULL'),
            // 877 => array('customer_name' => 'Shahana', 'address' => 'Chittagong Shopping Complex', 'mobile' => '01818738112', 'shipping_address' => 'NULL'),
            // 878 => array('customer_name' => 'Red Eart', 'address' => 'Chittagong', 'mobile' => '01818738112', 'shipping_address' => 'NULL'),
            // 879 => array('customer_name' => 'Ali Store', 'address' => 'R-bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 880 => array('customer_name' => '???? ??????? ?????', 'address' => '?????? ?????', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 881 => array('customer_name' => 'Cosmatics Collection', 'address' => 'R-Bazar', 'mobile' => '01821713145', 'shipping_address' => 'NULL'),
            // 882 => array('customer_name' => 'Priyojon Store', 'address' => 'Sirajgonj', 'mobile' => '01718341816', 'shipping_address' => 'jonopriyo mokka'),
            // 883 => array('customer_name' => 'Fatema-B', 'address' => 'Gulshan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 884 => array('customer_name' => 'Fatema-R', 'address' => 'Gulshan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 885 => array('customer_name' => 'Aziz Cloth Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 886 => array('customer_name' => 'Benu', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 887 => array('customer_name' => 'Moon City', 'address' => 'Taltola city', 'mobile' => '01753333151', 'shipping_address' => 'NULL'),
            // 888 => array('customer_name' => 'Kamona', 'address' => 'Kornofully market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 889 => array('customer_name' => 'Fashion World', 'address' => 'Foridpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 890 => array('customer_name' => 'Madani Traders', 'address' => 'Gulshan', 'mobile' => '01619543902', 'shipping_address' => 'NULL'),
            // 891 => array('customer_name' => 'Anisur-Rahman', 'address' => 'Dinajpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 892 => array('customer_name' => 'Mizan', 'address' => 'Gulshan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 893 => array('customer_name' => 'Bhuya Store', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 894 => array('customer_name' => 'Nippon', 'address' => 'Uttara', 'mobile' => '00000000001', 'shipping_address' => 'NULL'),
            // 895 => array('customer_name' => 'saika', 'address' => 'Gulsaha-1', 'mobile' => '00000000002', 'shipping_address' => 'NULL'),
            // 896 => array('customer_name' => 'Ata Gulshan', 'address' => 'Gulshan', 'mobile' => '00000000003', 'shipping_address' => 'NULL'),
            // 897 => array('customer_name' => 'Original cosmatics', 'address' => 'RAjdhani', 'mobile' => '00000000004', 'shipping_address' => 'NULL'),
            // 898 => array('customer_name' => 'Sahadat', 'address' => '', 'mobile' => '00000000005', 'shipping_address' => 'NULL'),
            // 899 => array('customer_name' => 'NM Cosmetics', 'address' => 'Faramgate', 'mobile' => '00000000006', 'shipping_address' => 'NULL'),
            // 900 => array('customer_name' => 'Imam Vai', 'address' => 'Parsona', 'mobile' => '00000000007', 'shipping_address' => 'NULL'),
            // 901 => array('customer_name' => 'Travel Center', 'address' => 'Sathkhira', 'mobile' => '01711310071', 'shipping_address' => 'NULL'),
            // 902 => array('customer_name' => 'Rims', 'address' => 'Eastarnplaza', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 903 => array('customer_name' => 'Anisur Rahman', 'address' => 'Momotaj Plaza', 'mobile' => '01833996014', 'shipping_address' => 'NULL'),
            // 904 => array('customer_name' => 'Asad Cosmatics', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 905 => array('customer_name' => 'Rizvi', 'address' => 'santirajor', 'mobile' => '01913383976', 'shipping_address' => 'NULL'),
            // 906 => array('customer_name' => 'M/S saudi arabia Store', 'address' => 'R- Bazar', 'mobile' => '01820272711', 'shipping_address' => 'NULL'),
            // 907 => array('customer_name' => 'Jonnoni Cosmetics', 'address' => 'Kapashiya', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 908 => array('customer_name' => 'Nill Akash', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 909 => array('customer_name' => 'Angels Cloud', 'address' => '213, Finlay Square Nasirabad Cgt', 'mobile' => '01833282323', 'shipping_address' => 'NULL'),
            // 910 => array('customer_name' => 'BK Store', 'address' => 'Jossore', 'mobile' => '01917816764', 'shipping_address' => 'NULL'),
            // 911 => array('customer_name' => 'Motiar', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 912 => array('customer_name' => 'The Maruf Cosmatics', 'address' => 'Rajshahi RDA', 'mobile' => '01745681983', 'shipping_address' => '1no tiger'),
            // 913 => array('customer_name' => 'Ovi Modhu Cosmetics', 'address' => 'RDA', 'mobile' => '01752032373', 'shipping_address' => 'NULL'),
            // 914 => array('customer_name' => 'Kohinoor', 'address' => '', 'mobile' => '01717783475', 'shipping_address' => 'NULL'),
            // 915 => array('customer_name' => 'Paris Collection', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 916 => array('customer_name' => 'Raisa Stone & Cosmetics Collection', 'address' => 'Index Plaza Bazir Mor Narsingdi', 'mobile' => '01718014263', 'shipping_address' => 'Hazimannan Transport'),
            // 917 => array('customer_name' => 'Dulu & Sons', 'address' => 'Gaibandha', 'mobile' => '01711077783', 'shipping_address' => 'Mala Transports'),
            // 918 => array('customer_name' => 'Sanonda Saj Ghor', 'address' => 'Gopalgonj', 'mobile' => '01674075164', 'shipping_address' => ''),
            // 919 => array('customer_name' => 'Ponno bithika', 'address' => 'Pabna', 'mobile' => '01715114537', 'shipping_address' => 'Sonar bangla emam'),
            // 920 => array('customer_name' => 'Belmon Mart 2', 'address' => 'Charag Ali', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 921 => array('customer_name' => 'Saika Exclusive', 'address' => 'Baridhara', 'mobile' => '01727531377', 'shipping_address' => 'NULL'),
            // 922 => array('customer_name' => 'Shafin Collection', 'address' => 'Badda', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 923 => array('customer_name' => 'Khalil Vai Glass Mistry', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 924 => array('customer_name' => 'Talukdar', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 925 => array('customer_name' => 'Mahabub Vai-Mirpur', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 926 => array('customer_name' => 'Mamuni Fashion', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 927 => array('customer_name' => 'Anas', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 928 => array('customer_name' => 'Banin Traders', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 929 => array('customer_name' => 'Buttu vai', 'address' => 'tajmahal', 'mobile' => '01989853782', 'shipping_address' => 'NULL'),
            // 930 => array('customer_name' => 'Skin Care Bangladesh', 'address' => 'Online', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 931 => array('customer_name' => 'Mahabub Trading', 'address' => 'Tajmohal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 932 => array('customer_name' => 'Nabila Store', 'address' => 'Tangail', 'mobile' => '01711703701', 'shipping_address' => 'Voreralo'),
            // 933 => array('customer_name' => 'Titon', 'address' => 'Rajshahi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 934 => array('customer_name' => 'Dipu Enterprise', 'address' => 'mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 935 => array('customer_name' => 'Royel Traders', 'address' => 'Chawkbazar', 'mobile' => '01813502212', 'shipping_address' => 'NULL'),
            // 936 => array('customer_name' => 'Nil Aksh', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 937 => array('customer_name' => 'Aid Pharma', 'address' => 'soni akhra', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 938 => array('customer_name' => 'Sikder Cosmatics', 'address' => 'Soni-Akra', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 939 => array('customer_name' => 'Nihas Gallery', 'address' => 'Gulshan', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 940 => array('customer_name' => 'Seza Cosmetic', 'address' => 'B.Baria', 'mobile' => '01768430304', 'shipping_address' => 'NULL'),
            // 941 => array('customer_name' => 'Bondhon Gift Corner', 'address' => 'Eastern Plaza Camilla', 'mobile' => '01712614570', 'shipping_address' => 'NULL'),
            // 942 => array('customer_name' => 'Top City', 'address' => 'Mirpur-2', 'mobile' => '01917886148', 'shipping_address' => 'NULL'),
            // 943 => array('customer_name' => 'Sadik vai', 'address' => 'Twin-tower', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 944 => array('customer_name' => 'Japan Cosmatics', 'address' => 'Adamjee', 'mobile' => '01714294001', 'shipping_address' => 'NULL'),
            // 945 => array('customer_name' => 'Siraj Store', 'address' => 'Dinajpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 946 => array('customer_name' => 'Hut Bazar', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 947 => array('customer_name' => 'Binodon', 'address' => 'Taingail', 'mobile' => '01735642008', 'shipping_address' => 'NULL'),
            // 948 => array('customer_name' => 'Rupushi Bangla Super shop', 'address' => 'Burimari,Kurigram', 'mobile' => '01729334081', 'shipping_address' => 'NULL'),
            // 949 => array('customer_name' => 'Big Bazar Pabna', 'address' => 'Pabna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 950 => array('customer_name' => 'Rustom store', 'address' => 'Jossore', 'mobile' => '01712925184', 'shipping_address' => 'Afjal Transport'),
            // 951 => array('customer_name' => 'New Aborini', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 952 => array('customer_name' => 'Bondhon Cosmetics R', 'address' => 'Rangamati', 'mobile' => '01820306493', 'shipping_address' => 'NULL'),
            // 953 => array('customer_name' => 'Sumon varaitis center', 'address' => 'Gazipur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 954 => array('customer_name' => 'Popy Dep. Store', 'address' => 'New Elephant Road', 'mobile' => '01820306493', 'shipping_address' => 'NULL'),
            // 955 => array('customer_name' => 'Rabbi Cosmetics', 'address' => 'Bogora', 'mobile' => '01714929642', 'shipping_address' => 'NULL'),
            // 956 => array('customer_name' => 'In Halal Beauty Concept', 'address' => 'Savar', 'mobile' => '01931692290', 'shipping_address' => 'NULL'),
            // 957 => array('customer_name' => 'Sha Mother Store', 'address' => 'Madaripur', 'mobile' => '00000000000', 'shipping_address' => 'Sahinmajhi Razar Ghat'),
            // 958 => array('customer_name' => 'Mohamadia Mini Bazar', 'address' => 'Noakhali', 'mobile' => '01858182536', 'shipping_address' => 'NULL'),
            // 959 => array('customer_name' => 'Saiful Store', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 960 => array('customer_name' => 'Momotaj & Sons', 'address' => 'Khulna-01711067463', 'mobile' => '0412832147', 'shipping_address' => 'jedda Transport'),
            // 961 => array('customer_name' => 'Salam Store J', 'address' => 'JOssore', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 962 => array('customer_name' => 'Kom-Bashi General Store', 'address' => 'Jikatola', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 963 => array('customer_name' => 'S Rahman Store', 'address' => 'R-Bazar', 'mobile' => '01822536716', 'shipping_address' => 'NULL'),
            // 964 => array('customer_name' => 'Nasrin Cosmetics', 'address' => 'Ctg Road', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 965 => array('customer_name' => 'Ladies World', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 966 => array('customer_name' => 'Bright Store', 'address' => 'Picture Plase -Khulna', 'mobile' => '01715915015', 'shipping_address' => 'NULL'),
            // 967 => array('customer_name' => 'Jal Poddo Ent', 'address' => 'Bogura', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 968 => array('customer_name' => 'Ruchita Cosmetics', 'address' => 'Khulna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 969 => array('customer_name' => 'City Pearl & Cosmetics', 'address' => 'Manikgonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 970 => array('customer_name' => 'Choice Cosmetics', 'address' => 'Khulna', 'mobile' => '01715018437', 'shipping_address' => 'NULL'),
            // 971 => array('customer_name' => 'Al Arob', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 972 => array('customer_name' => 'Samoly Square', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 973 => array('customer_name' => 'Soddosoa Beauty Concept', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 974 => array('customer_name' => 'Nimoza Enterprise', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 975 => array('customer_name' => 'UK Cosmetics', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 976 => array('customer_name' => 'Nazma Collection', 'address' => 'Gulshan Dcc Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 977 => array('customer_name' => 'AL Modina Traders', 'address' => 'Golden Plaza', 'mobile' => '01817020799', 'shipping_address' => 'NULL'),
            // 978 => array('customer_name' => 'Salfy', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 979 => array('customer_name' => 'London Exclusive', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 980 => array('customer_name' => 'anondo Traders', 'address' => 'Maizdi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 981 => array('customer_name' => 'Al Modina Enterprise', 'address' => 'Buri Market R-Bazar', 'mobile' => '01821707176', 'shipping_address' => 'NULL'),
            // 982 => array('customer_name' => 'Mile Collection', 'address' => 'MIrpur0-1', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 983 => array('customer_name' => 'Hat Bazaar', 'address' => 'Jassore', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 984 => array('customer_name' => 'Alamgir Cosmetics', 'address' => 'Khulna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 985 => array('customer_name' => 'Media Trust Pharma', 'address' => 'Uttara', 'mobile' => '01949001437', 'shipping_address' => 'NULL'),
            // 986 => array('customer_name' => 'Playess', 'address' => 'Khilgow', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 987 => array('customer_name' => 'Mollah Gift Corner', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 988 => array('customer_name' => 'Bismillah Cosmetics N', 'address' => 'Narayongonj', 'mobile' => '01819446812', 'shipping_address' => 'NULL'),
            // 989 => array('customer_name' => 'Jajri Enterprise', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => '01986066026'),
            // 990 => array('customer_name' => 'Cute Cosmetics', 'address' => 'Amin Bazar Savar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 991 => array('customer_name' => 'Katar Super Store', 'address' => 'Norsingdi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 992 => array('customer_name' => 'Karim & Son\'s', 'address' => 'Saheb Bazar Rajshahi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 993 => array('customer_name' => 'Nowrin Cosmetics', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 994 => array('customer_name' => 'Motaleb Cosmetics', 'address' => 'Madaripur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 995 => array('customer_name' => 'Nuha Enterprise', 'address' => 'Borishal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 996 => array('customer_name' => 'Badhon Enterprise', 'address' => 'Mir Shopping Complex, Chadpur', 'mobile' => '01731383688', 'shipping_address' => 'Sven Star'),
            // 997 => array('customer_name' => 'Black Cosmetics', 'address' => 'Norsingdi', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 998 => array('customer_name' => 'Choyon & Brothers', 'address' => 'Manikjong', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 999 => array('customer_name' => 'Sofiq Vai', 'address' => 'Bogura', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1000 => array('customer_name' => 'Botob', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1001 => array('customer_name' => 'J.S Shopping Center', 'address' => '', 'mobile' => '01815223774', 'shipping_address' => 'NULL'),
            // 1002 => array('customer_name' => 'Bismillah Pharma', 'address' => 'Nazira Bazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1003 => array('customer_name' => 'Saj Mohal', 'address' => 'Serajgonj', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1004 => array('customer_name' => 'Prosanto', 'address' => 'Khulna', 'mobile' => '01717946152', 'shipping_address' => 'NULL'),
            // 1005 => array('customer_name' => 'SB Enterprise', 'address' => 'Commila', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1006 => array('customer_name' => 'Save Way', 'address' => 'Mimi Super Market Ground Floor Nasirabad Ctg', 'mobile' => '01911841318', 'shipping_address' => 'NULL'),
            // 1007 => array('customer_name' => 'Anu Supper Shop', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1008 => array('customer_name' => 'Tajri Enterprise', 'address' => 'Hazisalim Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1009 => array('customer_name' => 'Chornofuly Mart', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1010 => array('customer_name' => 'Anupum', 'address' => 'Eastarn Plus', 'mobile' => '01877345789', 'shipping_address' => 'NULL'),
            // 1011 => array('customer_name' => 'Munni Cosmetics', 'address' => 'Pabna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1012 => array('customer_name' => 'Fancy Bazar', 'address' => 'Gandariya', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1013 => array('customer_name' => 'Abul Brothers', 'address' => 'Jessore Road Khulna', 'mobile' => '01711280272', 'shipping_address' => 'Rainbow'),
            // 1014 => array('customer_name' => 'Hankong Cosmatics', 'address' => 'Saver', 'mobile' => '01722298978', 'shipping_address' => 'NULL'),
            // 1015 => array('customer_name' => 'Maa Cosmetics', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1016 => array('customer_name' => 'Provati Store', 'address' => 'Faridpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1017 => array('customer_name' => 'A Plus', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1018 => array('customer_name' => 'Jonyr Dokan', 'address' => 'Jassore', 'mobile' => '01714441858', 'shipping_address' => 'NULL'),
            // 1019 => array('customer_name' => 'Nobo Kotha', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1020 => array('customer_name' => 'Nirala Beauty Concept', 'address' => 'Saver', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1021 => array('customer_name' => 'Rafe-Online', 'address' => 'All Bangladesh', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1022 => array('customer_name' => 'Shine & Glow', 'address' => 'Mirpur', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1023 => array('customer_name' => 'Mother & Baby', 'address' => 'New Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1024 => array('customer_name' => 'Tajmahal Store', 'address' => 'Kustiya', 'mobile' => '01672933095', 'shipping_address' => 'NULL'),
            // 1025 => array('customer_name' => 'Roki Jori Ghore', 'address' => 'Pabna', 'mobile' => '01736533548', 'shipping_address' => 'NULL'),
            // 1026 => array('customer_name' => 'Bitop', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1027 => array('customer_name' => 'Asharaful Vai', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1028 => array('customer_name' => 'G Mart Super Shop', 'address' => 'Mirpur', 'mobile' => '01970002013', 'shipping_address' => 'NULL'),
            // 1029 => array('customer_name' => 'Glamour Zone', 'address' => 'Uttara', 'mobile' => '01747411114', 'shipping_address' => 'NULL'),
            // 1030 => array('customer_name' => 'Roshni Enterprise', 'address' => 'R-Bazar', 'mobile' => '01815589025', 'shipping_address' => 'NULL'),
            // 1031 => array('customer_name' => 'Grameen', 'address' => 'Gawsia', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1032 => array('customer_name' => 'Shopping.com', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1033 => array('customer_name' => 'MIss & Mrs', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1034 => array('customer_name' => 'POP Company', 'address' => 'Ekram Ullah Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1035 => array('customer_name' => 'SB Store', 'address' => 'Ray Shopping R Bazar', 'mobile' => '01815822275', 'shipping_address' => 'NULL'),
            // 1036 => array('customer_name' => 'Beauty Care', 'address' => 'Rajdhani supper Market', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1037 => array('customer_name' => 'Friends Collection', 'address' => 'Kulna', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1038 => array('customer_name' => 'Alpha Cosmatics', 'address' => 'HM Plaza Uttara', 'mobile' => '01795827327', 'shipping_address' => 'NULL'),
            // 1039 => array('customer_name' => 'Bhai Bhai Store', 'address' => 'Nator', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1040 => array('customer_name' => 'Abu Beauty Con.', 'address' => 'Chowkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1041 => array('customer_name' => 'S.I Traders', 'address' => '', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1042 => array('customer_name' => 'Uzma Pharma', 'address' => 'West Tejturi Bazar, Farmgate', 'mobile' => '01728721641', 'shipping_address' => 'NULL'),
            // 1043 => array('customer_name' => 'Bilash Dep. Store Moulbibazar', 'address' => 'Moulbibazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1044 => array('customer_name' => 'Bilash Dep. Store Srimongal', 'address' => 'Srimongal', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1045 => array('customer_name' => 'TFN', 'address' => 'Uttara', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
            // 1046 => array('customer_name' => 'One Beauty Shop', 'address' => 'Chawkbazar', 'mobile' => '00000000000', 'shipping_address' => 'NULL'),
        );
    }
    public function mergeNatId()
    {
        $customers = DB::table('customerinfos')
            ->get();
        // print '<pre>';
        // print_r($customers);
        // return;
        $nid = 1000;
        foreach($customers as $key => $customer){
            DB::table('customerinfos')
            ->where('cus_id',$customer->cus_id)
            ->update([
                'national_id' => $nid,
            ]);
            $nid++;
        }
    }
}
