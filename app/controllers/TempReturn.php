<?php

class TempReturn extends \BaseController {

    public $timestamp;
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
		$this->timestamp = date('Y-m-d H:i:s');
    }
	public function returnQtyFromStock()
	{
           
            $payment_types = DB::table('paymenttypes')->where('status', '=', 1)->get();
                           $payment_type=array();
                           foreach ($payment_types as $value)
                            $payment_type[$value->payment_type_id]=$value->payment_type_name;

                return View::make('admin.items.qtyReturn.returnFromStock',compact('payment_type'));

	}
        public function stockItemAutoSuggestion(){
            $term = Input::get('q');
                $search_items = DB::table('stockitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                        ->where('stockitems.status', '=', 1)
                        ->where('iteminfos.item_name', 'LIKE', '%'. $term .'%')
			->orWhere('iteminfos.upc_code', '=',$term)
                        ->groupBy('stockitems.item_id')
			->get();

                foreach($search_items as $key => $item):
			$upc_code  =$item->upc_code;
                        $item_name = $item->item_name." (".$upc_code.")";
			$item_info = $upc_code.'|'.$item_name;
			echo  "$item_info\n";
		endforeach;

	}
        public function selectDeleteSupplier()	{
		$vdata=Input::all();
		if(isset($vdata['supplier']) == 'delete'){
			Session::forget("qty_stock_return_info");
		} else{
			$qdata=DB::table('supplierinfos')
							->where('supp_id', '=', $vdata['supp_id'])
							->first();
			if($qdata){
				Session::put("qty_stock_return_info.supp_id", $vdata['supp_id']);
				Session::put("qty_stock_return_info.supp_or_comp_name", $qdata->supp_or_comp_name);
			}
		}
		return Redirect::to('admin/returnQtyFromStock');

	}


       public function addReturnQtyFromStock()
	{
            
            $upc_code=Input::get('item_id');
            $data=DB::table('stockitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                        ->select('stockitems.stock_item_id','stockitems.item_id','stockitems.price_id','stockitems.available_quantity', 'iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price')
                        ->where('stockitems.status', '=', 1)
                        ->where('iteminfos.upc_code', '=', $upc_code)
                        ->get();


            foreach($data as $value){
                $item_info=array();
                $item_info['stock_item_id']=$stock_item_id=$value->stock_item_id;
                $item_info['item_id']=$value->item_id;
                $item_info['item_name']=$value->item_name;
                $item_info['price_id']=$value->price_id;
                $item_info['purchase_price']=$value->purchase_price;
                $item_info['sale_price']=$value->sale_price;
                 $item_info['discount']=0;
                $item_info['available_quantity']=$value->available_quantity;
                if($value->available_quantity>0)
                $item_info['quantity']=1;
                else
                continue;

                $item_info['total']=$value->purchase_price*$item_info['quantity'];


                    Session::put("returnFromStock.$stock_item_id", $item_info);

          }

        return Redirect::to('admin/returnQtyFromStock');
	}

   public function returnStockEditDeleteItem(){
  
		$vdata=Input::all();
		$stock_item_id=$vdata['stock_item_id'];

               // echo $stock_item_id;exit;
                
		if($vdata['edit_delete']=='edit'){
		$item_info=array();

                $item_info['stock_item_id']=$stock_item_id;
                $item_info['item_id']=$vdata['item_id'];
                $item_info['item_name']=$vdata['item_name'];
                $item_info['price_id']=$vdata['price_id'];
                $item_info['purchase_price']=$vdata['purchase_price'];
                $item_info['sale_price']=$vdata['sale_price'];
                 $item_info['discount']=0;
                $val=DB::table('stockitems')
                        ->where('stock_item_id', '=', $stock_item_id)
                        ->first();
                $item_info['available_quantity']=$val->available_quantity;
                if($vdata['quantity']>$val->available_quantity){
                    $item_info['quantity']=$val->available_quantity;
                }
                else{
                    $item_info['quantity']=$vdata['quantity'];
                }
                if($vdata['quantity']==0)
                    $item_info['quantity']=Session::get("returnFromStock.$stock_item_id.quantity");

                   
                $item_info['total']=($vdata['purchase_price']*$item_info['quantity']);
                $item_info['total']=round($item_info['total'],2);

                Session::put("returnFromStock.$stock_item_id", $item_info);

		}
		else{
			Session::forget("returnFromStock.$stock_item_id");
		}

       return Redirect::to('admin/returnQtyFromStock');

	}

public function invoiceAndStockReturn()
   {
        if(!Session::get("returnFromStock"))
            return Redirect::to('admin/returnQtyFromStock')->with('errorMsg', "Sorry!  now you do not select item for return");
            #### ----  Invoice Part ----####
       DB::beginTransaction();
	try {

        $vdata=Input::all();
                 $data=array();
                 $supp_id=Session::get('qty_stock_return_info.supp_id');
                 $sup_invoice_id="16012110000000";
                 $payment_type_id=$vdata['payment_type_id'];
                 $less_taka=$vdata['less_taka'];

                 $pay_amount=$vdata['pay_amount'];
                 $transaction_date=$vdata['date'];
                 $created_by=Session::get('emp_id');
                 $created_at=$this->timestamp;

                 $year=date("y");
                 $month=date("m");
                 $day=date("d");
                 $date=$year.$month.$day;


                $insert = DB::select("insert into supplierreturninvoices (sup_r_invoice_id,sup_invoice_id,supp_id,payment_type_id,amount,less_amount,transaction_date,created_by,created_at) values (
                                                    ifnull (concat('$date',1+(
									SELECT right(sup_re_inv.sup_r_invoice_id, 8) AS LAST8 FROM supplierreturninvoices as sup_re_inv
									  where( (SELECT left(sup_re_inv.sup_r_invoice_id, 6)='$date'))
									  order by LAST8 desc limit 1)),concat('$date','10000000')),'$sup_invoice_id','$supp_id','$payment_type_id','$pay_amount','$less_taka','$transaction_date','$created_by','$created_at')");

                 $last_insert_id =DB::getPdo()->lastInsertId();

                 $value = DB::table('supplierreturninvoices')->select('sup_r_invoice_id')
                                        ->where('id', '=', $last_insert_id)
                                        ->first();
                $sup_r_invoice_id=$value->sup_r_invoice_id;

              
            #### ---- End of Invoice Part ----####


            ### --- Item purchase Return Part --- ###

                $receipt_return_item_infos=array();
                 foreach(Session::get("returnFromStock") as $eachItem){
			 $receipt_return_item_infos[]=$eachItem;


                    $purchaseReturnData=array();
                    $purchaseReturnData['sup_r_invoice_id']=$sup_r_invoice_id;
                    $purchaseReturnData['item_id']=$eachItem['item_id'];
                    $item_id=$purchaseReturnData['item_id'];
                    $purchaseReturnData['price_id']=$eachItem['price_id'];
                    $price_id=$purchaseReturnData['price_id'];
                    $purchaseReturnData['quantity']=$eachItem['quantity'];
                    $purchaseReturnData['discount']=$eachItem['discount'];
                    $purchaseReturnData['amount']=$eachItem['total'];
                    $purchaseReturnData['created_by']=Session::get('emp_id');
                    $purchaseReturnData['created_at']=$this->timestamp;
                    $stock_item_id=$eachItem['stock_item_id'];

                    $purchaseReturn= DB::table('purchasereturntosupplier')->insert($purchaseReturnData);
                       if($purchaseReturn){
                       Session::forget("returnFromStock.$stock_item_id");

                       ###--- Stock item decreasing with super key consider ---###

                            $decreaseItem=DB::table('stockitems')
                                ->where('stockitems.item_id', '=', $item_id)
                                ->where('stockitems.price_id', '=', $price_id)
                                ->decrement('available_quantity', $purchaseReturnData['quantity']);
                       ###--- Ending Godown item increasing with super key consider ---###


                       }
                 }


                $receipt_info=array();
                $receipt_info['supp_or_comp_name']=Session::get('qty_stock_return_info.supp_or_comp_name');
                $receipt_info['date']=$transaction_date;
                $receipt_info['created_at']=$this->timestamp;
                $receipt_info['invoice_id']=$sup_r_invoice_id;
                $emp_info=DB::table('empinfos')
                        ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                        ->first();
                $payment_type_info=DB::table('paymenttypes')
                        ->where('paymenttypes.payment_type_id', '=', $payment_type_id)
                        ->first();
                $receipt_info['emp_name']=$emp_info->user_name;
                $receipt_info['payment_type_name']=$payment_type_info->payment_type_name;
                $receipt_info['less_amount']=$less_taka;
                $receipt_info['total_amount']=$pay_amount;
                 Session::forget("qty_stock_return_info");
                 DB::table('stockitems')
                    ->where('available_quantity', '<=', 0)
                    ->update(array('status' => 0));
                $company_info=DB::table('companyprofiles')
                                  ->first();

            DB::commit();
            return Redirect::to('admin/returnStockReceipt')->with('receipt_return_item_infos', $receipt_return_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
       } catch (Exception $e) {
            DB::rollback();
            return Redirect::to('admin/returnQtyFromStock')->with('errorMsg', 'Something is wrong in Stock Quantity returned.');
	}

 }
 
 public function returnStockReceipt()
	{
		return View::make('return.purchaseReturnReceipt');
	}
        
 
 public function returnQtyFromGodown()
	{
           
            $payment_types = DB::table('paymenttypes')->where('status', '=', 1)->get();
                           $payment_type=array();
                           foreach ($payment_types as $value)
                            $payment_type[$value->payment_type_id]=$value->payment_type_name;

                return View::make('admin.items.qtyReturn.returnFromGodown',compact('payment_type'));

	}
        
        public function godownItemAutoSuggestion(){
            $term = Input::get('q');
                $search_items = DB::table('godownitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'godownitems.item_id')
                        ->where('godownitems.status', '=', 1)
                        ->where('iteminfos.item_name', 'LIKE', '%'. $term .'%')
			->orWhere('iteminfos.upc_code', '=',$term)
                        ->groupBy('godownitems.item_id')
			->get();

                foreach($search_items as $key => $item):
			$upc_code  =$item->upc_code;
                        $item_name = $item->item_name." (".$upc_code.")";
			$item_info = $upc_code.'|'.$item_name;
			echo  "$item_info\n";
		endforeach;

	}
        public function selectDeleteSupplierGodown()	{
            
		$vdata=Input::all();
		if(isset($vdata['supplier']) == 'delete'){
			Session::forget("qty_godown_return_info");
		} else{
			$qdata=DB::table('supplierinfos')
							->where('supp_id', '=', $vdata['supp_id'])
							->first();
			if($qdata){
				Session::put("qty_godown_return_info.supp_id", $vdata['supp_id']);
				Session::put("qty_godown_return_info.supp_or_comp_name", $qdata->supp_or_comp_name);
			}
		}
		return Redirect::to('admin/returnQtyFromGodown');

	}
        public function addReturnQtyFromGodown()
	{
            
            
            $upc_code=Input::get('item_id');
            $data=DB::table('godownitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'godownitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'godownitems.price_id')
                        ->select('godownitems.godown_item_id','godownitems.item_id','godownitems.price_id','godownitems.available_quantity', 'iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price')
                        ->where('godownitems.status', '=', 1)
                        ->where('iteminfos.upc_code', '=', $upc_code)
                        ->get();


            foreach($data as $value){
                $item_info=array();
                $item_info['godown_item_id']=$godown_item_id=$value->godown_item_id;
                $item_info['item_id']=$value->item_id;
                $item_info['item_name']=$value->item_name;
                $item_info['price_id']=$value->price_id;
                $item_info['purchase_price']=$value->purchase_price;
                $item_info['sale_price']=$value->sale_price;
                 $item_info['discount']=0;
                $item_info['available_quantity']=$value->available_quantity;
                if($value->available_quantity>0)
                $item_info['quantity']=1;
                else
                continue;

                $item_info['total']=$value->purchase_price*$item_info['quantity'];


                    Session::put("returnFromGodown.$godown_item_id", $item_info);

          }

        return Redirect::to('admin/returnQtyFromGodown');
	}

   public function returnGodownEditDeleteItem(){
  
		$vdata=Input::all();
		$godown_item_id=$vdata['godown_item_id_'];
                
		if($vdata['edit_delete']=='edit'){
		$item_info=array();

                $item_info['godown_item_id']=$godown_item_id;
                $item_info['item_id']=$vdata['item_id'];
                $item_info['item_name']=$vdata['item_name'];
                $item_info['price_id']=$vdata['price_id'];
                $item_info['purchase_price']=$vdata['purchase_price'];
                $item_info['sale_price']=$vdata['sale_price'];
                 $item_info['discount']=0;
                $val=DB::table('godownitems')
                        ->where('godown_item_id', '=', $godown_item_id)
                        ->first();
                $item_info['available_quantity']=$val->available_quantity;
                if($vdata['quantity']>$val->available_quantity){
                    $item_info['quantity']=$val->available_quantity;
                }
                else{
                    $item_info['quantity']=$vdata['quantity'];
                }
                if($vdata['quantity']==0)
                    $item_info['quantity']=Session::get("returnFromGodown.$godown_item_id.quantity");

                   
                $item_info['total']=($vdata['purchase_price']*$item_info['quantity']);
                $item_info['total']=round($item_info['total'],2);

                Session::put("returnFromGodown.$godown_item_id", $item_info);

		}
		else{
			Session::forget("returnFromGodown.$godown_item_id");
		}

       return Redirect::to('admin/returnQtyFromGodown');

	}
  public function invoiceAndGodownReturn()
   {

        if(!Session::get("returnFromGodown"))
            return Redirect::to('admin/returnQtyFromGodown')->with('errorMsg', "Sorry!  now you do not select item for return");
            #### ----  Invoice Part ----####
        DB::beginTransaction();
	try {

        $vdata=Input::all();
       // echo'<pre>';print_r($vdata);exit;
                 $data=array();
                 $supp_id=Session::get('qty_godown_return_info.supp_id');
                 //$sup_invoice_id="16012110000000";
                 $sup_invoice_table = DB::table('supinvoices')->orderBy('id','ASC')->first();
                 $sup_invoice_id=$sup_invoice_table->sup_invoice_id;
                 $payment_type_id=$vdata['payment_type_id'];
                 $less_taka=$vdata['less_taka'];

                 $pay_amount=$vdata['pay_amount'];
                 $transaction_date=$vdata['date'];
                 $created_by=Session::get('emp_id');
                 $created_at=$this->timestamp;

                 $year=date("y");
                 $month=date("m");
                 $day=date("d");
                 $date=$year.$month.$day;


                $insert = DB::select("insert into supplierreturninvoices (sup_r_invoice_id,sup_invoice_id,supp_id,payment_type_id,amount,less_amount,transaction_date,created_by,created_at) values (
                                                    ifnull (concat('$date',1+(
									SELECT right(sup_re_inv.sup_r_invoice_id, 8) AS LAST8 FROM supplierreturninvoices as sup_re_inv
									  where( (SELECT left(sup_re_inv.sup_r_invoice_id, 6)='$date'))
									  order by LAST8 desc limit 1)),concat('$date','10000000')),'$sup_invoice_id','$supp_id','$payment_type_id','$pay_amount','$less_taka','$transaction_date','$created_by','$created_at')");

                 $last_insert_id =DB::getPdo()->lastInsertId();

                 $value = DB::table('supplierreturninvoices')->select('sup_r_invoice_id')
                                        ->where('id', '=', $last_insert_id)
                                        ->first();
                $sup_r_invoice_id=$value->sup_r_invoice_id;

              
            #### ---- End of Invoice Part ----####


            ### --- Item purchase Return Part --- ###

                $receipt_return_item_infos=array();
                 foreach(Session::get("returnFromGodown") as $eachItem){
			 $receipt_return_item_infos[]=$eachItem;


                    $purchaseReturnData=array();
                    $purchaseReturnData['sup_r_invoice_id']=$sup_r_invoice_id;
                    $purchaseReturnData['item_id']=$eachItem['item_id'];
                    $item_id=$purchaseReturnData['item_id'];
                    $purchaseReturnData['price_id']=$eachItem['price_id'];
                    $price_id=$purchaseReturnData['price_id'];
                    $purchaseReturnData['quantity']=$eachItem['quantity'];
                    $purchaseReturnData['discount']=$eachItem['discount'];
                    $purchaseReturnData['amount']=$eachItem['total'];
                    $purchaseReturnData['created_by']=Session::get('emp_id');
                    $purchaseReturnData['created_at']=$this->timestamp;
                    $godown_item_id=$eachItem['godown_item_id'];

                    $purchaseReturn= DB::table('purchasereturntosupplier')->insert($purchaseReturnData);
                       if($purchaseReturn){
                       Session::forget("returnFromGodown.$godown_item_id");

                       ###--- Stock item decreasing with super key consider ---###

                            $decreaseItem=DB::table('godownitems')
                                ->where('godownitems.item_id', '=', $item_id)
                                ->where('godownitems.price_id', '=', $price_id)
                                ->decrement('available_quantity', $purchaseReturnData['quantity']);
                       ###--- Ending Godown item increasing with super key consider ---###


                       }
                 }


                $receipt_info=array();
                $receipt_info['supp_or_comp_name']=Session::get('qty_godown_return_info.supp_or_comp_name');
                $receipt_info['date']=$transaction_date;
                $receipt_info['created_at']=$this->timestamp;
                $receipt_info['invoice_id']=$sup_r_invoice_id;
                $emp_info=DB::table('empinfos')
                        ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                        ->first();
                $payment_type_info=DB::table('paymenttypes')
                        ->where('paymenttypes.payment_type_id', '=', $payment_type_id)
                        ->first();
                $receipt_info['emp_name']=$emp_info->user_name;
                $receipt_info['payment_type_name']=$payment_type_info->payment_type_name;
                $receipt_info['less_amount']=$less_taka;
                $receipt_info['total_amount']=$pay_amount;
                 Session::forget("qty_godown_return_info");
                 DB::table('godownitems')
                    ->where('available_quantity', '<=', 0)
                    ->update(array('status' => 0));
                $company_info=DB::table('companyprofiles')
                                  ->first();

            DB::commit();
            return Redirect::to('admin/returnStockReceipt')->with('receipt_return_item_infos', $receipt_return_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
       } catch (Exception $e) {
            DB::rollback();
            return Redirect::to('admin/returnQtyFromGodown')->with('errorMsg', 'Something is wrong in Godown Quantity returned.');
	}

 }
 public function returnGodownReceipt()
	{
		return View::make('return.purchaseReturnReceipt');
	}
public function returnQtyFromCustomer()
	{
           
            $payment_types = DB::table('paymenttypes')->where('status', '=', 1)->get();
                           $payment_type=array();
                           foreach ($payment_types as $value)
                            $payment_type[$value->payment_type_id]=$value->payment_type_name;

                return View::make('admin.items.qtyReturn.returnQtyFromCustomer',compact('payment_type'));

	}
        
        public function itemAutoSugForCusReturn(){
            $term = Input::get('q');
                $search_items = DB::table('stockitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                        ->where('stockitems.status',  '=', 1)
                        ->where('iteminfos.item_name', 'LIKE', '%'. $term .'%')
			->orWhere('iteminfos.upc_code', '=',$term)
                        ->groupBy('stockitems.item_id')
			->get();

                foreach($search_items as $key => $item):
			$upc_code  =$item->upc_code;
                        $item_name = $item->item_name." (".$upc_code.")";
			$item_info = $upc_code.'|'.$item_name;
			echo  "$item_info\n";
		endforeach;

	}
        
        
        public function addItemFromCustomer()
	{
            $upc_code=Input::get('item_id');
            $data=DB::table('stockitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                        ->select('stockitems.stock_item_id','stockitems.item_id','stockitems.price_id','stockitems.available_quantity', 'iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price')
                        ->where('stockitems.status', '=', 1)
                        ->where('iteminfos.upc_code', '=', $upc_code)
                        ->get();

            foreach($data as $value){
                $item_info=array();
                $item_info['stock_item_id']=$stock_item_id=$value->stock_item_id;
                $item_info['item_id']=$value->item_id;
                $item_info['item_name']=$value->item_name;
                $item_info['price_id']=$value->price_id;
                $item_info['purchase_price']=$value->purchase_price;
                $item_info['sale_price']=$value->sale_price;
                $item_info['discount']=0;
                $item_info['tax']=0;
                $item_info['available_quantity']=$value->available_quantity;
                if(Session::get("returnFromCustomer.$stock_item_id")){
                  $item_info['quantity']=Session::get("returnFromCustomer.$stock_item_id.quantity")+1;
                }else{
                $item_info['quantity']=1;
                }
                $item_info['total']=$value->sale_price*$item_info['quantity'];

                    Session::put("returnFromCustomer.$stock_item_id", $item_info);

          }

        return Redirect::to('admin/returnQtyFromCustomer');
	}

        
        
   public function editDeleteItemFromCus(){
  
		$vdata=Input::all();
		$stock_item_id=$vdata['stock_item_id'];

		if($vdata['edit_delete']=='edit'){
		$item_info=array();
                $item_info['stock_item_id']=$stock_item_id;
                $item_info['item_id']=$vdata['item_id'];
                $item_info['item_name']=$vdata['item_name'];
                $item_info['price_id']=$vdata['price_id'];
                $item_info['purchase_price']=$vdata['purchase_price'];
                $item_info['sale_price']=$vdata['sale_price'];
                $item_info['discount']=0;
                $item_info['tax']=0;
                $val=DB::table('stockitems')
                        ->where('stock_item_id', '=', $stock_item_id)
                        ->first();
                $item_info['available_quantity']=$val->available_quantity;
               
                    $item_info['quantity']=$vdata['quantity'];
                if($vdata['quantity']==0)
                    $item_info['quantity']=Session::get("returnFromCustomer.$stock_item_id.quantity");

                   
                $item_info['total']=($vdata['sale_price']*$item_info['quantity']);
                $item_info['total']=round($item_info['total'],2);

                Session::put("returnFromCustomer.$stock_item_id", $item_info);

		}
		else{
			Session::forget("returnFromCustomer.$stock_item_id");
		}

       return Redirect::to('admin/returnQtyFromCustomer');

	}
        
    
        
        //Customer auto suggest
	
    public function selectDeleteCustomer(){
        
		$vdata=Input::all();
                //echo '<pre>';print_r(Session::get("sale_invoice_info"));exit;
		if(isset($vdata['customer']) == 'delete'){
                    
			Session::forget("return_cus_invoice_info");
			
		} else{
                  	$qdata=DB::table('customerinfos')
                                        ->leftjoin('customertypes', 'customertypes.cus_type_id', '=', 'customerinfos.cus_type_id')
					->where('cus_id', '=', $vdata['cus_id'])
					->first();
			if($qdata){
				Session::put("return_cus_invoice_info.cus_id", $vdata['cus_id']);
				Session::put("return_cus_invoice_info.user_name", $qdata->user_name);
                                Session::put("return_cus_invoice_info.customer_point", $qdata->point);
                                Session::put("return_cus_invoice_info.customer_due", $qdata->due);
			}
		}
                
		return Redirect::to('admin/returnQtyFromCustomer');

	}
        
        
        public function invoiceAndSaleReturnItemWise()
	{
          
        if(!Session::get("returnFromCustomer"))
            return Redirect::to('admin/returnQtyFromCustomer')->with('errorMsg', "Sorry!  You do not select item for return");
            #### ----  Invoice Part ----####

        DB::beginTransaction();
	try {
        $vdata=Input::all();
        //echo'<pre>';print_r($vdata);exit;
                 $data=array();
                 $cus_id=Session::get('return_cus_invoice_info.cus_id');
                 $sale_invoice_id=16012110000000;
                 $payment_type_id=$vdata['payment_type_id'];
                 $less_taka=$vdata['less_taka'];
                 
                 $pay_amount=$vdata['pay_amount'];
                 $transaction_date=$vdata['date'];
                 $created_by=Session::get('emp_id');
                 $created_at=$this->timestamp;
               
                 $year=date("y");
                 $month=date("m");
                 $day=date("d");
                 $date=$year.$month.$day;

                $insert = DB::select("insert into salereturninvoices (sale_r_invoice_id,sale_invoice_id,cus_id,payment_type_id,amount,less_amount,transaction_date,created_by,created_at) values (
                                                    ifnull (concat('$date',1+(
									SELECT right(sl_re_inv.sale_r_invoice_id, 8) AS LAST8 FROM salereturninvoices as sl_re_inv
									  where( (SELECT left(sl_re_inv.sale_r_invoice_id, 6)='$date'))
									  order by LAST8 desc limit 1)),concat('$date','10000000')),'$sale_invoice_id','$cus_id','$payment_type_id','$pay_amount','$less_taka','$transaction_date','$created_by','$created_at')");

                 $last_insert_id =DB::getPdo()->lastInsertId();


                 $value = DB::table('salereturninvoices')->select('sale_r_invoice_id')
                                        ->where('id', '=', $last_insert_id)
                                        ->first();
                $sale_r_invoice_id=$value->sale_r_invoice_id;
               
                
            #### ---- End of Invoice Part ----####


            ### --- Item Sale Return Part --- ###



                $receipt_return_item_infos=array();
                 foreach(Session::get("returnFromCustomer") as $eachItem){
                   // echo '<pre>';dd($eachItem);exit;
			 $receipt_return_item_infos[]=$eachItem;

                    
                    $saleReturnData=array();
                    $saleReturnData['sale_r_invoice_id']=$sale_r_invoice_id;
                    $saleReturnData['item_id']=$item_id=$eachItem['item_id'];
                    $saleReturnData['price_id']=$price_id=$eachItem['price_id'];
                    $saleReturnData['quantity']=$eachItem['quantity'];
                    $saleReturnData['discount']=$eachItem['discount'];
                    $saleReturnData['tax']=$eachItem['tax'];
                    $saleReturnData['amount']=$eachItem['total'];
                    $saleReturnData['created_by']=Session::get('emp_id');
                    $saleReturnData['created_at']=$this->timestamp;
                    $stock_item_id=$eachItem['stock_item_id'];

                    $saleReturn= DB::table('salereturntostocks')->insert($saleReturnData);
                       if($saleReturn){
                       Session::forget("returnFromCustomer.$stock_item_id");
                       
                       ###--- Stock item increasing with super key consider ---###

                            $stockItemInfo = DB::table('stockitems')
                                                    ->where('item_id', '=', $item_id)
                                                    ->where('price_id', '=', $price_id)
                                                    ->first();
                            if(!$stockItemInfo){
                                $insertData=array();
                                $insertData['item_id']=$item_id;
                                $insertData['price_id']=$price_id;
                                $insertData['available_quantity']=$saleReturnData['quantity'];
                                $insertData['created_by']=Session::get('emp_id');
                                $insertData['created_at']=$this->timestamp;

                                $insert=DB::table('stockitems')->insert($insertData);
                            }
                            else{
                                if($stockItemInfo->status==0){
                                   $update=DB::table('stockitems')
                                            ->where('stock_item_id', '=', $stockItemInfo->stock_item_id)
                                            ->update(array('status' => 1));
                                }
                                $increasingQuantity=DB::table('stockitems')
                                            ->where('stock_item_id', '=', $stockItemInfo->stock_item_id)
                                            ->increment('available_quantity', $saleReturnData['quantity']);
                            }
                       ###--- Ending Godown item increasing with super key consider ---###


                       }

                 }
                 
               // echo '<pre>';print_r(Session::get("invoice_info"));exit;

                 $receipt_info=array();
                $receipt_info['customer_name']=Session::get('sale_return_invoice_info.customer_name');
                $receipt_info['date']=$transaction_date;
                $receipt_info['created_at']=$this->timestamp;
                $receipt_info['invoice_id']=$sale_r_invoice_id;
                $emp_info=DB::table('empinfos')
                        ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                        ->first();
                $payment_type_info=DB::table('paymenttypes')
                        ->where('paymenttypes.payment_type_id', '=', $payment_type_id)
                        ->first();
                $receipt_info['emp_name']=$emp_info->user_name;
                $receipt_info['payment_type_name']=$payment_type_info->payment_type_name;
                $receipt_info['less_amount']=$less_taka;
                $receipt_info['total_amount']=$pay_amount;
              // echo'<pre>';print_r($receipt_info);exit;
                 Session::forget("return_cus_invoice_info");
                $company_info=DB::table('companyprofiles')
                                  ->first();
              DB::commit();
             return Redirect::to('admin/itemWisereturnReceipt')->with('receipt_return_item_infos', $receipt_return_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
             } catch (Exception $e) {
                    return $e->getMessage();
                    //return  DB::getQueryLog();
                         DB::rollback();
			return Redirect::to('admin/returnQtyFromCustomer')->with('errorMsg', 'Something is wrong in sale returned.');
	}

    }
    
     public function itemWiseReturnReceipt()
	{
		return View::make('admin.items.qtyReturn.itemWiseCusReturnReceipt');
	}

  
        
        

}
