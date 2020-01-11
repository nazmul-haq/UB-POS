<?php

class DamageController extends \BaseController {
	
    public $timestamp;
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
		$this->timestamp = date('Y-m-d H:i:s');
    }
	
	public function index(){
		
		return View::make('damageProducts.damageProductForm');
	}
	
	public function autoItemSuggest(){	
		$term = Input::get('q');
		$search_items = DB::table('stockitems')
                        ->join('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                        ->where('stockitems.status', '=', 1)
                        ->where('iteminfos.item_name', 'LIKE', '%'. $term .'%')
			->orWhere('iteminfos.upc_code', '=', $term)
                        ->groupBy('stockitems.item_id')
			->get();
						
		foreach($search_items as $key => $item):
			$upc_code  =$item->upc_code;
                        $item_name = $item->item_name." (".$upc_code.")";
			$item_info = $upc_code.'|'.$item_name;
			echo  "$item_info\n";
		endforeach;
	} 
	
	public function itemAddChart(){
	//Session::forget("damage_items");
		$upc_code = Input::get('item_id');
		$datas=DB::table('stockitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                        ->select('stockitems.stock_item_id','stockitems.item_id','stockitems.price_id','stockitems.available_quantity', 'iteminfos.item_name','iteminfos.tax_amount','iteminfos.offer','priceinfos.purchase_price')
                        ->where('stockitems.status', '=', 1)
                        //->where('stockitems.item_id', '=', $item_id)
			->where('iteminfos.upc_code', '=', $upc_code)
                        ->get();

		 if(!$datas){
            return Redirect::to('damage/damageProducts')->with('errorMsg', "This product are not available in the stock");
        }foreach($datas as $data){

            $item_info=array();
            $item_info['stock_item_id']=$data->stock_item_id;
	    $item_id=$data->item_id;
            $stock_item_id=$item_info['stock_item_id'];
            $item_info['item_id']=$data->item_id;
            $item_info['item_name']=$data->item_name;
            $item_info['price_id']=$data->price_id;
	    $item_info['purchase_price']=$data->purchase_price;
            $item_info['available_quantity']=$data->available_quantity;
            if($data->available_quantity>0)
            $item_info['damage_quantity']=1;
            else
                return Redirect::to('damage/damageProducts')->with('errorMsg', "This product quantity are not available in the stock");
				
            $item_info['total']=$data->purchase_price*$item_info['damage_quantity'];
            
            if(Session::get("damage_items.$stock_item_id")){
                $item_info['stock_item_id']=Session::get("damage_items.$stock_item_id.stock_item_id");
                if($data->available_quantity>Session::get("damage_items.$stock_item_id.damage_quantity"))
                $item_info['damage_quantity']=Session::get("damage_items.$stock_item_id.damage_quantity")+1;
                else
                $item_info['damage_quantity']=Session::get("damage_items.$stock_item_id.damage_quantity");
                $item_info['price_id']=Session::get("damage_items.$stock_item_id.price_id");
                $item_info['purchase_price']=Session::get("damage_items.$stock_item_id.purchase_price");
                
                $item_info['total']=$item_info['purchase_price']*$item_info['damage_quantity'];
            }
            $item_info['total']=round($item_info['total'],2);  
            Session::put("damage_items.$stock_item_id", $item_info);
        }
        return Redirect::to('damage/damageProducts');
	}	

    public function editDeleteItem(){
		$vdata=Input::all();
		$stock_item_id=$vdata['stock_item_id'];
                $data=DB::table('stockitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                        ->select('stockitems.stock_item_id','stockitems.item_id','stockitems.price_id','stockitems.available_quantity', 'iteminfos.item_name','priceinfos.purchase_price')
                        ->where('stockitems.status', '=', 1)
                        ->where('stockitems.stock_item_id', '=', $stock_item_id)
                        ->first();
						
						
		
		if($vdata['edit_delete']=='edit'){
			$item_info=array();
                $item_info['stock_item_id']=$data->stock_item_id;
                $stock_item_id=$item_info['stock_item_id'];
                $item_info['item_id']=$data->item_id;
                $item_info['item_name']=$data->item_name;
                $item_info['price_id']=$data->price_id;
				$item_info['purchase_price']=$data->purchase_price;
                $item_info['available_quantity']=$data->available_quantity;
                if($data->available_quantity>=$vdata['damage_quantity'])
                $item_info['damage_quantity']=$vdata['damage_quantity'];
                else
                return Redirect::to('damage/damageProducts')->with('errorMsg', "This product quantity are not available in the stock");

                if($vdata['damage_quantity']==0)
                    $item_info['damage_quantity']=Session::get("damage_items.$stock_item_id.damage_quantity");
                $item_info['total']=$item_info['damage_quantity']*$item_info['purchase_price'];
				
                Session::put("damage_items.$stock_item_id", $item_info);

		} else{
			Session::forget("damage_items.$stock_item_id");
		}
        return Redirect::to('damage/damageProducts');
	}
	
	 public function invoiceAndDamaged()
	{    
        if(!Session::get("damage_items"))
            return Redirect::to('damage/damageProducts')->with('errorMsg', "Sorry!  now you do not select item for sale");

                #### ----  Invoice Part ----####

               DB::beginTransaction();
               try {
                
                 $receipt_info=array();
                 $vdata=Input::all();
                 $data=array();
                 
                 $amount=0;
                 foreach(Session::get('damage_items') as $item)
                     $amount=$amount+$item['total'];
                 $amount=round($amount,2);
                 $transaction_date=$vdata['date'];
                 $created_by=Session::get('emp_id');
                 $created_at=$this->timestamp;
                 
                 
                
                 $date=date('ymd');
                $insert = DB::select("insert into damageinvoices (damage_invoice_id,amount,date,created_by,created_at) values
								 (ifnull(concat('$date',1+(
									SELECT right(demg_inv.damage_invoice_id, 8) AS LAST8 FROM damageinvoices as demg_inv
									  where( (SELECT left(demg_inv.damage_invoice_id, 6)='$date'))
									  order by LAST8 desc limit 1)),concat('$date','10000000')),
									  '$amount','$transaction_date','$created_by','$created_at')");

				
                 $last_insert_id =DB::getPdo()->lastInsertId();
                 $value = DB::table('damageinvoices')->select('damage_invoice_id')
                                        ->where('id', '=', $last_insert_id)
                                        ->first();
                $damage_invoice_id=$value->damage_invoice_id;
				//echo $damage_invoice_id;exit;
               

            #### ---- End of Invoice Part ----####

                
           


                
               
            ### --- Item damaged Part --- ###

                 $receipt_item_infos=array();
                 foreach(Session::get("damage_items") as $eachItem){
                     $receipt_item_infos[]=$eachItem;

                    $decrease_stock=DB::table('stockitems')
                               ->where('stock_item_id', '=', $eachItem['stock_item_id'])
                               ->where('available_quantity', '>=', $eachItem['damage_quantity'])
                               ->decrement('available_quantity', $eachItem['damage_quantity']);

                     if($decrease_stock){
                    
                    $damageData=array();
                    $damageData['damage_invoice_id']=$damage_invoice_id;
                    $damageData['item_id']=$eachItem['item_id'];
                    $damageData['price_id']=$eachItem['price_id'];
                    $damageData['quantity']=$eachItem['damage_quantity'];
                    $damageData['amount']=$eachItem['total'];
                    $damageData['created_by']=Session::get('emp_id');
                    $damageData['created_at']=$this->timestamp;
                    $stock_item_id=$eachItem['stock_item_id'];


                    $damage= DB::table('itemdamages')->insert($damageData);
                       if($damage){
                       Session::forget("damage_items.$stock_item_id");

                       }

                 }
                 
              }
			  
			  
              
                $receipt_info['date']=$transaction_date;
                $receipt_info['created_at']=$this->timestamp;
                $receipt_info['invoice_id']=$damage_invoice_id;
                $emp_info=DB::table('empinfos')
                        ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                        ->first();
                $receipt_info['emp_name']=$emp_info->user_name;
                $receipt_info['total_amount']=$amount;
             
              DB::table('stockitems')
                    ->where('available_quantity', '<=', 0)
                    ->update(array('status' => 0));
              $company_info=DB::table('companyprofiles')
                        ->first();

            DB::commit();
            return Redirect::to('damage/receipt')->with('receipt_item_infos', $receipt_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
            } catch (Exception $e) {
            DB::rollback();
			return Redirect::to('damage/damageProducts')->with('errorMsg', 'Something is wrong in sales.');
	}
    }
    public function damageReceipt()
	{   
		return View::make('damageProducts.damageProductReceipt');
	}
	
	
}