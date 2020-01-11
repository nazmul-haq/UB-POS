<?php

class SaleReturnController extends \BaseController {

    public $timestamp;
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
		$this->timestamp = date('Y-m-d H:i:s');
    }
	public function index()
	{

            $items = DB::table('iteminfos')
                        ->select('iteminfos.item_id','iteminfos.item_name')
                        ->where('iteminfos.status', '=', 1)
                        ->get();
            $customers = DB::table('customerinfos')->get();
            $payment_types = DB::table('paymenttypes')->where('status', '=', 1)->get();
                           $payment_type=array();
                           foreach ($payment_types as $value)
                            $payment_type[$value->payment_type_id]=$value->payment_type_name;

                return View::make('return.saleReturnForm',compact('items','customers','payment_type'));

	}

         public function autoInvoiceSuggest(){
        
		$term = Input::get('q');

                $search_items = DB::table('saleinvoices')
                        ->where('saleinvoices.sale_invoice_id', 'LIKE', $term .'%')
						->orderBy('saleinvoices.sale_invoice_id','DESC')
			->get();
                foreach($search_items as $key => $invoice):
			$invoice_id   = $invoice->sale_invoice_id;
			echo  "$invoice_id\n";
		endforeach;
               
	}

        public function returnItemAddTochart()
	{

            Session::forget("saleReturnItemInfo");
            Session::forget("sale_return_invoice_info");
            
            $sale_invoice_id = Input::get('sale_invoice_id');
            $data=DB::table('saleinvoices')
                        ->leftjoin('customerinfos', 'customerinfos.cus_id', '=', 'saleinvoices.cus_id')
                        ->select('saleinvoices.sale_invoice_id',
                                 'saleinvoices.cus_id',
                                 'saleinvoices.discount',
                                 'saleinvoices.point_use_taka',
                                 'saleinvoices.amount',
                                 'customerinfos.user_name',
                                 'customerinfos.full_name',
                                 'customerinfos.present_address',
                                 'customerinfos.due'
                                )
                        ->where('saleinvoices.sale_invoice_id', '=', $sale_invoice_id)
                        ->where('saleinvoices.branch_id', '=', Session::get('branch_id'))
                        ->first();

            if(!$data){
                return Redirect::to('sale/returns')->with('errorMsg', "This invoice Id is invalid");
            }
            $invoice_info=array(
                        'sale_invoice_id' =>  $data->sale_invoice_id,
                        'discount'        =>  $data->discount,
                        'point_use_taka'  =>  $data->point_use_taka,
			'cus_id' 	  =>  $data->cus_id,
                        'customer_name'   =>  $data->user_name,
                        'customer_full_name'   =>  $data->full_name,
                        'present_address'   =>  $data->present_address,
                        'customer_due'    =>  $data->due,

            );
           // echo'<pre>';print_r($invoice_info);exit;
            Session::put("sale_return_invoice_info", $invoice_info);

            $datas=DB::table('itemsales')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'itemsales.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'itemsales.price_id')
                        ->where('itemsales.sale_invoice_id', '=', $sale_invoice_id)
                        ->select('itemsales.i_sale_id',
                                 'itemsales.sale_invoice_id',
                                 'itemsales.item_id',
                                 'iteminfos.item_name',
                                 'itemsales.tax',
                                 'itemsales.price_id',
                                 'priceinfos.sale_price',
                                 'itemsales.quantity',
                                 'itemsales.discount',
                                 'itemsales.amount'
                                )
                        ->get();
           //echo'<pre>';print_r($datas);
            foreach($datas as $value){
                $item_sale_id=$value->i_sale_id;
                $item_info=array(
                        'i_sale_id'       =>  $value->i_sale_id,
			'sale_invoice_id' =>  $value->sale_invoice_id,
                        'item_id'         =>  $value->item_id,
                        'item_name' 	  =>  $value->item_name,
                        'price_id' 	  =>  $value->price_id,
                        'sale_price' 	  =>  $value->sale_price,
                        'quantity' 	  =>  $value->quantity,
                        'discount' 	  =>  $value->discount,
                        'tax' 	          =>  $value->tax,
                        'total' 	  =>  $value->amount,
                );

             Session::put("saleReturnItemInfo.$item_sale_id", $item_info);
            }

        return Redirect::to('sale/returns');

   }
   public function saleReturnEditDeleteItem(){

             
		$vdata=Input::all();
		$i_sale_id=$vdata['i_sale_id'];

		if($vdata['edit_delete']=='edit'){
		$item_info=array();
                
                $item_info['i_sale_id']=$i_sale_id;
                $item_info['item_id']=$vdata['item_id'];
                $item_info['item_name']=$vdata['item_name'];
                $item_info['price_id']=$vdata['price_id'];
                $item_info['sale_price']=$vdata['sale_price'];
                $val=DB::table('itemsales')
                        ->where('i_sale_id', '=', $i_sale_id)
                        ->first();
                if($vdata['quantity']>$val->quantity){
                    $item_info['quantity']=$val->quantity;
                }
                else{
                    $item_info['quantity']=$vdata['quantity'];
                }
                if($vdata['quantity']==0)
                    $item_info['quantity']=Session::get("saleReturnItemInfo.$i_sale_id.quantity");
                
                    $discount=Session::get("saleReturnItemInfo.$i_sale_id.discount");
                    $discount_per_item=$discount/Session::get("saleReturnItemInfo.$i_sale_id.quantity");
                $item_info['discount']=$discount_per_item*$item_info['quantity'];
                    $tax=Session::get("saleReturnItemInfo.$i_sale_id.tax");
                    $tax_per_item=$tax/Session::get("saleReturnItemInfo.$i_sale_id.quantity");
                $item_info['tax']=$tax_per_item*$item_info['quantity'];
                
                $item_info['discount']=round($item_info['discount'],2);
                $item_info['tax']=round($item_info['tax'],2);
                $sub_total=($vdata['sale_price']*$item_info['quantity'])-$item_info['discount'];
                $item_info['total']=$sub_total+$item_info['tax'];
                $item_info['total']=round($item_info['total'],2);
                
                Session::put("saleReturnItemInfo.$i_sale_id", $item_info);

		}
		else{
			Session::forget("saleReturnItemInfo.$i_sale_id");
		}

        return Redirect::to('sale/returns');

	}

