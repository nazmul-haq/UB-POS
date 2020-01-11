<?php

class SupplierInfoController extends \BaseController {

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
	public function index()
	{
		return View::make('admin.supplier.viewSuppliers');
	}



        public function getSupplierData() {
        return Datatable::query(DB::table('supplierinfos')->where('status', 1))
        		->addColumn('supp_id', function($model) {
                    $html='<a href="'.URL::to("admin/supplier/transactionDetails/$model->supp_id").'">'.$model->supp_id.'</a>';
                    return $html;
                })
                ->showColumns('supp_or_comp_name','mobile', 'due')
                ->addColumn('action', function($model) {
					$html = '<a class="btn btn-info btn-small" href="'.URL::to("admin/supplier/items/$model->supp_id").'">Items</a>'.' | '.
							'<a class="btn btn-success btn-small" href="'.URL::to("admin/supplier/purchaseOrder/$model->supp_id").'">Purchase Order</a>'.' | '.
							'<a class="btn btn-success btn-small" href="'.URL::to("admin/supplier/payment/$model->supp_id").'">Pay</a>'.' | '.
							'<a class="btn btn-primary btn-small" href="#" onclick="supplierDetails('.$model->supp_id.')" data-toggle="modal" data-target="#viewSupplier"><i class="icon-zoom-in"></i></a>' .' | '.
                            '<a class="btn btn-info btn-small" href="#" onclick="updateSupplier('.$model->supp_id.')" data-toggle="modal" data-target="#editSupplier"><i class="icon-edit"></i></a>' .' | '.
                            '<a class="btn btn-warning btn-small" href="#" onclick="return deleteConfirm('.$model->supp_id.')" id="'.$model->supp_id.'"><i class="icon-remove"></i></a>';
                                        
					return $html;
                })
                ->searchColumns('supp_id','supp_or_comp_name','user_name','mobile')
                ->setSearchWithAlias()
                ->orderColumns('supp_or_comp_name','user_name')
                ->make();
    }


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	 function transactionDetails($supp_id) {

        $get_supplier = DB::table('supplierinfos')
                ->where('supp_id', $supp_id)
                ->first();
         $purchase = DB::select(DB::raw("select sum(supinvoices.discount) as total_discount, sum(supinvoices.amount) as total_purchase_amount,sum(supinvoices.pay) as total_paid
										from supinvoices
										where supp_id='$supp_id'
										"))[0];
         $purchaseReturn = DB::select(DB::raw("select sum(supplierreturninvoices.less_amount) as total_less_amount, sum(supplierreturninvoices.amount) as total_purchase_return_amount
										from supplierreturninvoices
										where supp_id='$supp_id'"))[0];


        $pre_date 	= date('Y-m-d');
        $sub_date	= strtotime($pre_date. '-10 days');
        $cal_date	= date('Y-m-d', $sub_date);
        $from_date 	= empty(Input::get('from')) ? $cal_date : Input::get('from');
        $to 		= empty(Input::get('to')) ? $pre_date : Input::get('to');
        $display_date = $from_date.' '.$to;
        $date_exp	= explode(' ', $display_date);
        $report_type=Input::get('report_type');

        if($report_type==2) {
        	$reports= DB::table('supplierreturninvoices')
                    ->leftjoin('empinfos', 'empinfos.emp_id', '=', 'supplierreturninvoices.created_by')
                    ->leftjoin('paymenttypes', 'paymenttypes.payment_type_id', '=', 'supplierreturninvoices.payment_type_id')
                    ->select('supplierreturninvoices.*','paymenttypes.payment_type_name','empinfos.user_name as purchased_return_by')
                    ->where('supplierreturninvoices.status', '=', 1)
                    ->where('supplierreturninvoices.supp_id', '=', $supp_id)
                    ->whereBetween('supplierreturninvoices.transaction_date', array($from_date, $to))
                    ->orderBy('supplierreturninvoices.transaction_date', 'desc')
                    ->get();
        }else {
        	
             $reports= DB::table('supinvoices')
                    ->leftjoin('empinfos', 'empinfos.emp_id', '=', 'supinvoices.created_by')
                    ->leftjoin('paymenttypes', 'paymenttypes.payment_type_id', '=', 'supinvoices.payment_type_id')
                    ->select('supinvoices.*','paymenttypes.payment_type_name','empinfos.user_name as purchased_by')
                    ->where('supinvoices.status', '=', 1)
                    ->where('supinvoices.supp_id', '=', $supp_id)
                    ->whereBetween('supinvoices.transaction_date', array($from_date, $to))
                    ->orderBy('supinvoices.transaction_date', 'desc')
                    ->get();
        }
       //echo '<pre>';print_r($purchaseReturn);exit;
        return View::make('admin.supplier.transactionDetails',compact('get_supplier','date_exp','purchase','purchaseReturn','reports','report_type'));
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
           $vdata = Input::all();

		try{

                        

			$validator = Validator::make($vdata, Supplierinfo::$supp_rules);

			if($validator->fails()) {

				return Redirect::back()->withErrors($validator)->withInput();
			}
                        $data = array(
					'supp_or_comp_name'         => Input::get('supp_or_comp_name'),
                                        'user_name' 			=> Input::get('user_name'),
                                        'mobile' 			=> Input::get('mobile'),
                                        'email' 			=> Input::get('email'),
                                        'permanent_address' 		=> Input::get('permanent_address'),
                                        'present_address' 		=> Input::get('present_address'),
                                        'created_at'                    => $this->timestamp,
					'created_by'                    =>  Session::get('emp_id')
			);

			$insert = DB::table('supplierinfos')->insert($data);
                       
			if($insert) {
				return Redirect::to('admin/suppliers')->with('message', 'Added Supplier Successfully');
			}
			return Redirect::to('admin/suppliers')->with('errorMsg', 'Something must be wrong! Please check.');
		} catch(Exception $e){

			return Redirect::to('admin/suppliers')->with('errorMsg', 'Duplicated Occurred');
		}
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            $supplier_info = DB::table('supplierinfos')->where('supp_id', $id)->first();

                return View::make('admin.supplier.viewSupplierModal',compact('supplier_info'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$supplier_info = DB::table('supplierinfos')->where('supp_id', $id)->first();
		return View::make('admin.supplier.editSupplierModal',compact('supplier_info'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update()
	{
		$vdata=input::all();
//               
                try{

			$validator = Validator::make($vdata, Supplierinfo::$supp_rules);

			if($validator->fails()) {

				return Redirect::back()->withErrors($validator)->withInput();
			}
                        $data = array(
					'supp_id'                       => Input::get('supp_id'),
                                        'supp_or_comp_name'             => Input::get('supp_or_comp_name'),
                                        'user_name' 			=> Input::get('user_name'),
                                        'mobile' 			=> Input::get('mobile'),
                                        'email' 			=> Input::get('email'),
                                        'permanent_address' 		=> Input::get('permanent_address'),
                                        'present_address' 		=> Input::get('present_address'),
                                        'updated_at'                    => $this->timestamp,
					'updated_by'                    =>  Session::get('emp_id')
			);
                       


			$update=DB::table('supplierinfos')
                                    ->where('supp_id', $data['supp_id'])
                                    ->update($data);

			if($update) {
				return Redirect::to('admin/suppliers')->with('message', 'Update Supplier Successfully');
			}
			return Redirect::to('admin/suppliers')->with('errorMsg', 'Something must be wrong! Please check.');
		} catch(Exception $e){

                        

			return Redirect::to('admin/suppliers')->with('errorMsg', 'Duplicated Occured');
		}

	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($supp_id)
	{
         
		$suppDelete = DB::table('supplierinfos')
			->where('supp_id', $supp_id)
			->update(array('status' => 0, 'updated_by' => Session::get('emp_id'), 'updated_at' => $this->timestamp));
		if($suppDelete){
			return Response::json(['status' => 'success']);
		}
		return Response::json(['status' => 'error']);

	}

	public function viewSupplierItems($supp_id){

	$itemsInfo = DB::table('itempurchases as ip')
                ->select('ip.i_purchase_id', 'ip.sup_invoice_id', 'ip.item_id', 'i.item_name',  'i.upc_code', 'i.status', 'ic.category_name', 'si.supp_id', 'spi.user_name')
                ->leftJoin('iteminfos as i', 'i.item_id', '=', 'ip.item_id')
                ->leftJoin('itemcategorys as ic', 'ic.category_id', '=', 'i.category_id')
                ->leftJoin('supinvoices as si', 'si.sup_invoice_id', '=', 'ip.sup_invoice_id')
                ->leftJoin('supplierinfos as spi', 'spi.supp_id', '=', 'si.supp_id')
                ->where('si.supp_id', '=', $supp_id)
                ->groupBy('ip.item_id')
                ->get();
    $supplierInfo= DB::table('supplierinfos')
                ->where('supp_id', '=', $supp_id)
                ->first();
		
		return View::make('admin.supplier.items',compact('itemsInfo','supplierInfo'));
	}
	public function viewSupplierItemsForPurchase($supp_id){


	$itemsInfo = DB::table('stockitems as s')
            ->select([
            	'i.item_id', 'ic.category_name', 
            	'com.company_name', 'i.upc_code', 
            	'i.item_name','i.offer', 
            	'p.purchase_price', 'p.price_id', 
            	'p.sale_price', 'i.tax_amount', 
            	'i.offer', 
            	's.available_quantity as available_s_qty',
            	'gi.available_quantity as available_g_qty'
            ])
            ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
            ->leftJoin('itemcategorys as ic', 'ic.category_id', '=', 'i.category_id')
            ->leftJoin('companynames as com', 'com.company_id', '=', 'i.item_company_id')
            ->leftJoin('itemlocations as l', 'i.location_id', '=', 'l.location_id')
            ->leftJoin('priceinfos as p', 's.price_id', '=', 'p.price_id')
            ->leftjoin('godownitems as gi', 'i.item_id', '=', 'gi.item_id')
            ->leftjoin('itempurchases as ip', 's.item_id', '=', 'ip.item_id')
            ->leftJoin('supinvoices', 'ip.sup_invoice_id', '=', 'supinvoices.sup_invoice_id')
            ->leftjoin('supplierinfos as si', 'supinvoices.supp_id', '=', 'si.supp_id')
            // ->where('s.status', '=', '1')
            ->where('p.status', '=', '1')
            ->where('si.supp_id', '=', $supp_id)
            ->groupBy('i.item_id')
			->get();
	// echo "<pre>";
	// print_r($itemsInfo);
	// return;
    $supplierInfo= DB::table('supplierinfos')
                ->where('supp_id', '=', $supp_id)
                ->first();
		
		return View::make('admin.supplier.purchaseOrder',compact('itemsInfo','supplierInfo'));
	}

	public function addItemToPurchaseOrder(){
        $vdata=Input::all();
        $item_id=$vdata['item_id'];

        $item_info=array();
        $item_info['item_id']=$vdata['item_id'];
        $item_info['item_name']=$vdata['item_name'];
//            $item_info['price_id']=$price_id;
        $item_info['purchase_price']=$vdata['purchase_price'];
        $item_info['sale_price']=$vdata['sale_price'];
        if($item_info['purchase_price']>$item_info['sale_price'])
            $item_info['sale_price']=$item_info['purchase_price'];

        $item_info['quantity']=$vdata['quantity'];
                if($vdata['quantity']==0)
                    $item_info['quantity']=1;

                if($vdata['discount']>($vdata['purchase_price']*$vdata['quantity']))
                    $item_info['discount']=$vdata['purchase_price']*$vdata['quantity'];
                else
                    $item_info['discount']=$vdata['discount'];

                $item_info['discount']=round($item_info['discount'],2);
        $item_info['total']=($item_info['purchase_price']*$item_info['quantity'])-$item_info['discount'];
        $item_info['total']=round($item_info['total'],2);
        Session::put("items.$item_id", $item_info);
        Session::put('is_purchase_order',true);
        Session::put("invoice_info.supp_id", $vdata['supp_id']);
        Session::put("invoice_info.supp_or_comp_name", $vdata['supp_or_comp_name']);
        return Redirect::back();
    }
	/*
	*  Supplier Payment System
	*/
	public function payment($supp_id){
		$get_supplier = DB::table('supplierinfos')
			->select('supp_id','supp_or_comp_name', 'due')
			->where('supp_id', $supp_id)
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
		
		$get_transaction_infos = DB::table('supduepayments AS suppDuePay')
							->select('suppDuePay.date as pay_date','suppDuePay.amount','empInfo.user_name','payType.payment_type_name')
							->join('paymenttypes AS payType', 'suppDuePay.payment_type_id', '=', 'payType.payment_type_id')
							->join('empinfos AS empInfo', 'suppDuePay.created_by', '=', 'empInfo.emp_id')
							->where('suppDuePay.supp_id', $supp_id)
							->whereBetween('suppDuePay.date', array($from_date, $to))
							->where('suppDuePay.status', 1)
							->orderBy('suppDuePay.s_due_payment_id', 'DESC')
							->paginate(25);  
		
		return View::make('admin.supplier.payment',compact('get_supplier', 'payment_type', 'get_company_infos', 'get_transaction_infos', 'date_exp'));
	}
	
	public function paymentSaveSupplier(){
		DB::beginTransaction();
		try{
			$data = Input::all();
			//print_r($data); exit;
			$validator = Validator::make($data, Supplierinfo::$rules_payment);
			if($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			$supp_id = Input::get('supp_id');
			$get_supp_due =  DB::table('supplierinfos')
							->select('due')
							->where('supp_id', $supp_id)
							->first();
			$due = $get_supp_due->due;
			$cal_due = $due - Input::get('amount');			
			$supp_due_update = array(
					'due' 			=>  $cal_due,
					'updated_by' 	=>  Session::get('emp_id'),
					'updated_at' 	=>  $this->timestamp
			);
			DB::table('supplierinfos')			
					->where('supp_id', $supp_id)
					->update($supp_due_update);
			
			$payment_supplier = array(
					'supp_id' 			=>  $supp_id,
					'payment_type_id' 	=>  Input::get('payment_type_id'),
					'amount'			=>  Input::get('amount'),
					'date'				=>  date('Y-m-d'),
					'created_by' 		=>  Session::get('emp_id'),
					'created_at' 		=>  $this->timestamp
			);
			$insert = DB::table('supduepayments')->insert($payment_supplier);
			DB::commit();
			return Redirect::to('admin/supplier/payment/'.$supp_id.'')->with('message', 'Added Successfully');
			
		} catch(\Exception $e){
			DB::rollback();
			Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError.".$e->errorInfo[1]);
			return Redirect::to('admin/supplier/payment/'.$supp_id.'')->with('errorMsg', $err_msg)->withInput();
		}    
	}
	


}
