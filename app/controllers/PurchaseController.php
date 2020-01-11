<?php
class PurchaseController extends \BaseController {
    public $timestamp;
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->timestamp = date('Y-m-d H:i:s');
    }
    public function index()
    {
        // Session::forget('items');
        $items = DB::table('iteminfos')->where('status', '=', 1)->get();
        $suppliers = DB::table('supplierinfos')->get();
        $payment_types = DB::table('paymenttypes')->where('status', '=', 1)->get();
        $payment_type=array();
        foreach ($payment_types as $value)
        $payment_type[$value->payment_type_id]=$value->payment_type_name;
        return View::make('purchase.purchaseForm',compact('items','suppliers','payment_type'));
    }
    public function autoItemSuggest(){
        $term = Input::get('q');
        $search_items = DB::table('iteminfos')->where('item_name', 'LIKE', '%'. $term .'%')
                  ->orWhere('upc_code', 'LIKE', '%'. $term .'%')
                          ->get();
        foreach($search_items as $key => $item):
                        $upc_code   =$item->upc_code;
                        $item_name = $item->item_name." (".$upc_code.")";
            $item_info = $upc_code.'|'.$item_name;
            echo  "$item_info\n";
        endforeach;
    }
    public function itemAddChart()
    {
            $item_id = Input::get('item_id');
            $data=DB::table('iteminfos')
                ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'iteminfos.price_id')
                ->select('iteminfos.item_id', 'iteminfos.item_name', 'priceinfos.price_id', 'priceinfos.purchase_price', 'priceinfos.sale_price')
                ->where('priceinfos.status', '=', 1)
                ->where('iteminfos.upc_code', '=', $item_id)
                //->orWhere('iteminfos.upc_code', '=', $item_id)
                ->first();
