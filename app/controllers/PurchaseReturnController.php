<?php

class PurchaseReturnController extends \BaseController {

    public $timestamp;
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
		$this->timestamp = date('Y-m-d H:i:s');
    }
	public function index()
	{
           
            $payment_types = DB::table('paymenttypes')->where('status', '=', 1)->get();
                           $payment_type=array();
                           foreach ($payment_types as $value)
                            $payment_type[$value->payment_type_id]=$value->payment_type_name;

                return View::make('return.purchaseReturnForm',compact('payment_type'));

	}
        public function autoInvoiceSuggest(){
            // echo$term;exit;
		$term = Input::get('q');

                $memo_ids = DB::table('supinvoices')
                        ->where('supinvoices.sup_memo_no', 'LIKE', $term .'%')
			->get();
               //  echo'<pre>';print_r($search_items);exit;
                foreach($memo_ids as $key => $memo):
			$memo_id   = $memo->sup_memo_no;
			echo  "$memo_id\n";
		endforeach;

	}


        public function returnItemAddTochart()
	{

            Session::forget("purchaseReturnItemInfo");
            Session::forget("purchase_return_invoice_info");

            $sup_memo_no = Input::get('sup_memo_no');
            $data=DB::table('supinvoices')
                        ->leftjoin('supplierinfos', 'supplierinfos.supp_id', '=', 'supinvoices.supp_id')
                        ->select('supinvoices.sup_invoice_id',
                                 'supinvoices.sup_memo_no',
                                 'supinvoices.supp_id',
                                 'supinvoices.discount',
                                 'supinvoices.amount',
                                 'supplierinfos.supp_or_comp_name',
                                 'supplierinfos.due'
                                )
                        ->where('supinvoices.sup_memo_no', '=', $sup_memo_no)
                        ->first();

            if(!$data){
                return Redirect::to('purchase/returns')->with('errorMsg', "This Memo Id is invalid");
            }
            $invoice_info=array(
                        'sup_invoice_id'  =>  $data->sup_invoice_id,
                        'sup_memo_no'     =>  $data->sup_memo_no,
                        'invoice_discount'=>  $data->discount,
			'supp_id' 	  =>  $data->supp_id,
                        'supp_or_comp_name'=>  $data->supp_or_comp_name,
                        'supplier_due'    =>  $data->due

            );
            Session::put("purchase_return_invoice_info", $invoice_info);
            
            $datas=DB::table('itempurchases')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'itempurchases.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'itempurchases.price_id')
                        ->where('itempurchases.sup_invoice_id', '=', $data->sup_invoice_id)
                        ->select('itempurchases.i_purchase_id',
                                 'itempurchases.sup_invoice_id',
                                 'itempurchases.item_id',
                                 'iteminfos.item_name',
                                 'itempurchases.price_id',
                                 'priceinfos.purchase_price',
                                 'itempurchases.quantity',
                                 'itempurchases.discount',
                                 'itempurchases.amount'
                                )
                        ->get();
            
            foreach($datas as $value){
                $i_purchase_id=$value->i_purchase_id;
                $item_info=array(
                        'i_purchase_id'   =>  $value->i_purchase_id,
			'sup_invoice_id'  =>  $value->sup_invoice_id,
                        'item_id'         =>  $value->item_id,
                        'item_name' 	  =>  $value->item_name,
                        'price_id' 	  =>  $value->price_id,
                        'purchase_price'  =>  $value->purchase_price,
                        'quantity' 	  =>  $value->quantity,
                        'discount' 	  =>  $value->discount,
                        'total' 	  =>  $value->amount,
                );

             Session::put("purchaseReturnItemInfo.$i_purchase_id", $item_info);
            }

           
        return Redirect::to('purchase/returns');

   }

   public function purchaseReturnEditDeleteItem(){


		$vdata=Input::all();
		$i_purchase_id=$vdata['i_purchase_id'];

		if($vdata['edit_delete']=='edit'){
		$item_info=array();

                $item_info['i_purchase_id']=$i_purchase_id;
                $item_info['item_id']=$vdata['item_id'];
                $item_info['item_name']=$vdata['item_name'];
                $item_info['price_id']=$vdata['price_id'];
                $item_info['purchase_price']=$vdata['purchase_price'];
                $val=DB::table('itempurchases')
                        ->where('i_purchase_id', '=', $i_purchase_id)
                        ->first();
                if($vdata['quantity']>$val->quantity){
                    $item_info['quantity']=$val->quantity;
                }
                else{
                    $item_info['quantity']=$vdata['quantity'];
                }
                if($vdata['quantity']==0)
                    $item_info['quantity']=Session::get("purchaseReturnItemInfo.$i_purchase_id.quantity");

                    $discount=Session::get("purchaseReturnItemInfo.$i_purchase_id.discount");
                    $discount_per_item=$discount/Session::get("purchaseReturnItemInfo.$i_purchase_id.quantity");
                $item_info['discount']=$discount_per_item*$item_info['quantity'];
                $item_info['discount']=round($item_info['discount'],2);
                $item_info['total']=($vdata['purchase_price']*$item_info['quantity'])-$item_info['discount'];
                $item_info['total']=round($item_info['total'],2);

                Session::put("purchaseReturnItemInfo.$i_purchase_id", $item_info);

		}
		else{
			Session::forget("purchaseReturnItemInfo.$i_purchase_id");
		}

       return Redirect::to('purchase/returns');

	}

