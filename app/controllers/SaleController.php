<?php
class SaleController extends \BaseController
{
    public $timestamp;
    public function __construct() 
    { 
        // Session::forget('saleItems');
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->timestamp = date('Y-m-d H:i:s');
    }
    public function index()
    {
        // Session::forget('saleItems');
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
        // return response()->json(['success' => true, 'item' => $item_id]);
        $datas = DB::table('stockitems as s')
            ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.tax_amount','i.offer','i.unit','i.carton', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
            ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
            ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
            ->where('s.status', '=', 1)
            // ->where('s.branch_id', '=', Session::get('branch_id'))
            ->where('i.upc_code', '=', $item_id)
            ->groupBy('p.sale_price')
            ->get();
        if(!$datas){
            return Redirect::to('sale/sales')->with('errorMsg', "This product are not available in the stock");
        }
        foreach($datas as $data){
            $item_info=array();
            $item_info['stock_item_id']=$data->stock_item_id;
            $stock_item_id           = $item_info['stock_item_id'];
            $item_info['row_no']     = $data->row_no;
            $item_info['item_id']    = $data->item_id;
            $item_info['item_name']  = $data->item_name;
            $item_info['price_id']   = $data->price_id;
            $key = $item_info['key'] = $item_info['item_id'].'-'.$item_info['price_id'];
            $item_info['offer']      = $data->offer;
            $item_info['unit']       = $data->unit;
            $item_info['pcs_per_carton'] = $data->carton;
            
           // $item_info['purchase_price']=$data->purchase_price;
            $item_info['sale_price'] = $data->sale_price;
            $item_info['purchase_price'] = $data->purchase_price;
            $item_info['available_quantity'] = $data->available_quantity;
            if($data->available_quantity >= 1)
                $item_info['sale_quantity'] = 1;
            else
            $item_info['sale_quantity'] = 0;
            $item_info['dozz']      = ($item_info['sale_quantity']/12);
            $item_info['set']       = ($item_info['sale_quantity']/1);
            $item_info['carton']    = ($item_info['sale_quantity']/$data->carton);
            $item_info['discount']  = ($data->sale_price/100)*$data->offer;
            $item_info['discount']  = round($item_info['discount'],2);
            $sub_total = ($data->sale_price*$item_info['sale_quantity'])-$item_info['discount'];
            $item_info['tax']=($sub_total/100)*$data->tax_amount;
            $item_info['total']=$sub_total+$item_info['tax'];
            $item_id=$data->item_id;
            //echo'<pre>';print_r(Session::get("saleItems.$key"));exit;
            $appendFlag = false;
            if(Session::get("saleItems.$key")){
                $appendFlag = true;
                $item_info['stock_item_id'] = Session::get("saleItems.$key.stock_item_id");
                if(($data->available_quantity > Session::get("saleItems.$key.sale_quantity")) && Session::get("saleItems.$key.sale_quantity") > 0)
                    $item_info['sale_quantity'] = Session::get("saleItems.$key.sale_quantity")+1;
                else
                    $item_info['sale_quantity'] = Session::get("saleItems.$key.sale_quantity");
               // $item_info['purchase_price']=Session::get("saleItems.$item_id.purchase_price");
                $item_info['price_id']=Session::get("saleItems.$key.price_id");
                $item_info['sale_price']=Session::get("saleItems.$key.sale_price");
                $item_info['discount']=((($data->sale_price/100)*$data->offer)*$item_info['sale_quantity']);
                $item_info['discount']=round($item_info['discount'],2);
                $sub_total=($data->sale_price*$item_info['sale_quantity'])-$item_info['discount'];
                $item_info['tax']=($sub_total/100)*$data->tax_amount;
                $item_info['pcs_per_carton'] = $data->carton;
                $item_info['unit']=$data->unit;
                $item_info['carton']=($item_info['sale_quantity']/$data->carton);
                $item_info['dozz']      = ($item_info['sale_quantity']/12);
                $item_info['set']       = ($item_info['sale_quantity']/1);
                $item_info['tax']=round($item_info['tax'],2);
                $item_info['total']=$sub_total+$item_info['tax'];
            }
            $item_info['total']=round($item_info['total'],2);
            Session::put("saleItems.$key", $item_info);
        }
        return Response::json(['success' => true,'append_flag' => $appendFlag,'item' => $item_info, 'allItem' => count(Session::get("saleItems"))]);
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
        ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.tax_amount','i.offer','i.unit','i.carton', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
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
        $item_info['unit']=$data->unit;
        $item_info['pcs_per_carton']=$data->carton;
        
        $item_info['price_id']=$data->price_id;
       // $item_info['purchase_price']=$data->purchase_price;
        $item_info['sale_price']=$vdata['sale_price'];
        $item_info['purchase_price']=$vdata['purchase_price'];
        $item_info['available_quantity']=$data->available_quantity;
        
        if($data->available_quantity>$vdata['sale_quantity']){
            $item_info['sale_quantity']=$vdata['sale_quantity'];
            // $item_info['item_point']=$data->item_point * $vdata['sale_quantity'];
        }else{
            $item_info['sale_quantity']=$data->available_quantity;
        }

        if($data->available_quantity>($vdata['sale_quantity_carton']*$item_info['pcs_per_carton'])){
            $item_info['carton'] = $vdata['sale_quantity_carton'];
        }else{
            $item_info['carton']=$data->available_quantity/$item_info['pcs_per_carton'];
        }

        if($data->available_quantity>($vdata['sale_quantity_dozz']*12)){
            $item_info['dozz'] = $vdata['sale_quantity_dozz'];
        }else{
            $item_info['dozz']=$data->available_quantity/12;
        }

        if($data->available_quantity>($vdata['sale_quantity_set']*1)){
            $item_info['set'] = $vdata['sale_quantity_set'];
        }else{
            $item_info['set']=$data->available_quantity/1;
        }
        if($item_info['sale_quantity'] != Session::get("saleItems.$key.sale_quantity")){
            $item_info['dozz'] = round($item_info['sale_quantity']/12,4);
            $item_info['set'] = round($item_info['sale_quantity']/1,4);
            $item_info['carton'] = round($item_info['sale_quantity'] / $item_info['pcs_per_carton'],4);
        }else if($item_info['carton'] != Session::get("saleItems.$key.carton")){
            $item_info['sale_quantity'] = round($item_info['carton'] * $item_info['pcs_per_carton']);
            $item_info['dozz'] = round($item_info['carton'] * $item_info['pcs_per_carton'])/12;
            $item_info['set'] = round($item_info['carton'] * $item_info['pcs_per_carton'])/1;
        }else if($item_info['dozz'] != Session::get("saleItems.$key.dozz")){
            $item_info['sale_quantity'] = round($item_info['dozz'] * 12);
            $item_info['set'] = round($item_info['dozz'] * 12)/1;
            $item_info['carton'] = round($item_info['dozz'] * 12) / $item_info['pcs_per_carton'];
        }else if($item_info['set'] != Session::get("saleItems.$key.set")){
            $item_info['sale_quantity'] = round($item_info['set'] * 1);
            $item_info['dozz'] = round($item_info['set'] * 1)/12;
            $item_info['carton'] = round($item_info['set'] * 1)/$item_info['pcs_per_carton'];
        }
        if($vdata['sale_quantity']==0){
            $item_info['sale_quantity']=Session::get("saleItems.$key.sale_quantity");
            $item_info['carton']= Session::get("saleItems.$key.sale_quantity")/$item_info['pcs_per_carton'];
        }
        
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
        $sub_total=($vdata['sale_price']*$item_info['sale_quantity'])-$item_info['discount'];
       
        $item_info['tax']=(($sub_total/100)*$data->tax_amount);
        $item_info['tax']=round($item_info['tax'],2);
        $item_info['total']=$sub_total+$item_info['tax'];
        $item_info['total']=round($item_info['total'],2);
        Session::put("saleItems.$key", $item_info);
        return Response::json(['success' => true,'item_info' => $item_info]);
    }
    else{
      Session::forget("saleItems.$key");
      return Response::json(['success' => true]);
    }
  }

  public function saleFormCalculate()
  {
    $datas = Session::get('saleItems');
    $subTotalAmount = 0;
    $totalItem = 0;
    $totalItemQuantity = 0;
    $totalItem = 0;
    $calculate = [];
    foreach($datas as $data){
        $subTotalAmount += $data['sale_price'] * $data['sale_quantity'];
        $totalItemQuantity += $data['sale_quantity'];
        $totalItem++;
    }
    $calculate['subTotalAmount'] = $subTotalAmount;
    $calculate['totalItemQuantity'] = $totalItemQuantity;
    $calculate['totalItem'] = $totalItem;
    return $calculate;
  }
  public function autoCustomerSuggest(){
    $term = Input::get('q');
    $customers = DB::table('customerinfos')
            //->where('user_name', 'LIKE', '%'. $term .'%')
            ->where('national_id',$term)
            ->orWhere('user_name', 'LIKE', '%'. $term .'%')
            ->orWhere('full_name', 'LIKE', '%'. $term .'%')
            ->get();
    foreach($customers as $key => $customer):
      $customer_id  = $customer->cus_id;
      $customer_name  = $customer->full_name;
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
      Session::forget("sale_invoice_info.full_name");
      Session::forget("sale_invoice_info.user_name");
      Session::forget("sale_invoice_info.discount_percent");
      Session::forget("sale_invoice_info.customer_due");
      Session::forget("sale_invoice_info.cust_address");
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
        Session::put("sale_invoice_info.full_name", $qdata->full_name);
        Session::put("sale_invoice_info.cust_address", $qdata->present_address);
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
        
        $vdata = Input::all();
        if(isset($vdata['saleOrder'])){
            Session::put("saleOrderItems",Session::get("saleItems"));
            Session::put("sale_order_invoice_info",Session::get("sale_invoice_info"));

            Session::forget("saleItems");
            Session::forget("sale_invoice_info");
            return $this->invoiceAndSaleOrder();
        }
        
        $branch_id = Session::get('branch_id');
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
                // $totalPoint = $vdata['totalPoint'];
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

                $insert = DB::select("insert into saleinvoices (branch_id,sale_invoice_id,cus_id,payment_type_id,discount,amount,pay,due,pay_note,date,created_by,created_at) values
               ('$branch_id',ifnull(concat('$date',1+(
                SELECT right(sale_inv.sale_invoice_id, 8) AS LAST8 FROM saleinvoices as sale_inv
                  where( (SELECT left(sale_inv.sale_invoice_id, 6)='$date'))
                  order by LAST8 desc limit 1)),concat('$date','10000000')),
                  '$cus_id','$payment_type_id','$discount','$amount','$pay','$due','$pay_note','$transaction_date','$created_by','$created_at')");

             $last_insert_id =DB::getPdo()->lastInsertId();
             $value = DB::table('saleinvoices')
                ->select('sale_invoice_id')
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
                        $saleData['branch_id']         = $branch_id;
                        $saleData['item_id']         = $eachItem['item_id'];
                        $saleData['price_id']        = $eachItem['price_id'];
                        $saleData['quantity']        = $eachItem['sale_quantity'];
                        $saleData['discount']        = $eachItem['discount'];
                        $saleData['tax']             = $eachItem['tax'];
                        $saleData['amount']          = $eachItem['total'];
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
                        ->where('stockitems.branch_id', '=', $branch_id)
                        ->where('stockitems.item_id', '=', $eachItem['item_id'])
                        ->where('priceinfos.sale_price', '=', $eachItem['sale_price'])
                        ->orderBy('stockitems.available_quantity', 'asc')
                        ->get();
                    if(count($tempData)<1){
                        $tempData = DB::table('stockitems')
                            ->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                            ->where('stockitems.status', '=', 1)
                            ->where('stockitems.branch_id', '=', $branch_id)
                            ->where('stockitems.item_id', '=', $eachItem['item_id'])
                            ->orderBy('stockitems.available_quantity', 'asc')
                            ->get();
                    }
                    
                    foreach($tempData as $d){
                        if($qty<=0)
                            continue;
                        if($qty>$d->available_quantity){
                           $decrease=DB::table('stockitems')
                               ->where('branch_id', '=', $branch_id)
                               ->where('stock_item_id', '=', $d->stock_item_id)
                               ->where('available_quantity', '>=', $d->available_quantity)
                               ->decrement('available_quantity', $d->available_quantity); 
                           if($decrease){
                                $qty=$qty-$d->available_quantity;
                                $minus_qty=$minus_qty+$d->available_quantity;
                           
                                $saleData=array();
                                $saleData['sale_invoice_id']=$sale_invoice_id;
                                $saleData['item_id']=$eachItem['item_id'];
                                $saleData['branch_id']=$branch_id;
                                $saleData['price_id']=$d->price_id;
                                $saleData['quantity']=$d->available_quantity;
                                $saleData['discount']=($eachItem['discount']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['tax']=($eachItem['tax']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['amount']=($eachItem['total']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['created_by']=Session::get('emp_id');
                                $saleData['created_at']=$this->timestamp;
                                $stock_item_id=$eachItem['stock_item_id'];
                                $sale= DB::table('itemsales')->insert($saleData);
                           }
                        }
                        else{
                            $dec=DB::table('stockitems')
                               ->where('stock_item_id', '=', $d->stock_item_id)
                               ->where('branch_id', '=', $branch_id)
                               ->where('available_quantity', '>=', $qty)
                               ->decrement('available_quantity', $qty); 
                                if($dec){
                                    $minus_qty=$minus_qty+$qty;
                                    $qty_last=$qty;
                                    $qty=$qty-$qty;
                                    
                                    $saleData=array();
                                    $saleData['sale_invoice_id']=$sale_invoice_id;
                                    $saleData['item_id']=$eachItem['item_id'];
                                    $saleData['branch_id']=$branch_id;
                                    $saleData['price_id']=$d->price_id;
                                    $saleData['quantity']=$qty_last;
                                    $saleData['discount']=($eachItem['discount']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['tax']=($eachItem['tax']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['amount']=($eachItem['total']/$eachItem['sale_quantity'])*$qty_last;
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
            $receipt_info['customer_full_name']=Session::get("sale_invoice_info.full_name");
            $receipt_info['cust_address']=Session::get("sale_invoice_info.cust_address");
            $receipt_info['customer_due']=Session::get("sale_invoice_info.customer_due");
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
            $receipt_info['branch_name'] = ($branch_id == 1) ? 'MB Trade' : (($branch_id == 2) ? "MB Collection" : 'MB Gulshan');
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

  // ============== @@ Sales Order @@ =================
  
    public function salesOrder()
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

        return View::make('sale.saleOrderForm',compact('items','customers','payment_type'));
    }
    public function saleOrderReceipt()
    {   
        return View::make('sale.saleOrderReceipt');
    }

    public function selectDeleteCustomerForOrder()
    {
        $vdata=Input::all();
        if(isset($vdata['customer']) == 'delete'){
          Session::forget("sale_order_invoice_info.cus_id");
          Session::forget("sale_order_invoice_info.user_name");
          Session::forget("sale_order_invoice_info.discount_percent");
          Session::forget("sale_order_invoice_info.customer_due");
          Session::forget("sale_order_invoice_info.full_name");
          Session::forget("sale_order_invoice_info.cust_address");
        } else {
                $selectCustomer = DB::table('customerinfos')->where('national_id', '=', $vdata['national_id'])
                    ->first();
                $vdata['cus_id'] = $selectCustomer->cus_id;
                $qdata = DB::table('customerinfos')
                    ->leftjoin('customertypes', 'customertypes.cus_type_id', '=', 'customerinfos.cus_type_id')
                    ->where('cus_id', '=', $vdata['cus_id'])
                    ->first();
          if($qdata){
            Session::put("sale_order_invoice_info.cus_id", $vdata['cus_id']);
            Session::put("sale_order_invoice_info.user_name", $qdata->user_name);
            Session::put("sale_order_invoice_info.full_name", $qdata->full_name);
            Session::put("sale_order_invoice_info.cust_address", $qdata->present_address);
            Session::put("sale_order_invoice_info.discount_percent", $qdata->discount_percent);
            Session::put("sale_order_invoice_info.customer_due", $qdata->due);
          }
        }
        return Redirect::to('sale/salesOrder');
    }
    public function itemAddTochartForSaleOrder()
    {
        $item_id = Input::get('item_id');
        $datas = DB::table('stockitems as s')
            ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.tax_amount','i.offer','i.unit','i.carton', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
            ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
            ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
            ->where('s.status', '=', 1)
            ->where('s.branch_id', '=', Session::get('branch_id'))
            ->where('i.upc_code', '=', $item_id)
            ->groupBy('p.sale_price')
            ->get();
        if(!$datas){
            return Redirect::to('sale/salesOrder')->with('errorMsg', "This product are not available in the stock");
        }
        foreach($datas as $data){
            $item_info=array();
            $item_info['stock_item_id'] = $data->stock_item_id;
            $stock_item_id           = $item_info['stock_item_id'];
            $item_info['row_no']     = $data->row_no;
            $item_info['item_id']    = $data->item_id;
            $item_info['item_name']  = $data->item_name;
            $item_info['price_id']   = $data->price_id;
            $key = $item_info['key'] = $item_info['item_id'].'-'.$item_info['price_id'];
            $item_info['offer']      = $data->offer;
            $item_info['unit']       = $data->unit;
            $item_info['pcs_per_carton'] = $data->carton;
            
           // $item_info['purchase_price']=$data->purchase_price;
            $item_info['sale_price'] = $data->sale_price;
            $item_info['purchase_price'] = $data->purchase_price;
            $item_info['available_quantity'] = $data->available_quantity;
            if($data->available_quantity >= 1)
                $item_info['sale_quantity'] = 1;
            else
            $item_info['sale_quantity'] = 0;
            $item_info['dozz']      = ($item_info['sale_quantity']/12);
            $item_info['set']       = ($item_info['sale_quantity']/1);
            $item_info['carton']    = ($item_info['sale_quantity']/$data->carton);
            $item_info['discount']  = ($data->sale_price/100)*$data->offer;
            $item_info['discount']  = round($item_info['discount'],2);
            $sub_total = ($data->sale_price*$item_info['sale_quantity'])-$item_info['discount'];
            $item_info['tax']=($sub_total/100)*$data->tax_amount;
            $item_info['total']=$sub_total+$item_info['tax'];
            $item_id=$data->item_id;
            //echo'<pre>';print_r(Session::get("saleItems.$key"));exit;
                $appendFlag = false;
            if(Session::get("saleOrderItems.$key")){
                $appendFlag = true;
                $item_info['stock_item_id']=Session::get("saleOrderItems.$key.stock_item_id");
                if(($data->available_quantity > Session::get("saleOrderItems.$key.sale_quantity")) && Session::get("saleOrderItems.$key.sale_quantity") > 0)
                    $item_info['sale_quantity']=Session::get("saleOrderItems.$key.sale_quantity")+1;
                else
                    $item_info['sale_quantity']=Session::get("saleOrderItems.$key.sale_quantity");
               // $item_info['purchase_price']=Session::get("saleOrderItems.$item_id.purchase_price");
                $item_info['price_id']=Session::get("saleOrderItems.$key.price_id");
                $item_info['sale_price']=Session::get("saleOrderItems.$key.sale_price");
                $item_info['discount']=((($data->sale_price/100)*$data->offer)*$item_info['sale_quantity']);
                $item_info['discount']=round($item_info['discount'],2);
                $sub_total=($data->sale_price*$item_info['sale_quantity'])-$item_info['discount'];
               
                $item_info['tax']=($sub_total/100)*$data->tax_amount;
                $item_info['pcs_per_carton'] = $data->carton;
                $item_info['unit']=$data->unit;
                $item_info['carton']=($item_info['sale_quantity']/$data->carton);
                $item_info['dozz']      = ($item_info['sale_quantity']/12);
                $item_info['set']       = ($item_info['sale_quantity']/1);
                $item_info['tax']=round($item_info['tax'],2);
                $item_info['total']=$sub_total+$item_info['tax'];
            }
            $item_info['total']=round($item_info['total'],2);
            Session::put("saleOrderItems.$key", $item_info);
        }
      //  echo'<pre>';print_r(Session::get('saleOrderItems'));exit;
        return Response::json(['success' => true,'append_flag' => $appendFlag,'item' => $item_info, 'allItem' => count(Session::get("saleOrderItems"))]);
   }

    public function emptySaleOrderCart()
    {
        if(Session::get('saleOrderItems')){
            Session::forget('saleOrderItems');
        }
        return Redirect::to('sale/salesOrder');
    }

    public function saleOrderFormCalculate()
    {
        $datas = Session::get('saleOrderItems');
        $subTotalAmount = 0;
        $totalItem = 0;
        $totalItemQuantity = 0;
        $totalItem = 0;
        $calculate = [];
        foreach($datas as $data){
            $subTotalAmount += $data['sale_price'] * $data['sale_quantity'];
            $totalItemQuantity += $data['sale_quantity'];
            $totalItem++;
        }
        $calculate['subTotalAmount'] = $subTotalAmount;
        $calculate['totalItemQuantity'] = $totalItemQuantity;
        $calculate['totalItem'] = $totalItem;
        return $calculate;
    }
    public function editDeleteItemForSaleOrder()
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
            ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.tax_amount','i.offer','i.unit','i.carton', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
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
        $item_info['unit']=$data->unit;
        $item_info['pcs_per_carton']=$data->carton;
        $item_info['price_id']=$data->price_id;
        $item_info['sale_price']=$vdata['sale_price'];
        $item_info['purchase_price']=$vdata['purchase_price'];
        $item_info['available_quantity']=$data->available_quantity;
        
        if($data->available_quantity>$vdata['sale_quantity']){
            $item_info['sale_quantity']=$vdata['sale_quantity'];
        }else{
            $item_info['sale_quantity']=$data->available_quantity;
        }
        if($data->available_quantity>($vdata['sale_quantity_carton']*$item_info['pcs_per_carton'])){
            $item_info['carton'] = $vdata['sale_quantity_carton'];
        }else{
            $item_info['carton']=$data->available_quantity/$item_info['pcs_per_carton'];
        }

        if($data->available_quantity>($vdata['sale_quantity_dozz']*12)){
            $item_info['dozz'] = $vdata['sale_quantity_dozz'];
        }else{
            $item_info['dozz']=$data->available_quantity/12;
        }

        if($data->available_quantity>($vdata['sale_quantity_set']*1)){
            $item_info['set'] = $vdata['sale_quantity_set'];
        }else{
            $item_info['set']=$data->available_quantity/1;
        }

        if($item_info['sale_quantity'] != Session::get("saleOrderItems.$key.sale_quantity")){
            $item_info['dozz'] = $item_info['sale_quantity']/12;
            $item_info['set'] = $item_info['sale_quantity']/1;
            $item_info['carton'] = $item_info['sale_quantity'] / $item_info['pcs_per_carton'];
        }else if($item_info['carton'] != Session::get("saleOrderItems.$key.carton")){
            $item_info['sale_quantity'] = round($item_info['carton'] * $item_info['pcs_per_carton']);
            $item_info['dozz'] = round($item_info['carton'] * $item_info['pcs_per_carton'])/12;
            $item_info['set'] = round($item_info['carton'] * $item_info['pcs_per_carton'])/1;
        }else if($item_info['dozz'] != Session::get("saleOrderItems.$key.dozz")){
            $item_info['sale_quantity'] = round($item_info['dozz'] * 12);
            $item_info['set'] = round($item_info['dozz'] * 12)/1;
            $item_info['carton'] = round($item_info['dozz'] * 12) / $item_info['pcs_per_carton'];
        }else if($item_info['set'] != Session::get("saleOrderItems.$key.set")){
            $item_info['sale_quantity'] = round($item_info['set'] * 1);
            $item_info['dozz'] = round($item_info['set'] * 1)/12;
            $item_info['carton'] = round($item_info['set'] * 1)/$item_info['pcs_per_carton'];
        }

        if($vdata['sale_quantity']==0){
            $item_info['sale_quantity']=Session::get("saleOrderItems.$key.sale_quantity");
            $item_info['carton']= Session::get("saleOrderItems.$key.sale_quantity")/$item_info['pcs_per_carton'];
        }
        
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
        $sub_total=($vdata['sale_price']*$item_info['sale_quantity'])-$item_info['discount'];
       
        $item_info['tax']=(($sub_total/100)*$data->tax_amount);
        $item_info['tax']=round($item_info['tax'],2);
        $item_info['total']=$sub_total+$item_info['tax'];
        $item_info['total']=round($item_info['total'],2);

        Session::put("saleOrderItems.$key", $item_info);
        return Response::json(['success' => true,'item_info' => $item_info]);
    }
    else{
      Session::forget("saleOrderItems.$key");
      return Response::json(['success' => true]);
    }
  }
    public function invoiceAndSaleOrder()
    {
        $branch_id = Session::get('branch_id');
        if(!Session::get("saleOrderItems"))
            return Redirect::to('sale/salesOrder')->with('errorMsg', "Sorry!  now you do not select item for sale order");
            #### ----  Invoice Part ----####
            DB::beginTransaction();
            try {
                $receipt_info = array();
                $vdata = Input::all();
                // return $vdata;
                $data = array();
                $cus_id = Session::get('sale_order_invoice_info.cus_id');
                 //echo $cus_id;exit;
                $payment_type_id = $vdata['payment_type_id'];
                $discount = (empty($vdata['invoice_discount']))?0:$vdata['invoice_discount'];
                $amount = 0;
                // $totalPoint = $vdata['totalPoint'];
                foreach(Session::get('saleOrderItems') as $item){
                    $amount = $amount + $item['total'];
                }
                $amount = $amount-$discount;
                $amount = round($amount,2);
                $pay = 0;             
                $due = $amount-($pay);            
                $pay_note = 0;             
                $transaction_date = $vdata['date'];
                $created_by = Session::get('emp_id');
                $created_at = $this->timestamp;           
                $payable_amount = $amount-$discount;
                $point = (int)($payable_amount*0.01);
                $date = date('ymd');
                $insert = DB::select("insert into saleinvoices_order (branch_id,sale_order_invoice_id,cus_id,payment_type_id,discount,amount,pay,due,pay_note,date,created_by,created_at) values
               ('$branch_id',ifnull(concat('$date',1+(
                SELECT right(sale_inv.sale_order_invoice_id, 8) AS LAST8 FROM saleinvoices_order as sale_inv
                  where( (SELECT left(sale_inv.sale_order_invoice_id, 6)='$date'))
                  order by LAST8 desc limit 1)),concat('$date','10000000')),
                  '$cus_id','$payment_type_id','$discount','$amount','$pay','$due','$pay_note','$transaction_date','$created_by','$created_at')");
             $last_insert_id = DB::getPdo()->lastInsertId();
             $value = DB::table('saleinvoices_order')
                ->select('sale_order_invoice_id')
                ->where('id', '=', $last_insert_id)
                ->first();
            $sale_invoice_id = $value->sale_order_invoice_id;
            //@@========no need due increment
            // DB::table('customerinfos')
            //        ->where('cus_id', '=', $cus_id)
            //        ->increment('due', $due);
            //@@========no need due increment
        #### ---- End of Invoice Part ----####
        ### --- Item Sale Part --- ###
             $receipt_item_infos = array();
             foreach(Session::get("saleOrderItems") as $eachItem){
                 $receipt_item_infos[] = $eachItem;
                  $key = $eachItem['key'];
                ### --- Available Quantity from one row --- ###
                if($eachItem['row_no'] == 1){
                    //@@========no need decrement stock
                    // $decrease_stock = DB::table('stockitems')
                    //    ->where('stock_item_id', '=', $eachItem['stock_item_id'])
                    //    ->where('available_quantity', '>=', $eachItem['sale_quantity'])
                    //    ->decrement('available_quantity', $eachItem['sale_quantity']);
                       //@@========no need decrement stock
                    // if($decrease_stock){
                        $saleData = array();
                        $saleData['sale_order_invoice_id'] = $sale_invoice_id;
                        $saleData['branch_id']       = $branch_id;
                        $saleData['item_id']         = $eachItem['item_id'];
                        $saleData['price_id']        = $eachItem['price_id'];
                        $saleData['quantity']        = $eachItem['sale_quantity'];
                        $saleData['discount']        = $eachItem['discount'];
                        $saleData['tax']             = $eachItem['tax'];
                        $saleData['amount']          = $eachItem['total'];
                        $saleData['created_by']      = Session::get('emp_id');
                        $saleData['created_at']      = $this->timestamp;
                        $stock_item_id               = $eachItem['stock_item_id'];
                        
                        $sale = DB::table('itemsales_order')->insert($saleData);
                        if($sale){
                            Session::forget("saleOrderItems.$key");
                        }
                    // }
                }
                ### --- Ending of Available Quantity from one row --- ###
                ### --- Available Quantity from Multiple rows --- ###
                else{
                    $minus_qty = 0;
                    $qty = $eachItem['sale_quantity'];
                    $tempData = DB::table('stockitems')
                        ->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                        ->where('stockitems.status', '=', 1)
                        ->where('stockitems.branch_id', '=', $branch_id)
                        ->where('stockitems.item_id', '=', $eachItem['item_id'])
                        ->where('priceinfos.sale_price', '=', $eachItem['sale_price'])
                        ->orderBy('stockitems.available_quantity', 'asc')
                        ->get();
                    if(count($tempData)<1){
                        $tempData = DB::table('stockitems')
                            ->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                            ->where('stockitems.status', '=', 1)
                            ->where('stockitems.branch_id', '=', $branch_id)
                            ->where('stockitems.item_id', '=', $eachItem['item_id'])
                            ->orderBy('stockitems.available_quantity', 'asc')
                            ->get();
                    }
                    foreach($tempData as $d){
                        if($qty <= 0)
                            continue;
                        if($qty>$d->available_quantity){
                       // $decrease = DB::table('stockitems')
                       //     ->where('branch_id', '=', $branch_id)
                       //     ->where('stock_item_id', '=', $d->stock_item_id)
                       //     ->where('available_quantity', '>=', $d->available_quantity)
                       //     ->decrement('available_quantity', $d->available_quantity); 
                           // if($decrease){
                                $qty = $qty-$d->available_quantity;
                                $minus_qty=$minus_qty+$d->available_quantity;
                                $saleData=array();
                                $saleData['sale_order_invoice_id']=$sale_invoice_id;
                                $saleData['item_id']=$eachItem['item_id'];
                                $saleData['branch_id']=$branch_id;
                                $saleData['price_id']=$d->price_id;
                                $saleData['quantity']=$d->available_quantity;
                                $saleData['discount']=($eachItem['discount']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['tax']=($eachItem['tax']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['amount']=($eachItem['total']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['created_by']=Session::get('emp_id');
                                $saleData['created_at']=$this->timestamp;
                                $stock_item_id=$eachItem['stock_item_id'];
                                $sale= DB::table('itemsales_order')->insert($saleData);
                           // }
                        }
                        else{
                            // $dec=DB::table('stockitems')
                            //    ->where('stock_item_id', '=', $d->stock_item_id)
                            //    ->where('branch_id', '=', $branch_id)
                            //    ->where('available_quantity', '>=', $qty)
                            //    ->decrement('available_quantity', $qty); 
                                // if($dec){
                                    $minus_qty=$minus_qty+$qty;
                                    $qty_last=$qty;
                                    $qty=$qty-$qty;
                                    $saleData=array();
                                    $saleData['sale_order_invoice_id']=$sale_invoice_id;
                                    $saleData['item_id']=$eachItem['item_id'];
                                    $saleData['branch_id']=$branch_id;
                                    $saleData['price_id']=$d->price_id;
                                    $saleData['quantity']=$qty_last;
                                    $saleData['discount']=($eachItem['discount']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['tax']=($eachItem['tax']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['amount']=($eachItem['total']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['created_by']=Session::get('emp_id');
                                    $saleData['created_at']=$this->timestamp;
                                    $sale = DB::table('itemsales_order')->insert($saleData);
                                // }
                            }
                    }
                    if($minus_qty != $eachItem['sale_quantity']){
                    DB::rollback();
                    return Redirect::to('sale/salesOrder')->with('errorMsg', 'Sales Quantity are not available in the stock.');
                }
                else {
                     Session::forget("saleOrderItems.$key");
                }
            }
                ### --- Ending of Available Quantity from Multiple rows --- ###
            }
            $receipt_info['customer_name'] = Session::get("sale_order_invoice_info.user_name");
            $receipt_info['customer_full_name'] = Session::get("sale_order_invoice_info.full_name");
            $receipt_info['cust_address'] = Session::get("sale_order_invoice_info.cust_address");
            $receipt_info['customer_due'] = Session::get("sale_order_invoice_info.customer_due");
            $receipt_info['date'] = $transaction_date;
            $receipt_info['created_at'] = $this->timestamp;
            $receipt_info['invoice_id'] = $sale_invoice_id;
            $emp_info = DB::table('empinfos')
                ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                ->first();
            $payment_type_info = DB::table('paymenttypes')
                ->where('paymenttypes.payment_type_id', '=', $payment_type_id)
                ->first();
            $receipt_info['emp_name'] = $emp_info->user_name;
            $receipt_info['branch_name'] = ($branch_id == 1) ? 'MB Trade' : (($branch_id == 2) ? "MB Collection" : 'MB Gulshan');
            $receipt_info['total_amount'] = $amount;
            $receipt_info['payment_type_name'] = $payment_type_info->payment_type_name;
            $receipt_info['invoice_discount'] = $discount;
            $receipt_info['pay'] = $pay;
            $receipt_info['due'] = $due;
            $receipt_info['pay_note'] = $pay_note;
            Session::forget("sale_order_invoice_info");
            DB::table('stockitems')
                ->where('available_quantity', '<=', 0)
                ->update(array('status' => 0));
            $company_info=DB::table('companyprofiles')
                ->first();
            DB::commit();
            return Redirect::to('sale/saleOrderReceipt')->with('receipt_item_infos', $receipt_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
        } catch (Exception $e) {
            return $e;
            DB::rollback();
        return Redirect::to('sale/salesOrder')->with('errorMsg', 'Something is wrong in sales.');
        }
    }

    public function sendOrderToSale($saleReportInvoiceId){
        if(Session::has('saleItems')){
            Session::forget('saleItems');
        }
        $item_infos = DB::table('itemsales_order')
            ->join('saleinvoices_order', 'itemsales_order.sale_order_invoice_id', '=', 'saleinvoices_order.sale_order_invoice_id')
            ->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'itemsales_order.item_id')
            ->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'itemsales_order.price_id')
            ->leftJoin('customerinfos', 'saleinvoices_order.cus_id', '=', 'customerinfos.cus_id')
            ->select(['itemsales_order.quantity', 'itemsales_order.amount', 'ii.item_name','ii.upc_code','ii.item_id', 'pi.sale_price','pi.price_id','customerinfos.national_id'])
            ->where('itemsales_order.sale_order_invoice_id', $saleReportInvoiceId)
            ->where('itemsales_order.status', 1)
            ->get(); 

        foreach($item_infos as $key => $order_item_info){
            if($key == 0){
                if(Session::has("sale_invoice_info")){
                    Session::forget("sale_invoice_info");
                }
                $selectCustomer=DB::table('customerinfos')->where('national_id', '=', $order_item_info->national_id)->first();
                $vdata['cus_id']=$selectCustomer->cus_id;
                $qdata=DB::table('customerinfos')
                    ->leftjoin('customertypes', 'customertypes.cus_type_id', '=', 'customerinfos.cus_type_id')
                    ->where('cus_id', '=', $vdata['cus_id'])
                    ->first();
            if($qdata){
                    Session::put("sale_invoice_info.cus_id", $vdata['cus_id']);
                    Session::put("sale_invoice_info.user_name", $qdata->user_name);
                    Session::put("sale_invoice_info.full_name", $qdata->full_name);
                    Session::put("sale_invoice_info.cust_address", $qdata->present_address);
                    Session::put("sale_invoice_info.discount_percent", $qdata->discount_percent);
                    Session::put("sale_invoice_info.customer_due", $qdata->due);
                }
            }
            $item_id = $order_item_info->upc_code;
            $datas = DB::table('stockitems as s')
                ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.tax_amount','i.offer','i.unit','i.carton', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
                ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
                ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
                ->where('s.status', '=', 1)
                ->where('s.branch_id', '=', Session::get('branch_id'))
                ->where('i.upc_code', '=', $item_id)
                ->groupBy('p.sale_price')
                ->get();
            if(count($datas)>0){
                foreach($datas as $data){
                    $item_info=array();
                    $item_info['stock_item_id']=$data->stock_item_id;
                    $stock_item_id           = $item_info['stock_item_id'];
                    $item_info['row_no']     = $data->row_no;
                    $item_info['item_id']    = $data->item_id;
                    $item_info['item_name']  = $data->item_name;
                    $item_info['price_id']   = $data->price_id;
                    $key = $item_info['key'] = $item_info['item_id'].'-'.$item_info['price_id'];
                    $item_info['offer']      = $data->offer;
                    $item_info['unit']       = $data->unit;
                    $item_info['pcs_per_carton'] = $data->carton;
                    
                    $item_info['sale_price'] = ($order_item_info->amount/$order_item_info->quantity);
                    $item_info['purchase_price'] = $data->purchase_price;
                    $item_info['available_quantity'] = $data->available_quantity;
                    if($data->available_quantity >= $order_item_info->quantity){
                        $item_info['sale_quantity'] = $order_item_info->quantity;
                    }else{
                        $item_info['sale_quantity'] = $data->available_quantity;
                    }
                    $item_info['dozz']      = ($item_info['sale_quantity']/12);
                    $item_info['set']       = ($item_info['sale_quantity']/1);
                    $item_info['carton']    = ($item_info['sale_quantity']/$data->carton);
                    $item_info['discount']  = ($item_info['sale_price']/100)*$data->offer;
                    $item_info['discount']  = round($item_info['discount'],2);
                    $sub_total = ($item_info['sale_price']*$item_info['sale_quantity'])-$item_info['discount'];
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
                        $item_info['pcs_per_carton'] = $data->carton;
                        $item_info['unit']=$data->unit;
                        $item_info['carton']=($item_info['sale_quantity']/$data->carton);
                        $item_info['dozz']      = ($item_info['sale_quantity']/12);
                        $item_info['set']       = ($item_info['sale_quantity']/1);
                        $item_info['tax']=round($item_info['tax'],2);
                        $item_info['total']=$sub_total+$item_info['tax'];
                    }
                    $item_info['total']=round($item_info['total'],2);
                    
                    Session::put("saleItems.$key", $item_info);
                }
            }
        }
        
      //  echo'<pre>';print_r(Session::get('saleItems'));exit;
        return Redirect::to('sale/sales');
    }
    
    public function editSaleOrder($saleReportInvoiceId){
        if(Session::has('saleOrderItems')){
            Session::forget('saleOrderItems');
        }
        $item_infos = DB::table('itemsales_order')
            ->join('saleinvoices_order', 'itemsales_order.sale_order_invoice_id', '=', 'saleinvoices_order.sale_order_invoice_id')
            ->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'itemsales_order.item_id')
            ->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'itemsales_order.price_id')
            ->leftJoin('customerinfos', 'saleinvoices_order.cus_id', '=', 'customerinfos.cus_id')
            ->select(['itemsales_order.quantity', 'itemsales_order.amount', 'ii.item_name','ii.upc_code','ii.item_id', 'pi.sale_price','pi.price_id','customerinfos.national_id'])
            ->where('itemsales_order.sale_order_invoice_id', $saleReportInvoiceId)
            ->where('itemsales_order.status', 1)
            ->get(); 

        foreach($item_infos as $key => $order_item_info){
            if($key == 0){
                if(Session::has("sale_order_invoice_info")){
                    Session::forget("sale_order_invoice_info");
                }
                $selectCustomer=DB::table('customerinfos')->where('national_id', '=', $order_item_info->national_id)->first();
                $vdata['cus_id']=$selectCustomer->cus_id;
                $qdata=DB::table('customerinfos')
                    ->leftjoin('customertypes', 'customertypes.cus_type_id', '=', 'customerinfos.cus_type_id')
                    ->where('cus_id', '=', $vdata['cus_id'])
                    ->first();
            if($qdata){
                    Session::put("sale_order_invoice_info.cus_id", $vdata['cus_id']);
                    Session::put("sale_order_invoice_info.user_name", $qdata->user_name);
                    Session::put("sale_order_invoice_info.full_name", $qdata->full_name);
                    Session::put("sale_order_invoice_info.cust_address", $qdata->present_address);
                    Session::put("sale_order_invoice_info.discount_percent", $qdata->discount_percent);
                    Session::put("sale_order_invoice_info.customer_due", $qdata->due);
                }
            }
            $item_id = $order_item_info->upc_code;
            $datas = DB::table('stockitems as s')
                ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.tax_amount','i.offer','i.unit','i.carton', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
                ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
                ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
                ->where('s.status', '=', 1)
                ->where('s.branch_id', '=', Session::get('branch_id'))
                ->where('i.upc_code', '=', $item_id)
                ->groupBy('p.sale_price')
                ->get();
            if(count($datas)>0){
                foreach($datas as $data){
                    $item_info=array();
                    $item_info['stock_item_id']=$data->stock_item_id;
                    $stock_item_id           = $item_info['stock_item_id'];
                    $item_info['row_no']     = $data->row_no;
                    $item_info['item_id']    = $data->item_id;
                    $item_info['item_name']  = $data->item_name;
                    $item_info['price_id']   = $data->price_id;
                    $key = $item_info['key'] = $item_info['item_id'].'-'.$item_info['price_id'];
                    $item_info['offer']      = $data->offer;
                    $item_info['unit']       = $data->unit;
                    $item_info['pcs_per_carton'] = $data->carton;
                    
                    $item_info['sale_price'] = ($order_item_info->amount/$order_item_info->quantity);
                    $item_info['purchase_price'] = $data->purchase_price;
                    $item_info['available_quantity'] = $data->available_quantity;
                    if($data->available_quantity >= $order_item_info->quantity){
                        $item_info['sale_quantity'] = $order_item_info->quantity;
                    }else{
                        $item_info['sale_quantity'] = $data->available_quantity;
                    }
                    $item_info['dozz']      = ($item_info['sale_quantity']/12);
                    $item_info['set']       = ($item_info['sale_quantity']/1);
                    $item_info['carton']    = ($item_info['sale_quantity']/$data->carton);
                    $item_info['discount']  = ($item_info['sale_price']/100)*$data->offer;
                    $item_info['discount']  = round($item_info['discount'],2);
                    $sub_total = ($item_info['sale_price']*$item_info['sale_quantity'])-$item_info['discount'];
                    $item_info['tax']=($sub_total/100)*$data->tax_amount;
                    $item_info['total']=$sub_total+$item_info['tax'];
                    $item_id=$data->item_id;
                    //echo'<pre>';print_r(Session::get("saleOrderItems.$key"));exit;
                    if(Session::get("saleOrderItems.$key")){
                        $item_info['stock_item_id']=Session::get("saleOrderItems.$key.stock_item_id");
                        if(($data->available_quantity > Session::get("saleOrderItems.$key.sale_quantity")) && Session::get("saleOrderItems.$key.sale_quantity") > 0)
                            $item_info['sale_quantity']=Session::get("saleOrderItems.$key.sale_quantity")+1;
                        else
                            $item_info['sale_quantity']=Session::get("saleOrderItems.$key.sale_quantity");
                       // $item_info['purchase_price']=Session::get("saleOrderItems.$item_id.purchase_price");
                        $item_info['price_id']=Session::get("saleOrderItems.$key.price_id");
                        $item_info['sale_price']=Session::get("saleOrderItems.$key.sale_price");
                        $item_info['discount']=((($data->sale_price/100)*$data->offer)*$item_info['sale_quantity']);
                        $item_info['discount']=round($item_info['discount'],2);
                        $sub_total=($data->sale_price*$item_info['sale_quantity'])-$item_info['discount'];
                       
                        $item_info['tax']=($sub_total/100)*$data->tax_amount;
                        $item_info['pcs_per_carton'] = $data->carton;
                        $item_info['unit']=$data->unit;
                        $item_info['carton']=($item_info['sale_quantity']/$data->carton);
                        $item_info['dozz']      = ($item_info['sale_quantity']/12);
                        $item_info['set']       = ($item_info['sale_quantity']/1);
                        $item_info['tax']=round($item_info['tax'],2);
                        $item_info['total']=$sub_total+$item_info['tax'];
                    }
                    $item_info['total']=round($item_info['total'],2);
                    Session::put("saleOrderItems.$key", $item_info);
                }
            }
        }
      //  echo'<pre>';print_r(Session::get('saleOrderItems'));exit;
        Session::put("oldOrderInvoiceId", $saleReportInvoiceId);
        return Redirect::to('sale/salesOrder');
    }

    public function editInvoiceAndSaleOrder()
    {
        $branch_id = Session::get('branch_id');
        $saleOrderInvoiceId = Session::get("oldOrderInvoiceId");
        if(!Session::get("saleOrderItems"))
            return Redirect::to('sale/salesOrder')->with('errorMsg', "Sorry!  now you do not select item for sale order");
            #### ----  Invoice Part ----####
            DB::beginTransaction();
            try {
                $receipt_info = array();
                $vdata = Input::all();
                // return $vdata;
                $data = array();
                $cus_id = Session::get('sale_order_invoice_info.cus_id');
                 //echo $cus_id;exit;
                $payment_type_id = $vdata['payment_type_id'];
                $discount = (empty($vdata['invoice_discount']))?0:$vdata['invoice_discount'];
                $amount = 0;
                // $totalPoint = $vdata['totalPoint'];
                foreach(Session::get('saleOrderItems') as $item){
                    $amount = $amount + $item['total'];
                }
                $amount = $amount-$discount;
                $amount = round($amount,2);
                $pay = $vdata['pay'];             
                $due = $amount-($pay);            
                $pay_note = (empty($vdata['paynote']))?$pay:$vdata['paynote'];             
                $transaction_date = $vdata['date'];
                $created_by = Session::get('emp_id');
                $created_at = $this->timestamp;           
                $payable_amount = $amount-$discount;
                $point = (int)($payable_amount*0.01);
                $date = date('ymd');

                if($saleOrderInvoiceId>0){
                    $deleteItemSalesOrder = DB::table('itemsales_order')
                        ->where('sale_order_invoice_id',$saleOrderInvoiceId)
                        ->delete();
                    $deleteSaleOrderInvoice = DB::table('saleinvoices_order')
                        ->where('sale_order_invoice_id',$saleOrderInvoiceId)
                        ->delete();
                }else{
                    return Redirect::to('sale/salesOrder')->with('errorMsg', "Sorry!  Invocie Not Found");
                }
                $insert = DB::select("insert into saleinvoices_order (branch_id,sale_order_invoice_id,cus_id,payment_type_id,discount,amount,pay,due,pay_note,date,created_by,created_at) values
               ('$branch_id','$saleOrderInvoiceId','$cus_id','$payment_type_id','$discount','$amount','$pay','$due','$pay_note','$transaction_date','$created_by','$created_at')");
             $last_insert_id = DB::getPdo()->lastInsertId();
             // $value = DB::table('saleinvoices_order')
             //    ->select('sale_order_invoice_id')
             //    ->where('id', '=', $last_insert_id)
             //    ->first();
            $sale_invoice_id = $saleOrderInvoiceId;
            //@@========no need due increment
            // DB::table('customerinfos')
            //        ->where('cus_id', '=', $cus_id)
            //        ->increment('due', $due);
            //@@========no need due increment
        #### ---- End of Invoice Part ----####
        ### --- Item Sale Part --- ###
             $receipt_item_infos = array();
             foreach(Session::get("saleOrderItems") as $eachItem){
                 $receipt_item_infos[] = $eachItem;
                  $key = $eachItem['key'];
                ### --- Available Quantity from one row --- ###
                if($eachItem['row_no'] == 1){
                    //@@========no need decrement stock
                    // $decrease_stock = DB::table('stockitems')
                    //    ->where('stock_item_id', '=', $eachItem['stock_item_id'])
                    //    ->where('available_quantity', '>=', $eachItem['sale_quantity'])
                    //    ->decrement('available_quantity', $eachItem['sale_quantity']);
                       //@@========no need decrement stock
                    // if($decrease_stock){
                        $saleData = array();
                        $saleData['sale_order_invoice_id'] = $sale_invoice_id;
                        $saleData['branch_id']       = $branch_id;
                        $saleData['item_id']         = $eachItem['item_id'];
                        $saleData['price_id']        = $eachItem['price_id'];
                        $saleData['quantity']        = $eachItem['sale_quantity'];
                        $saleData['discount']        = $eachItem['discount'];
                        $saleData['tax']             = $eachItem['tax'];
                        $saleData['amount']          = $eachItem['total'];
                        $saleData['created_by']      = Session::get('emp_id');
                        $saleData['created_at']      = $this->timestamp;
                        $stock_item_id               = $eachItem['stock_item_id'];
                        
                        $sale = DB::table('itemsales_order')->insert($saleData);
                        if($sale){
                            Session::forget("saleOrderItems.$key");
                        }
                    // }
                }
                ### --- Ending of Available Quantity from one row --- ###
                ### --- Available Quantity from Multiple rows --- ###
                else{
                    $minus_qty = 0;
                    $qty = $eachItem['sale_quantity'];
                    $tempData = DB::table('stockitems')
                        ->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                        ->where('stockitems.status', '=', 1)
                        ->where('stockitems.branch_id', '=', $branch_id)
                        ->where('stockitems.item_id', '=', $eachItem['item_id'])
                        ->where('priceinfos.sale_price', '=', $eachItem['sale_price'])
                        ->orderBy('stockitems.available_quantity', 'asc')
                        ->get();
                    if(count($tempData)<1){
                        $tempData = DB::table('stockitems')
                            ->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                            ->where('stockitems.status', '=', 1)
                            ->where('stockitems.branch_id', '=', $branch_id)
                            ->where('stockitems.item_id', '=', $eachItem['item_id'])
                            ->orderBy('stockitems.available_quantity', 'asc')
                            ->get();
                    }
                    foreach($tempData as $d){
                        if($qty <= 0)
                            continue;
                        if($qty>$d->available_quantity){
                       // $decrease = DB::table('stockitems')
                       //     ->where('branch_id', '=', $branch_id)
                       //     ->where('stock_item_id', '=', $d->stock_item_id)
                       //     ->where('available_quantity', '>=', $d->available_quantity)
                       //     ->decrement('available_quantity', $d->available_quantity); 
                           // if($decrease){
                                $qty = $qty-$d->available_quantity;
                                $minus_qty=$minus_qty+$d->available_quantity;
                                $saleData=array();
                                $saleData['sale_order_invoice_id']=$sale_invoice_id;
                                $saleData['item_id']=$eachItem['item_id'];
                                $saleData['branch_id']=$branch_id;
                                $saleData['price_id']=$d->price_id;
                                $saleData['quantity']=$d->available_quantity;
                                $saleData['discount']=($eachItem['discount']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['tax']=($eachItem['tax']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['amount']=($eachItem['total']/$eachItem['sale_quantity'])*$d->available_quantity;
                                $saleData['created_by']=Session::get('emp_id');
                                $saleData['created_at']=$this->timestamp;
                                $stock_item_id=$eachItem['stock_item_id'];
                                $sale= DB::table('itemsales_order')->insert($saleData);
                           // }
                        }
                        else{
                            // $dec=DB::table('stockitems')
                            //    ->where('stock_item_id', '=', $d->stock_item_id)
                            //    ->where('branch_id', '=', $branch_id)
                            //    ->where('available_quantity', '>=', $qty)
                            //    ->decrement('available_quantity', $qty); 
                                // if($dec){
                                    $minus_qty=$minus_qty+$qty;
                                    $qty_last=$qty;
                                    $qty=$qty-$qty;
                                    $saleData=array();
                                    $saleData['sale_order_invoice_id']=$sale_invoice_id;
                                    $saleData['item_id']=$eachItem['item_id'];
                                    $saleData['branch_id']=$branch_id;
                                    $saleData['price_id']=$d->price_id;
                                    $saleData['quantity']=$qty_last;
                                    $saleData['discount']=($eachItem['discount']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['tax']=($eachItem['tax']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['amount']=($eachItem['total']/$eachItem['sale_quantity'])*$qty_last;
                                    $saleData['created_by']=Session::get('emp_id');
                                    $saleData['created_at']=$this->timestamp;
                                    $sale = DB::table('itemsales_order')->insert($saleData);
                                // }
                            }
                    }
                    if($minus_qty != $eachItem['sale_quantity']){
                    DB::rollback();
                    return Redirect::to('sale/salesOrder')->with('errorMsg', 'Sales Quantity are not available in the stock.');
                }
                else {
                     Session::forget("saleOrderItems.$key");
                }
            }
                ### --- Ending of Available Quantity from Multiple rows --- ###
            }
            $receipt_info['customer_name'] = Session::get("sale_order_invoice_info.user_name");
            $receipt_info['customer_full_name'] = Session::get("sale_order_invoice_info.full_name");
            $receipt_info['cust_address'] = Session::get("sale_order_invoice_info.cust_address");
            $receipt_info['customer_due'] = Session::get("sale_order_invoice_info.customer_due");
            $receipt_info['date'] = $transaction_date;
            $receipt_info['created_at'] = $this->timestamp;
            $receipt_info['invoice_id'] = $sale_invoice_id;
            $emp_info = DB::table('empinfos')
                ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                ->first();
            $payment_type_info = DB::table('paymenttypes')
                ->where('paymenttypes.payment_type_id', '=', $payment_type_id)
                ->first();
            $receipt_info['emp_name'] = $emp_info->user_name;
            $receipt_info['branch_name'] = ($branch_id == 1) ? 'MB Trade' : (($branch_id == 2) ? "MB Collection" : 'MB Gulshan');
            $receipt_info['total_amount'] = $amount;
            $receipt_info['payment_type_name'] = $payment_type_info->payment_type_name;
            $receipt_info['invoice_discount'] = $discount;
            $receipt_info['pay'] = $pay;
            $receipt_info['due'] = $due;
            $receipt_info['pay_note'] = $pay_note;
            Session::forget("sale_order_invoice_info");
            DB::table('stockitems')
                ->where('available_quantity', '<=', 0)
                ->update(array('status' => 0));
            $company_info=DB::table('companyprofiles')
                ->first();
            DB::commit();
            return Redirect::to('sale/saleOrderReceipt')->with('receipt_item_infos', $receipt_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
        } catch (Exception $e) {
            // return $e;
            DB::rollback();
        return Redirect::to('sale/salesOrder')->with('errorMsg', 'Something is wrong in sales.');
        }
    }
    
    public function deleteSaleOrder($invoiceId = null)
    {
        $delete = DB::table('saleinvoices_order')
            ->where('sale_order_invoice_id',$invoiceId)
            ->update([
                'status' => 0,
                'updated_by' => Session::get('emp_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        if($delete){
            return Redirect::to('admin/saleOrder/report')->with('message', 'Sale Order Invoice Delete Successfull !');
        }
        return Redirect::to('admin/saleOrder/report')->with('errorMsg', 'Sale Order Invoice Delete Unsuccessfull !');
    }
  // ============== @@ Sales Order End @@ =================
  // ============== @@ Sales Order End @@ =================

    public function translate()
    {
        

    }

}