//echo'<pre>';print_r($data);exit;

            if(!$data){
                return Redirect::to('purchase/purchases')->with('errorMsg', "This product are not available in the item list");
            }
            $item_info=array();
            $item_info['item_id']=$data->item_id;
            $item_info['item_name']=$data->item_name;
            $item_info['price_id']=$data->price_id;
            $item_info['purchase_price']=$data->purchase_price;
            $item_info['sale_price']=$data->sale_price;
            if($item_info['purchase_price']>$item_info['sale_price'])
                $item_info['sale_price']=$item_info['purchase_price'];  
            $item_info['quantity']=1;
            $item_info['discount']=0;
            $item_info['discount']=round($item_info['discount'],2);
            $item_info['total']=($data->purchase_price*$item_info['quantity'])-$item_info['discount'];

            $item_id=$data->item_id;
            $appendFlag = false;
            if(Session::get("items.$item_id")){
                $appendFlag = true;
                $item_info['quantity']=Session::get("items.$item_id.quantity")+1;
                $item_info['purchase_price']=Session::get("items.$item_id.purchase_price");
                $item_info['sale_price']=Session::get("items.$item_id.sale_price");
                $item_info['discount']=Session::get("items.$item_id.discount");
                $item_info['discount']=round($item_info['discount'],2);
                $item_info['total']=(Session::get("items.$item_id.purchase_price")*$item_info['quantity'])-$item_info['discount'];
            }
            $item_info['total']=round($item_info['total'],2);

            Session::put("items.$item_id", $item_info);

            return Response::json(['success' => true,'append_flag' => $appendFlag,'item' => $item_info, 'allItem' => count(Session::get("items"))]);

        // return Redirect::to('purchase/purchases');
    }

    public function purchaseFormCalculate()
    {
        $datas = Session::get('items');
        $subTotalAmount = 0;
        $totalItem = 0;
        $totalItemQuantity = 0;
        $totalItem = 0;
        $calculate = [];
        foreach($datas as $data){
            $subTotalAmount += $data['purchase_price'] * $data['quantity'];
            $totalItemQuantity += $data['quantity'];
            $totalItem++;
        }
        $calculate['subTotalAmount'] = $subTotalAmount;
        $calculate['totalItemQuantity'] = $totalItemQuantity;
        $calculate['totalItem'] = $totalItem;
        return $calculate;
    }

    public function itemAddTochartExcel()
    {
    $array1 = self::firstArray();
    $array2 = self::secondArray();
    $array = array_merge($array1,$array2);
    echo "<pre>";
    // print_r($array);exit;
    // $array3 = self::thirdArray();
    //$arr1 = $arr2 = $arr3 = $diff = [];

    // foreach($array as $value){
    //     $arr1[] = $value['code'];
    // }
    // foreach($array2 as $value){
    //     $arr2[] = $value['code'];
    // }
    // foreach($array3 as $value){
    //     $arr3[] = $value['code'];
    // }

    // $diff = array_diff($arr3, $arr1);
    // print_r($array);
    // return;
    Session::forget("items");
    $dbData = [];
    $notAdded = [];
    $qtyLow = [];
    $sessionData = [];
            foreach($array as $datas){
                $dataObject = (object) $datas;
                $item_id = $dataObject->code;
                $data=DB::table('iteminfos')
                            ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'iteminfos.price_id')
                            ->select('iteminfos.item_id', 'iteminfos.item_name', 'priceinfos.price_id', 'priceinfos.purchase_price', 'priceinfos.sale_price')
                            ->where('priceinfos.status', '=', 1)
                            ->where('iteminfos.upc_code', '=', $item_id)
                            //->orWhere('iteminfos.upc_code', '=', $item_id)
                            ->first();
                // $dbData[] = $data->item_id;
                if(!$data){
                    $notAdded[] = $datas;
                }else{
                    if($data->sale_price == 0 || $data->purchase_price == 0){
                        $notPurchased[] = $datas;
                    }else if($data->sale_price < $data->purchase_price){
                        $salePriceGreaterThen[] = $datas;
                    }else if($dataObject->qty > 0){
                        $qtyLow[] = $datas;

                        $item_info=array();
                        $item_info['item_id']=$data->item_id;
                        $item_info['item_name']=$data->item_name;
                        $item_info['price_id']=$data->price_id;
                        $item_info['purchase_price']=($data->purchase_price > 0) ? $data->purchase_price : 1;
                        $item_info['sale_price']=$data->sale_price;
                        if($item_info['purchase_price']>$item_info['sale_price'])
                            $item_info['sale_price']=$item_info['purchase_price'];  
                        $item_info['quantity']=$dataObject->qty;
                        $item_info['discount']=0;
                        $item_info['discount']=round($item_info['discount'],2);
                        $item_info['total']=($data->purchase_price*$item_info['quantity'])-$item_info['discount'];

                        $item_id=$data->item_id;
                        if(Session::get("items.$item_id")){
                            $item_info['quantity']=Session::get("items.$item_id.quantity")+1;
                            $item_info['purchase_price']=Session::get("items.$item_id.purchase_price");
                            $item_info['sale_price']=Session::get("items.$item_id.sale_price");
                            $item_info['discount']=Session::get("items.$item_id.discount");
                            $item_info['discount']=round($item_info['discount'],2);
                            $item_info['total']=(Session::get("items.$item_id.purchase_price")*$item_info['quantity'])-$item_info['discount'];
                        }
                        $item_info['total']=round($item_info['total'],2);
                        // $sessionData[] = $item_id;
                        Session::put("items.$item_id", $item_info);
                    }
                }
            }
            // print_r($notPurchased);
            // // print_r($notAdded);
            // // print_r($salePriceGreaterThen);
            // print(count($array);
            // return;
            return Redirect::to('purchase/purchases');
    }

    public function editDeleteItem(){
        $vdata=Input::all();
        $item_id=$vdata['item_id'];

        if($vdata['edit_delete']=='edit'){
        $item_info=array();
        $item_info['item_id']=$vdata['item_id'];
        $item_info['price_id']=$vdata['price_id'];
        $item_info['item_name']=$vdata['item_name'];
//            $item_info['price_id']=$price_id;
        $item_info['purchase_price']=$vdata['purchase_price'];
        $item_info['sale_price']=$vdata['sale_price'];
        if($item_info['purchase_price']>$item_info['sale_price'])
            $item_info['sale_price']=$item_info['purchase_price'];

        $item_info['quantity']=$vdata['quantity'];
                if($vdata['quantity']==0)
                    $item_info['quantity']=Session::get("items.$item_id.quantity");

                if($vdata['discount']>($vdata['purchase_price']*$vdata['quantity']))
                    $item_info['discount']=$vdata['purchase_price']*$vdata['quantity'];
                else
                    $item_info['discount']=$vdata['discount'];

                $item_info['discount']=round($item_info['discount'],2);
        $item_info['total']=($item_info['purchase_price']*$item_info['quantity'])-$item_info['discount'];
        $item_info['total']=round($item_info['total'],2);
        Session::put("items.$item_id", $item_info);
            return Response::json(['success' => true,'item' => $item_info]);
        }
        else{
            Session::forget("items.$item_id");
            return Response::json(['success' => true]);
        }
        return Response::json(['success' => true,'item' => Session::get("items")]);

    }
	 public function emptyCart(){
        if(Session::get('items')){
            Session::forget('items');
        }
        if(Session::has('is_purchase_order')){
            Session::forget('is_purchase_order');
        }
        if(Session::has('invoice_info')){
            Session::forget('invoice_info');
        }
        return Redirect::to('purchase/purchases');
   }
    //supplier auto suggest
    public function autoSupplierSuggest(){
        $term = Input::get('q');        
        $suppliers = DB::table('supplierinfos')->where('supp_or_comp_name', 'LIKE', '%'. $term .'%')->get();
        foreach($suppliers as $key => $supplier):
            $supplier_id    = $supplier->supp_id;
            $supplier_name  = $supplier->supp_or_comp_name;
            $suppliers_info         = $supplier_id.'|'.$supplier_name;
            echo  "$suppliers_info\n";
        endforeach;
    } 
    
    public function selectDeleteSupplier()  {
        $vdata=Input::all();
        if(isset($vdata['supplier']) == 'delete'){
            Session::forget("invoice_info.supp_id");
            Session::forget("invoice_info.supp_or_comp_name");
        } else{
            $qdata=DB::table('supplierinfos')
                            ->where('supp_id', '=', $vdata['supp_id'])
                            ->first();
            if($qdata){
                Session::put("invoice_info.supp_id", $vdata['supp_id']);
                Session::put("invoice_info.supp_or_comp_name", $qdata->supp_or_comp_name);
            }
        }
        return Redirect::to('purchase/purchases');

    }

    public function invoiceAndPurchase()
    {
        $branch_id = Session::get('branch_id');
        if(!Session::get("items"))
           return Redirect::back()->with('errorMsg', "Sorry!  now you do not select item for purchase");
        if(Session::has('is_purchase_order')){
        foreach(Session::get("items") as $eachItem){
            if(($eachItem['purchase_price']<=0)||($eachItem['sale_price']<=0))
                 return Redirect::back()->with('errorMsg', "Sorry!  price is not allowed of less then or equal 0 tk");
           }
                #### ----  Invoice Part ----####
                  DB::beginTransaction();

                  try {   
                    $vdata=Input::all();
                    $data=array();
                    $supp_id=Session::get('invoice_info.supp_id');
                    $sup_memo_no=$vdata['sup_momo_no'];
                    $sum_memo_duplicate_check= DB::table('supinvoices_order')
                        ->where('sup_memo_no', '=', $sup_memo_no)
                        ->first();
                    if($sum_memo_duplicate_check){
                        $sup_memo_no=null;
                    }
                    
                    $payment_type_id=$vdata['payment_type_id'];
                    if($vdata['invoice_discount'] > 0){
                        $discount = $vdata['invoice_discount'];
                    }else{
                        $discount = 0;
                    }
                    
                    $amount=0;
                    foreach(Session::get('items') as $item)
                        $amount=$amount+$item['total'];
                        
                    $amount=$amount-$discount;
                    $pay=$vdata['pay'];
                    $due=$amount-$pay;
                    $transaction_date=$vdata['date'];
                    $created_by=Session::get('emp_id');
                    $created_at=$this->timestamp;

                    $year=date("y");
                    $month=date("m");
                    $day=date("d");
                    $date=$year.$month.$day;
                    
                    $insert = DB::select("insert into supinvoices_order (branch_id,sup_invoice_id,sup_memo_no,supp_id,payment_type_id,discount,amount,pay,due,transaction_date,created_by,created_at) values ('$branch_id',
                                    ifnull (concat('$date',1+(
                        SELECT right(sup_inv.sup_invoice_id, 8) AS LAST8 FROM supinvoices_order as sup_inv
                          where( (SELECT left(sup_inv.sup_invoice_id, 6)='$date'))
                          order by LAST8 desc limit 1)),concat('$date','10000000')),'$sup_memo_no','$supp_id','$payment_type_id','$discount','$amount','$pay','$due','$transaction_date','$created_by','$created_at')");

                    $last_insert_id =DB::getPdo()->lastInsertId();
                    $value = DB::table('supinvoices_order')->select('sup_invoice_id')
                        ->where('id', '=', $last_insert_id)
                        ->first();
                    $invoice_id=$value->sup_invoice_id;
                    $memo_id=null;
                    if($invoice_id){
                        DB::table('supplierinfos')
                        ->where('supp_id', '=', $supp_id)
                        ->increment('due', $due);

                        if(empty($sup_memo_no)){
                        $memo_id=$invoice_id;
                        $update_sup_memo_no=DB::table('supinvoices_order')
                                ->where('id', '=', $last_insert_id)
                                ->update(array('sup_memo_no' => $invoice_id));
                        }
                    }
                 #### ---- End of Invoice Part ----####

                 ### --- Item Purchase Part --- ###
                    $receipt_item_infos=array();
                    foreach(Session::get("items") as $eachItem){
                        $receipt_item_infos[]=$eachItem;
                        $itemId=$eachItem['item_id'];
                        $purchasePrice=$eachItem['purchase_price'];
                        $salePrice=$eachItem['sale_price'];
                        $duplicat=DB::select("select * from priceinfos where (item_id=$itemId and purchase_price=$purchasePrice and sale_price=$salePrice)");
                        $duplicat=isset($duplicat[0])?$duplicat[0]:null;
                        if($duplicat){
                             if($duplicat->status==0){
                                $update=DB::table('priceinfos')
                                                ->where('item_id',$eachItem['item_id'])
                                                ->where('item_id',$eachItem['item_id'])
                                                ->where('price_id', '!=', $duplicat->price_id)
                                                ->update(array('status' => 0));
                                 $update=DB::table('priceinfos')
                                                ->where('price_id', '=', $duplicat->price_id)
                                                ->update(array('status' => 1));
                                 $price_id=$duplicat->price_id;
                             }
                             else{
                                $price_id=$duplicat->price_id;
                             }
                         }
                         else{
                                $update=DB::table('priceinfos')
                                                ->where('item_id',$eachItem['item_id'])
                                                ->update(array('status' => 0));

                                $price_info = array(
                                            'item_id'           =>  $eachItem['item_id'],
                                            'purchase_price'    =>  $eachItem['purchase_price'],
                                            'sale_price'        =>  $eachItem['sale_price'],
                                            'created_by'        =>  Session::get('emp_id'),
                                            'created_at'        =>  $this->timestamp
                                            ); 

                                $price_id = DB::table('priceinfos')->insertGetId($price_info);
                         }

                       $update_item_info=DB::table('iteminfos')
                                                ->where('item_id',$eachItem['item_id'])
                                                ->update(array('price_id' => $price_id));

                        $purchaseData=array();
                        $purchaseData['sup_invoice_id']=$invoice_id;
                        $purchaseData['item_id']=$eachItem['item_id'];
                        $purchaseData['price_id']=$price_id;
                        $purchaseData['quantity']=$eachItem['quantity'];
                        $purchaseData['discount']=$eachItem['discount'];
                        $purchaseData['amount']=$eachItem['total'];
                        $purchaseData['created_by']=Session::get('emp_id');
                        $purchaseData['created_at']=$this->timestamp;
                        $item_id=$eachItem['item_id'];

                        $purchase= DB::table('itempurchases_order')->insert($purchaseData);
                        if($purchase){
                            Session::forget("items.$item_id");
                        }
                     }
                    $receipt_info=array();
                    $receipt_info['supplier_name']=Session::get("invoice_info.supp_or_comp_name");
                    $receipt_info['branch_name'] = ($branch_id == 1) ? 'MB Trade' : (($branch_id == 2) ? "MB Collection" : 'MB Gulshan');
                    $receipt_info['supplier_memo_no']=$memo_id;
                    $receipt_info['date']=$transaction_date;
                    $receipt_info['created_at']=$this->timestamp;
                    $receipt_info['invoice_id']=$invoice_id;
                    $emp_info=DB::table('empinfos')
                            ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                            ->first();
                    $payment_type_info=DB::table('paymenttypes')
                            ->where('paymenttypes.payment_type_id', '=', $payment_type_id)
                            ->first();
                    $receipt_info['emp_name']=$emp_info->user_name;
                    $receipt_info['payment_type_name']=$payment_type_info->payment_type_name;
                    $receipt_info['total_amount']=$amount;
                    $receipt_info['invoice_discount']=$discount;
                    $receipt_info['pay']=$pay;
                    $receipt_info['due']=$due;
                    Session::forget("invoice_info");
                    if(Session::has('is_purchase_order')){
                        Session::forget('is_purchase_order');
                    }
                    $company_info=DB::table('companyprofiles')->first();
                DB::commit();
                return Redirect::to('purchase/orderReceipt')->with('receipt_item_infos', $receipt_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info)->with('purchase_order', 'Purchase Order');
            } catch (Exception $e) {
                DB::rollback();
                return $e;
                return Redirect::to('purchase/receipt')->with('errorMsg', 'Something is wrong in purchase.');
            }
        }else{
            foreach(Session::get("items") as $eachItem){
            if(($eachItem['purchase_price']<=0)||($eachItem['sale_price']<=0))
                 return Redirect::back()->with('errorMsg', "Sorry!  price is not allowed of less then or equal 0 tk");
            }
                #### ----  Invoice Part ----####
                  DB::beginTransaction();
                  try {   
                    $vdata=Input::all();
                    $data=array();
                    $supp_id=Session::get('invoice_info.supp_id');
                    $sup_memo_no=$vdata['sup_momo_no'];
                    $sum_memo_duplicate_check= DB::table('supinvoices')
                        ->where('sup_memo_no', '=', $sup_memo_no)
                        ->first();
                    if($sum_memo_duplicate_check){
                        $sup_memo_no=null;
                    }
                    $payment_type_id=$vdata['payment_type_id'];
                    if($vdata['invoice_discount'] > 0){
                        $discount = $vdata['invoice_discount'];
                    }else{
                        $discount = 0;
                    }
                    $amount=0;
                    foreach(Session::get('items') as $item)
                        $amount=$amount+$item['total'];
                        
                    $amount=$amount-$discount;
                    $pay=$vdata['pay'];
                    $due=$amount-$pay;
                    $transaction_date=$vdata['date'];
                    $created_by=Session::get('emp_id');
                    $created_at=$this->timestamp;

                    $year=date("y");
                    $month=date("m");
                    $day=date("d");
                    $date=$year.$month.$day;
                    $insert = DB::select("insert into supinvoices (branch_id,sup_invoice_id,sup_memo_no,supp_id,payment_type_id,discount,amount,pay,due,transaction_date,created_by,created_at) values ('$branch_id',
                                    ifnull (concat('$date',1+(
                        SELECT right(sup_inv.sup_invoice_id, 8) AS LAST8 FROM supinvoices as sup_inv
                          where( (SELECT left(sup_inv.sup_invoice_id, 6)='$date'))
                          order by LAST8 desc limit 1)),concat('$date','10000000')),'$sup_memo_no','$supp_id','$payment_type_id','$discount','$amount','$pay','$due','$transaction_date','$created_by','$created_at')");

                    $last_insert_id =DB::getPdo()->lastInsertId();
                    $value = DB::table('supinvoices')->select('sup_invoice_id')
                        ->where('id', '=', $last_insert_id)
                        ->first();
                    $invoice_id=$value->sup_invoice_id;
                    $memo_id=null;
                    if($invoice_id){
                        DB::table('supplierinfos')
                        ->where('supp_id', '=', $supp_id)
                        ->increment('due', $due);

                        if(empty($sup_memo_no)){
                        $memo_id=$invoice_id;
                        $update_sup_memo_no=DB::table('supinvoices')
                                ->where('id', '=', $last_insert_id)
                                ->update(array('sup_memo_no' => $invoice_id));
                        }
                    }

                 #### ---- End of Invoice Part ----####

                 ### --- Item Purchase Part --- ###

                    $receipt_item_infos=array();
                    foreach(Session::get("items") as $eachItem){
                        $receipt_item_infos[]=$eachItem;
                        $itemId=$eachItem['item_id'];
                        $purchasePrice=$eachItem['purchase_price'];
                        $salePrice=$eachItem['sale_price'];
                        $duplicat=DB::select("select * from priceinfos where (item_id=$itemId and purchase_price=$purchasePrice and sale_price=$salePrice)");
                        $duplicat=isset($duplicat[0])?$duplicat[0]:null;
                        if($duplicat){
                             if($duplicat->status==0){
                                $update=DB::table('priceinfos')
                                                ->where('item_id',$eachItem['item_id'])
                                                ->where('price_id', '!=', $duplicat->price_id)
                                                ->update(array('status' => 0));
                                 $update=DB::table('priceinfos')
                                                ->where('price_id', '=', $duplicat->price_id)
                                                ->update(array('status' => 1));
                                 $price_id=$duplicat->price_id;

                             }
                             else{
                                $price_id=$duplicat->price_id;
                             }

                         }
                         else{

                                $update=DB::table('priceinfos')
                                                ->where('item_id',$eachItem['item_id'])
                                                ->update(array('status' => 0));

                                $price_info = array(
                                            'item_id'           =>  $eachItem['item_id'],
                                            'purchase_price'    =>  $eachItem['purchase_price'],
                                            'sale_price'        =>  $eachItem['sale_price'],
                                            'created_by'        =>  Session::get('emp_id'),
                                            'created_at'        =>  $this->timestamp
                                            ); 

                                $price_id = DB::table('priceinfos')->insertGetId($price_info);
                         }


                       $update_item_info=DB::table('iteminfos')
                                                ->where('item_id',$eachItem['item_id'])
                                                ->update(array('price_id' => $price_id));

                        $purchaseData=array();
                        $purchaseData['branch_id']=$branch_id;
                        $purchaseData['sup_invoice_id']=$invoice_id;
                        $purchaseData['item_id']=$eachItem['item_id'];
                        $purchaseData['price_id']=$price_id;
                        $purchaseData['quantity']=$eachItem['quantity'];
                        $purchaseData['discount']=$eachItem['discount'];
                        $purchaseData['amount']=$eachItem['total'];
                        $purchaseData['created_by']=Session::get('emp_id');
                        $purchaseData['created_at']=$this->timestamp;
                        $item_id=$eachItem['item_id'];


                        $purchase= DB::table('itempurchases')->insert($purchaseData);
                           if($purchase){
                           Session::forget("items.$item_id");

                           ###--- Godown item increasing with super key consider ---###

                                $godownItemInfo = DB::table('godownitems')
                                                        ->where('item_id', '=', $item_id)
                                                        ->where('price_id', '=', $price_id)
                                                        ->first();
                                if(!$godownItemInfo){
                                    $insertData=array();
                                    $insertData['item_id']=$item_id;
                                    $insertData['price_id']=$price_id;
                                    $insertData['available_quantity']=$purchaseData['quantity'];
                                    $insertData['created_by']=Session::get('emp_id');
                                    $insertData['created_at']=$this->timestamp;

                                    $insert=DB::table('godownitems')->insert($insertData);
                                }
                                else{
                                    if($godownItemInfo->status==0){
                                       $update=DB::table('godownitems')
                                                ->where('godown_item_id', '=', $godownItemInfo->godown_item_id)
                                                ->update(array('status' => 1));
                                    }
                                    $nowQuantity=$godownItemInfo->available_quantity+$purchaseData['quantity'];
                                    $increasingQuantity=DB::table('godownitems')
                                                ->where('godown_item_id', '=', $godownItemInfo->godown_item_id)
                                                ->update(array('available_quantity' => $nowQuantity));
                                }
                           ###--- Ending Godown item increasing with super key consider ---###
                           

                           }


                     }
                    $receipt_info=array();
                    $receipt_info['supplier_name']=Session::get("invoice_info.supp_or_comp_name");
                    $receipt_info['branch_name'] = ($branch_id == 1) ? 'MB Trade' : (($branch_id == 2) ? "MB Collection" : 'MB Gulshan');
                    $receipt_info['supplier_memo_no']=$memo_id;
                    $receipt_info['date']=$transaction_date;
                    $receipt_info['created_at']=$this->timestamp;
                    $receipt_info['invoice_id']=$invoice_id;
                    $emp_info=DB::table('empinfos')
                            ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                            ->first();
                    $payment_type_info=DB::table('paymenttypes')
                            ->where('paymenttypes.payment_type_id', '=', $payment_type_id)
                            ->first();
                    $receipt_info['emp_name']=$emp_info->user_name;
                    $receipt_info['payment_type_name']=$payment_type_info->payment_type_name;
                    $receipt_info['total_amount']=$amount;
                    $receipt_info['invoice_discount']=$discount;
                    $receipt_info['pay']=$pay;
                    $receipt_info['due']=$due;
                     Session::forget("invoice_info");
                     if(Session::has('is_purchase_order')){
                        Session::forget('is_purchase_order','true');
                    }
                    $company_info=DB::table('companyprofiles')->first();
                DB::commit();
                return Redirect::to('purchase/receipt')->with('receipt_item_infos', $receipt_item_infos)->with('receipt_info', $receipt_info)->with('company_info', $company_info);
            } catch (Exception $e) {
                DB::rollback();
                return $e;
                return Redirect::to('purchase/receipt')->with('errorMsg', 'Something is wrong in purchase.');
            }
        }

    }

    public function purchaseReceipt(){
        return View::make('purchase.purchaseReceipt');
    }

    public function orderReceipt(){
        return View::make('purchase.orderReceipt');
    }

    public function sendOrderToPurchase($sup_invoice_id){

        $orderDetails = DB::table('supinvoices_order')
            ->join('itempurchases_order','supinvoices_order.sup_invoice_id','=','itempurchases_order.sup_invoice_id')
            ->join('supplierinfos','supinvoices_order.supp_id','=','supplierinfos.supp_id')
            ->select([
                'supinvoices_order.sup_invoice_id',
                'itempurchases_order.item_id',
                'itempurchases_order.quantity',
                'itempurchases_order.discount',
                'supplierinfos.supp_id',
                'supplierinfos.supp_or_comp_name'
            ])
            ->where('supinvoices_order.sup_invoice_id',$sup_invoice_id)
            ->get();
        foreach($orderDetails as $datas){

                if(!Session::has('invoice_info')){
                    Session::put("invoice_info.supp_id", $datas->supp_id);
                    Session::put("invoice_info.supp_or_comp_name", $datas->supp_or_comp_name);
                }
                
                $item_id = $datas->item_id;
                $data=DB::table('iteminfos')
                            ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'iteminfos.price_id')
                            ->select('iteminfos.item_id', 'iteminfos.item_name', 'priceinfos.price_id', 'priceinfos.purchase_price', 'priceinfos.sale_price')
                            ->where('priceinfos.status', '=', 1)
                            ->where('iteminfos.item_id', '=', $item_id)
                            //->orWhere('iteminfos.upc_code', '=', $item_id)
                            ->first();

                    $item_info=array();
                    $item_info['item_id']=$data->item_id;
                    $item_info['item_name']=$data->item_name;
                    $item_info['price_id']=$data->price_id;
                    $item_info['purchase_price']=($data->purchase_price > 0) ? $data->purchase_price : 1;
                    $item_info['sale_price']=$data->sale_price;
                    if($item_info['purchase_price']>$item_info['sale_price'])
                        $item_info['sale_price']=$item_info['purchase_price'];  
                    $item_info['quantity']=$datas->quantity;
                    $item_info['discount']=$datas->discount;
                    $item_info['discount']=round($item_info['discount'],2);
                    $item_info['total']=($data->purchase_price*$item_info['quantity'])-$item_info['discount'];

                    $item_id=$data->item_id;
                    if(Session::get("items.$item_id")){
                        $item_info['quantity']=Session::get("items.$item_id.quantity")+1;
                        $item_info['purchase_price']=Session::get("items.$item_id.purchase_price");
                        $item_info['sale_price']=Session::get("items.$item_id.sale_price");
                        $item_info['discount']=Session::get("items.$item_id.discount");
                        $item_info['discount']=round($item_info['discount'],2);
                        $item_info['total']=(Session::get("items.$item_id.purchase_price")*$item_info['quantity'])-$item_info['discount'];
                    }
                    $item_info['total']=round($item_info['total'],2);
                    // $sessionData[] = $item_id;
                    Session::put("items.$item_id", $item_info);

            }
            
            return Redirect::to('purchase/purchases');
        // return View::make('purchase.orderReceipt');
    }

    public static function firstArray()
    {
        return array(
            // 0 => array('code' => '1802114760000', 'product_name' => '24K Gold Mask 220ml', 'qty' => '49', 'purchase_price' => '250', 'sale_price' => '300'),
            // 1 => array('code' => '8859128303829 8859128302372 8859128302334 8859128302310 8859128302945 8859128302358', 'product_name' => '3D Face Mask', 'qty' => '381', 'purchase_price' => '350', 'sale_price' => '500'),
            
            

            // 5 => array('code' => '8992821100415', 'product_name' => 'Acnes sealing gel 9 ml', 'qty' => '11', 'purchase_price' => '160', 'sale_price' => '200'),
            // 6 => array('code' => '1802113050000', 'product_name' => 'Acness FW 100ml', 'qty' => '185', 'purchase_price' => '180', 'sale_price' => '240'),
            // 7 => array('code' => '1802113170000', 'product_name' => 'Acness FW 50ml', 'qty' => '183', 'purchase_price' => '100', 'sale_price' => '140'),
            // 8 => array('code' => '3607343816656 3607345401119 3607345380247 3614222813781', 'product_name' => 'Adidas Body Spray 150ml', 'qty' => '193', 'purchase_price' => '160', 'sale_price' => '165'),
            // 9 => array('code' => '3607343871273 3607343871174 3607343871228', 'product_name' => 'Adidas Rollon 50ml', 'qty' => '334', 'purchase_price' => '120', 'sale_price' => '150'),
            // 10 => array('code' => '3607340723346 3607340722127 3607340725982 3607343567893 3607340726682', 'product_name' => 'Adidas Shower Gel 250ml', 'qty' => '104', 'purchase_price' => '140', 'sale_price' => '180'),
            
            // 12 => array('code' => '18851299001174', 'product_name' => 'Aim Brush', 'qty' => '360', 'purchase_price' => '60', 'sale_price' => '80'),

            // 14 => array('code' => '8859010900051', 'product_name' => 'Almaya Whitening perl Cream 5g', 'qty' => '184', 'purchase_price' => '140', 'sale_price' => '180'),
            
            
            // 17 => array('code' => '8858907800078', 'product_name' => 'Alpha Arbutin Soap 70g', 'qty' => '462', 'purchase_price' => '140', 'sale_price' => '200'),
            
            // 19 => array('code' => '8992895141154', 'product_name' => 'Anais BS 150ml', 'qty' => '73', 'purchase_price' => '650', 'sale_price' => '850'),
            // 20 => array('code' => '8859244200019', 'product_name' => 'Anasia Nail Remover 120ml', 'qty' => '1540', 'purchase_price' => '90', 'sale_price' => '120'),
            // 21 => array('code' => '8859244200026', 'product_name' => 'Anasia Nail Remover 45ml', 'qty' => '3473', 'purchase_price' => '40', 'sale_price' => '60'),

            
            // 24 => array('code' => '1802114600000', 'product_name' => 'Angelina Nail Remover 20ml', 'qty' => '101', 'purchase_price' => '40', 'sale_price' => '60'),
            // 25 => array('code' => '8850722126869 8858918300932', 'product_name' => 'Annasia Lip Gloss 4ml', 'qty' => '151', 'purchase_price' => '40', 'sale_price' => '60'),
            // 26 => array('code' => '8851495001117', 'product_name' => 'Anti Lice Shampoo 200ML', 'qty' => '36', 'purchase_price' => '300', 'sale_price' => '500'),
            

            
            // 30 => array('code' => '8850252030049', 'product_name' => 'Arche Pearl Cream 3ml', 'qty' => '136', 'purchase_price' => '552', 'sale_price' => '600'),
            


            // 34 => array('code' => '8853502010280', 'product_name' => 'Argeville USA Black Soap 120gm', 'qty' => '523', 'purchase_price' => '70', 'sale_price' => '80'),
            // 35 => array('code' => '3360372115601 3360372058892', 'product_name' => 'Armani Body Spray 150ml', 'qty' => '7', 'purchase_price' => '1050', 'sale_price' => '1250'),
            // 36 => array('code' => '8850722092751', 'product_name' => 'Aron Aloe Face wash 150ml', 'qty' => '1058', 'purchase_price' => '150', 'sale_price' => '280'),
            // 37 => array('code' => '8850722067933', 'product_name' => 'Aron Baby lipstick 16ml', 'qty' => '65', 'purchase_price' => '160', 'sale_price' => '250'),
            // 38 => array('code' => '8850722129471 8850722129464 8850722068893', 'product_name' => 'Aron BB Magic Lip Care 3.5gm', 'qty' => '3596', 'purchase_price' => '50', 'sale_price' => '80'),
            

            // 41 => array('code' => '8850722180328', 'product_name' => 'Aron Eyeliner', 'qty' => '487', 'purchase_price' => '90', 'sale_price' => '120'),
            
            // 43 => array('code' => '8850722291659 8850722291666', 'product_name' => 'Aron Gluta Face& Body Scrub 150ml', 'qty' => '234', 'purchase_price' => '200', 'sale_price' => '250'),
            // 44 => array('code' => '8850722044590', 'product_name' => 'Aron Gluta Milky Body Lotion 800ml', 'qty' => '5', 'purchase_price' => '650', 'sale_price' => '850'),
            // 45 => array('code' => '8850722400068', 'product_name' => 'Aron Goat Milk Hair Treatment 500ml', 'qty' => '2', 'purchase_price' => '350', 'sale_price' => '400'),
            // 46 => array('code' => '8850722043708 8850722200514', 'product_name' => 'Aron Gold Lotion UV50 300ml', 'qty' => '571', 'purchase_price' => '200', 'sale_price' => '350'),
            // 47 => array('code' => '8850722129259', 'product_name' => 'Aron Imortal Lipstick', 'qty' => '222', 'purchase_price' => '150', 'sale_price' => '180'),
            // 48 => array('code' => '8850722044323', 'product_name' => 'Aron Lemon Whitening Body Lotion 400ml', 'qty' => '316', 'purchase_price' => '300', 'sale_price' => '480'),
            // 49 => array('code' => '8850722126852', 'product_name' => 'Aron Lip Tiant Colour', 'qty' => '439', 'purchase_price' => '80', 'sale_price' => '120'),
            // 50 => array('code' => '8850722093017 8850722092928', 'product_name' => 'Aron milky Charcoal facewash 190ml', 'qty' => '1652', 'purchase_price' => '160', 'sale_price' => '250'),
            // 51 => array('code' => '8850722039121', 'product_name' => 'Aron Oil Control Face Powder 14g', 'qty' => '1452', 'purchase_price' => '90', 'sale_price' => '150'),
            // 52 => array('code' => '8850722044620', 'product_name' => 'Aron Snail Now Body Lotion 400ml', 'qty' => '78', 'purchase_price' => '300', 'sale_price' => '400'),
            
            // 54 => array('code' => '8850722043647', 'product_name' => 'Aron UV Lotion 250ml', 'qty' => '69', 'purchase_price' => '180', 'sale_price' => '200'),
            // 55 => array('code' => '8850722043203', 'product_name' => 'Aron V-E Serum Lotion 400ml', 'qty' => '353', 'purchase_price' => '200', 'sale_price' => '380'),
            // 56 => array('code' => '1802114190000', 'product_name' => 'Aron Vitamin-E Moisturizing Lip Care 3.5g', 'qty' => '46', 'purchase_price' => '80', 'sale_price' => '120'),
            // 57 => array('code' => '8850722093079', 'product_name' => 'Aron Water Melon F.W 190ml', 'qty' => '304', 'purchase_price' => '150', 'sale_price' => '250'),
            // 58 => array('code' => '8850722091990', 'product_name' => 'Aron Whitening Oil Control Facial Fome 210ml', 'qty' => '546', 'purchase_price' => '150', 'sale_price' => '220'),
            // 59 => array('code' => '8850273151303', 'product_name' => 'ART Rat Killer 80ml', 'qty' => '1', 'purchase_price' => '90', 'sale_price' => '120'),
            // 60 => array('code' => '8855753000010', 'product_name' => 'Asantee Tamarind Goat Milk Soap 135g', 'qty' => '135', 'purchase_price' => '90', 'sale_price' => '150'),
            // 61 => array('code' => '7640129890118 7640129892198 7640129892662', 'product_name' => 'Asap Soap 80gm', 'qty' => '403', 'purchase_price' => '70', 'sale_price' => '90'),

            // 63 => array('code' => '8850535010812', 'product_name' => 'ATM Air Freshener Hangar 70gm', 'qty' => '388', 'purchase_price' => '38', 'sale_price' => '42'),
            // 64 => array('code' => '8850535630904', 'product_name' => 'ATM Air Freshener Refill 70gm', 'qty' => '2458', 'purchase_price' => '28', 'sale_price' => '32'),
            // 65 => array('code' => '8859288000569', 'product_name' => 'Aura White Virgin Soap 120gm', 'qty' => '28', 'purchase_price' => '120', 'sale_price' => '300'),
            // 66 => array('code' => '8888047857045 8888047857014', 'product_name' => 'Aussie Hand Wash 414ml', 'qty' => '99', 'purchase_price' => '100', 'sale_price' => '120'),
            // 67 => array('code' => '5037378006322', 'product_name' => 'Autumn & may Lotion 500ml Uk', 'qty' => '1', 'purchase_price' => '500', 'sale_price' => '700'),

            // 69 => array('code' => '8888115000144', 'product_name' => 'Axe oil 10ml', 'qty' => '249', 'purchase_price' => '100', 'sale_price' => '100'),
            // 70 => array('code' => '8888115000137', 'product_name' => 'Axe oil 14ml', 'qty' => '150', 'purchase_price' => '120', 'sale_price' => '125'),
            // 71 => array('code' => '8888115000120', 'product_name' => 'Axe Oil 28ml', 'qty' => '152', 'purchase_price' => '170', 'sale_price' => '180'),
            // 72 => array('code' => '8992895300025 8992895300018 8992915600036', 'product_name' => 'AXL Deo Body Spray 150ml', 'qty' => '51', 'purchase_price' => '350', 'sale_price' => '500'),
            // 73 => array('code' => '8993417502217', 'product_name' => 'B & B Cologne 120ml', 'qty' => '57', 'purchase_price' => '120', 'sale_price' => '180'),
            // 74 => array('code' => '8993417176227 8993417476266 8993417476235 8993417176234', 'product_name' => 'B & B Kids Body Powder 150gm', 'qty' => '335', 'purchase_price' => '120', 'sale_price' => '120'),
            // 75 => array('code' => '5012251007047', 'product_name' => 'B.F Foot Cream 100ml', 'qty' => '23', 'purchase_price' => '240', 'sale_price' => '280'),


            // 78 => array('code' => '8851123341035', 'product_name' => 'Baby Mild Powder 180gm', 'qty' => '21', 'purchase_price' => '120', 'sale_price' => '180'),
            // 79 => array('code' => '8993417372230 8993417312236 8993417372216 8993417312229 8993417312212 8993417312243 8993417312267', 'product_name' => 'Baby Mist Cologne B/M/E 100ml', 'qty' => '2223', 'purchase_price' => '100', 'sale_price' => '160'),
            // 80 => array('code' => '8993417372131 8993417172113 8993417372117 8993417372124 8993417172120', 'product_name' => 'Baby Mist Cologne B/M/E 60ml', 'qty' => '4556', 'purchase_price' => '80', 'sale_price' => '120'),
            
            
            // 83 => array('code' => '8999338169997', 'product_name' => 'Barber Hair Muse 180ml', 'qty' => '40', 'purchase_price' => '180', 'sale_price' => '180'),
            // 84 => array('code' => '6949392944858', 'product_name' => 'Be Matte Lipstick', 'qty' => '660', 'purchase_price' => '60', 'sale_price' => '70'),
            
            // 86 => array('code' => '5012251011327 5012251009621', 'product_name' => 'Beauty Formula Nose Strips', 'qty' => '202', 'purchase_price' => '110', 'sale_price' => '150'),
            // 87 => array('code' => '8992832603684', 'product_name' => 'Bellagio Deodorant Spray 175ml', 'qty' => '362', 'purchase_price' => '350', 'sale_price' => '500'),
            // 88 => array('code' => '8992832191167 8992832191181 8992832603622 8992832191198 8992832191174', 'product_name' => 'Bellagio Homme Cologne Spray 100ml', 'qty' => '280', 'purchase_price' => '280', 'sale_price' => '350'),
            // 89 => array('code' => '8850233260236 8850233260069', 'product_name' => 'Bhasaj Body Lotion 150ml', 'qty' => '210', 'purchase_price' => '100', 'sale_price' => '220'),
            
            // 91 => array('code' => '8850722045016', 'product_name' => 'Bio Active Body Whitening Cream 100ml', 'qty' => '1245', 'purchase_price' => '250', 'sale_price' => '300'),
            // 92 => array('code' => '8850722530819', 'product_name' => 'Bio Active Facial Whitening Cream 100ml', 'qty' => '403', 'purchase_price' => '250', 'sale_price' => '300'),
            // 93 => array('code' => '8850722200941', 'product_name' => 'Bio Active Sun Protection Cream 75ml', 'qty' => '1731', 'purchase_price' => '250', 'sale_price' => '300'),
            // 94 => array('code' => '8853252009909', 'product_name' => 'Bio Anne Bust Cream 60ml', 'qty' => '74', 'purchase_price' => '240', 'sale_price' => '260'),
            


            
            // 99 => array('code' => '8855140002757', 'product_name' => 'Bio Women Shampoo 300ml', 'qty' => '200', 'purchase_price' => '380', 'sale_price' => '420'),
            // 100 => array('code' => '8992727003537 8992727005159 8992727005111', 'product_name' => 'Biore Men FW 100ml', 'qty' => '285', 'purchase_price' => '200', 'sale_price' => '260'),
            // 101 => array('code' => '8851818010505 8851818020405 8992727005937 8992727002837 8992727005555 8992727005555', 'product_name' => 'Biore Nose Strips', 'qty' => '1216', 'purchase_price' => '140', 'sale_price' => '200'),
            // 102 => array('code' => '1802113340000', 'product_name' => 'Biore Women Facewash 100ml', 'qty' => '290', 'purchase_price' => '200', 'sale_price' => '250'),
            // 103 => array('code' => '8858854550057', 'product_name' => 'Birthday Eyelash Gum', 'qty' => '514', 'purchase_price' => '350', 'sale_price' => '480'),
            

            
            // 107 => array('code' => '8850722092744', 'product_name' => 'Bm.B Whitening Facial Foam 120ml', 'qty' => '130', 'purchase_price' => '280', 'sale_price' => '350'),
            
            // 109 => array('code' => '5028197182151', 'product_name' => 'Body Shop Banana/Rainforce Shampoo 250ml UK', 'qty' => '6', 'purchase_price' => '480', 'sale_price' => '550'),
            // 110 => array('code' => '5028197549855', 'product_name' => 'Body shop Mineral Mask 100ml', 'qty' => '1', 'purchase_price' => '780', 'sale_price' => '850'),
            
            
            
            // 114 => array('code' => '5028197542948 5028197578763 5028197449636', 'product_name' => 'Body Shop Shawer Gel 250ml uk', 'qty' => '5', 'purchase_price' => '430', 'sale_price' => '450'),
            // 115 => array('code' => '5028197549893', 'product_name' => 'Body Shop Tea Tree Facial Scrub 100ml UK', 'qty' => '5', 'purchase_price' => '540', 'sale_price' => '600'),
            
            
            
            

            
            
            
            // 124 => array('code' => '5000167189735', 'product_name' => 'Boots Hand Cream 100ml', 'qty' => '3', 'purchase_price' => '200', 'sale_price' => '250'),
            // 125 => array('code' => '737052530215 737052351704', 'product_name' => 'Boss Body Spray 150ml', 'qty' => '8', 'purchase_price' => '800', 'sale_price' => '850'),
            // 126 => array('code' => '3014230021039 8711700634922', 'product_name' => 'Brut EDT Parfume 100ml', 'qty' => '18', 'purchase_price' => '350', 'sale_price' => '350'),
            // 127 => array('code' => '827755070023', 'product_name' => 'Brut Stick 63g', 'qty' => '311', 'purchase_price' => '160', 'sale_price' => '240'),

            // 129 => array('code' => '8858831000247', 'product_name' => 'Bust Collagen Soap 70gm', 'qty' => '252', 'purchase_price' => '60', 'sale_price' => '100'),

            // 131 => array('code' => '8850007650645', 'product_name' => 'C & C Acne Gel', 'qty' => '156', 'purchase_price' => '200', 'sale_price' => '250'),
            
            // 133 => array('code' => '9556006283127 9556006283110', 'product_name' => 'C & C FW Facial Foam 100ml Bottle', 'qty' => '2324', 'purchase_price' => '150', 'sale_price' => '170'),
            // 134 => array('code' => '8991111112350', 'product_name' => 'C & C FW Tube 100ml', 'qty' => '1678', 'purchase_price' => '140', 'sale_price' => '200'),
            // 135 => array('code' => '8991111111995', 'product_name' => 'C & C Toner 100ml', 'qty' => '51', 'purchase_price' => '130', 'sale_price' => '150'),

            // 137 => array('code' => '8992832604179 8992832604162 8992832604148 8992832604155', 'product_name' => 'Camellia Body mist 100ml', 'qty' => '115', 'purchase_price' => '130', 'sale_price' => '160'),
            // 138 => array('code' => '8992832191136 8992832191150', 'product_name' => 'Camellia Body Spray 100ml', 'qty' => '123', 'purchase_price' => '120', 'sale_price' => '140'),
            
            
            // 141 => array('code' => '8851427010200', 'product_name' => 'Carabeau Hair Loss Shampoo 250ml', 'qty' => '13', 'purchase_price' => '350', 'sale_price' => '650'),
            // 142 => array('code' => '8851427002380 8851427002397 8851427002410', 'product_name' => 'Carebeau Beauty Spa Salt Scrub Jhar 700ml', 'qty' => '583', 'purchase_price' => '250', 'sale_price' => '380'),
            // 143 => array('code' => '8851427009884 8851427009907 8851427009891', 'product_name' => 'Carebeau Enjoy Hair Protein Serum', 'qty' => '85', 'purchase_price' => '250', 'sale_price' => '350'),
            // 144 => array('code' => '8851427013928', 'product_name' => 'Carebeau Goat MIlk F.W 100ML', 'qty' => '1355', 'purchase_price' => '130', 'sale_price' => '250'),
            // 145 => array('code' => '1802117900000', 'product_name' => 'Carebeau Goat MIlk Lotion 300ml', 'qty' => '322', 'purchase_price' => '200', 'sale_price' => '350'),
            // 146 => array('code' => '8851427007941 8851427007934 8851427012822 8851427007958', 'product_name' => 'Carebeau Goat Milk Lotion 600ml', 'qty' => '328', 'purchase_price' => '350', 'sale_price' => '600'),
            // 147 => array('code' => '8851427007552', 'product_name' => 'Carebeau Goat milk Shawer Gel 600ml', 'qty' => '211', 'purchase_price' => '350', 'sale_price' => '500'),
            // 148 => array('code' => '8851427003851 8851427012693', 'product_name' => 'Carebeau Goat Milk Shower Cream 300g', 'qty' => '241', 'purchase_price' => '150', 'sale_price' => '350'),
            // 149 => array('code' => '8851427013171 8851427013188', 'product_name' => 'Carebeau Hair Spa 500ml', 'qty' => '204', 'purchase_price' => '350', 'sale_price' => '400'),
            
            // 151 => array('code' => '8851427005589', 'product_name' => 'Carebeau Massage Cream 450ml', 'qty' => '163', 'purchase_price' => '280', 'sale_price' => '400'),
            // 152 => array('code' => '8851427004056', 'product_name' => 'Carebeau Massage Oil 1000ml', 'qty' => '303', 'purchase_price' => '550', 'sale_price' => '700'),
            // 153 => array('code' => '8851427003240', 'product_name' => 'Carebeau Massage Oil 450ml', 'qty' => '485', 'purchase_price' => '300', 'sale_price' => '400'),
            // 154 => array('code' => '8851427001390 8851427001406 8851427004018', 'product_name' => 'Carebeau V-E Cream 500ml', 'qty' => '103', 'purchase_price' => '300', 'sale_price' => '350'),
            // 155 => array('code' => '8851427007897 8851427007927', 'product_name' => 'Carebeau White Spa Lotion 200ml', 'qty' => '1759', 'purchase_price' => '130', 'sale_price' => '200'),
            // 156 => array('code' => '8851427003295', 'product_name' => 'Carebue Anti Hair Loss Serum 50ml', 'qty' => '325', 'purchase_price' => '300', 'sale_price' => '350'),
            
            // 158 => array('code' => '8852053015010', 'product_name' => 'Carring Hair Muse 220ml', 'qty' => '234', 'purchase_price' => '260', 'sale_price' => '280'),
            // 159 => array('code' => '8852053016031 8852053016178 8852053016062 8852053016147', 'product_name' => 'Carring Hair Treatment 250ml', 'qty' => '1920', 'purchase_price' => '220', 'sale_price' => '250'),
            // 160 => array('code' => '8852053016185 8852053016154 8852053016024 8852053016055', 'product_name' => 'Carring Hair Treatment 500ml', 'qty' => '358', 'purchase_price' => '350', 'sale_price' => '400'),
            // 161 => array('code' => '8852053016635', 'product_name' => 'Carring Magix Hair Spa 500ml', 'qty' => '1413', 'purchase_price' => '600', 'sale_price' => '700'),
            // 162 => array('code' => '8992832130012', 'product_name' => 'Casablanca Body Spray 200ml', 'qty' => '18', 'purchase_price' => '300', 'sale_price' => '350'),
            // 163 => array('code' => '8858842025048 8858842010044', 'product_name' => 'Cathy Doll CC Body Cream 128ml', 'qty' => '80', 'purchase_price' => '380', 'sale_price' => '500'),
            
            // 165 => array('code' => '8858842011676', 'product_name' => 'Cathy Doll Sunscreen 50g', 'qty' => '4', 'purchase_price' => '220', 'sale_price' => '350'),
            // 166 => array('code' => '8850722092898', 'product_name' => 'Caviar Aloe Plus Gluta Facial Foam 150ml', 'qty' => '143', 'purchase_price' => '160', 'sale_price' => '250'),
            
            // 168 => array('code' => '8850722092775', 'product_name' => 'Cavier Rose Gold Foam 150ml', 'qty' => '133', 'purchase_price' => '150', 'sale_price' => '250'),
            // 169 => array('code' => '8850722200668', 'product_name' => 'Cavier Sun Screen Lotion 150ml', 'qty' => '6', 'purchase_price' => '120', 'sale_price' => '150'),


            // 172 => array('code' => '8850014440017', 'product_name' => 'Cherie Baby 3IN1', 'qty' => '759', 'purchase_price' => '120', 'sale_price' => '150'),
            // 173 => array('code' => '1802111530000', 'product_name' => 'Cherie Baby 3IN2 Set', 'qty' => '61', 'purchase_price' => '120', 'sale_price' => '160'),
            // 174 => array('code' => '8850014519393', 'product_name' => 'Cherie Baby Cotton Bud 100 Can', 'qty' => '4752', 'purchase_price' => '35', 'sale_price' => '50'),
            // 175 => array('code' => '8850014513889', 'product_name' => 'Cherie Baby Cotton Bud 200 Can', 'qty' => '4394', 'purchase_price' => '50', 'sale_price' => '80'),
            // 176 => array('code' => '8850014411093', 'product_name' => 'Cherie Baby Cotton Bud 300 Can', 'qty' => '1317', 'purchase_price' => '70', 'sale_price' => '90'),
            // 177 => array('code' => '1802111500000', 'product_name' => 'Cherie Cotton Buds Normal soft 100', 'qty' => '298', 'purchase_price' => '210', 'sale_price' => '280'),
            // 178 => array('code' => '1802111510000', 'product_name' => 'Cherie Cotton Buds Normal soft 200', 'qty' => '275', 'purchase_price' => '350', 'sale_price' => '580'),
            // 179 => array('code' => '8850014453222', 'product_name' => 'Cherie Cotton Pad Non Press', 'qty' => '409', 'purchase_price' => '100', 'sale_price' => '120'),
            // 180 => array('code' => '8850014421238', 'product_name' => 'Cherie Cotton Pleats 4.5m', 'qty' => '251', 'purchase_price' => '120', 'sale_price' => '160'),
            // 181 => array('code' => '1802111440000', 'product_name' => 'Cherie Mini Cotton Bud 100', 'qty' => '189', 'purchase_price' => '210', 'sale_price' => '300'),
            // 182 => array('code' => '1802111480000', 'product_name' => 'Cherie Mini Cotton Bud 200', 'qty' => '143', 'purchase_price' => '350', 'sale_price' => '580'),
            
            
            
            
            // 187 => array('code' => '8998866106191', 'product_name' => 'Ciptadent Paste 190gm', 'qty' => '363', 'purchase_price' => '100', 'sale_price' => '150'),
            // 188 => array('code' => '8850722044811', 'product_name' => 'Civic Aloevera Whitening Lotion 200ml', 'qty' => '531', 'purchase_price' => '150', 'sale_price' => '250'),
            // 189 => array('code' => '8850722092720', 'product_name' => 'Civic Apple Red facewash 150ml', 'qty' => '267', 'purchase_price' => '140', 'sale_price' => '250'),
            // 190 => array('code' => '8850722092713', 'product_name' => 'Civic Banana F.W 150ml', 'qty' => '139', 'purchase_price' => '140', 'sale_price' => '250'),
            // 191 => array('code' => '8850722034782', 'product_name' => 'Civic Oil Control Whitening Face Powder', 'qty' => '216', 'purchase_price' => '90', 'sale_price' => '150'),
            // 192 => array('code' => '8850722092164', 'product_name' => 'Civic Papaya Facewash 180ml', 'qty' => '217', 'purchase_price' => '150', 'sale_price' => '220'),
            // 193 => array('code' => '8850722092935', 'product_name' => 'Civic Red Milk F.W 180ml', 'qty' => '256', 'purchase_price' => '110', 'sale_price' => '180'),
            // 194 => array('code' => '1802113560000', 'product_name' => 'Civic Royal No1 Face Power', 'qty' => '232', 'purchase_price' => '100', 'sale_price' => '180'),
            // 195 => array('code' => '8850722039077', 'product_name' => 'Civic Snail White Face powder', 'qty' => '243', 'purchase_price' => '90', 'sale_price' => '150'),

            // 197 => array('code' => '8993286421206', 'product_name' => 'Classic White Soap 85gm', 'qty' => '2855', 'purchase_price' => '35', 'sale_price' => '100'),
            // 198 => array('code' => '8999999720940', 'product_name' => 'Clear Hair Cream Anti Dandruff 100gm', 'qty' => '337', 'purchase_price' => '110', 'sale_price' => '180'),
            // 199 => array('code' => '8851932265485 8851932263177 8851932314626', 'product_name' => 'Clear Shampoo 350ml', 'qty' => '75', 'purchase_price' => '250', 'sale_price' => '280'),
            
            // 201 => array('code' => '8851932265386 8851932265409 8851932265324 8851932265348', 'product_name' => 'Clear Shampoo Men/Women 180ml', 'qty' => '2238', 'purchase_price' => '140', 'sale_price' => '160'),
            
            
            
            
            
            // 207 => array('code' => '8851748000102', 'product_name' => 'Cockroach powder 10G', 'qty' => '55', 'purchase_price' => '55', 'sale_price' => '80'),
            
            // 209 => array('code' => '8850006922729', 'product_name' => 'Colgate Baby Brush', 'qty' => '1338', 'purchase_price' => '30', 'sale_price' => '40'),
            // 210 => array('code' => '8850006324172', 'product_name' => 'Colgate Max Fresh Toothpast 100ml', 'qty' => '2606', 'purchase_price' => '100', 'sale_price' => '120'),
            // 211 => array('code' => '8850006304686 8850006306260 8850006305201 8850006303269', 'product_name' => 'Colgate MW 500ml', 'qty' => '850', 'purchase_price' => '280', 'sale_price' => '340'),
            // 212 => array('code' => '9556031159466 9556031159473', 'product_name' => 'Colgate Paste 75g', 'qty' => '519', 'purchase_price' => '80', 'sale_price' => '90'),
            
            
            
            
            
            


            // 221 => array('code' => '1802114460000', 'product_name' => 'Cosmetics Mirror (C)', 'qty' => '5', 'purchase_price' => '280', 'sale_price' => '340'),
            // 222 => array('code' => '8850014481003', 'product_name' => 'Cotton Ball 40gm', 'qty' => '1850', 'purchase_price' => '30', 'sale_price' => '40'),
            // 223 => array('code' => '1802111420000', 'product_name' => 'Cotton Bud 3Diamond 100', 'qty' => '240', 'purchase_price' => '150', 'sale_price' => '220'),
            
            // 225 => array('code' => '8850722090320', 'product_name' => 'Cucumber FW 190ml', 'qty' => '337', 'purchase_price' => '100', 'sale_price' => '120'),
            // 226 => array('code' => '8850169547210', 'product_name' => 'Cussons Baby Oil 100ml', 'qty' => '49', 'purchase_price' => '140', 'sale_price' => '160'),
            // 227 => array('code' => '8998103004495', 'product_name' => 'Cussons Baby Oil 200ml', 'qty' => '8', 'purchase_price' => '240', 'sale_price' => '260'),
            // 228 => array('code' => '8998103012308', 'product_name' => 'Cussons Baby Powder 350+150gm', 'qty' => '14', 'purchase_price' => '160', 'sale_price' => '200'),
            // 229 => array('code' => '8998103001029', 'product_name' => 'Cussons Baby Set Mini', 'qty' => '3', 'purchase_price' => '260', 'sale_price' => '300'),
            // 230 => array('code' => '8998103001586 8998103001579 8998103001593', 'product_name' => 'Cussons Kids Toothpaste 45gm', 'qty' => '350', 'purchase_price' => '50', 'sale_price' => '60'),
            
            
            // 233 => array('code' => '8851228007058', 'product_name' => 'Darlie Double Action Toothpaste 180gm', 'qty' => '282', 'purchase_price' => '130', 'sale_price' => '150'),
            
            // 235 => array('code' => '8857123698117', 'product_name' => 'Deemild Toothbrush 526', 'qty' => '200', 'purchase_price' => '60', 'sale_price' => '100'),


            
            // 239 => array('code' => '8858947903784 8858947904644', 'product_name' => 'DERLISE SPA SALT 300ml', 'qty' => '137', 'purchase_price' => '150', 'sale_price' => '250'),
            // 240 => array('code' => '8993560025021 8993560025069 8993560025106', 'product_name' => 'Dettol Soap 110gm', 'qty' => '1203', 'purchase_price' => '50', 'sale_price' => '60'),
            // 241 => array('code' => '8850360026347 8850360029010', 'product_name' => 'Dettole Body wash 500ml', 'qty' => '45', 'purchase_price' => '400', 'sale_price' => '450'),
            
            // 243 => array('code' => '799439309296', 'product_name' => 'Dexe Black Musk Box', 'qty' => '54', 'purchase_price' => '250', 'sale_price' => '300'),
            // 244 => array('code' => '6932511215069', 'product_name' => 'Disaar Gel 100g', 'qty' => '413', 'purchase_price' => '120', 'sale_price' => '200'),
            // 245 => array('code' => '6932511204865', 'product_name' => 'Disaar Musel Pain 50ml', 'qty' => '444', 'purchase_price' => '120', 'sale_price' => '150'),
            // 246 => array('code' => '6932511214161', 'product_name' => 'Disaar Rapid Relif 50ml', 'qty' => '742', 'purchase_price' => '120', 'sale_price' => '150'),
            // 247 => array('code' => '8858927500439', 'product_name' => 'Dorliene Slim Soap 80gm', 'qty' => '28', 'purchase_price' => '80', 'sale_price' => '120'),
            // 248 => array('code' => '8718114562575 8711600451650 8718114627922 8718114562506 8718114627007', 'product_name' => 'Dove Conditioner 200ml', 'qty' => '357', 'purchase_price' => '160', 'sale_price' => '185'),
            // 249 => array('code' => '8851932351249', 'product_name' => 'Dove Conditioner 460ml', 'qty' => '120', 'purchase_price' => '350', 'sale_price' => '400'),
            
            // 251 => array('code' => '4800888150066 4800888150042 4800888183132', 'product_name' => 'Dove Roll on 50ml', 'qty' => '515', 'purchase_price' => '100', 'sale_price' => '130'),
            // 252 => array('code' => '8718114561660 8711600450523 8718114621371 8718114622521 8718114560939', 'product_name' => 'Dove Shampoo 250ml', 'qty' => '700', 'purchase_price' => '160', 'sale_price' => '180'),
            // 253 => array('code' => '8851932275149 8851932231374', 'product_name' => 'Dove Shampoo 375ml', 'qty' => '24', 'purchase_price' => '250', 'sale_price' => '280'),
            // 254 => array('code' => '8851932351188 8851932280686 8851932227537', 'product_name' => 'Dove Shampoo 480ml', 'qty' => '233', 'purchase_price' => '350', 'sale_price' => '350'),
            
            
            // 257 => array('code' => '30056671 50287062 50285662 96030257', 'product_name' => 'Dove Stick 40ml', 'qty' => '1372', 'purchase_price' => '130', 'sale_price' => '160'),
            // 258 => array('code' => '6921199182893', 'product_name' => 'DR. Dave Black Mask 50ml', 'qty' => '160', 'purchase_price' => '90', 'sale_price' => '100'),
            // 259 => array('code' => '8853252009282', 'product_name' => 'DR. Jems Sliming Gel+Soap', 'qty' => '439', 'purchase_price' => '300', 'sale_price' => '450'),

            // 261 => array('code' => '8850722068930 8850722069326', 'product_name' => 'EH Aloe Vera Magic Lip 3.2g', 'qty' => '645', 'purchase_price' => '60', 'sale_price' => '100'),
            // 262 => array('code' => '8850722044101', 'product_name' => 'EH Green Apple Body Lotion 240ml', 'qty' => '117', 'purchase_price' => '250', 'sale_price' => '400'),
            // 263 => array('code' => '8850722068848', 'product_name' => 'EH Lip Balm', 'qty' => '173', 'purchase_price' => '60', 'sale_price' => '100'),
            // 264 => array('code' => '8850722065953', 'product_name' => 'EH Mixed Fruits Lip Balm 2ml', 'qty' => '295', 'purchase_price' => '15', 'sale_price' => '25'),
            // 265 => array('code' => '8850722200095 8850722200705', 'product_name' => 'EH Moisture UV 50+ Cream 30ml', 'qty' => '1061', 'purchase_price' => '100', 'sale_price' => '150'),
            // 266 => array('code' => '8850722040820 8850722043807 8850722040806', 'product_name' => 'Eh Velvet Whitening Lotion 400g', 'qty' => '371', 'purchase_price' => '350', 'sale_price' => '380'),
            
            
            // 269 => array('code' => '8850002850415 8850002851962', 'product_name' => 'Enfant Baby Powder 500ml', 'qty' => '30', 'purchase_price' => '180', 'sale_price' => '250'),
            // 270 => array('code' => '8993417384219 8993417384233', 'product_name' => 'Eskulin Disney Baby Powder 150gm', 'qty' => '391', 'purchase_price' => '120', 'sale_price' => '120'),
            // 271 => array('code' => '8993417390210 8993417390234 8993417390227', 'product_name' => 'Eskulin Kid\'s Shampoo & Conditioner 200ml', 'qty' => '248', 'purchase_price' => '130', 'sale_price' => '200'),
            
            
            // 274 => array('code' => '8858842029480 8858842042823', 'product_name' => 'Eye Musk', 'qty' => '132', 'purchase_price' => '350', 'sale_price' => '350'),
            // 275 => array('code' => '8858842030110 8858842051573', 'product_name' => 'Eye Roll on', 'qty' => '194', 'purchase_price' => '260', 'sale_price' => '350'),

            // 277 => array('code' => '4015000519915', 'product_name' => 'Fa Body Spray 200ml', 'qty' => '353', 'purchase_price' => '140', 'sale_price' => '160'),
            // 278 => array('code' => '1802112010000', 'product_name' => 'Fair & Lovely 100gm', 'qty' => '20', 'purchase_price' => '320', 'sale_price' => '350'),

            // 280 => array('code' => '8901030649912', 'product_name' => 'Fair & Lovely 80gm', 'qty' => '2744', 'purchase_price' => '140', 'sale_price' => '145'),
            // 281 => array('code' => '1802115570000', 'product_name' => 'Fern Soap 80G', 'qty' => '4753', 'purchase_price' => '90', 'sale_price' => '100'),

            // 283 => array('code' => '8858832533553', 'product_name' => 'Fiore Papaya Soap 135gm', 'qty' => '1227', 'purchase_price' => '65', 'sale_price' => '90'),
            
            
            

            // 288 => array('code' => '8998866605953 8998866605922 8998866605946', 'product_name' => 'Fres & Natural Body Mist 100ml', 'qty' => '206', 'purchase_price' => '100', 'sale_price' => '250'),

            // 290 => array('code' => '1400000020647', 'product_name' => 'Fruits soap 50ml', 'qty' => '182', 'purchase_price' => '100', 'sale_price' => '180'),
            // 291 => array('code' => '8850722060323', 'product_name' => 'G Litter Lipstick 2.3g', 'qty' => '116', 'purchase_price' => '80', 'sale_price' => '100'),
            // 292 => array('code' => '3600541333789', 'product_name' => 'Garnier Body lotion 250ml', 'qty' => '4', 'purchase_price' => '400', 'sale_price' => '450'),
            // 293 => array('code' => '8850434078197', 'product_name' => 'Garnier Body Lotion 400ml', 'qty' => '138', 'purchase_price' => '400', 'sale_price' => '450'),
            // 294 => array('code' => '8992304033872', 'product_name' => 'Garnier Cream Sakura Day 50g', 'qty' => '1373', 'purchase_price' => '350', 'sale_price' => '400'),
            
            // 296 => array('code' => '8992304042522 8992304042539 8992304042546 8991380700029 8992304019524 8992304019562 8991380700609', 'product_name' => 'Garnier FW Women\'s 100ml', 'qty' => '1911', 'purchase_price' => '160', 'sale_price' => '220'),
            // 297 => array('code' => '8992304009204 8992304024740 8992304009167', 'product_name' => 'Garnier Men FW 100ml', 'qty' => '3020', 'purchase_price' => '180', 'sale_price' => '220'),
            // 298 => array('code' => '8992304047152', 'product_name' => 'Garnier Sakura FW 100ml', 'qty' => '1783', 'purchase_price' => '180', 'sale_price' => '220'),

            // 300 => array('code' => '1802113840000', 'product_name' => 'Gatsby Hair Cream 125gm', 'qty' => '330', 'purchase_price' => '110', 'sale_price' => '120'),
            // 301 => array('code' => '1802113830000', 'product_name' => 'Gatsby Hair Cream 70ml', 'qty' => '1300', 'purchase_price' => '65', 'sale_price' => '70'),
            // 302 => array('code' => '8992222054010 8992222051262 8992222054041', 'product_name' => 'Gatsby Roll on 50ml', 'qty' => '240', 'purchase_price' => '120', 'sale_price' => '150'),
            // 303 => array('code' => '8992222053013 8992222050104', 'product_name' => 'Gatsby Water Gloss Hard/Soft 150ml', 'qty' => '984', 'purchase_price' => '100', 'sale_price' => '120'),
            // 304 => array('code' => '8992222050401', 'product_name' => 'Gatsby Water Gloss Hard/Soft 30ml', 'qty' => '6447', 'purchase_price' => '34', 'sale_price' => '40'),
            
            // 306 => array('code' => '047400097742 047400650237 047400307308 047400097872', 'product_name' => 'Gillette Sticks 107gm Gel', 'qty' => '578', 'purchase_price' => '300', 'sale_price' => '350'),
            
            // 308 => array('code' => '8992779145100', 'product_name' => 'Glade Air Freshener 250ml', 'qty' => '28', 'purchase_price' => '160', 'sale_price' => '180'),
            // 309 => array('code' => '8992779139406 8992779140402 8992779136405', 'product_name' => 'Glade Air Freshener 350ml', 'qty' => '129', 'purchase_price' => '200', 'sale_price' => '250'),
            
            // 311 => array('code' => '8850175021667', 'product_name' => 'Glade Car 7ml', 'qty' => '224', 'purchase_price' => '240', 'sale_price' => '500'),
            
            // 313 => array('code' => '8850175012290 8850175012276 8850175012283', 'product_name' => 'Glade Scented Gel 200ml', 'qty' => '666', 'purchase_price' => '160', 'sale_price' => '180'),
            // 314 => array('code' => '1802115390000', 'product_name' => 'Gluta Pure White Soap 70g', 'qty' => '1889', 'purchase_price' => '120', 'sale_price' => '200'),
            
            // 316 => array('code' => '8850722111346 8850722111131 8850722111346 8850722111162 8850722111810 8850722111346 8850722111148', 'product_name' => 'Gozzi Roll On 60ml', 'qty' => '481', 'purchase_price' => '60', 'sale_price' => '90'),

            // 318 => array('code' => '8998824551261', 'product_name' => 'Hanasui Body Spa 250ml', 'qty' => '28', 'purchase_price' => '200', 'sale_price' => '250'),
            // 319 => array('code' => '8998823551248 8998824551346', 'product_name' => 'Hanasui Peel of Musk', 'qty' => '60', 'purchase_price' => '150', 'sale_price' => '250'),


            // 322 => array('code' => '8993379200855 8993379200886 8993379241858', 'product_name' => 'Harmony Soap 70gm', 'qty' => '6347', 'purchase_price' => '22', 'sale_price' => '26'),
            
            // 324 => array('code' => '4902430412049', 'product_name' => 'Head & Shoulders Conditioner 160ml', 'qty' => '27', 'purchase_price' => '160', 'sale_price' => '180'),
            // 325 => array('code' => '8850722038872', 'product_name' => 'Hello Flawless Foundation Powder', 'qty' => '320', 'purchase_price' => '50', 'sale_price' => '80'),
            // 326 => array('code' => '8857102910162', 'product_name' => 'Herbal Clove Toothpaset', 'qty' => '236', 'purchase_price' => '80', 'sale_price' => '120'),
            
            
            // 329 => array('code' => '6923589178523', 'product_name' => 'Huda Beauty Eyeliner', 'qty' => '2022', 'purchase_price' => '60', 'sale_price' => '80'),
            
            // 331 => array('code' => '1802113430000', 'product_name' => 'Huda Beauty Lip stick', 'qty' => '3089', 'purchase_price' => '900', 'sale_price' => '900'),
            // 332 => array('code' => '6922221777551 6973089540160', 'product_name' => 'Huda Beauty Mascara', 'qty' => '921', 'purchase_price' => '60', 'sale_price' => '80'),
            
            
            // 335 => array('code' => '5029053563732', 'product_name' => 'HUGGIES BABY WIPES 72\'S', 'qty' => '371', 'purchase_price' => '140', 'sale_price' => '160'),
            // 336 => array('code' => '6946221235118 6946221235118', 'product_name' => 'ICe Suminer Lotion Aloe/Cucomber/Honer 300ml', 'qty' => '39', 'purchase_price' => '150', 'sale_price' => '180'),
            // 337 => array('code' => '8998103013565 8998103013558', 'product_name' => 'Imperial Body Mist', 'qty' => '190', 'purchase_price' => '120', 'sale_price' => '180'),
            // 338 => array('code' => '8850169450213 8850169450466', 'product_name' => 'Imperial Body Wash 200ml', 'qty' => '400', 'purchase_price' => '140', 'sale_price' => '160'),
            // 339 => array('code' => '8850169436019 8850169406012', 'product_name' => 'Imperial Body Wash 400ml', 'qty' => '5', 'purchase_price' => '240', 'sale_price' => '260'),
            // 340 => array('code' => '8998103002729 8998103011707', 'product_name' => 'Imperial Body Wash 400ml Refill', 'qty' => '19', 'purchase_price' => '140', 'sale_price' => '170'),
            // 341 => array('code' => '8850169940318 8850169850792 8850169940301 8850169851355', 'product_name' => 'Imperial Body Wash Ref 220ml', 'qty' => '68', 'purchase_price' => '120', 'sale_price' => '130'),
            
            
            // 344 => array('code' => '9300830021871 9300830021857 9300830021901 9300830021840', 'product_name' => 'Impuls Body Spray 75ml', 'qty' => '328', 'purchase_price' => '120', 'sale_price' => '140'),
            // 345 => array('code' => '795144060071 795144060163', 'product_name' => 'Intimate Lotion 590ml', 'qty' => '22', 'purchase_price' => '300', 'sale_price' => '350'),
            
            // 347 => array('code' => '8853434000021', 'product_name' => 'Is Me Lotion 190ml', 'qty' => '372', 'purchase_price' => '150', 'sale_price' => '220'),
            // 348 => array('code' => '9789741075973', 'product_name' => 'Is Me Lotion 400ml', 'qty' => '354', 'purchase_price' => '400', 'sale_price' => '480'),
            // 349 => array('code' => '8851929010654', 'product_name' => 'Is Me Lotion 500ml', 'qty' => '529', 'purchase_price' => '400', 'sale_price' => '550'),
            // 350 => array('code' => '8851929016441', 'product_name' => 'ISME alovera Cooling Gel 100ml', 'qty' => '49', 'purchase_price' => '180', 'sale_price' => '250'),
            // 351 => array('code' => '8852522143725', 'product_name' => 'Isme Eye Gel 10ml', 'qty' => '68', 'purchase_price' => '200', 'sale_price' => '260'),
            // 352 => array('code' => '8852522040970', 'product_name' => 'ISME MALASMA CREAM 10ml', 'qty' => '215', 'purchase_price' => '200', 'sale_price' => '280'),

            // 354 => array('code' => '8852525368002', 'product_name' => 'Isme Whitening Leg Therapy Cream', 'qty' => '66', 'purchase_price' => '170', 'sale_price' => '300'),
            // 355 => array('code' => '8852522027094', 'product_name' => 'Isme Whitening Nipple Cream 30ml', 'qty' => '81', 'purchase_price' => '260', 'sale_price' => '300'),
            // 356 => array('code' => '8992856890831 8992856891654 8992856890824 8992856890855', 'product_name' => 'Izzi Body Mist 100ml', 'qty' => '232', 'purchase_price' => '140', 'sale_price' => '180'),
            // 357 => array('code' => '8992856891609 8992856891593 8992856891647', 'product_name' => 'Izzi Body Mist 60ml', 'qty' => '179', 'purchase_price' => '100', 'sale_price' => '120'),
            // 358 => array('code' => '1802114270000', 'product_name' => 'Jackelin Lipstick', 'qty' => '946', 'purchase_price' => '50', 'sale_price' => '100'),

            // 360 => array('code' => '8857122751622 8857122751820 8857122751707 8858891602887 8857122751103 8857122751974 8858891600104 8858891600517', 'product_name' => 'JAM Harbal Soap 60gm', 'qty' => '5640', 'purchase_price' => '30', 'sale_price' => '70'),
            // 361 => array('code' => '8857122751769', 'product_name' => 'Jam Tamarind Soap 135gm', 'qty' => '1016', 'purchase_price' => '70', 'sale_price' => '80'),
            // 362 => array('code' => '8858891602917 8858891602894', 'product_name' => 'Jam Tomato Soap Box 60g', 'qty' => '465', 'purchase_price' => '50', 'sale_price' => '100'),
            // 363 => array('code' => '8991111102719 8991111101613', 'product_name' => 'Johnson Baby Cologne 125ml', 'qty' => '154', 'purchase_price' => '160', 'sale_price' => '180'),
            // 364 => array('code' => '88530215', 'product_name' => 'Johnson Baby Cream Milk/Pink 100ml', 'qty' => '578', 'purchase_price' => '270', 'sale_price' => '285'),
            // 365 => array('code' => '88531649 88530208', 'product_name' => 'Johnson Baby Cream Milk/Pink 50ml', 'qty' => '657', 'purchase_price' => '180', 'sale_price' => '190'),
            // 366 => array('code' => '9556006060346 9556006060254', 'product_name' => 'Johnson Baby Lotion 100ml', 'qty' => '7068', 'purchase_price' => '100', 'sale_price' => '110'),
            // 367 => array('code' => '9556006060353', 'product_name' => 'Johnson Baby Lotion 200ml (Milk)', 'qty' => '2435', 'purchase_price' => '200', 'sale_price' => '240'),
            
            // 369 => array('code' => '8850007032274 8850007032267', 'product_name' => 'Johnson Baby Lotion 500ml', 'qty' => '289', 'purchase_price' => '450', 'sale_price' => '550'),
            // 370 => array('code' => '8410207115872', 'product_name' => 'Johnson Baby Oil 300ml', 'qty' => '69', 'purchase_price' => '220', 'sale_price' => '240'),
            // 371 => array('code' => '8410207175852', 'product_name' => 'Johnson Baby Oil 500ml', 'qty' => '96', 'purchase_price' => '380', 'sale_price' => '400'),
            // 372 => array('code' => '1802113600000', 'product_name' => 'Johnson Baby Powder 100gm Pink/White', 'qty' => '1860', 'purchase_price' => '70', 'sale_price' => '80'),
            // 373 => array('code' => '8850007010265 8850007010302', 'product_name' => 'Johnson Baby Powder 200gm', 'qty' => '1830', 'purchase_price' => '110', 'sale_price' => '115'),
            // 374 => array('code' => '8850007010722', 'product_name' => 'Johnson Baby Powder 400gm', 'qty' => '132', 'purchase_price' => '200', 'sale_price' => '230'),
            // 375 => array('code' => '8850007010241 8850007010104', 'product_name' => 'Johnson Baby Powder 50gm', 'qty' => '5602', 'purchase_price' => '38', 'sale_price' => '45'),
            // 376 => array('code' => '9556006014547', 'product_name' => 'Johnson Baby Shampoo 100ml', 'qty' => '306', 'purchase_price' => '90', 'sale_price' => '92'),
            // 377 => array('code' => '9556006000250', 'product_name' => 'Johnson Baby Shampoo 200ml', 'qty' => '925', 'purchase_price' => '170', 'sale_price' => '170'),
            // 378 => array('code' => '1802114400000', 'product_name' => 'Johnson Baby Shampoo 50ml', 'qty' => '1258', 'purchase_price' => '40', 'sale_price' => '46'),
            // 379 => array('code' => '3574660668322', 'product_name' => 'Johnson Baby Shampoo 750ml', 'qty' => '38', 'purchase_price' => '350', 'sale_price' => '400'),
            // 380 => array('code' => '4801010560500 4801010562108 4801010561309', 'product_name' => 'Johnson Baby Soap 100gm', 'qty' => '7412', 'purchase_price' => '40', 'sale_price' => '50'),
            
            // 382 => array('code' => '8850007020462 8850007020257', 'product_name' => 'Johnson Baby Soap 75gm', 'qty' => '522', 'purchase_price' => '40', 'sale_price' => '45'),
            // 383 => array('code' => '3574661265551', 'product_name' => 'Johnson Baby Wipes', 'qty' => '112', 'purchase_price' => '120', 'sale_price' => '140'),
            
            // 385 => array('code' => '1802112300000', 'product_name' => 'Johnson Gift Set Large', 'qty' => '218', 'purchase_price' => '800', 'sale_price' => '850'),
            // 386 => array('code' => '1802112400000', 'product_name' => 'Johnson Gift Set Small', 'qty' => '576', 'purchase_price' => '420', 'sale_price' => '450'),
            // 387 => array('code' => '9556006060308', 'product_name' => 'Johnson Milk Bath 100ml', 'qty' => '1803', 'purchase_price' => '100', 'sale_price' => '110'),
            // 388 => array('code' => '9556006000304', 'product_name' => 'Johnson Milk Bath 200ml', 'qty' => '5079', 'purchase_price' => '160', 'sale_price' => '200'),
            // 389 => array('code' => '8850007090281', 'product_name' => 'Johnson Milk Bath 500ml', 'qty' => '538', 'purchase_price' => '350', 'sale_price' => '400'),
            // 390 => array('code' => '9556006012086', 'product_name' => 'Johnson Top To Toe 100ml', 'qty' => '2647', 'purchase_price' => '80', 'sale_price' => '110'),
            // 391 => array('code' => '9556006012093', 'product_name' => 'Johnson Top To Toe 200ml', 'qty' => '2592', 'purchase_price' => '170', 'sale_price' => '200'),
            // 392 => array('code' => '1802111800000', 'product_name' => 'Johnson Top To Toe 500ml', 'qty' => '539', 'purchase_price' => '350', 'sale_price' => '400'),

            
            // 395 => array('code' => '8853252002719 8853252002702', 'product_name' => 'K.Brothers Aha Soap 100g', 'qty' => '1397', 'purchase_price' => '120', 'sale_price' => '150'),
            // 396 => array('code' => '8853252007387', 'product_name' => 'K.Brothers Gold Soap', 'qty' => '568', 'purchase_price' => '40', 'sale_price' => '50'),
            // 397 => array('code' => '8853252007738', 'product_name' => 'K.Brothers Lift Up Cream 80ml', 'qty' => '49', 'purchase_price' => '400', 'sale_price' => '500'),
            // 398 => array('code' => '8853252005864', 'product_name' => 'K.Brothers Rice Milk Soap 100ml', 'qty' => '156', 'purchase_price' => '80', 'sale_price' => '150'),
            // 399 => array('code' => '8853252006656', 'product_name' => 'K.Brothers Sliming Gel 150ml', 'qty' => '30', 'purchase_price' => '280', 'sale_price' => '350'),
            // 400 => array('code' => '8853252004515', 'product_name' => 'K.Brothers Slimming & Firming Soap 40g', 'qty' => '596', 'purchase_price' => '80', 'sale_price' => '120'),
            // 401 => array('code' => '8853252008339 8853252008315 8853252005369 8853252008346 8853252008322', 'product_name' => 'K.Brothers Spa Salt Scrub', 'qty' => '273', 'purchase_price' => '200', 'sale_price' => '250'),
            // 402 => array('code' => '8850822071137 8850822071144 8850822071113 8850822060773', 'product_name' => 'Ka Lip Care', 'qty' => '885', 'purchase_price' => '50', 'sale_price' => '60'),
            
            // 404 => array('code' => '8998183425005', 'product_name' => 'Kids Shampoo 500ml', 'qty' => '22', 'purchase_price' => '150', 'sale_price' => '160'),
            // 405 => array('code' => '8850002123410 8859123456759', 'product_name' => 'Kim Cream 20ml', 'qty' => '13', 'purchase_price' => '1400', 'sale_price' => '1680'),
            // 406 => array('code' => '8850002123403', 'product_name' => 'Kim Soap 60gm', 'qty' => '4637', 'purchase_price' => '30', 'sale_price' => '70'),
            
            
            
            
            
            
            // 413 => array('code' => '1802115300000', 'product_name' => 'Kodomo Baby Paste 45gm Mix', 'qty' => '8998', 'purchase_price' => '50', 'sale_price' => '60'),
            // 414 => array('code' => '8850002006614', 'product_name' => 'Kodomo Baby Powder 180ml', 'qty' => '383', 'purchase_price' => '100', 'sale_price' => '120'),
            
            // 416 => array('code' => '1802114900000', 'product_name' => 'Kodomo Brush', 'qty' => '972', 'purchase_price' => '22', 'sale_price' => '40'),
            
            
            // 419 => array('code' => '8850002017009', 'product_name' => 'Kodomo Fiter Wash 750ml', 'qty' => '79', 'purchase_price' => '270', 'sale_price' => '320'),
            // 420 => array('code' => '8850002907645', 'product_name' => 'Kodomo Gift Set Large Box', 'qty' => '65', 'purchase_price' => '700', 'sale_price' => '880'),
            // 421 => array('code' => '8850002907638', 'product_name' => 'Kodomo Gift Set Small', 'qty' => '93', 'purchase_price' => '450', 'sale_price' => '500'),
            // 422 => array('code' => '8850002019614', 'product_name' => 'Kodomo Lotion 200ml', 'qty' => '708', 'purchase_price' => '200', 'sale_price' => '245'),
            
            
            
            
            
            
            
            
            // 431 => array('code' => '022200963435 022200963442 022200962902', 'product_name' => 'Lady Speed Deo Stick 39.6ml', 'qty' => '117', 'purchase_price' => '140', 'sale_price' => '180'),

            
            
            // 435 => array('code' => '1802111670000', 'product_name' => 'Lay Now Whitening Cream 6ml', 'qty' => '112', 'purchase_price' => '220', 'sale_price' => '300'),
            // 436 => array('code' => '8858831002234', 'product_name' => 'LC Le Care Soap 100gm', 'qty' => '188', 'purchase_price' => '100', 'sale_price' => '120'),
            // 437 => array('code' => '8857110780818 8857110780818 8857110780818', 'product_name' => 'Lee Papaya Kojic Acid Soap 160ml', 'qty' => '1117', 'purchase_price' => '120', 'sale_price' => '250'),
            // 438 => array('code' => '8856680001446', 'product_name' => 'Legano Spa Salt 250ml', 'qty' => '65', 'purchase_price' => '200', 'sale_price' => '250'),
            // 439 => array('code' => '8856680001002 8856680001385 8856680004379 8856680000982 8856680001019', 'product_name' => 'Legano Spa Salt 750ml', 'qty' => '1633', 'purchase_price' => '250', 'sale_price' => '380'),
            
            // 441 => array('code' => '8858229013965 8858229013972 8858229013941', 'product_name' => 'Lip Ice Sheer Color 7ml', 'qty' => '931', 'purchase_price' => '50', 'sale_price' => '60'),
            // 442 => array('code' => '8850969367018 8850969369760 8850969369777', 'product_name' => 'Liquid Dish Wash 800ml', 'qty' => '474', 'purchase_price' => '120', 'sale_price' => '150'),
            // 443 => array('code' => '8992725910202 8850007811213', 'product_name' => 'Listerine MW 250ml', 'qty' => '303', 'purchase_price' => '160', 'sale_price' => '200'),
            // 444 => array('code' => '8992725910226 8850007811237', 'product_name' => 'Listerine MW 500ml', 'qty' => '738', 'purchase_price' => '320', 'sale_price' => '350'),
            // 445 => array('code' => '8850007811251', 'product_name' => 'Listerine MW 750+250ml', 'qty' => '183', 'purchase_price' => '500', 'sale_price' => '650'),
            // 446 => array('code' => '8850460990654', 'product_name' => 'Lolane Daily Hair Serum 50ml', 'qty' => '19', 'purchase_price' => '180', 'sale_price' => '200'),
            // 447 => array('code' => '8850460999879', 'product_name' => 'Lolane Gold Spray 350ml', 'qty' => '170', 'purchase_price' => '250', 'sale_price' => '290'),
            
            // 449 => array('code' => '8850460999886 8850460998698', 'product_name' => 'Lolane Hair Style Gel Spray 215ml', 'qty' => '759', 'purchase_price' => '180', 'sale_price' => '210'),
            
            // 451 => array('code' => '8850460997417', 'product_name' => 'Lolane Hairtreatment 250ml', 'qty' => '75', 'purchase_price' => '190', 'sale_price' => '250'),
            // 452 => array('code' => '8850460997400', 'product_name' => 'Lolane Hairtreatment 500+100', 'qty' => '460', 'purchase_price' => '420', 'sale_price' => '460'),
            // 453 => array('code' => '8850460988767 8850460984790', 'product_name' => 'Lolane Intense Mask Set 300ml', 'qty' => '24', 'purchase_price' => '350', 'sale_price' => '400'),
            // 454 => array('code' => '8850460989504', 'product_name' => 'Lolane Pixel Hair Treatment 110ml', 'qty' => '814', 'purchase_price' => '260', 'sale_price' => '300'),
            // 455 => array('code' => '8850460990067 8850460999664', 'product_name' => 'Lolane Pixel Hair Treatment 50ml', 'qty' => '776', 'purchase_price' => '150', 'sale_price' => '200'),
            // 456 => array('code' => '8850460990616', 'product_name' => 'Lolane Pixxel Color 50ml', 'qty' => '35', 'purchase_price' => '120', 'sale_price' => '200'),
            // 457 => array('code' => '8850460786363 8850460986442 8850460986503', 'product_name' => 'Lolane Pixxel Detoxifier Shampoo 500ml', 'qty' => '18', 'purchase_price' => '450', 'sale_price' => '550'),
            // 458 => array('code' => '8850460993235', 'product_name' => 'Lolane Rebonding Gel 125ml', 'qty' => '57', 'purchase_price' => '700', 'sale_price' => '1000'),
            // 459 => array('code' => '8850460988408 8850460988392', 'product_name' => 'Lolane Serum Leave On Spray 120ml', 'qty' => '45', 'purchase_price' => '230', 'sale_price' => '250'),
            // 460 => array('code' => '1802119500000', 'product_name' => 'Lopa Body Wash', 'qty' => '958', 'purchase_price' => '60', 'sale_price' => '100'),
            
            // 462 => array('code' => '8992304082078', 'product_name' => 'Loreal FW White Perfect 100ml', 'qty' => '394', 'purchase_price' => '280', 'sale_price' => '320'),
            
            // 464 => array('code' => '8992304050091', 'product_name' => 'Loreal Men Expart Cream 50ml', 'qty' => '1', 'purchase_price' => '580', 'sale_price' => '600'),
            // 465 => array('code' => '8992304034534 8992304025556', 'product_name' => 'Loreal Men FW 100ml', 'qty' => '298', 'purchase_price' => '220', 'sale_price' => '250'),
            // 466 => array('code' => '6955818256320', 'product_name' => 'Loreal Men FW Pump 150ml', 'qty' => '1', 'purchase_price' => '400', 'sale_price' => '450'),
            
            
            // 469 => array('code' => '8992304054143', 'product_name' => 'Loreal Shampoo 120ml', 'qty' => '391', 'purchase_price' => '80', 'sale_price' => '120'),
            
            
            
            
            // 474 => array('code' => '1802113450000', 'product_name' => 'Lucky Sponge', 'qty' => '1709', 'purchase_price' => '250', 'sale_price' => '300'),
            // 475 => array('code' => '8851932332385 8851932332262 8851932332347 8851932350655', 'product_name' => 'Lux Body Wash 500ml', 'qty' => '1162', 'purchase_price' => '300', 'sale_price' => '360'),

            // 477 => array('code' => '1802114260000', 'product_name' => 'Mac Lipstick', 'qty' => '483', 'purchase_price' => '60', 'sale_price' => '100'),
            // 478 => array('code' => '8853502010679', 'product_name' => 'Madame Acnee Clear Soap 150gm', 'qty' => '296', 'purchase_price' => '120', 'sale_price' => '250'),
            // 479 => array('code' => '6905843323382', 'product_name' => 'Magic Girl Lip Gloss', 'qty' => '1400', 'purchase_price' => '50', 'sale_price' => '60'),
            // 480 => array('code' => '6931904260129', 'product_name' => 'Magic Girl Waterproof Eyeliner', 'qty' => '945', 'purchase_price' => '80', 'sale_price' => '100'),
            

            

            
            
            
            
            
            
            
            
            
            
            
            
            // 497 => array('code' => '8992902152005 8992902152012', 'product_name' => 'Marlboro Body Spray 200ml', 'qty' => '338', 'purchase_price' => '350', 'sale_price' => '400'),
            // 498 => array('code' => '6970673570619', 'product_name' => 'Mars Lipstick', 'qty' => '2750', 'purchase_price' => '50', 'sale_price' => '80'),
            // 499 => array('code' => '8850434097013', 'product_name' => 'Maybelline Eye liner', 'qty' => '103', 'purchase_price' => '300', 'sale_price' => '380'),
            // 500 => array('code' => '1802113520000', 'product_name' => 'Maybelline Face Powder Thai', 'qty' => '145', 'purchase_price' => '300', 'sale_price' => '380'),
            // 501 => array('code' => '8850434020158', 'product_name' => 'Maybelline Hypercurl Mascara 9.2ml', 'qty' => '31', 'purchase_price' => '300', 'sale_price' => '380'),
            
            

            // 505 => array('code' => '1802114310000', 'product_name' => 'Miss Five Lipstick', 'qty' => '48', 'purchase_price' => '80', 'sale_price' => '120'),
            // 506 => array('code' => '6953743310216 6953743310308 6953743310308 6953743310254', 'product_name' => 'Miss Rose Lip stick', 'qty' => '600', 'purchase_price' => '80', 'sale_price' => '150'),
            // 507 => array('code' => '8859178707011', 'product_name' => 'Misten Acne Clear F.W 100ml', 'qty' => '71', 'purchase_price' => '140', 'sale_price' => '180'),
            // 508 => array('code' => '8859178707004', 'product_name' => 'Misten Papy F.W 100ml', 'qty' => '180', 'purchase_price' => '130', 'sale_price' => '160'),
            
            // 510 => array('code' => '8859178707516 8859178707615', 'product_name' => 'Mistin White Spa Lotion 400ml', 'qty' => '267', 'purchase_price' => '280', 'sale_price' => '310'),
            // 511 => array('code' => '8852053104042 8852053104035', 'product_name' => 'Modern Hair Tonic 90ml', 'qty' => '115', 'purchase_price' => '300', 'sale_price' => '400'),
            // 512 => array('code' => '1802114830000', 'product_name' => 'Monglya Prikly Heat Powder 200gm', 'qty' => '63', 'purchase_price' => '150', 'sale_price' => '250'),
            // 513 => array('code' => '8853603501069', 'product_name' => 'Mosquito Racket', 'qty' => '100', 'purchase_price' => '200', 'sale_price' => '250'),
            // 514 => array('code' => '5021464959029', 'product_name' => 'Mother Care Lotion/Bath/sh 300ml UK', 'qty' => '4', 'purchase_price' => '400', 'sale_price' => '430'),
            
            
            // 517 => array('code' => '8850722290508 8850722290546 8850722290560', 'product_name' => 'My Choice Salt 350ml', 'qty' => '499', 'purchase_price' => '150', 'sale_price' => '250'),

            // 519 => array('code' => '1802114490000', 'product_name' => 'Nail Cutter Set', 'qty' => '37', 'purchase_price' => '280', 'sale_price' => '300'),
            // 520 => array('code' => '5010724526392 5010724529669', 'product_name' => 'Nair Body Wax Stips Body & leg', 'qty' => '45', 'purchase_price' => '110', 'sale_price' => '180'),
            // 521 => array('code' => '8859288000019 8859288000088 8859288000095 8859288000057 8859288000071', 'product_name' => 'Nano Extra White Soap 160gm', 'qty' => '751', 'purchase_price' => '140', 'sale_price' => '220'),

            // 523 => array('code' => '1802115350000', 'product_name' => 'Natripa Orange Soap 50g', 'qty' => '96', 'purchase_price' => '50', 'sale_price' => '60'),
            // 524 => array('code' => '8901248104043', 'product_name' => 'Navaratana Oil 200ml', 'qty' => '24', 'purchase_price' => '120', 'sale_price' => '160'),
            // 525 => array('code' => '8901248104050', 'product_name' => 'Navaratana Oil 400ml', 'qty' => '53', 'purchase_price' => '220', 'sale_price' => '260'),
            // 526 => array('code' => '1802114590000', 'product_name' => 'Neo Nice Nail Remover 450ml', 'qty' => '49', 'purchase_price' => '100', 'sale_price' => '150'),
            // 527 => array('code' => '070501017104', 'product_name' => 'Neutrogena Acne Wash 177ml', 'qty' => '21', 'purchase_price' => '600', 'sale_price' => '650'),
            // 528 => array('code' => '070501017128', 'product_name' => 'Neutrogena Acne Wash 269ml', 'qty' => '81', 'purchase_price' => '700', 'sale_price' => '800'),
            // 529 => array('code' => '070501811030', 'product_name' => 'Neutrogena Cleanser Fome 100ml', 'qty' => '168', 'purchase_price' => '280', 'sale_price' => '350'),
            // 530 => array('code' => '070501060902', 'product_name' => 'Neutrogena Deep Clean FW 250ml', 'qty' => '23', 'purchase_price' => '600', 'sale_price' => '650'),
            // 531 => array('code' => '070501092002', 'product_name' => 'Neutrogena T.Gel Shampoo 4.4oz', 'qty' => '5', 'purchase_price' => '550', 'sale_price' => '600'),
            // 532 => array('code' => '4056800891535', 'product_name' => 'New Wella Hair Gel 150ml Uk', 'qty' => '1', 'purchase_price' => '300', 'sale_price' => '320'),
            // 533 => array('code' => '8857098300312 8857098300329', 'product_name' => 'Nine Teen Bleach Cream 80ml', 'qty' => '22', 'purchase_price' => '180', 'sale_price' => '350'),
            

            
            
            // 538 => array('code' => '8850029024196 8850029022833', 'product_name' => 'Nivea Lotion Extra White 600+600ml', 'qty' => '2135', 'purchase_price' => '900', 'sale_price' => '1250'),
            // 539 => array('code' => '4005808353200 4005808355877', 'product_name' => 'Nivea Lotion UV Whitening 400ml', 'qty' => '464', 'purchase_price' => '350', 'sale_price' => '450'),
            // 540 => array('code' => '4005808300228', 'product_name' => 'Nivea Men Body Spray 150ml', 'qty' => '306', 'purchase_price' => '140', 'sale_price' => '160'),
            
            // 542 => array('code' => '8999777005405 8999777006365', 'product_name' => 'Nivea Men FW Pump 120ml', 'qty' => '314', 'purchase_price' => '280', 'sale_price' => '320'),
            
            
            
            
            // 547 => array('code' => '8999777006693', 'product_name' => 'Nivea Toner 200ml', 'qty' => '744', 'purchase_price' => '250', 'sale_price' => '280'),
            // 548 => array('code' => '8850007850038', 'product_name' => 'Nizoral Shampoo 100ml', 'qty' => '23', 'purchase_price' => '560', 'sale_price' => '580'),
            
            
            
            
            // 553 => array('code' => '8850175066026', 'product_name' => 'Off! Spray 170gm', 'qty' => '269', 'purchase_price' => '400', 'sale_price' => '450'),
            
            
            
            
            
            
            // 560 => array('code' => '012044343104 012044342404 012044342503', 'product_name' => 'Old Spice Sticks 63g', 'qty' => '171', 'purchase_price' => '160', 'sale_price' => '180'),
            // 561 => array('code' => '012044038888 012044039540 012044000236', 'product_name' => 'Old Spice Sticks 85g', 'qty' => '155', 'purchase_price' => '250', 'sale_price' => '290'),
            // 562 => array('code' => '8858692210137', 'product_name' => 'OMO Face Serum 120ml', 'qty' => '48', 'purchase_price' => '200', 'sale_price' => '400'),
            // 563 => array('code' => '1802118600000', 'product_name' => 'Omo Lotion 500ml', 'qty' => '270', 'purchase_price' => '500', 'sale_price' => '700'),
            // 564 => array('code' => '8858692901004', 'product_name' => 'OMO White Plus Mix Colour Soap 100g', 'qty' => '5189', 'purchase_price' => '85', 'sale_price' => '100'),
            
            // 566 => array('code' => '4902430502214', 'product_name' => 'Oral B Brush Set', 'qty' => '1095', 'purchase_price' => '120', 'sale_price' => '150'),
            // 567 => array('code' => '04902430357968', 'product_name' => 'Oral B Shiny clean Brush', 'qty' => '2967', 'purchase_price' => '30', 'sale_price' => '40'),
            
            // 569 => array('code' => '8992821100866', 'product_name' => 'Oxy FW 100ml Deep Wash', 'qty' => '456', 'purchase_price' => '180', 'sale_price' => '240'),
            
            // 571 => array('code' => '5996175230982', 'product_name' => 'Palmolive BW 500ml', 'qty' => '42', 'purchase_price' => '250', 'sale_price' => '280'),
            
            
            // 574 => array('code' => '8858927505779 8858927505694 8858927505786 8858927505410 8858927505601', 'product_name' => 'Panamas Bust Cream 100ml', 'qty' => '181', 'purchase_price' => '240', 'sale_price' => '300'),
            
            // 576 => array('code' => '8858927505717', 'product_name' => 'Panamas Under Arm Cream 40ml', 'qty' => '331', 'purchase_price' => '180', 'sale_price' => '300'),
            // 577 => array('code' => '8858927505533', 'product_name' => 'Pannamas AHA firming Gel 100g', 'qty' => '19', 'purchase_price' => '350', 'sale_price' => '500'),
            // 578 => array('code' => '8858927505311', 'product_name' => 'Pannamas Bergamot Shampoo 364gm', 'qty' => '13', 'purchase_price' => '450', 'sale_price' => '650'),
            
            // 580 => array('code' => '8858927505595 8858927505618', 'product_name' => 'Pannamas Lighting Lotion 450ml', 'qty' => '2679', 'purchase_price' => '400', 'sale_price' => '800'),
            // 581 => array('code' => '8858927505212', 'product_name' => 'Pannamas Nipple Pink Cream 12g', 'qty' => '183', 'purchase_price' => '250', 'sale_price' => '300'),
            
            
            
            // 585 => array('code' => '1802115000000', 'product_name' => 'Pantene Shampoo 140ml', 'qty' => '76', 'purchase_price' => '140', 'sale_price' => '180'),
            // 586 => array('code' => '6921199178889', 'product_name' => 'Papaya Soap 100ml', 'qty' => '13', 'purchase_price' => '50', 'sale_price' => '70'),
            
            
            
            
            // 591 => array('code' => '4015400622413', 'product_name' => 'PEMPERS BABY WIPS 56\'S', 'qty' => '366', 'purchase_price' => '140', 'sale_price' => '160'),
            // 592 => array('code' => '8999999037987 8999999038007', 'product_name' => 'Pepsodent Brush Action123', 'qty' => '3902', 'purchase_price' => '40', 'sale_price' => '80'),
            // 593 => array('code' => '8999999043278', 'product_name' => 'Pepsodent Brush Deep Clean', 'qty' => '102', 'purchase_price' => '40', 'sale_price' => '90'),
            // 594 => array('code' => '8999999039233', 'product_name' => 'Pepsodent Brush Triple Clean', 'qty' => '6252', 'purchase_price' => '28', 'sale_price' => '40'),
            
            // 596 => array('code' => '8999999710873', 'product_name' => 'Pepsodent Harbal Paste 120gm', 'qty' => '2638', 'purchase_price' => '90', 'sale_price' => '150'),
            // 597 => array('code' => '8999999710866', 'product_name' => 'Pepsodent Herbal Past 190g', 'qty' => '1789', 'purchase_price' => '100', 'sale_price' => '180'),
            // 598 => array('code' => '8999999705985 8999999707743', 'product_name' => 'Pepsodent Junior Paste 50gm', 'qty' => '11035', 'purchase_price' => '55', 'sale_price' => '70'),
            // 599 => array('code' => '8999999706180', 'product_name' => 'Pepsodent Paste 190gm', 'qty' => '1750', 'purchase_price' => '100', 'sale_price' => '130'),
            // 600 => array('code' => '8999999030186', 'product_name' => 'Pepsodent Paste 190gm Action123', 'qty' => '2638', 'purchase_price' => '100', 'sale_price' => '150'),
            // 601 => array('code' => '8999999037765', 'product_name' => 'Pepsodent Paste 225gm', 'qty' => '541', 'purchase_price' => '120', 'sale_price' => '200'),
            // 602 => array('code' => '8992895171106', 'product_name' => 'Pierre Cardin Body Spray 200ml', 'qty' => '248', 'purchase_price' => '350', 'sale_price' => '700'),
            // 603 => array('code' => '8992222060349 8992222060110 8992222060134', 'product_name' => 'Pixy Stick Roll On 34ml', 'qty' => '103', 'purchase_price' => '80', 'sale_price' => '90'),
            // 604 => array('code' => '8855140006748 8855140006762', 'product_name' => 'Plante Camu camu White Body Lotion 850ml', 'qty' => '41', 'purchase_price' => '600', 'sale_price' => '850'),
            // 605 => array('code' => '3607343499262 3607343392945', 'product_name' => 'Play Body Body Mist 100ml', 'qty' => '105', 'purchase_price' => '160', 'sale_price' => '220'),
            // 606 => array('code' => '3614221642313 3614221641989 3614221642054 3614221642160 3614221642443 3614221642511', 'product_name' => 'Play Boy Body Spray 150ml', 'qty' => '266', 'purchase_price' => '190', 'sale_price' => '200'),

            
            // 609 => array('code' => '8809248459487', 'product_name' => 'Pomegranate Soothing Gel 400g', 'qty' => '439', 'purchase_price' => '250', 'sale_price' => '250'),
            // 610 => array('code' => '8851932199179', 'product_name' => 'Ponds Age Miracle FW 100ml', 'qty' => '466', 'purchase_price' => '260', 'sale_price' => '280'),
            // 611 => array('code' => '8999999719746', 'product_name' => 'Ponds Cleansing Milk 150ml', 'qty' => '798', 'purchase_price' => '170', 'sale_price' => '250'),
            
            // 613 => array('code' => '8851932172264', 'product_name' => 'Ponds Flawless FW 100ml', 'qty' => '195', 'purchase_price' => '260', 'sale_price' => '280'),
            // 614 => array('code' => '8999999058722', 'product_name' => 'Ponds FW Women 50ml', 'qty' => '336', 'purchase_price' => '100', 'sale_price' => '120'),
            // 615 => array('code' => '8999999717025 8999999053062 8999999053048 8999999053055 8999999040451', 'product_name' => 'Ponds FW Women\'s 100ml', 'qty' => '4323', 'purchase_price' => '160', 'sale_price' => '180'),
            // 616 => array('code' => '8851932082983', 'product_name' => 'Ponds Lemon Cold Cream 60ml', 'qty' => '73', 'purchase_price' => '200', 'sale_price' => '250'),
            // 617 => array('code' => '8851932111805 8851932111799', 'product_name' => 'Ponds Magic Powder 50ml', 'qty' => '32', 'purchase_price' => '65', 'sale_price' => '80'),
            // 618 => array('code' => '8850722092003 8850722092027', 'product_name' => 'Ponds Men F.W 100ml', 'qty' => '193', 'purchase_price' => '160', 'sale_price' => '200'),
            // 619 => array('code' => '8901030638589', 'product_name' => 'Ponds Powder 100ml', 'qty' => '186', 'purchase_price' => '95', 'sale_price' => '100'),



            // 623 => array('code' => '8999999720094', 'product_name' => 'Ponds Toner 150ml', 'qty' => '763', 'purchase_price' => '170', 'sale_price' => '250'),
            // 624 => array('code' => '98853318000546', 'product_name' => 'Pop Cream 20gm', 'qty' => '1639', 'purchase_price' => '73', 'sale_price' => '90'),

            // 626 => array('code' => '8853318000642', 'product_name' => 'Pop Cream Popular 4gm', 'qty' => '80', 'purchase_price' => '200', 'sale_price' => '280'),
            // 627 => array('code' => '8853318001373', 'product_name' => 'Pop Gel 50ml', 'qty' => '55', 'purchase_price' => '240', 'sale_price' => '350'),

            

            
            // 632 => array('code' => '8851447010006', 'product_name' => 'Poy Sian Inhalar', 'qty' => '941', 'purchase_price' => '50', 'sale_price' => '60'),

            

            // 636 => array('code' => '8850512902024', 'product_name' => 'Preven Speciali Hair Tonic 90ml', 'qty' => '28', 'purchase_price' => '200', 'sale_price' => '400'),
            // 637 => array('code' => '5031413905434 5031413000023', 'product_name' => 'Pritty Cotton Roll/Pad/Plate Uk', 'qty' => '98', 'purchase_price' => '160', 'sale_price' => '180'),
            // 638 => array('code' => '8850252004415', 'product_name' => 'Promina Cream', 'qty' => '512', 'purchase_price' => '150', 'sale_price' => '160'),
            // 639 => array('code' => '6281056598619 6281056598602', 'product_name' => 'Prophecy Body Spray 100ml', 'qty' => '1813', 'purchase_price' => '120', 'sale_price' => '120'),
            // 640 => array('code' => '6281056585466', 'product_name' => 'Prophecy Body Spray 125ml', 'qty' => '148', 'purchase_price' => '150', 'sale_price' => '150'),
            // 641 => array('code' => '6281056585657', 'product_name' => 'Prophecy Body spray 150ml', 'qty' => '169', 'purchase_price' => '220', 'sale_price' => '250'),
            // 642 => array('code' => '6281056585459', 'product_name' => 'Prophecy Body Spray 250ml', 'qty' => '355', 'purchase_price' => '320', 'sale_price' => '350'),
            // 643 => array('code' => '6281056175704', 'product_name' => 'Prophecy Parfume 100ml', 'qty' => '382', 'purchase_price' => '400', 'sale_price' => '420'),
            // 644 => array('code' => '8850006534359', 'product_name' => 'Protex Soap 75gm', 'qty' => '53', 'purchase_price' => '55', 'sale_price' => '70'),


            
            // 648 => array('code' => '8992832603615 8992832603172 8992832603165', 'product_name' => 'Regazza Spray Cologne 100ml', 'qty' => '85', 'purchase_price' => '280', 'sale_price' => '350'),
            // 649 => array('code' => '4902430396608 4902430432344 4902430695732', 'product_name' => 'Rejoice Conditioner 140ml', 'qty' => '140', 'purchase_price' => '140', 'sale_price' => '180'),
            // 650 => array('code' => '4902430452564 4902430452922 4902430452557', 'product_name' => 'Rejoice Conditioner 320ml', 'qty' => '211', 'purchase_price' => '300', 'sale_price' => '350'),
            // 651 => array('code' => '4902430396639 4902430396646 4902430432337', 'product_name' => 'Rejoice Shampoo 140ml', 'qty' => '169', 'purchase_price' => '130', 'sale_price' => '180'),
            
            // 653 => array('code' => '5019091500691', 'product_name' => 'Revitale Eye Gel', 'qty' => '3', 'purchase_price' => '180', 'sale_price' => '220'),

            // 655 => array('code' => '009976232125', 'product_name' => 'Revlon Lipstick', 'qty' => '485', 'purchase_price' => '60', 'sale_price' => '100'),
            
            
            // 658 => array('code' => '8901030598913 8901030598852 8901030598838 7791293033600 7791293033570', 'product_name' => 'Rexona Body Body Spreay 150ml', 'qty' => '39', 'purchase_price' => '140', 'sale_price' => '160'),
            // 659 => array('code' => '54024502 96009628 96086223 50075188', 'product_name' => 'Rexona Deo Sticks 40ml', 'qty' => '626', 'purchase_price' => '130', 'sale_price' => '155'),
            // 660 => array('code' => '4800888143389 4800888158499 4800888153876 4800888192486 4800888142894 4800888142887 4800888143402', 'product_name' => 'Rexona Roll On 50ml', 'qty' => '34', 'purchase_price' => '90', 'sale_price' => '100'),
            // 661 => array('code' => '8850722091945', 'product_name' => 'Rice Milk F.W 100ml', 'qty' => '2220', 'purchase_price' => '110', 'sale_price' => '180'),

            
            
            // 665 => array('code' => '8851412145627', 'product_name' => 'Riya Co Soap 80gm', 'qty' => '241', 'purchase_price' => '120', 'sale_price' => '150'),
            // 666 => array('code' => '8858790800384', 'product_name' => 'Rose line Black Parl Soap 90gm', 'qty' => '38', 'purchase_price' => '90', 'sale_price' => '200'),
            // 667 => array('code' => '8858790800391', 'product_name' => 'Roselyn Anti-Aging Soap 60g', 'qty' => '119', 'purchase_price' => '150', 'sale_price' => '200'),
            // 668 => array('code' => '8858790800377', 'product_name' => 'Roselyn Gold Dust Soap 80g', 'qty' => '97', 'purchase_price' => '130', 'sale_price' => '280'),
            // 669 => array('code' => '8858790801886', 'product_name' => 'Roselyn Stretch Mark Cream 120g', 'qty' => '28', 'purchase_price' => '300', 'sale_price' => '380'),

            

            
            
            // 675 => array('code' => '1802114940000', 'product_name' => 'Selsun Shampoo 100ml', 'qty' => '214', 'purchase_price' => '380', 'sale_price' => '500'),
            // 676 => array('code' => '8851520060225', 'product_name' => 'Selsun Shampoo 50ml', 'qty' => '150', 'purchase_price' => '350', 'sale_price' => '450'),
            
            
            
            
            // 681 => array('code' => '4936613097259', 'product_name' => 'Shichade\' Cream 5ml', 'qty' => '46', 'purchase_price' => '140', 'sale_price' => '180'),
            // 682 => array('code' => '1802115260000', 'product_name' => 'Shichade\' Soap 50gm', 'qty' => '98', 'purchase_price' => '50', 'sale_price' => '120'),
            // 683 => array('code' => '6295124003714 4780030970219', 'product_name' => 'Shirley May Parfume 100ml', 'qty' => '23', 'purchase_price' => '350', 'sale_price' => '400'),

            
            

            // 688 => array('code' => '8856995001513', 'product_name' => 'Sipan Polish Powder 150gm', 'qty' => '39', 'purchase_price' => '200', 'sale_price' => '250'),

            // 690 => array('code' => '1802112000000', 'product_name' => 'Skin Expart Men Aftershave+BW Set', 'qty' => '1', 'purchase_price' => '700', 'sale_price' => '750'),
            // 691 => array('code' => '1802111200000', 'product_name' => 'Sliming Gel', 'qty' => '53', 'purchase_price' => '400', 'sale_price' => '500'),
            // 692 => array('code' => '1802111550000', 'product_name' => 'Sliming Tea', 'qty' => '100', 'purchase_price' => '220', 'sale_price' => '250'),
            
            // 694 => array('code' => '8855059001414', 'product_name' => 'Snail White Soap 60ml', 'qty' => '140', 'purchase_price' => '130', 'sale_price' => '200'),
            // 695 => array('code' => '8852086000526 8852086923177', 'product_name' => 'Snak Active Powder 280ml', 'qty' => '365', 'purchase_price' => '180', 'sale_price' => '250'),
            // 696 => array('code' => '8852086001004 8852086101018', 'product_name' => 'Snake Pricly Heat Powder 50ml', 'qty' => '560', 'purchase_price' => '40', 'sale_price' => '70'),
            // 697 => array('code' => '1802115580000', 'product_name' => 'Soap Case', 'qty' => '528', 'purchase_price' => '40', 'sale_price' => '50'),
            // 698 => array('code' => '8992772064286', 'product_name' => 'Soffel mosqoito spray 80g', 'qty' => '144', 'purchase_price' => '200', 'sale_price' => '220'),
            // 699 => array('code' => '8853827002779 8853827001383 8853827002786 8853827000683', 'product_name' => 'Spa Hair Lotion 150ml', 'qty' => '168', 'purchase_price' => '120', 'sale_price' => '160'),
            // 700 => array('code' => '8853827003004', 'product_name' => 'Spa Olive OIl 50ml', 'qty' => '25', 'purchase_price' => '50', 'sale_price' => '80'),
            // 701 => array('code' => '8859159200043', 'product_name' => 'Spa Soap 90gm', 'qty' => '63', 'purchase_price' => '100', 'sale_price' => '120'),
            
            
            
            // 705 => array('code' => '022200940207 022200940214', 'product_name' => 'Speed Deo Sticks 51g', 'qty' => '221', 'purchase_price' => '140', 'sale_price' => '180'),
            
            // 707 => array('code' => '077043358023 077043355862', 'product_name' => 'Stives Scrub F.W 170ml', 'qty' => '77', 'purchase_price' => '250', 'sale_price' => '260'),
            // 708 => array('code' => '077043112427 077043104729', 'product_name' => 'Stives Scrub Jar 283ml', 'qty' => '140', 'purchase_price' => '310', 'sale_price' => '350'),

            // 710 => array('code' => '6942644317585 6942644317615', 'product_name' => 'Subaru Black Hair Colour120ml', 'qty' => '104', 'purchase_price' => '120', 'sale_price' => '140'),
            // 711 => array('code' => '6942644305209', 'product_name' => 'Subaru Black Hair Shampoo 200ml', 'qty' => '57', 'purchase_price' => '200', 'sale_price' => '240'),

            
            
            // 715 => array('code' => '8851932354332 8851932355339', 'product_name' => 'Sunsilk Conditioner 320ml', 'qty' => '78', 'purchase_price' => '250', 'sale_price' => '265'),
            // 716 => array('code' => '8851932355018 8851932355049 8851932354981 8851932355001 8851932354974 8851932355032', 'product_name' => 'Sunsilk Shampoo 160ml', 'qty' => '2591', 'purchase_price' => '120', 'sale_price' => '160'),
            
            // 718 => array('code' => '8851932353854 8851932353823 8851932353861 8851932353847', 'product_name' => 'Sunsilk Shampoo 450ml', 'qty' => '387', 'purchase_price' => '300', 'sale_price' => '380'),
            
            
            // 721 => array('code' => '8992902102017 8992902102024', 'product_name' => 'Tabac BS 200ml', 'qty' => '323', 'purchase_price' => '350', 'sale_price' => '580'),
            // 722 => array('code' => '1802114530000', 'product_name' => 'Tajbbe', 'qty' => '10', 'purchase_price' => '50', 'sale_price' => '60'),
            // 723 => array('code' => '8857200098489 8857200098427', 'product_name' => 'Taoyeablok Deodorant Powder 22g', 'qty' => '41', 'purchase_price' => '100', 'sale_price' => '120'),
            // 724 => array('code' => '8997014402031', 'product_name' => 'Temalawak Face Cream 50ml', 'qty' => '401', 'purchase_price' => '100', 'sale_price' => '180'),
            // 725 => array('code' => '8991182082453', 'product_name' => 'Temulawak Face Wash 100ml', 'qty' => '6', 'purchase_price' => '120', 'sale_price' => '180'),
            
            // 727 => array('code' => '8856051009989', 'product_name' => 'Thai Balm', 'qty' => '840', 'purchase_price' => '50', 'sale_price' => '100'),

            // 729 => array('code' => '8858953200426', 'product_name' => 'Thai Donla Olive Oil 480ml', 'qty' => '93', 'purchase_price' => '250', 'sale_price' => '350'),


            // 732 => array('code' => '1802114540000', 'product_name' => 'Thai Nail Polish 10ml', 'qty' => '9563', 'purchase_price' => '50', 'sale_price' => '80'),

            // 734 => array('code' => '8853252005963 8853252005956', 'product_name' => 'Thanaka K.Brothers Soap 60gm', 'qty' => '9252', 'purchase_price' => '50', 'sale_price' => '120'),
            
            // 736 => array('code' => '8994037110011', 'product_name' => 'Tokyo Air Freshner 70ml', 'qty' => '59', 'purchase_price' => '50', 'sale_price' => '60'),
            // 737 => array('code' => '8859128304512', 'product_name' => 'Tomato Cream Day/Nigh 30g', 'qty' => '93', 'purchase_price' => '300', 'sale_price' => '500'),
            // 738 => array('code' => '8858989875230', 'product_name' => 'Tomato Lotion 500ml Thai', 'qty' => '152', 'purchase_price' => '500', 'sale_price' => '850'),

            // 740 => array('code' => '1802113440000', 'product_name' => 'Touch Me Puff', 'qty' => '1729', 'purchase_price' => '350', 'sale_price' => '400'),

            // 742 => array('code' => '8851932350310 8851932350198', 'product_name' => 'Tresemme Conditioner 480ml', 'qty' => '171', 'purchase_price' => '330', 'sale_price' => '400'),
            // 743 => array('code' => '8999999041106 8999999041045 8999999041120', 'product_name' => 'Tresemme Shampoo 170ml', 'qty' => '353', 'purchase_price' => '180', 'sale_price' => '190'),
            // 744 => array('code' => '8999999040956', 'product_name' => 'Tresemme Shampoo 340ml', 'qty' => '358', 'purchase_price' => '340', 'sale_price' => '360'),
            // 745 => array('code' => '8851932350303 8851932350181 8851932350143', 'product_name' => 'Tresemme Shampoo 480ml KS/PS/SR/SS', 'qty' => '22', 'purchase_price' => '330', 'sale_price' => '380'),
            
            // 747 => array('code' => '8853252009923', 'product_name' => 'USA Black Soap 50gm', 'qty' => '6053', 'purchase_price' => '25', 'sale_price' => '35'),
            // 748 => array('code' => '8858311001320', 'product_name' => 'Vampier Soap 100ml', 'qty' => '465', 'purchase_price' => '140', 'sale_price' => '200'),
            
            // 750 => array('code' => '8901030025280', 'product_name' => 'Vaseline Hair Tonic 100ml', 'qty' => '372', 'purchase_price' => '90', 'sale_price' => '100'),
            // 751 => array('code' => '8851932143578', 'product_name' => 'Vaseline Hand & Nails Lotion 85ml', 'qty' => '77', 'purchase_price' => '200', 'sale_price' => '240'),
            // 752 => array('code' => '6001085120946 60022141 60022127 6001085121028 60019066 42182634', 'product_name' => 'Vaseline Jelly 100ml', 'qty' => '2323', 'purchase_price' => '100', 'sale_price' => '120'),
            // 753 => array('code' => '6001087005647 6001087010894 6001087010870', 'product_name' => 'Vaseline Jelly 250ml', 'qty' => '100', 'purchase_price' => '240', 'sale_price' => '260'),
            // 754 => array('code' => '5099802150117', 'product_name' => 'Vaseline lip Tharapy', 'qty' => '50', 'purchase_price' => '110', 'sale_price' => '150'),
            
            
            // 757 => array('code' => '8851932143370 8851932283953', 'product_name' => 'Vaseline Lotion Deep/ Healthy White 600ml Thai', 'qty' => '282', 'purchase_price' => '550', 'sale_price' => '650'),
            // 758 => array('code' => '8999999001988 8999999034030', 'product_name' => 'Vaseline Men FW 100ml', 'qty' => '1185', 'purchase_price' => '200', 'sale_price' => '250'),

            
            // 761 => array('code' => '8851938902223', 'product_name' => 'Victory Brush Sslima Travel Box', 'qty' => '1021', 'purchase_price' => '80', 'sale_price' => '120'),
            // 762 => array('code' => '8851938902131', 'product_name' => 'Victory remla Toothbrush', 'qty' => '240', 'purchase_price' => '120', 'sale_price' => '150'),
            
            // 764 => array('code' => '8858831005044', 'product_name' => 'Vipada Honey & Gold Soap 130g', 'qty' => '309', 'purchase_price' => '120', 'sale_price' => '200'),
            // 765 => array('code' => '8858831000612', 'product_name' => 'Vipada Papaya Soap 70gm', 'qty' => '147', 'purchase_price' => '50', 'sale_price' => '120'),
            // 766 => array('code' => '8850722043616', 'product_name' => 'Vitamin E Aron Cream 200ml', 'qty' => '80', 'purchase_price' => '160', 'sale_price' => '220'),
            // 767 => array('code' => '1802113490000', 'product_name' => 'Vlue Vatiny POwder 11g', 'qty' => '238', 'purchase_price' => '55', 'sale_price' => '62'),
            
            // 769 => array('code' => '5012345678900', 'product_name' => 'White Active Cream 20g', 'qty' => '538', 'purchase_price' => '130', 'sale_price' => '180'),
            // 770 => array('code' => '8858868300426', 'product_name' => 'Whitening Herbal Soap 160gm', 'qty' => '98', 'purchase_price' => '60', 'sale_price' => '60'),
            
            
            // 773 => array('code' => '6297000669243 6297000669267 6297000442617', 'product_name' => 'Yardeley Lotion 200ml', 'qty' => '15', 'purchase_price' => '240', 'sale_price' => '260'),
            // 774 => array('code' => '6297000226767 6297000226750 6297000669274', 'product_name' => 'Yardley Lotion 400ml', 'qty' => '9', 'purchase_price' => '440', 'sale_price' => '260'),


            // 777 => array('code' => '8857101110754', 'product_name' => 'YC Acne Soap 100gm', 'qty' => '508', 'purchase_price' => '110', 'sale_price' => '120'),
            
            // 779 => array('code' => '1802112290000', 'product_name' => 'YC Breast Cream 120ml', 'qty' => '442', 'purchase_price' => '180', 'sale_price' => '250'),
            // 780 => array('code' => '8855322000472', 'product_name' => 'Yc Eye Lightning Cream', 'qty' => '389', 'purchase_price' => '180', 'sale_price' => '220'),
            // 781 => array('code' => '1802115200000', 'product_name' => 'YC Harbal Soap 100gm', 'qty' => '1603', 'purchase_price' => '100', 'sale_price' => '110'),
            // 782 => array('code' => '1802114230000', 'product_name' => 'YC LIP & Cheek Pink Gel 15ml', 'qty' => '125', 'purchase_price' => '220', 'sale_price' => '280'),
            // 783 => array('code' => '8857101118446 8857101150538 8857101157230 8857101110440 8857101157889', 'product_name' => 'YC Peel Of Musk 120ml', 'qty' => '643', 'purchase_price' => '160', 'sale_price' => '180'),
            // 784 => array('code' => '8857101146074', 'product_name' => 'YC Spa Salt Pack 300ml', 'qty' => '890', 'purchase_price' => '900', 'sale_price' => '130'),
            
            // 786 => array('code' => '8857101125772', 'product_name' => 'YC UV Expert Whitening Lotion 250ml', 'qty' => '19', 'purchase_price' => '200', 'sale_price' => '220'),
            
            
            // 789 => array('code' => '1802115230000', 'product_name' => 'YC Whitenning Milk Soap 90gm', 'qty' => '636', 'purchase_price' => '100', 'sale_price' => '110'),
            // 790 => array('code' => '5014697027948 5014697027962 5014697027924 5014697027900 8903105', 'product_name' => 'Yeardly Body Spray 200ml', 'qty' => '16', 'purchase_price' => '165', 'sale_price' => '180'),
            // 791 => array('code' => '6297000442938 6297000442457', 'product_name' => 'Yeardly Gentle Man Edt 100ml', 'qty' => '6', 'purchase_price' => '750', 'sale_price' => '800'),
            // 792 => array('code' => '8853976006208', 'product_name' => 'Yoko  Argan Oil hair&Skin Care Set', 'qty' => '25', 'purchase_price' => '750', 'sale_price' => '1200'),

            // 794 => array('code' => '8853976004587', 'product_name' => 'YOKO ACNE MELESMA SOAP 80GM', 'qty' => '601', 'purchase_price' => '150', 'sale_price' => '200'),
            // 795 => array('code' => '8853976004433', 'product_name' => 'Yoko Aloe Vera Whitening Soap 100gm', 'qty' => '429', 'purchase_price' => '100', 'sale_price' => '120'),
            // 796 => array('code' => '8853976000244', 'product_name' => 'Yoko Anti-Aging Cream 30g', 'qty' => '283', 'purchase_price' => '160', 'sale_price' => '280'),
            
            // 798 => array('code' => '8853976005867', 'product_name' => 'Yoko Argan Oil Eye Cream 15g', 'qty' => '6', 'purchase_price' => '210', 'sale_price' => '280'),
            // 799 => array('code' => '8853976005744', 'product_name' => 'Yoko Argan oil Hair Treatment 250ml', 'qty' => '243', 'purchase_price' => '260', 'sale_price' => '300'),
            // 800 => array('code' => '8853976005874', 'product_name' => 'Yoko Argan Oil Leav on Serum 100ml', 'qty' => '72', 'purchase_price' => '250', 'sale_price' => '350'),
            
            // 802 => array('code' => '8853976004310', 'product_name' => 'Yoko Cold Cream 50ml', 'qty' => '108', 'purchase_price' => '160', 'sale_price' => '250'),
            // 803 => array('code' => '8853976004402 8853976000299', 'product_name' => 'Yoko Eye Gel 20gm', 'qty' => '436', 'purchase_price' => '220', 'sale_price' => '250'),
            // 804 => array('code' => '8853976004495', 'product_name' => 'Yoko Facial Fade Cream 50ml', 'qty' => '364', 'purchase_price' => '180', 'sale_price' => '280'),
            // 805 => array('code' => '8853976005928', 'product_name' => 'Yoko Herbal Paste 40g', 'qty' => '28', 'purchase_price' => '80', 'sale_price' => '90'),
            // 806 => array('code' => '8853976004051', 'product_name' => 'Yoko Herbal Soap Box', 'qty' => '404', 'purchase_price' => '110', 'sale_price' => '160'),
            // 807 => array('code' => '8853976004419', 'product_name' => 'Yoko Milk Day/Night Cream 50ml', 'qty' => '328', 'purchase_price' => '160', 'sale_price' => '260'),
            // 808 => array('code' => '8853976000428', 'product_name' => 'Yoko Milk Soap 100ml', 'qty' => '502', 'purchase_price' => '90', 'sale_price' => '110'),
            // 809 => array('code' => '8853976004457', 'product_name' => 'Yoko MIlk Soap 100ml Box', 'qty' => '42', 'purchase_price' => '90', 'sale_price' => '110'),
            // 810 => array('code' => '8853976004631', 'product_name' => 'Yoko Pomegranate Cream 30gm', 'qty' => '120', 'purchase_price' => '180', 'sale_price' => '280'),
            // 811 => array('code' => '8853976004983', 'product_name' => 'Yoko Q10 MIlk Cream 50ml', 'qty' => '28', 'purchase_price' => '200', 'sale_price' => '260'),
            // 812 => array('code' => '8853976005751', 'product_name' => 'Yoko Sakura Cream', 'qty' => '32', 'purchase_price' => '300', 'sale_price' => '580'),
            // 813 => array('code' => '8853976004471', 'product_name' => 'Yoko Under Arm Cream 40ml', 'qty' => '322', 'purchase_price' => '160', 'sale_price' => '260'),
            // 814 => array('code' => '8853976006024', 'product_name' => 'Yoko Vit-C Brightening Face Mask 20g', 'qty' => '188', 'purchase_price' => '300', 'sale_price' => '350'),
            // 815 => array('code' => '1802112050000', 'product_name' => 'Yoko Yogurt Milk Cream 50ml', 'qty' => '15', 'purchase_price' => '160', 'sale_price' => '260'),

            // 817 => array('code' => '8998866102636', 'product_name' => 'Zact Paste 150gm', 'qty' => '361', 'purchase_price' => '120', 'sale_price' => '150'),
            // 818 => array('code' => '8850002009011', 'product_name' => 'Zact Paste Smokers 150gm', 'qty' => '269', 'purchase_price' => '160', 'sale_price' => '190'),
        );
    }

    public static function secondArray()
    {
        return array(
            // 0 => array('code' => '1802112910000', 'product_name' => 'Acmonica Eye Brow', 'qty' => '694', 'purchase_price' => '50', 'sale_price' => '80'),
            // 1 => array('code' => '031655530079 031655671222', 'product_name' => 'ADIDAS STICK', 'qty' => '280', 'purchase_price' => '100', 'sale_price' => '200'),
            // 2 => array('code' => '1802114080000', 'product_name' => 'Almas Mehedi', 'qty' => '1', 'purchase_price' => '35', 'sale_price' => '40'),
            // 3 => array('code' => '6955702401386 6925267301728', 'product_name' => 'Aloe 99% Soothing Gel 250ml', 'qty' => '1', 'purchase_price' => '140', 'sale_price' => '150'),
            // 4 => array('code' => '8859239301349', 'product_name' => 'Aloevera Snail Soap 70g', 'qty' => '102', 'purchase_price' => '100', 'sale_price' => '160'),
            // 5 => array('code' => '8851495003296 8851495002572', 'product_name' => 'Anti Lice Shampoo 20ML', 'qty' => '267', 'purchase_price' => '45', 'sale_price' => '60'),
            // 6 => array('code' => '6001424129906', 'product_name' => 'Aquafresh Brush (C)', 'qty' => '43', 'purchase_price' => '30', 'sale_price' => '33'),
            // 7 => array('code' => '5060120163714', 'product_name' => 'Argan Oil  100ml', 'qty' => '1', 'purchase_price' => '240', 'sale_price' => '250'),
            // 8 => array('code' => '1802111570000', 'product_name' => 'Argan Oil  50ml', 'qty' => '1', 'purchase_price' => '140', 'sale_price' => '150'),
            // 9 => array('code' => '8850722080277', 'product_name' => 'Aron Eyeshadow Platte 12 Colour', 'qty' => '1', 'purchase_price' => '300', 'sale_price' => '400'),
            // 10 => array('code' => '50007820', 'product_name' => 'Astral Cream 50ml', 'qty' => '26', 'purchase_price' => '120', 'sale_price' => '130'),
            // 11 => array('code' => '1802113810000', 'product_name' => 'Avon Hair Color', 'qty' => '1', 'purchase_price' => '450', 'sale_price' => '500'),
            // 12 => array('code' => '8855555555558', 'product_name' => 'Bamboo Toothpick 1001 pak pp', 'qty' => '83', 'purchase_price' => '30', 'sale_price' => '40'),
            // 13 => array('code' => '1802112490000', 'product_name' => 'Bamboo Toothpick Jar', 'qty' => '524', 'purchase_price' => '24', 'sale_price' => '30'),
            // 14 => array('code' => '5012251008105', 'product_name' => 'Beauty Formula Foot Powder 100g', 'qty' => '1', 'purchase_price' => '250', 'sale_price' => '280'),
            // 15 => array('code' => '8858838100056', 'product_name' => 'Bigen Color POwder 6ml', 'qty' => '17', 'purchase_price' => '100', 'sale_price' => '120'),
            // 16 => array('code' => '8809014275020 8809435660108 8809014275013', 'product_name' => 'Bio Care Brush (Korea)', 'qty' => '157', 'purchase_price' => '130', 'sale_price' => '160'),
            // 17 => array('code' => '6001159111351', 'product_name' => 'Bio oil 125ml', 'qty' => '1', 'purchase_price' => '700', 'sale_price' => '720'),
            // 18 => array('code' => '6001159111641', 'product_name' => 'Bio Oil 60ml UK', 'qty' => '1', 'purchase_price' => '430', 'sale_price' => '450'),
            // 19 => array('code' => '5028197295592', 'product_name' => 'Body Shop V-E Aqua Boost Cream', 'qty' => '1', 'purchase_price' => '850', 'sale_price' => '1000'),
            // 20 => array('code' => '1802112070000', 'product_name' => 'Body Shop V-E Day Cream 100ml', 'qty' => '1', 'purchase_price' => '980', 'sale_price' => '1050'),
            // 21 => array('code' => '1802111290000', 'product_name' => 'Bohua Comb 3 Set', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '180'),
            // 22 => array('code' => '025929165708 025929166538', 'product_name' => 'Bondage Body spray 150ml', 'qty' => '1', 'purchase_price' => '200', 'sale_price' => '200'),
            // 23 => array('code' => '5045092618615', 'product_name' => 'Boots Cucumber Cream 100gm UK', 'qty' => '1', 'purchase_price' => '200', 'sale_price' => '220'),
            // 24 => array('code' => '1802115490000', 'product_name' => 'Bumbim Soap', 'qty' => '435', 'purchase_price' => '180', 'sale_price' => '250'),
            // 25 => array('code' => '3574660407853', 'product_name' => 'C & C Facewash 150ml UK', 'qty' => '1', 'purchase_price' => '350', 'sale_price' => '380'),
            // 26 => array('code' => '8851427001239 8851427001079', 'product_name' => 'Carabeau Hair Color', 'qty' => '1', 'purchase_price' => '350', 'sale_price' => '380'),
            // 27 => array('code' => '8851427008351 8851427009624 8851427004148', 'product_name' => 'Carebeau Hair Treatment 500ml', 'qty' => '1', 'purchase_price' => '350', 'sale_price' => '400'),
            // 28 => array('code' => '8852053014044 8852053014013', 'product_name' => 'Carring Hair Gel Spray 220ml', 'qty' => '35', 'purchase_price' => '180', 'sale_price' => '200'),
            // 29 => array('code' => '8850722038988', 'product_name' => 'CC Powder & Blush', 'qty' => '1', 'purchase_price' => '120', 'sale_price' => '150'),
            // 30 => array('code' => '1802111540000', 'product_name' => 'Chic De Mirror No: 430-5 small', 'qty' => '1', 'purchase_price' => '50', 'sale_price' => '60'),
            // 31 => array('code' => '1802114440000', 'product_name' => 'Chic De Mirror No: 430-6 Medium', 'qty' => '1', 'purchase_price' => '70', 'sale_price' => '80'),
            // 32 => array('code' => '1802114450000', 'product_name' => 'Chic De Mirror No: 430-8 Big', 'qty' => '1', 'purchase_price' => '80', 'sale_price' => '90'),
            // 33 => array('code' => '5011417546284', 'product_name' => 'Clearasil Facewash Tube uk', 'qty' => '1', 'purchase_price' => '370', 'sale_price' => '400'),
            // 34 => array('code' => '5011417563694 5011417546185 5011417563717', 'product_name' => 'Clearasil FW 150ml Pump UK', 'qty' => '1', 'purchase_price' => '380', 'sale_price' => '400'),
            // 35 => array('code' => '6001067003366', 'product_name' => 'Colgate Advance Whitening Past 100g', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '180'),
            // 36 => array('code' => '9556031089626', 'product_name' => 'Colgate Toothbrush Twister', 'qty' => '356', 'purchase_price' => '65', 'sale_price' => '70'),
            // 37 => array('code' => '1802111750000', 'product_name' => 'COLLAGEN Cream+Soap Set', 'qty' => '1', 'purchase_price' => '180', 'sale_price' => '220'),
            // 38 => array('code' => '1802112310000', 'product_name' => 'Collagen Vitamin E Soap 85g', 'qty' => '1', 'purchase_price' => '60', 'sale_price' => '70'),
            // 39 => array('code' => '25929121506', 'product_name' => 'Colour Me Parfume 50ml', 'qty' => '1', 'purchase_price' => '390', 'sale_price' => '400'),
            // 40 => array('code' => '025929127256 025929122831 025929122732', 'product_name' => 'Colour Me Women Body Spray 150ml', 'qty' => '1', 'purchase_price' => '180', 'sale_price' => '190'),
            // 41 => array('code' => '1802111240000', 'product_name' => 'Comb', 'qty' => '1', 'purchase_price' => '0', 'sale_price' => '0'),
            // 42 => array('code' => '1802111270000', 'product_name' => 'Comb Prc', 'qty' => '1', 'purchase_price' => '13', 'sale_price' => '15'),
            // 43 => array('code' => '8809341837656', 'product_name' => 'Cucumber Body Gell 250ml', 'qty' => '1', 'purchase_price' => '140', 'sale_price' => '150'),
            // 44 => array('code' => '9557348002506', 'product_name' => 'Cuticura Talcum Powder 250gm', 'qty' => '1', 'purchase_price' => '170', 'sale_price' => '200'),
            // 45 => array('code' => '9557348004005', 'product_name' => 'Cuticura Talcum Powder 400gm', 'qty' => '1', 'purchase_price' => '270', 'sale_price' => '300'),
            // 46 => array('code' => '79400251602', 'product_name' => 'Degree Stick 45g', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '220'),
            // 47 => array('code' => '79400116765', 'product_name' => 'Degree Stick 48g', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '250'),
            // 48 => array('code' => '8717163332122 5000228023411', 'product_name' => 'Dove Deo Spray 150ml', 'qty' => '1', 'purchase_price' => '150', 'sale_price' => '160'),
            // 49 => array('code' => '8712561593335 8712561630764 8712561641173 8712561609364', 'product_name' => 'Dove Shower Gel 250ml', 'qty' => '1', 'purchase_price' => '140', 'sale_price' => '160'),
            // 50 => array('code' => '8712561625760 8712561615242 8712561645690 8712561727105 8712561607988', 'product_name' => 'Dove Shower Gel 500ml', 'qty' => '1', 'purchase_price' => '240', 'sale_price' => '260'),
            // 51 => array('code' => '8718158005052', 'product_name' => 'Elegance Foot Scrub', 'qty' => '1', 'purchase_price' => '80', 'sale_price' => '120'),
            // 52 => array('code' => '8888202060327', 'product_name' => 'Enchantaur Body Spray 200ml', 'qty' => '1', 'purchase_price' => '138', 'sale_price' => '140'),
            // 53 => array('code' => '8888202055927', 'product_name' => 'Eversoft Cleansing Serum 150ml', 'qty' => '1', 'purchase_price' => '250', 'sale_price' => '260'),
            // 54 => array('code' => '8888202004406 8888202033253', 'product_name' => 'Eversoft Facewash 195ml', 'qty' => '1', 'purchase_price' => '270', 'sale_price' => '290'),
            // 55 => array('code' => '8851322000474', 'product_name' => 'Fider Brush', 'qty' => '1', 'purchase_price' => '120', 'sale_price' => '150'),
            // 56 => array('code' => '9556171100649 9556171100656', 'product_name' => 'Flow Me Body Wash 1000ml', 'qty' => '1', 'purchase_price' => '580', 'sale_price' => '600'),
            // 57 => array('code' => '1802113720000', 'product_name' => 'Foot Scrub', 'qty' => '1', 'purchase_price' => '25', 'sale_price' => '40'),
            // 58 => array('code' => '8854302810476 8854302810421 8854302810414 8854302810438', 'product_name' => 'Giffine Body Salt', 'qty' => '1', 'purchase_price' => '80', 'sale_price' => '150'),
            // 59 => array('code' => '9557887175105', 'product_name' => 'Goat Milk Massage Body Wash 1000ml', 'qty' => '1', 'purchase_price' => '480', 'sale_price' => '600'),
            // 60 => array('code' => '1802111260000', 'product_name' => 'Hair Academy Comb', 'qty' => '1', 'purchase_price' => '200', 'sale_price' => '250'),
            // 61 => array('code' => '381519071935 381519071942', 'product_name' => 'Herbal Essences Ehampoo Set', 'qty' => '1', 'purchase_price' => '950', 'sale_price' => '1000'),
            // 62 => array('code' => '6291105540087 6291105540049 6291105540148', 'product_name' => 'Hot Ice Body Spray 150ml', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '165'),
            // 63 => array('code' => '6291106050271', 'product_name' => 'Huda Beauty Face Powder 5027', 'qty' => '1', 'purchase_price' => '220', 'sale_price' => '220'),
            // 64 => array('code' => '861000166863', 'product_name' => 'Huda Beauty The Red Lip Stick 41', 'qty' => '1', 'purchase_price' => '220', 'sale_price' => '300'),
            // 65 => array('code' => '8589230017535', 'product_name' => 'Huda Face Powder 2in1 Beauty 53', 'qty' => '1', 'purchase_price' => '220', 'sale_price' => '220'),
            // 66 => array('code' => '8852525369030', 'product_name' => 'Isme Paste 30G', 'qty' => '1', 'purchase_price' => '40', 'sale_price' => '80'),
            // 67 => array('code' => '8850007020271', 'product_name' => 'Johnson Baby Soap 150gm', 'qty' => '1', 'purchase_price' => '62', 'sale_price' => '65'),
            // 68 => array('code' => '041065516020 041065516068 041065516051 041065516013', 'product_name' => 'Jordana Face-powder', 'qty' => '1', 'purchase_price' => '270', 'sale_price' => '290'),
            // 69 => array('code' => '6955549390201 6955549370135 6955549380073 6927704610646 6955549307889', 'product_name' => 'KemeI Trimmer', 'qty' => '20', 'purchase_price' => '500', 'sale_price' => '450'),
            // 70 => array('code' => '6903072295135', 'product_name' => 'Kiss Beauty Gel Eyeliner', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '180'),
            // 71 => array('code' => '6903072279326', 'product_name' => 'Kiss Beauty Liquid Lipsticks', 'qty' => '1', 'purchase_price' => '180', 'sale_price' => '180'),
            // 72 => array('code' => '6903072272723', 'product_name' => 'Kiss Beauty Matte Lipsticks', 'qty' => '1', 'purchase_price' => '50', 'sale_price' => '80'),
            // 73 => array('code' => '8850002010314', 'product_name' => 'Kodomo Baby Bath 100ml', 'qty' => '1', 'purchase_price' => '90', 'sale_price' => '115'),
            // 74 => array('code' => '8850002010338', 'product_name' => 'Kodomo Baby Bath 200ml', 'qty' => '1', 'purchase_price' => '170', 'sale_price' => '215'),
            // 75 => array('code' => '8850002010352', 'product_name' => 'Kodomo Baby Bath 400ml', 'qty' => '1', 'purchase_price' => '350', 'sale_price' => '350'),
            // 76 => array('code' => '8998866102605', 'product_name' => 'Kodomo Brush+Paste 2 in 1', 'qty' => '1', 'purchase_price' => '80', 'sale_price' => '120'),
            // 77 => array('code' => '8998866105354', 'product_name' => 'Kodomo Brush+Paste+Kids 3 in 1', 'qty' => '1', 'purchase_price' => '100', 'sale_price' => '150'),
            // 78 => array('code' => '8850002012844 8850002906457 8850002012547 8850002011878', 'product_name' => 'Kodomo Shampoo 100ml', 'qty' => '1', 'purchase_price' => '80', 'sale_price' => '110'),
            // 79 => array('code' => '8998866105279 8998866106030', 'product_name' => 'Kodomo Shampoo 200ml (IN)', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '220'),
            // 80 => array('code' => '8850002011892 8850002012554', 'product_name' => 'Kodomo Shampoo 400ml', 'qty' => '1', 'purchase_price' => '340', 'sale_price' => '350'),
            // 81 => array('code' => '8850002016934', 'product_name' => 'Kodomo Top-to Wash 100ml', 'qty' => '1', 'purchase_price' => '95', 'sale_price' => '115'),
            // 82 => array('code' => '8850002016958', 'product_name' => 'Kodomo Top-to Wash 200ml', 'qty' => '1', 'purchase_price' => '180', 'sale_price' => '215'),
            // 83 => array('code' => '8850002019690', 'product_name' => 'Kodomo Top-to Wash 400ml', 'qty' => '1', 'purchase_price' => '350', 'sale_price' => '350'),
            // 84 => array('code' => '680065018013', 'product_name' => 'Kylie Lip liner DGM B-15', 'qty' => '1', 'purchase_price' => '600', 'sale_price' => '600'),
            // 85 => array('code' => '88600000003003', 'product_name' => 'Kylie Lip Stick Limited Edition', 'qty' => '1', 'purchase_price' => '150', 'sale_price' => '160'),
            // 86 => array('code' => '8901030595783', 'product_name' => 'Lakme Kajal', 'qty' => '1', 'purchase_price' => '16', 'sale_price' => '17'),
            // 87 => array('code' => '8854245501073', 'product_name' => 'Lappa Nail Remover 50ml', 'qty' => '1', 'purchase_price' => '500', 'sale_price' => '600'),
            // 88 => array('code' => '8852086002810', 'product_name' => 'Lavender Body Wash 150ml', 'qty' => '1', 'purchase_price' => '120', 'sale_price' => '180'),
            // 89 => array('code' => '6291106030938 8860000001394', 'product_name' => 'Lip Contour Set Huda Beauty', 'qty' => '1', 'purchase_price' => '180', 'sale_price' => '180'),
            // 90 => array('code' => '4037900225666 4037900225642 4037900225673 4037900225659', 'product_name' => 'Loreal Age Perfect Nourishing Oil Cream 50ml', 'qty' => '1', 'purchase_price' => '850', 'sale_price' => '900'),
            // 91 => array('code' => '3600522086970', 'product_name' => 'Loreal Revitalift Cream 50 ml uk', 'qty' => '1', 'purchase_price' => '700', 'sale_price' => '750'),
            // 92 => array('code' => '8992304017827 3600522279105', 'product_name' => 'Loreal Revitalift Day/Night Cream 50ml', 'qty' => '1', 'purchase_price' => '850', 'sale_price' => '880'),
            // 93 => array('code' => '3600521719541 3600521719596 3600521719640', 'product_name' => 'Loreal Triple Active Day/Night Cream 50ml UK', 'qty' => '1', 'purchase_price' => '650', 'sale_price' => '780'),
            // 94 => array('code' => '1802112030000', 'product_name' => 'Loreal UV Cream 50ml', 'qty' => '1', 'purchase_price' => '600', 'sale_price' => '700'),
            // 95 => array('code' => '3600523183500 3600523183494', 'product_name' => 'Loreal Wrinkle Cream 35+,45+,55+', 'qty' => '1', 'purchase_price' => '650', 'sale_price' => '750'),
            // 96 => array('code' => '8850722129631 8850722129648', 'product_name' => 'Magic Lip Care', 'qty' => '1', 'purchase_price' => '40', 'sale_price' => '50'),
            // 97 => array('code' => '1802113470000', 'product_name' => 'Makeup Brush set', 'qty' => '1', 'purchase_price' => '200', 'sale_price' => '250'),
            // 98 => array('code' => '8858518001604', 'product_name' => 'Mei Linda Dip Eyeliner 5ml', 'qty' => '1', 'purchase_price' => '90', 'sale_price' => '120'),
            // 99 => array('code' => '1802114470000', 'product_name' => 'Mirror 2 Way', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '180'),
            // 100 => array('code' => '8859178707608 8859178707493', 'product_name' => 'Mistin White Spa Lotion 200ml', 'qty' => '1', 'purchase_price' => '150', 'sale_price' => '200'),
            // 101 => array('code' => '1802114550000', 'product_name' => 'Nail Brush', 'qty' => '1', 'purchase_price' => '40', 'sale_price' => '60'),
            // 102 => array('code' => '5010724526392 5010724529669', 'product_name' => 'Nair Body Wax Stips Body & leg', 'qty' => '45', 'purchase_price' => '110', 'sale_price' => '180'),
            // 103 => array('code' => '8904256001441 4005900207999 8904256001410', 'product_name' => 'Nivea  Deo Box 100ml', 'qty' => '1', 'purchase_price' => '240', 'sale_price' => '250'),
            // 104 => array('code' => '8999777801304', 'product_name' => 'Nivea Cream Blue 100ml Jhar', 'qty' => '1', 'purchase_price' => '140', 'sale_price' => '160'),
            // 105 => array('code' => '42240211 42217565', 'product_name' => 'Nivea Deo Stick 40ml', 'qty' => '1', 'purchase_price' => '180', 'sale_price' => '200'),
            // 106 => array('code' => '4005808817009 4005808222698', 'product_name' => 'Nivea Men Shaving Foam 200ml', 'qty' => '1', 'purchase_price' => '260', 'sale_price' => '280'),
            // 107 => array('code' => '4005900009715', 'product_name' => 'Nivea Soft Cream 100ml', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '170'),
            // 108 => array('code' => '4005808890507', 'product_name' => 'Nivea Soft Cream 200ml', 'qty' => '1', 'purchase_price' => '268', 'sale_price' => '270'),
            // 109 => array('code' => '1802111790000', 'product_name' => 'Nivea Soft Cream 50ml', 'qty' => '1', 'purchase_price' => '100', 'sale_price' => '110'),
            // 110 => array('code' => '5410076780341', 'product_name' => 'Olay Anti Wrinkle Day/Night cream 50ml Uk', 'qty' => '1', 'purchase_price' => '600', 'sale_price' => '700'),
            // 111 => array('code' => '5000174789379 5410076780341', 'product_name' => 'Olay Complete Day/Night Cream 50gm UK', 'qty' => '1', 'purchase_price' => '600', 'sale_price' => '650'),
            // 112 => array('code' => '5410076481682 5011321252639', 'product_name' => 'Olay F/W 150ml UK', 'qty' => '1', 'purchase_price' => '300', 'sale_price' => '350'),
            // 113 => array('code' => '4084500806450 4015400834663', 'product_name' => 'Olay Gift Set', 'qty' => '1', 'purchase_price' => '800', 'sale_price' => '1400'),
            // 114 => array('code' => '5011321976320', 'product_name' => 'Olay Total Effects7 Cream 30gm UK', 'qty' => '1', 'purchase_price' => '550', 'sale_price' => '680'),
            // 115 => array('code' => '4902430733830', 'product_name' => 'Olay White Radiance Cream 50G', 'qty' => '1', 'purchase_price' => '850', 'sale_price' => '1000'),
            // 116 => array('code' => '6935193250014 6926292527794', 'product_name' => 'Omuda Nail Cutter', 'qty' => '1', 'purchase_price' => '70', 'sale_price' => '80'),
            // 117 => array('code' => '6281001816423 6281001816638 6281001816485 6281001816461', 'product_name' => 'Palmolive Soap 175gm', 'qty' => '1', 'purchase_price' => '75', 'sale_price' => '78'),
            // 118 => array('code' => '8858927505564', 'product_name' => 'Pannamas Lighting Body Wash', 'qty' => '1', 'purchase_price' => '400', 'sale_price' => '500'),
            // 119 => array('code' => '7980036045479', 'product_name' => 'Pannamas Rice/Goat MIlk Lotion 500ml', 'qty' => '1', 'purchase_price' => '400', 'sale_price' => '600'),
            // 120 => array('code' => '4902430624541', 'product_name' => 'Pantene Conditioner 3minute 70ml', 'qty' => '1', 'purchase_price' => '80', 'sale_price' => '80'),
            // 121 => array('code' => '1802111120000', 'product_name' => 'Patisserie Body Butter 175ml', 'qty' => '1', 'purchase_price' => '250', 'sale_price' => '300'),
            // 122 => array('code' => '5060082258329', 'product_name' => 'Patisserie Body wash 175ml', 'qty' => '1', 'purchase_price' => '250', 'sale_price' => '300'),
            // 123 => array('code' => '5060082258237', 'product_name' => 'Patisserie Shower Set 2x175', 'qty' => '1', 'purchase_price' => '350', 'sale_price' => '500'),
            // 124 => array('code' => '1802114320000', 'product_name' => 'Peiyen Girl Fashion Lipstick', 'qty' => '1', 'purchase_price' => '80', 'sale_price' => '120'),
            // 125 => array('code' => '8857119430578 8857079430724 8858646500659 8857119430561', 'product_name' => 'Polla Gold/Platinam/Masta cream 50ml', 'qty' => '1', 'purchase_price' => '180', 'sale_price' => '300'),
            // 126 => array('code' => '8901030323782', 'product_name' => 'ponds Powder 400ml', 'qty' => '1', 'purchase_price' => '270', 'sale_price' => '290'),
            // 127 => array('code' => '8853318000833', 'product_name' => 'Pop Cream 4gm', 'qty' => '1', 'purchase_price' => '276', 'sale_price' => '300'),
            // 128 => array('code' => '850241000716', 'product_name' => 'Power Stick 57g Men', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '220'),
            // 129 => array('code' => '850241000419', 'product_name' => 'Power Stick Women 57gm', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '220'),
            // 130 => array('code' => '8851447430101', 'product_name' => 'Poy Sian Balm Oil', 'qty' => '1', 'purchase_price' => '40', 'sale_price' => '60'),
            // 131 => array('code' => '8851447430026', 'product_name' => 'Poy Sian Balm Oil Jar', 'qty' => '1', 'purchase_price' => '50', 'sale_price' => '80'),
            // 132 => array('code' => '8851447430064', 'product_name' => 'Poy Sian Roll on', 'qty' => '1', 'purchase_price' => '90', 'sale_price' => '120'),
            // 133 => array('code' => '1802111250000', 'product_name' => 'Pretty comb Uk', 'qty' => '1', 'purchase_price' => '140', 'sale_price' => '200'),
            // 134 => array('code' => '1802114510000', 'product_name' => 'Pucan Nail Cutter', 'qty' => '1', 'purchase_price' => '60', 'sale_price' => '70'),
            // 135 => array('code' => '1802113460000', 'product_name' => 'Puff', 'qty' => '1', 'purchase_price' => '250', 'sale_price' => '300'),
            // 136 => array('code' => '8850407015723 8850407015730', 'product_name' => 'Punk Hair Gel 250ml', 'qty' => '1', 'purchase_price' => '200', 'sale_price' => '250'),
            // 137 => array('code' => '4902430450362 4902430450355 4902430450379', 'product_name' => 'Rejoice Shampoo 340ml', 'qty' => '1', 'purchase_price' => '280', 'sale_price' => '340'),
            // 138 => array('code' => '1802112950000', 'product_name' => 'Revulation Flawless 2 Eye Shawdow', 'qty' => '1', 'purchase_price' => '700', 'sale_price' => '750'),
            // 139 => array('code' => '1802112940000', 'product_name' => 'Revulation Salvation Plate', 'qty' => '1', 'purchase_price' => '400', 'sale_price' => '450'),
            // 140 => array('code' => '17000068374', 'product_name' => 'Right Guard 85g Gel', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '250'),
            // 141 => array('code' => '17000251738', 'product_name' => 'Right Guart Stick 51g', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '220'),
            // 142 => array('code' => '017000068084 017000068121', 'product_name' => 'Right Guart Stick 73g', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '250'),
            // 143 => array('code' => '6939625400633', 'product_name' => 'S.S.S Nail Cutter', 'qty' => '1', 'purchase_price' => '220', 'sale_price' => '250'),
            // 144 => array('code' => '6921199168897 2608262900052', 'product_name' => 'Salon 5pcs Comb', 'qty' => '1', 'purchase_price' => '280', 'sale_price' => '340'),
            // 145 => array('code' => '1802113780000', 'product_name' => 'Salon Kit', 'qty' => '1', 'purchase_price' => '40', 'sale_price' => '60'),
            // 146 => array('code' => '8851369112017', 'product_name' => 'Sasimi 99% Eyeliner Pancil', 'qty' => '221', 'purchase_price' => '90', 'sale_price' => '120'),
            // 147 => array('code' => '037000124306 037000123408', 'product_name' => 'Secret Deo Stick 73g', 'qty' => '1', 'purchase_price' => '180', 'sale_price' => '250'),
            // 148 => array('code' => '8901088062497 8901088062480', 'product_name' => 'Set Wet Hair Gel 100ml', 'qty' => '1', 'purchase_price' => '60', 'sale_price' => '60'),
            // 149 => array('code' => '1802112590000', 'product_name' => 'Show Polish (c)', 'qty' => '76', 'purchase_price' => '50', 'sale_price' => '70'),
            // 150 => array('code' => '8712561467636', 'product_name' => 'Simple Facewash 200ml pump', 'qty' => '1', 'purchase_price' => '400', 'sale_price' => '450'),
            // 151 => array('code' => '8850233120080', 'product_name' => 'Spring Song Skin Lotion 115gm', 'qty' => '1', 'purchase_price' => '75', 'sale_price' => '80'),
            // 152 => array('code' => '79400403582', 'product_name' => 'Suave Sticks 39g', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '220'),
            // 153 => array('code' => '1802111470000', 'product_name' => 'Sunn', 'qty' => '1', 'purchase_price' => '25', 'sale_price' => '25'),
            // 154 => array('code' => '883484310052 883484717936 883484717943', 'product_name' => 'Sure Stick 73g/76g', 'qty' => '1', 'purchase_price' => '160', 'sale_price' => '250'),
            // 155 => array('code' => '1802113710000', 'product_name' => 'Thai Baby Feeder Bottle 8Oz', 'qty' => '1', 'purchase_price' => '80', 'sale_price' => '120'),
            // 156 => array('code' => '1802111310000', 'product_name' => 'Thai Hair Brush', 'qty' => '1', 'purchase_price' => '116', 'sale_price' => '250'),
            // 157 => array('code' => '1802113770000', 'product_name' => 'Thai Hair Clip', 'qty' => '1', 'purchase_price' => '200', 'sale_price' => '200'),
            // 158 => array('code' => '1802113700000', 'product_name' => 'Thai Nipple', 'qty' => '1', 'purchase_price' => '20', 'sale_price' => '25'),
            // 159 => array('code' => '1802113680000', 'product_name' => 'Thi Baby Feeder Bottle 4Oz', 'qty' => '1', 'purchase_price' => '60', 'sale_price' => '90'),
            // 160 => array('code' => '8858842053454', 'product_name' => 'Tomato Sooting Gel', 'qty' => '47', 'purchase_price' => '320', 'sale_price' => '600'),
            // 161 => array('code' => '1802111280000', 'product_name' => 'Tresemme Brush Set', 'qty' => '1', 'purchase_price' => '240', 'sale_price' => '400'),
            // 162 => array('code' => '8850348530019', 'product_name' => 'TWIN LOTUS SHAMPOO 300ML', 'qty' => '1', 'purchase_price' => '500', 'sale_price' => '650'),
            // 163 => array('code' => '667534419601 667536447671 667528031123 667537008642 667535677185 667535677185', 'product_name' => 'Victoria Secret Body Mist 100ml', 'qty' => '41', 'purchase_price' => '480', 'sale_price' => '600'),
            // 164 => array('code' => '8858831000209 8858831000018', 'product_name' => 'Vipada Goatmilk Soap 60ml', 'qty' => '1', 'purchase_price' => '120', 'sale_price' => '200'),
            // 165 => array('code' => '1802112450000', 'product_name' => 'Watson Dental Floss', 'qty' => '1', 'purchase_price' => '70', 'sale_price' => '80'),
            // 166 => array('code' => '5060120166746 5060120166685 5012251010634', 'product_name' => 'XBC Facial Wips', 'qty' => '1', 'purchase_price' => '200', 'sale_price' => '220'),
            // 167 => array('code' => '8855322000373', 'product_name' => 'YC Acne Cream 4g', 'qty' => '1', 'purchase_price' => '35', 'sale_price' => '50'),
            // 168 => array('code' => '8857101158343', 'product_name' => 'Yc Aloevera & Herbal Cream', 'qty' => '1', 'purchase_price' => '40', 'sale_price' => '80'),
            // 169 => array('code' => '1802111970000', 'product_name' => 'YC Stretch Mark Cream 120ml', 'qty' => '1', 'purchase_price' => '180', 'sale_price' => '250'),
            // 170 => array('code' => '8857101110877', 'product_name' => 'YC Whitenning Facial Scrub 175ml', 'qty' => '1', 'purchase_price' => '115', 'sale_price' => '130'),
            // 171 => array('code' => '8853976004655', 'product_name' => 'Yoko Acne Cream', 'qty' => '1', 'purchase_price' => '130', 'sale_price' => '180'),
            // 172 => array('code' => '8853976005812 8853976004938 8853976004242', 'product_name' => 'Yoko Body Butter Cream 200ml', 'qty' => '1', 'purchase_price' => '250', 'sale_price' => '300'),
            // 173 => array('code' => '1802111300000', 'product_name' => 'Yuabo Comb', 'qty' => '1', 'purchase_price' => '60', 'sale_price' => '70'),
        );
    }
}