public function invoiceAndPurchaseReturn()
   {

        

       
        if(!Session::get("purchaseReturnItemInfo"))
            return Redirect::to('purchase/returns')->with('errorMsg', "Sorry!  now you do not select item for return");
            #### ----  Invoice Part ----####
        DB::beginTransaction();
	try {

        $vdata=Input::all();

                 $data=array();
                 $supp_id=Session::get('purchase_return_invoice_info.supp_id');
                 $sup_invoice_id=Session::get('purchase_return_invoice_info.sup_invoice_id');
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


            ### --- Item Sale Return Part --- ###



                $receipt_return_item_infos=array();
                 foreach(Session::get("purchaseReturnItemInfo") as $eachItem){
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
                    $i_purchase_id=$eachItem['i_purchase_id'];

                    $purchaseReturn= DB::table('purchasereturntosupplier')->insert($purchaseReturnData);
                       if($purchaseReturn){
                       Session::forget("purchaseReturnItemInfo.$i_purchase_id");

                       ###--- Godown item decreasing with super key consider ---###

                            $decreaseItem=DB::table('godownitems')
                                ->where('godownitems.item_id', '=', $item_id)
                                ->where('godownitems.price_id', '=', $price_id)
                                ->decrement('available_quantity', $purchaseReturnData['quantity']);
                       ###--- Ending Godown item increasing with super key consider ---###


                       }

                 }


                 $receipt_info=array();
                $receipt_info['supp_or_comp_name']=Session::get('purchase_return_invoice_info.supp_or_comp_name');
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
                $receipt_info['invoiced_discount']=Session::get('purchase_return_invoice_info.invoice_discount');
                $receipt_info['less_amount']=$less_taka;
                $receipt_info['total_amount']=$pay_amount;
                 Session::forget("purchase_return_invoice_info");
                $company_info=DB::table('companyprofiles')
                                  ->first();

            DB::commit();
            return Redirect::to('purchase/returnReceipt')->with('receipt_return_item_infos', $receipt_return_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
       } catch (Exception $e) {
            DB::rollback();
            return Redirect::to('purchase/returnReceipt')->with('errorMsg', 'Something is wrong in purchase returned.');
	}

 }
 
 public function purchaseReturnReceipt()
	{
		return View::make('return.purchaseReturnReceipt');
	}
	




}