public function invoiceAndSaleReturn()
	{
        if(!Session::get("saleReturnItemInfo"))
            return Redirect::to('sale/returns')->with('errorMsg', "Sorry!  now you do not select item for return");
            #### ----  Invoice Part ----####

        DB::beginTransaction();
	try {
        $vdata=Input::all();

                 $data=array();
                 $cus_id=Session::get('sale_return_invoice_info.cus_id');
                 $sale_invoice_id=Session::get('sale_return_invoice_info.sale_invoice_id');
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
                 $branch_id = Session::get('branch_id');
                $insert = DB::select("insert into salereturninvoices (branch_id,sale_r_invoice_id,sale_invoice_id,cus_id,payment_type_id,amount,less_amount,transaction_date,created_by,created_at) values ('$branch_id',
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
                 foreach(Session::get("saleReturnItemInfo") as $eachItem){
                   // echo '<pre>';dd($eachItem);exit;
			 $receipt_return_item_infos[]=$eachItem;
                    $saleReturnData=array();
                    $saleReturnData['branch_id']=$branch_id;
                    $saleReturnData['sale_r_invoice_id']=$sale_r_invoice_id;
                    $saleReturnData['item_id']=$eachItem['item_id'];
                    $item_id=$saleReturnData['item_id'];
                    $saleReturnData['price_id']=$eachItem['price_id'];
                    $price_id=$saleReturnData['price_id'];
                    $saleReturnData['quantity']=$eachItem['quantity'];
                    $saleReturnData['discount']=$eachItem['discount'];
                    $saleReturnData['tax']=$eachItem['tax'];
                    $saleReturnData['amount']=$eachItem['total'];
                    $saleReturnData['created_by']=Session::get('emp_id');
                    $saleReturnData['created_at']=$this->timestamp;
                    $i_sale_id=$eachItem['i_sale_id'];

                    $saleReturn= DB::table('salereturntostocks')->insert($saleReturnData);
                       if($saleReturn){
                       Session::forget("saleReturnItemInfo.$i_sale_id");
                       
                       ###--- Stock item increasing with super key consider ---###

                            $stockItemInfo = DB::table('stockitems')
                                                    ->where('item_id', '=', $item_id)
                                                    ->where('price_id', '=', $price_id)
                                                    ->first();
                            if(!$stockItemInfo){
                                $insertData=array();
                                $insertData['branch_id']=Session::get('branch_id');
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
                $receipt_info['customer_full_name']=Session::get('sale_return_invoice_info.customer_full_name');
                $receipt_info['customer_name']=Session::get('sale_return_invoice_info.present_address');
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
                $receipt_info['less_amount']= ($less_taka>0) ? $less_taka : 0;
                $receipt_info['total_amount']=$pay_amount;
              // echo'<pre>';print_r($receipt_info);exit;
                 Session::forget("sale_return_invoice_info");
                $company_info=DB::table('companyprofiles')
                                  ->first();
              DB::commit();
             return Redirect::to('sale/returnReceipt')->with('receipt_return_item_infos', $receipt_return_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
             } catch (Exception $e) {
                    //return  DB::getQueryLog();
                         DB::rollback();
			return Redirect::to('sale/returnReceipt')->with('errorMsg', 'Something is wrong in sale returned.');
	}

    }
     public function saleReturnReceipt()
	{
		return View::make('return.saleReturnReceipt');
	}
	
        













}