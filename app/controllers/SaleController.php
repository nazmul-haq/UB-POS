<?php

class SaleController extends \BaseController
{
    public $timestamp;

    public function __construct() 
    {
        $this->beforeFilter('csrf', array('on'=>'post'));
    $this->timestamp = date('Y-m-d H:i:s');
    }

  public function index()
  {
        $items = DB::table('stockitems')
            ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
            ->select('stockitems.stock_item_id','stockitems.item_id','iteminfos.item_name')
            ->where('stockitems.status', '=', 1)
            ->get();

        $customers = DB::table('customerinfos')->get();
        $payment_types = DB::table('paymenttypes')->where('status', '=', 1)->get();
        $payment_type=array();
        foreach ($payment_types as $value)
            $payment_type[$value->payment_type_id]=$value->payment_type_name;
        
        if(!Session::has('sale_invoice_info.discount_permission')){
            Session::put('sale_invoice_info.discount_permission',0);
        }
        return View::make('sale.saleForm',compact('items','customers','payment_type'));
  }

    public function autoItemSuggest()
    {
    $term = Input::get('q');
        $search_items = DB::table('stockitems')
            ->join('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
            ->join('priceinfos', 'iteminfos.item_id', '=', 'priceinfos.item_id')
            ->where('stockitems.status', '=', 1)
            ->where('priceinfos.status', '=', 1)
            ->where('iteminfos.item_name', 'LIKE', '%'. $term .'%')
            ->orWhere('iteminfos.upc_code', '=', $term)
            ->groupBy('stockitems.item_id')
        ->get();

        foreach($search_items as $key => $item):
            $upc_code  =$item->upc_code;
            $item_name = $item->item_name." (".$upc_code.")"."(".$item->sale_price.")";
      $item_info = $upc_code.'|'.$item_name;
      echo  "$item_info\n";
    endforeach;
  }
  public function discountPermission()
  {
        $data = Input::all();
        $getpPassword = DB::table('empinfos')
            ->where('emp_id',1)
            ->first();
        if(Hash::check($data['password'], $getpPassword->password)){
            Session::forget('sale_invoice_info.discount_permission');
            Session::put('sale_invoice_info.discount_permission',1);
            return Redirect::to('sale/sales')->with('message','Password Accepted !');
        }else{
            Session::put('sale_invoice_info.discount_permission',0);
            return Redirect::to('sale/sales')->with('errorMsg','Wrong Password !');
        }
  }
    public function saleItemAddChart()
  {
        $item_id = Input::get('item_id');
        
       /*$datas=DB::table('stockitems')
                   ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                   ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                   ->select('stockitems.stock_item_id','stockitems.item_id','stockitems.price_id','stockitems.available_quantity', 'iteminfos.item_name','iteminfos.tax_amount','iteminfos.offer','priceinfos.sale_price')
                   ->where('stockitems.status', '=', 1)
    ->where('iteminfos.upc_code', '=', $item_id)
                   ->get();*/
       
        
        $datas = DB::table('stockitems as s')
            ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.item_point','i.tax_amount','i.offer', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
            ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
            ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
            ->where('s.status', '=', 1)
            ->where('i.upc_code', '=', $item_id)
            ->groupBy('p.sale_price')
            ->get();
        if(!$datas){
            return Redirect::to('sale/sales')->with('errorMsg', "This product are not available in the stock");
        }
            
        foreach($datas as $data){
            $item_info=array();
            $item_info['stock_item_id']=$data->stock_item_id;
            $stock_item_id=$item_info['stock_item_id'];
            $item_info['row_no']=$data->row_no;
            $item_info['item_id']=$data->item_id;
            $item_info['item_name']=$data->item_name;
            $item_info['item_point']=$data->item_point;
            $item_info['price_id']=$data->price_id;
            $key = $item_info['key'] = $item_info['item_id'].'-'.$item_info['price_id'];
            $item_info['offer']=$data->offer;
           // $item_info['purchase_price']=$data->purchase_price;
            $item_info['sale_price']=$data->sale_price;
            $item_info['available_quantity']=$data->available_quantity;
            if($data->available_quantity>=1)
                $item_info['sale_quantity']=1;
            else
               $item_info['sale_quantity']=0;
            $item_info['discount']=($data->sale_price/100)*$data->offer;
            $item_info['discount']=round($item_info['discount'],2);
            $sub_total=($data->sale_price*$item_info['sale_quantity'])-$item_info['discount'];
            $item_info['tax']=($sub_total/100)*$data->tax_amount;
            $item_info['total']=$sub_total+$item_info['tax'];
            $item_id=$data->item_id;
            //echo'<pre>';print_r(Session::get("saleItems.$key"));exit;
            if(Session::get("saleItems.$key")){
                $item_info['stock_item_id']=Session::get("saleItems.$key.stock_item_id");
                if(($data->available_quantity > Session::get("saleItems.$key.sale_quantity")) && Session::get("saleItems.$key.sale_quantity") > 0)
                    $item_info['sale_quantity']=Session::get("saleItems.$key.sale_quantity")+1;
                else
                    $item_info['sale_quantity']=Session::get("saleItems.$key.sale_quantity");
               // $item_info['purchase_price']=Session::get("saleItems.$item_id.purchase_price");
                $item_info['price_id']=Session::get("saleItems.$key.price_id");
                $item_info['sale_price']=Session::get("saleItems.$key.sale_price");
                $item_info['discount']=((($data->sale_price/100)*$data->offer)*$item_info['sale_quantity']);
                $item_info['discount']=round($item_info['discount'],2);
                $sub_total=($data->sale_price*$item_info['sale_quantity'])-$item_info['discount'];
               
                $item_info['tax']=($sub_total/100)*$data->tax_amount;
                $item_info['tax']=round($item_info['tax'],2);
                $item_info['total']=$sub_total+$item_info['tax'];
            }
            $item_info['total']=round($item_info['total'],2);
            
            Session::put("saleItems.$key", $item_info);
        }
      //  echo'<pre>';print_r(Session::get('saleItems'));exit;
        return Redirect::to('sale/sales');
   }

   public function emptyCart()
   {
        if(Session::get('saleItems')){
            Session::forget('saleItems');
        }
        return Redirect::to('sale/sales');
   }

    public function saleEditDeleteItem()
    {
    $vdata=Input::all();
    $key=$vdata['key'];
        $parameters=explode("-",$key);
        $item_id=$parameters[0];
        $price_id=$parameters[1];

        $price_info=DB::table('priceinfos')
                ->where('priceinfos.price_id', '=', $price_id)
                ->first();
        
        $data = DB::table('stockitems as s')
        ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.tax_amount','i.item_point','i.offer', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
        ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
        ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
        ->where('s.status', '=', 1)
        ->where('s.item_id', '=', $item_id)
        ->where('p.sale_price', '=', $price_info->sale_price)
        ->groupBy('p.sale_price')
        ->first();

    if($vdata['edit_delete']=='edit'){
    $item_info=array();
        $item_info['stock_item_id']=$data->stock_item_id;
        //$stock_item_id=$item_info['stock_item_id'];
        $item_info['row_no']=$data->row_no;
        $item_info['item_id']=$data->item_id;
        $item_info['key']=$key;
        $item_info['item_name']=$data->item_name;
        $item_info['item_point']=$data->item_point;
        $item_info['price_id']=$data->price_id;
       // $item_info['purchase_price']=$data->purchase_price;
        $item_info['sale_price']=$data->sale_price;
        $item_info['available_quantity']=$data->available_quantity;
        if($data->available_quantity>$vdata['sale_quantity']){
            $item_info['sale_quantity']=$vdata['sale_quantity'];
            // $item_info['item_point']=$data->item_point * $vdata['sale_quantity'];
        }else{
            $item_info['sale_quantity']=$data->available_quantity;
        }

        if($vdata['sale_quantity']==0)
            $item_info['sale_quantity']=Session::get("saleItems.$key.sale_quantity");

        if($data->offer>0)
        $item_info['discount']=((($data->sale_price/100)*$data->offer)*$item_info['sale_quantity']);
        else{
            if($vdata['discount']>($data->sale_price*$item_info['sale_quantity']))
            $item_info['discount']=$data->sale_price*$item_info['sale_quantity'];
            else
            $item_info['discount']=$vdata['discount'];
        }
            //echo $data->tax_amount;exit;
        $item_info['discount']=round($item_info['discount'],2);
        $sub_total=($data->sale_price*$item_info['sale_quantity'])-$item_info['discount'];
       
        $item_info['tax']=(($sub_total/100)*$data->tax_amount);
        $item_info['tax']=round($item_info['tax'],2);
        $item_info['total']=$sub_total+$item_info['tax'];
        $item_info['total']=round($item_info['total'],2);

        Session::put("saleItems.$key", $item_info);

    }
    else{
      Session::forget("saleItems.$key");
    }
        return Redirect::to('sale/sales');
  }
        
        //Customer auto suggest
  public function autoCustomerSuggest(){
    $term = Input::get('q');
    $customers = DB::table('customerinfos')
            //->where('user_name', 'LIKE', '%'. $term .'%')
            ->where('national_id',$term)
            ->orWhere('mobile',$term)
            ->get();
    foreach($customers as $key => $customer):
      $customer_id  = $customer->cus_id;
      $customer_name  = $customer->user_name;
            $national_id= $customer->national_id;
      $customers_info   = $national_id.'|'.$customer_name;
      echo  "$customers_info\n";
    endforeach;
  }

    public function selectDeleteCustomer()
    {
    $vdata=Input::all();
    if(isset($vdata['customer']) == 'delete'){
      Session::forget("sale_invoice_info.cus_id");
      Session::forget("sale_invoice_info.user_name");
      Session::forget("sale_invoice_info.discount_percent");
    } else{
            $selectCustomer=DB::table('customerinfos')->where('national_id', '=', $vdata['national_id'])->first();
            $vdata['cus_id']=$selectCustomer->cus_id;
            $qdata=DB::table('customerinfos')
                ->leftjoin('customertypes', 'customertypes.cus_type_id', '=', 'customerinfos.cus_type_id')
        ->where('cus_id', '=', $vdata['cus_id'])
        ->first();
      if($qdata){
        Session::put("sale_invoice_info.cus_id", $vdata['cus_id']);
        Session::put("sale_invoice_info.user_name", $qdata->user_name);
        Session::put("sale_invoice_info.discount_percent", $qdata->discount_percent);
        Session::put("sale_invoice_info.customer_due", $qdata->due);
      }
    }
    return Redirect::to('sale/sales');
  }

     public function addInvoiceToQueue()
     {
        if(Session::get("sale_invoice_info.user_name")){
           $customer = Session::get("sale_invoice_info.user_name");
        }
        else {
           $customer = Input::get('customer'); 
        }
        if( Session::get("saleInvoiceQueue.$customer")){
            return Redirect::back()->with('errorMsg', "$customer is already existed in the Queue");  
        }
        Session::put("saleInvoiceQueue.$customer.sale_invoice_info",Session::get("sale_invoice_info"));
        Session::put("saleInvoiceQueue.$customer.saleItems",Session::get("saleItems"));

        Session::forget("sale_invoice_info");
        Session::forget("saleItems");

        return Redirect::back()->with('message', 'Invoice added to Queue');
    }

     public function reloadDeleteInvoiceQueueElement()
     {
        $vdata=Input::all();
        $customer=$vdata['customer'];
        if($vdata['reloadDelete'] == 'delete'){
            Session::forget("saleInvoiceQueue.$customer");
            return Redirect::back()->with('errorMsg', 'Deleted one invoice from Queue');
        }
        else{
            Session::put("sale_invoice_info", Session::get("saleInvoiceQueue.$customer.sale_invoice_info"));
            Session::put("saleItems", Session::get("saleInvoiceQueue.$customer.saleItems"));
            Session::forget("saleInvoiceQueue.$customer");
            return Redirect::back()->with('errorMsg', 'One invoice reloaded from Queue');

        }
    }

    public function invoiceAndSale()
  {
        if(!Session::get("saleItems"))
            return Redirect::to('sale/sales')->with('errorMsg', "Sorry!  now you do not select item for sale");
                
            #### ----  Invoice Part ----####

            DB::beginTransaction();
            try {
                $receipt_info=array();
                $vdata=Input::all();
                // return $vdata;
                $data=array();
                $cus_id=Session::get('sale_invoice_info.cus_id');
                 //echo $cus_id;exit;
                $payment_type_id=$vdata['payment_type_id'];
                $discount=(empty($vdata['invoice_discount']))?0:$vdata['invoice_discount'];
                $amount=0;
                $totalPoint = 0;
                $totalPoint = $vdata['totalPoint'];
                foreach(Session::get('saleItems') as $item){
                    $amount = $amount + $item['total'];
                }
                // return $totalPoint;
                $amount=$amount-$discount;
                $amount=round($amount,2);
                $pay=$vdata['pay'];             
             
                $due=$amount-($pay);            
                $pay_note=(empty($vdata['paynote']))?$pay:$vdata['paynote'];             
                $transaction_date=$vdata['date'];
                $created_by=Session::get('emp_id');
                $created_at=$this->timestamp;           

                $payable_amount=$amount-$discount;
                $point=(int)($payable_amount*0.01);
                $date=date('ymd');

                $insert = DB::select("insert into saleinvoices (sale_invoice_id,cus_id,payment_type_id,discount,total_point,amount,pay,due,pay_note,date,created_by,created_at) values
               (ifnull(concat('$date',1+(
                SELECT right(sale_inv.sale_invoice_id, 8) AS LAST8 FROM saleinvoices as sale_inv
                  where( (SELECT left(sale_inv.sale_invoice_id, 6)='$date'))
                  order by LAST8 desc limit 1)),concat('$date','10000000')),
                  '$cus_id','$payment_type_id','$discount','$totalPoint','$amount','$pay','$due','$pay_note','$transaction_date','$created_by','$created_at')");

             $last_insert_id =DB::getPdo()->lastInsertId();
             $value = DB::table('saleinvoices')->select('sale_invoice_id')
                                    ->where('id', '=', $last_insert_id)
                                    ->first();
            $sale_invoice_id=$value->sale_invoice_id;
            DB::table('customerinfos')
                   ->where('cus_id', '=', $cus_id)
                   ->increment('due', $due);

        #### ---- End of Invoice Part ----####

        ### --- Item Sale Part --- ###

             $receipt_item_infos=array();
             foreach(Session::get("saleItems") as $eachItem){
                 $receipt_item_infos[]=$eachItem;
                  $key=$eachItem['key'];
                ### --- Available Quantity from one row --- ###
                if($eachItem['row_no']==1){
                    $decrease_stock=DB::table('stockitems')
                               ->where('stock_item_id', '=', $eachItem['stock_item_id'])
                               ->where('available_quantity', '>=', $eachItem['sale_quantity'])
                               ->decrement('available_quantity', $eachItem['sale_quantity']);
                     
                    if($decrease_stock){
                        $saleData=array();
                        $saleData['sale_invoice_id'] = $sale_invoice_id;
                        $saleData['item_id']         = $eachItem['item_id'];
                        $saleData['price_id']        = $eachItem['price_id'];
                        $saleData['quantity']        = $eachItem['sale_quantity'];
                        $saleData['discount']        = $eachItem['discount'];
                        $saleData['tax']             = $eachItem['tax'];
                        $saleData['amount']          = $eachItem['total'];
                        $saleData['item_point']      = $eachItem['item_point'];
                        $saleData['created_by']      = Session::get('emp_id');
                        $saleData['created_at']      = $this->timestamp;
                        $stock_item_id               = $eachItem['stock_item_id'];
                        
                        $sale= DB::table('itemsales')->insert($saleData);
                        if($sale){
                            Session::forget("saleItems.$key");
                        }
                    }
                }
                ### --- Ending of Available Quantity from one row --- ###
                
                ### --- Available Quantity from Multiple rows --- ###
                else{
                    $minus_qty=0;
                    $qty=$eachItem['sale_quantity'];
                    $tempData = DB::table('stockitems')
                        ->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                        ->where('stockitems.status', '=', 1)
                        ->where('stockitems.item_id', '=', $eachItem['item_id'])
                        ->where('priceinfos.sale_price', '=', $eachItem['sale_price'])
                        ->orderBy('stockitems.available_quantity', 'asc')
                        ->get();
                    
                    foreach($tempData as $d){
                        if($qty<=0)
                            continue;
                        if($qty>$d->available_quantity){
                           $decrease=DB::table('stockitems')
                               ->where('stock_item_id', '=', $d->stock_item_id)
                               ->where('available_quantity', '>=', $d->available_quantity)
                               ->decrement('available_quantity', $d->available_quantity); 
                           if($decrease){
                                $qty=$qty-$d->available_quantity;
                                $minus_qty=$minus_qty+$d->available_quantity;
                           
                                $saleData=array();
                                $saleData['sale_invoice_id']=$sale_invoice_id;
                                $saleData['item_id']=$eachItem['item_id'];
                                $saleData['price_id']=$d->price_id;
                                $saleData['quantity']=$d->available_quantity;
                                $saleData['discount']=($eachItem['discount']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['tax']=($eachItem['tax']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['amount']=($eachItem['total']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['item_point']=($eachItem['item_point']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['created_by']=Session::get('emp_id');
                                $saleData['created_at']=$this->timestamp;
                                $stock_item_id=$eachItem['stock_item_id'];
                                $sale= DB::table('itemsales')->insert($saleData);
                           }
                        }
                        else{
                            $dec=DB::table('stockitems')
                               ->where('stock_item_id', '=', $d->stock_item_id)
                               ->where('available_quantity', '>=', $qty)
                               ->decrement('available_quantity', $qty); 
                                if($dec){
                                    $minus_qty=$minus_qty+$qty;
                                    $qty_last=$qty;
                                    $qty=$qty-$qty;
                                    
                                    $saleData=array();
                                    $saleData['sale_invoice_id']=$sale_invoice_id;
                                    $saleData['item_id']=$eachItem['item_id'];
                                    $saleData['price_id']=$d->price_id;
                                    $saleData['quantity']=$qty_last;
                                    $saleData['discount']=($eachItem['discount']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['tax']=($eachItem['tax']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['amount']=($eachItem['total']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['item_point']=($eachItem['item_point']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['created_by']=Session::get('emp_id');
                                    $saleData['created_at']=$this->timestamp;
                                    $sale = DB::table('itemsales')->insert($saleData);
                                }
                            }
                    }
                    if($minus_qty != $eachItem['sale_quantity']){
                        DB::rollback();
                 return Redirect::to('sale/sales')->with('errorMsg', 'Sales Quantity are not available in the stock.');

                }
                else {
                     Session::forget("saleItems.$key");
                }
            }
                ### --- Ending of Available Quantity from Multiple rows --- ###
            }
            $receipt_info['customer_name']=Session::get("sale_invoice_info.user_name");
            $receipt_info['date']=$transaction_date;
            $receipt_info['created_at']=$this->timestamp;
            $receipt_info['invoice_id']=$sale_invoice_id;
            $emp_info=DB::table('empinfos')
                ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                ->first();
            $payment_type_info=DB::table('paymenttypes')
                ->where('paymenttypes.payment_type_id', '=', $payment_type_id)
                ->first();
            $receipt_info['emp_name']=$emp_info->user_name;
            $receipt_info['total_amount']=$amount;
            $receipt_info['payment_type_name']=$payment_type_info->payment_type_name;
            $receipt_info['invoice_discount']=$discount;
            $receipt_info['pay']=$pay;
            $receipt_info['due']=$due;
            $receipt_info['pay_note']=$pay_note;
            
            Session::forget("sale_invoice_info");
            DB::table('stockitems')
                ->where('available_quantity', '<=', 0)
                ->update(array('status' => 0));
            $company_info=DB::table('companyprofiles')
                ->first();

            DB::commit();
            return Redirect::to('sale/receipt')->with('receipt_item_infos', $receipt_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
        } catch (Exception $e) {
            // return $e;
            DB::rollback();
        return Redirect::to('sale/sales')->with('errorMsg', 'Something is wrong in sales.');
      }
    }

    public function saleReceipt()
  {   
    return View::make('sale.saleReceipt');
  }

  public function pointIncreaseReport()
  {   $reports=0;
    return View::make('sale.viewPointIncreasingReport',compact('reports'));
  }

    public function viewPointIncreasingReport()
  {       
        $vdata=Input::all();
        $reports= DB::table('pointincreasingrecords')
            ->leftjoin('saleinvoices', 'saleinvoices.sale_invoice_id', '=', 'pointincreasingrecords.sale_invoice_id')
            ->leftjoin('customerinfos', 'customerinfos.cus_id', '=', 'pointincreasingrecords.cus_id')
            ->where('pointincreasingrecords.cus_id', '=', $vdata['cus_id'])
            ->where('pointincreasingrecords.status', '=', 1)
            ->get();
      return View::make('sale.viewPointIncreasingReport',compact('reports'));
  }

    public function pointUsingReport()
  {
                $reports=0;
    return View::make('sale.viewPointUsingReport',compact('reports'));
  }

  public function viewPointUsingReport()
  {
    $vdata=Input::all();
        $reports= DB::table('pointusingrecords')
            ->leftjoin('saleinvoices', 'saleinvoices.sale_invoice_id', '=', 'pointusingrecords.sale_invoice_id')
            ->leftjoin('customerinfos', 'customerinfos.cus_id', '=', 'pointusingrecords.cus_id')
            ->where('pointusingrecords.cus_id', '=', $vdata['cus_id'])
            ->where('pointusingrecords.status', '=', 1)
            ->get();
      return View::make('sale.viewPointUsingReport',compact('reports'));
  }

}
