<?php

class SendingReceivingController extends \BaseController {

    public $timestamp;

        public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->timestamp = date('Y-m-d H:i:s');
        }

    
    public function index()
    {
            $items = DB::table('godownitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'godownitems.item_id')
                        ->select('godownitems.godown_item_id','godownitems.item_id','iteminfos.item_name')
                        ->where('godownitems.status', '=', 1)
                        ->groupBy('godownitems.item_id')
                        ->get();
                return View::make('sendingreceiving.sendingForm',compact('items'));

    }
        public function autoItemSuggest(){
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

        public function itemAddForSending()
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
                $item_info['godown_item_id']=$value->godown_item_id;
                $godown_item_id=$item_info['godown_item_id'];
                $item_info['item_id']=$value->item_id;
                $item_info['item_name']=$value->item_name;
                $item_info['price_id']=$value->price_id;
                $item_info['purchase_price']=$value->purchase_price;
                $item_info['sale_price']=$value->sale_price;
                $item_info['available_quantity']=$value->available_quantity;
                
                if($value->available_quantity>0)
                $item_info['now_send']=1;
                else
                continue;
                
                $item_info['total_price']=$value->sale_price*$item_info['now_send'];

                    Session::put("sending.$godown_item_id", $item_info);

          }


        return Redirect::to('sending');
    }

    public function editDeleteItemSending()
    {
        $vdata=Input::all();
        $godown_item_id=$vdata['godown_item_id'];
        
        if($vdata['edit_delete']=='edit'){
            $data=DB::table('godownitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'godownitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'godownitems.price_id')
                        ->select('godownitems.godown_item_id','godownitems.item_id','godownitems.price_id','godownitems.available_quantity', 'iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price')
                        ->where('godownitems.status', '=', 1)
                        ->where('godownitems.godown_item_id', '=', $godown_item_id)
                        ->first();
            $item_info=array();
            $item_info['godown_item_id']=$data->godown_item_id;
            $item_info['item_id']=$data->item_id;
            $item_info['item_name']=$data->item_name;
            $item_info['price_id']=$data->price_id;
            $item_info['purchase_price']=$data->purchase_price;
            $item_info['sale_price']=$data->sale_price;
            $item_info['available_quantity']=$data->available_quantity;
            if($data->available_quantity>0){
                if($vdata['now_send']>$data->available_quantity){
                $item_info['now_send']=$data->available_quantity;
                Session::flash('quantityError', 'You can not send quantity more than available quantity');
                }
                elseif($vdata['now_send']==0){
                    return Redirect::to('sending')->with('errorMsg', "You can not send zero(0) quantity.");
                }
                else{
                   $item_info['now_send']=$vdata['now_send'];
                }
            }
            else{
             return Redirect::to('sending')->with('errorMsg', "This product are not available in the godown");
            }
            $item_info['total_price']=$data->sale_price*$item_info['now_send'];

                Session::put("sending.$godown_item_id", $item_info);

         }
        else{
            Session::forget("sending.$godown_item_id");
           }

        return Redirect::to('sending');
    }

        
    public function saveSending()
    {
        $flag=0;
         foreach(Session::get("sending") as $eachItem):
            $isAvailableQuantity = DB::table('godownitems')
                        ->where('godownitems.godown_item_id', '=', $eachItem['godown_item_id'])
                        ->where('godownitems.available_quantity', '<', $eachItem['now_send'])
                        ->where('godownitems.status', '=',1)
                        ->first();
            if($isAvailableQuantity)
                $flag=1;
         endforeach;   
         if($flag==1)
             return Redirect::back()->with('errorMsg', 'There are item quantity contradiction occured.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Please remove items and again select items.');
         

        DB::beginTransaction();

        try {
            $success=0;
            $fail=0;
            $sendingDateTime=Input::get('sendingDateTime')." ".date('h:i:s');
            foreach(Session::get("sending") as $eachItem){
                    $sendingData=array();
                    $sendingData['item_id']=$eachItem['item_id'];
                    $sendingData['price_id']=$eachItem['price_id'];
                    $sendingData['quantity']=$eachItem['now_send'];
                    $sendingData['sending_date']=$sendingDateTime;
                    $sendingData['sending_by']=Session::get('emp_id');
                    $sendingData['created_at']=$this->timestamp;
                    $godown_item_id=$eachItem['godown_item_id'];
                    //echo'<pre>';print_r($sendingData);exit;
                    $insertSendItem= DB::table('receivingitems')->insert($sendingData);
                       if($insertSendItem){
                       $decreaseItem=DB::table('godownitems')
                                ->where('godownitems.godown_item_id', '=', $godown_item_id)
                                ->decrement('available_quantity', $sendingData['quantity'],
                                        array('sending_by' => $sendingData['sending_by'], 'sending_at' => $sendingDateTime));


                       Session::forget("sending.$godown_item_id");
                       $success++;
                       }
                       else{
                          $fail++;
                       }
                 }
                  DB::table('godownitems')
                    ->where('available_quantity', '<=', 0)
                    ->update(array('status' => 0));
              DB::commit();
             return Redirect::to('sending')->with('success', $success)->with('fail', $fail);

            } catch (Exception $e) {
                    //return  DB::getQueryLog();
                         DB::rollback();
            return Redirect::to('sending')->with('errorMsg', 'Something is wrong in save sending.');
        }
    }
    

public function receive()
    {
        $items = DB::table('receivingitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'receivingitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'receivingitems.price_id')
                        ->select('receivingitems.branch_id','receivingitems.receiving_item_id','receivingitems.item_id','receivingitems.quantity','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id')
                        ->where('receivingitems.status', '=', 1)
                        ->get();
                return View::make('sendingreceiving.receivingForm',compact('items'));
    }


    public function saveReceiveItem()
    {
        
            DB::beginTransaction();

        try {
                    
                
        $vdata=Input::all();
        $branch_id = Session::get('branch_id');
        if(empty($vdata['receiving_item_id'])){
            return Redirect::back()->with('errorMsg', 'Please Select  Items');
        }
        $receiving_ids = $vdata['receiving_item_id'];
        /* echo'<pre>';
        print_r($vdata);
        exit; */
        if($vdata['accept_cancel']=="Accept"){

         $flag=0;
         foreach($receiving_ids as $receiving_id):
            $isOperateData = DB::table('receivingitems')
                        ->where('receivingitems.receiving_item_id', '=', $receiving_id)
                        ->where('receivingitems.status', '!=',1)
                        ->first();
            if($isOperateData)
                $flag=1;
         endforeach;   
         if($flag==1)
             return Redirect::back()->with('errorMsg', 'There are item contradiction occured.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Please select again.');
         foreach($receiving_ids as $receiving_id):
            $items = DB::table('receivingitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'receivingitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'receivingitems.price_id')
                        ->select('receivingitems.receiving_item_id','receivingitems.item_id','receivingitems.quantity','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id')
                        ->where('receivingitems.receiving_item_id', '=', $receiving_id)
                        ->first();
                
                

                    ### ---   For Receive table update --- ###
                        $result=DB::table('receivingitems')
                            ->where('receiving_item_id', $items->receiving_item_id)
                            ->update(array(
                                'status' => 0,
                                'receive_cancel_date'=>$this->timestamp,
                                'receive_cancel_by'=>Session::get('emp_id')
                                ));
                        
                    ### ---  Ending For Receive table update --- ###


                     ###--- Stock item increasing with super key consider ---###
                      if($result){
                       // print_r($items);
                            $stockItemInfo = DB::table('stockitems')
                                                    ->where('branch_id', '=', $branch_id)
                                                    ->where('item_id', '=', $items->item_id)
                                                    ->where('price_id', '=', $items->price_id)
                                                    ->first();
                           // return $stockItemInfo;
                            $mergeQnt=0;
                            if(!$stockItemInfo){

                                //24-4-16 updated
                                /*
                                ||    at first check with available quantity price id with current price id
                                || if available quantity's sale price same as newprice id so no change required.

                                || if available quantity's sale price are different with current price then check 
                                || if purchase price are same then  the price id will replace by current price_id

                                || else
                                || check from priceinfos if those purchase_price and now sales price related
                                || any id found. then replace priceId of those available item id into 
                                || founded priceId

                                */
                                    $getAvailable=DB::table('stockitems')
                                                    ->join('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                                                    ->where('stockitems.item_id', '=', $items->item_id)
                                                    ->where('stockitems.branch_id', '=', $branch_id)
                                                    ->where('stockitems.available_quantity', '>',0)
                                                    ->get();
                                                    
                                    $mergeQnt=0;
                                    foreach($getAvailable as $val){
                                        if($val->sale_price!=$items->sale_price){
                                            if($val->purchase_price==$items->purchase_price){
                                               $mergeQnt+=$val->available_quantity;
                                            DB::table('stockitems')->where('stock_item_id',$val->stock_item_id)->delete();
                                            }else{
                                               $found= DB::table('priceinfos')
                                                    ->where('item_id', '=', $items->item_id)
                                                    ->where('purchase_price', '=', $val->purchase_price)
                                                    ->where('sale_price', '=', $items->sale_price)
                                                    ->first();
                                                    /*echo $val->price_id;
                                                    print_r($found);
                                                    return;*/
                                        if($found){

                                            $stockId=DB::table('stockitems')
                                            ->where('price_id', '=', $found->price_id)
                                            ->first();
                                            
                                                if(isset($stockId->stock_item_id)){

                                                    $mergeQnty=$val->available_quantity+$stockId->available_quantity;
                                                 DB::table('stockitems')
                                                ->where('stock_item_id', '=', $stockId->stock_item_id)
                                                ->update(['available_quantity'=>$mergeQnty,
                                                    'updated_at'=>date('Y-m-d h:i:s'),
                                                    'status'=>1
                                                    ]);   
                                            DB::table('stockitems')->where('stock_item_id',$val->stock_item_id)->delete();
                                                } else{

                                                DB::table('stockitems')
                                                    ->where('item_id', '=', $items->item_id)
                                                    ->where('price_id', '=', $val->price_id)
                                                    ->update(['price_id'=>$found->price_id,'updated_at'=>date('Y-m-d h:i:s')]);
                                                    }
                                                }else{
                                                    $price_info = array(
                                                    'item_id'           =>  $items->item_id,
                                                    'purchase_price'    =>  $val->purchase_price,
                                                    'sale_price'        =>  $items->sale_price,
                                                    'status'            =>  0,
                                                    'created_by'        =>  Session::get('emp_id'),
                                                    'created_at'        =>  $this->timestamp
                                            ); 

                            $newPriceId = DB::table('priceinfos')->insertGetId($price_info);
                            DB::table('stockitems')->where('stock_item_id',$val->stock_item_id)
                                                   ->update(['price_id'=>$newPriceId,'updated_at'=>date('Y-m-d h:i:s')]);
                                                }
                                            }
                                        }
                                    }


                                //end of updated
                                $insertData=array();
                                $insertData['branch_id']=$branch_id;
                                $insertData['item_id']=$items->item_id;
                                $insertData['price_id']=$items->price_id;
                                $insertData['available_quantity']=$items->quantity+$mergeQnt;
                                $insertData['created_by']=Session::get('emp_id');
                                $insertData['created_at']=$this->timestamp;

                                $insert=DB::table('stockitems')->insert($insertData);
                            }
                            else{
                                
                                 //24-4-16 updated
                                /*
                                ||    at first check with available quantity price id with current price id
                                || if available quantity's sale price same as newprice id so no change required.

                                || if available quantity's sale price are different with current price then check 
                                || if purchase price are same then  the price id will replace by current price_id

                                || else
                                || check from priceinfos if those purchase_price and now sales price related
                                || any id found. then replace priceId of those available item id into 
                                || founded priceId

                                */
                                    $getAvailable=DB::table('stockitems')
                                                    ->join('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                                                    ->where('stockitems.item_id', '=', $items->item_id)
                                                    ->where('stockitems.branch_id', '=', $branch_id)
                                                    ->where('stockitems.item_id', '!=', $stockItemInfo->stock_item_id)
                                                    ->where('stockitems.available_quantity', '>',0)
                                                    ->get();

                                    $mergeQnt=0;
                                    foreach($getAvailable as $val){
                                        if($val->sale_price!=$items->sale_price){
                                            if($val->purchase_price==$items->purchase_price){
                                               $mergeQnt+=$val->available_quantity;
                                            DB::table('stockitems')->where('stock_item_id',$val->stock_item_id)->delete();
                                            }else{
                                               $found= DB::table('priceinfos')
                                                    ->where('item_id', '=', $items->item_id)
                                                    ->where('purchase_price', '=', $val->purchase_price)
                                                    ->where('sale_price', '=', $items->sale_price)
                                                    ->first();
                                                if($found){
                                               
                                                $stockId=DB::table('stockitems')
                                            ->where('price_id', '=', $found->price_id)
                                            ->first();
                                            
                                                if(isset($stockId->stock_item_id)){

                                                    $mergeQnty=$val->available_quantity+$stockId->available_quantity;
                                                 DB::table('stockitems')
                                                ->where('stock_item_id', '=', $stockId->stock_item_id)
                                                ->update(['available_quantity'=>$mergeQnty,
                                                    'updated_at'=>date('Y-m-d h:i:s'),
                                                    'status'=>1
                                                    ]);   
                                            DB::table('stockitems')->where('stock_item_id',$val->stock_item_id)->delete();
                                                } else{

                                                DB::table('stockitems')
                                                    ->where('item_id', '=', $items->item_id)
                                                    ->where('branch_id', '=', $branch_id)
                                                    ->where('price_id', '=', $val->price_id)
                                                    ->update(['price_id'=>$found->price_id,'updated_at'=>date('Y-m-d h:i:s')]);
                                                    }
                                                }else{
                                                    $price_info = array(
                                                    'item_id'           =>  $items->item_id,
                                                    'purchase_price'    =>  $val->purchase_price,
                                                    'sale_price'        =>  $items->sale_price,
                                                    'status'        =>0,
                                                    'created_by'        =>  Session::get('emp_id'),
                                                    'created_at'        =>  $this->timestamp
                                            ); 

                            $newPriceId = DB::table('priceinfos')->insertGetId($price_info);
                            DB::table('stockitems')->where('stock_item_id',$val->stock_item_id)
                                                   ->update(['price_id'=>$newPriceId,'updated_at'=>date('Y-m-d h:i:s')]);
                                                }
                                            }
                                        }
                                    }
                                    $increasingQuantity=DB::table('stockitems')
                                            ->where('stock_item_id', '=', $stockItemInfo->stock_item_id)
                                            ->increment('available_quantity', $items->quantity+$mergeQnt,
                                                        array(
                                                               'status' => 1,
                                                               'receiving_by'=>Session::get('emp_id'),
                                                               'receiving_at'=>$this->timestamp,
                                                              )
                                                        );

                            }
                      }
                       ###--- Ending Stock item increasing with super key consider ---###


                endforeach;
                }
                ####--- for discard action ---####
            else{
                 $flag=0;
                 foreach($receiving_ids as $receiving_id):
                    $isOperateData = DB::table('receivingitems')
                                ->where('receivingitems.receiving_item_id', '=', $receiving_id)
                                ->where('receivingitems.status', '!=',1)
                                ->first();
                    if($isOperateData)
                        $flag=1;
                 endforeach;   
                 if($flag==1)
                     return Redirect::back()->with('errorMsg', 'There are item contradiction occured.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Please select again.');
                 


                foreach($receiving_ids as $receiving_id):
                     $items = DB::table('receivingitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'receivingitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'receivingitems.price_id')
                        ->select('receivingitems.receiving_item_id','receivingitems.item_id','receivingitems.quantity','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id')
                        ->where('receivingitems.receiving_item_id', '=', $receiving_id)
                        ->first();
                    
                     $getReturn=DB::table('receivingitems')
                            ->where('receiving_item_id', $items->receiving_item_id)
                            ->update(array(
                                'status' => 2,
                                'receive_cancel_date'=>$this->timestamp,
                                'receive_cancel_by'=>Session::get('emp_id')
                                ));

                     ###--- Godown item increasing ---###
                      if($getReturn){
                          $godownItemInfo = DB::table('godownitems')
                                                    ->where('item_id', '=', $items->item_id)
                                                    ->where('price_id', '=', $items->price_id)
                                                    ->first();


                           $increasingQuantity=DB::table('godownitems')
                                            ->where('godown_item_id', '=', $godownItemInfo->godown_item_id)
                                            ->increment('available_quantity', $items->quantity,
                                                          array(
                                                                'status' => 1,
                                                                'receiving_by' => Session::get('emp_id'),
                                                                'receiving_at' => $this->timestamp,
                                                                )
                                                       );
                            }
                       ###--- Ending Godown item increasing ---###
             
                endforeach;
                }
                DB::commit();
            return Redirect::to('receiving')->with('message', 'Operation Successful');
            } catch (Exception $e) {
                         DB::rollback();
                         return $e;
            return Redirect::to('receiving')->with('errorMsg', 'Something is wrong in receiving item.');
        }
        
  }


public function returnToGodown()
    {
            $items = DB::table('stockitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                        ->select('stockitems.stock_item_id','stockitems.item_id','iteminfos.item_name')
                        ->where('stockitems.status', '=', 1)
                        ->groupBy('stockitems.item_id')
                        ->get();
           // echo'<pre>';print_r($items);exit;
                return View::make('sendingreceiving.returnToGodownForm',compact('items'));

    }

public function returnToGodownAutoSuggest(){
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

public function returnItemAddForsending()
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
                $item_info['stock_item_id']=$value->stock_item_id;
                $stock_item_id=$item_info['stock_item_id'];
                $item_info['item_id']=$value->item_id;
                $item_info['item_name']=$value->item_name;
                $item_info['price_id']=$value->price_id;
                $item_info['purchase_price']=$value->purchase_price;
                $item_info['sale_price']=$value->sale_price;
                $item_info['available_quantity']=$value->available_quantity;
                if($value->available_quantity>0)
                $item_info['now_send']=1;
                else
                continue;

                $item_info['total_price']=$value->sale_price*$item_info['now_send'];


                    Session::put("returnToGodown.$stock_item_id", $item_info);

          }

        return Redirect::to('returnToGodown');
    }

     public function returnEditDeleteSending()
    {
        $vdata=Input::all();
        $stock_item_id=$vdata['stock_item_id'];

        if($vdata['edit_delete']=='edit'){
            $data=DB::table('stockitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
                        ->select('stockitems.stock_item_id','stockitems.item_id','stockitems.price_id','stockitems.available_quantity', 'iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price')
                        ->where('stockitems.status', '=', 1)
                        ->where('stockitems.stock_item_id', '=', $stock_item_id)
                        ->first();
            $item_info=array();
            $item_info['stock_item_id']=$data->stock_item_id;
            $item_info['item_id']=$data->item_id;
            $item_info['item_name']=$data->item_name;
            $item_info['price_id']=$data->price_id;
            $item_info['purchase_price']=$data->purchase_price;
            $item_info['sale_price']=$data->sale_price;
            $item_info['available_quantity']=$data->available_quantity;
            if($data->available_quantity>0){
                if($vdata['now_send']>$data->available_quantity){
                $item_info['now_send']=$data->available_quantity;
                Session::flash('quantityError', 'You can not send quantity more than available quantity');
                }
                elseif($vdata['now_send']==0){
                    return Redirect::to('returnToGodown')->with('errorMsg', "You can not send zero(0) quantity.");
                }
                else{
                   $item_info['now_send']=$vdata['now_send'];
                }
            }
            else{
             return Redirect::to('returnToGodown')->with('errorMsg', "This product are not available in the stock");
            }
            $item_info['total_price']=$data->sale_price*$item_info['now_send'];

                Session::put("returnToGodown.$stock_item_id", $item_info);

         }
        else{
            Session::forget("returnToGodown.$stock_item_id");
           }

        return Redirect::to('returnToGodown');
    }

    public function saveReturnToGodown()
    {
        $flag=0;
         foreach(Session::get("returnToGodown") as $eachItem):
            $isAvailableQuantity = DB::table('stockitems')
                        ->where('stockitems.stock_item_id', '=', $eachItem['stock_item_id'])
                        ->where('stockitems.available_quantity', '<', $eachItem['now_send'])
                        ->where('stockitems.status', '=',1)
                        ->first();
            if($isAvailableQuantity)
                $flag=1;
         endforeach;   
         if($flag==1)
             return Redirect::back()->with('errorMsg', 'There are item quantity contradiction occured.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Please remove items and again select items.');
         

            DB::beginTransaction();
        try {
            $success=0;
            $fail=0;
            $sendingDateTime=Input::get('sendingDateTime')." ".date('h:i:s');
            foreach(Session::get("returnToGodown") as $eachItem){
                    $sendingData=array();
                    $sendingData['item_id']=$eachItem['item_id'];
                    $sendingData['price_id']=$eachItem['price_id'];
                    $sendingData['quantity']=$eachItem['now_send'];
                    $sendingData['returning_date']=$sendingDateTime;
                    $sendingData['returning_by']=Session::get('emp_id');
                    $sendingData['created_at']=$this->timestamp;
                    $stock_item_id=$eachItem['stock_item_id'];
                    $insertSendItem= DB::table('returnreceivingitems')->insert($sendingData);
                       if($insertSendItem){
                       $decreaseItem=DB::table('stockitems')
                                ->where('stockitems.stock_item_id', '=', $stock_item_id)
                                ->decrement('available_quantity', $sendingData['quantity'],
                                            array(
                                                 'sending_by' => Session::get('emp_id'),
                                                 'sending_at' => $this->timestamp,
                                                 )
                                           );
                       
                       Session::forget("returnToGodown.$stock_item_id");
                       $success++;
                       }
                       else{
                          $fail++;
                       }
                 }
                 DB::table('stockitems')
                    ->where('available_quantity', '<=', 0)
                    ->update(array('status' => 0));
              DB::commit();
             return Redirect::to('returnToGodown')->with('success', $success)->with('fail', $fail);

             } catch (Exception $e) {
                         DB::rollback();
            return Redirect::to('returnToGodown')->with('errorMsg', 'Something is wrong in return to godown.');
        }
    }

    public function returnReceive()
    {
        $items = DB::table('returnreceivingitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'returnreceivingitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'returnreceivingitems.price_id')
                        ->select('returnreceivingitems.r_receiving_item_id','returnreceivingitems.item_id','returnreceivingitems.quantity','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id')
                        ->where('returnreceivingitems.status', '=', 1)
                        ->get();
                return View::make('sendingreceiving.returnReceivingForm',compact('items'));
    }

public function savereturnReceiveItem()
  {
   DB::beginTransaction();

        try {
                    
                
        $vdata=Input::all();
        if(empty($vdata['r_receiving_item_ids'])){
            return Redirect::back()->with('errorMsg', 'Please Select Receiving Item');
        }
        $receiving_ids = $vdata['r_receiving_item_ids'];

           
        if($vdata['accept_cancel']=="Accept"){

         $flag=0;
         foreach($receiving_ids as $receiving_id):
            $isOperateData = DB::table('returnreceivingitems')
                        ->where('returnreceivingitems.r_receiving_item_id', '=', $receiving_id)
                        ->where('returnreceivingitems.status', '!=',1)
                        ->first();
            if($isOperateData)
                $flag=1;
         endforeach;   
         if($flag==1)
             return Redirect::back()->with('errorMsg', 'There are item contradiction occured.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Please select again.');


          foreach($receiving_ids as $receiving_id):
                   
            $items = DB::table('returnreceivingitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'returnreceivingitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'returnreceivingitems.price_id')
                        ->select('returnreceivingitems.r_receiving_item_id','returnreceivingitems.item_id','returnreceivingitems.quantity','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id')
                        ->where('returnreceivingitems.r_receiving_item_id', '=', $receiving_id)
                        ->first();
              

                    ### ---   For ReturnReceiving table update --- ###
                        $result=DB::table('returnreceivingitems')
                            ->where('r_receiving_item_id', $items->r_receiving_item_id)
                            ->update(array(
                                'status' => 0,
                                'receive_cancel_date'=>$this->timestamp,
                                'receive_cancel_by'=>Session::get('emp_id')
                                ));

                    ### ---   End of ReturnReceiving table update --- ###


                     ###--- Godown item increasing with super key consider ---###
                      if($result){
                            $godownItemInfo = DB::table('godownitems')
                                                    ->where('item_id', '=', $items->item_id)
                                                    ->where('price_id', '=', $items->price_id)
                                                    ->first();
                            if(!$godownItemInfo){
                                $insertData=array();
                                $insertData['item_id']=$items->item_id;
                                $insertData['price_id']=$items->price_id;
                                $insertData['available_quantity']=$items->quantity;
                                $insertData['created_by']=Session::get('emp_id');
                                $insertData['created_at']=$this->timestamp;

                                $insert=DB::table('godownitems')->insert($insertData);
                            }
                            else{

                                $increasingQuantity=DB::table('godownitems')
                                            ->where('godown_item_id', '=', $godownItemInfo->godown_item_id)
                                            ->increment('available_quantity', $items->quantity, 
                                                array('receiving_by' => Session::get('emp_id'),
                                                      'receiving_at' => $this->timestamp,
                                                      'status'=>1));
                            }
                      }
                       ###--- Ending Godown item increasing with super key consider ---###


            endforeach;
           }
                ####--- for discard action ---####
                else{


                    $flag=0;
                     foreach($receiving_ids as $receiving_id):
                        $isOperateData = DB::table('returnreceivingitems')
                                    ->where('returnreceivingitems.r_receiving_item_id', '=', $receiving_id)
                                    ->where('returnreceivingitems.status', '!=',1)
                                    ->first();
                        if($isOperateData)
                            $flag=1;
                     endforeach;   
                     if($flag==1)
                         return Redirect::back()->with('errorMsg', 'There are item contradiction occured.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Please select again.');

                    foreach($receiving_ids as $receiving_id):
                        $items = DB::table('returnreceivingitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'returnreceivingitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'returnreceivingitems.price_id')
                        ->select('returnreceivingitems.r_receiving_item_id','returnreceivingitems.item_id','returnreceivingitems.quantity','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id')
                        ->where('returnreceivingitems.r_receiving_item_id', '=', $receiving_id)
                        ->first();

                     $result=DB::table('returnreceivingitems')
                            ->where('r_receiving_item_id', $items->r_receiving_item_id)
                            ->update(array(
                                'status' => 2,
                                'receive_cancel_date'=>$this->timestamp,
                                'receive_cancel_by'=>Session::get('emp_id')
                                ));

                     ###--- Stock item increasing ---###
                      if($result){
                          $stockItemInfo = DB::table('stockitems')
                                                    ->where('item_id', '=', $items->item_id)
                                                    ->where('price_id', '=', $items->price_id)
                                                    ->first();

                          $increasingQuantity=DB::table('stockitems')
                                            ->where('stock_item_id', '=', $stockItemInfo->stock_item_id)
                                            ->increment('available_quantity', $items->quantity, 
                                                        array(
                                                            'receiving_by' => Session::get('emp_id'),
                                                            'receiving_at' => $this->timestamp,
                                                            'status'=>1
                                                            )
                                                      );

                          }
                       ###--- Ending Stock item increasing ---###

                endforeach;
                }
        DB::commit();
          return Redirect::to('returnReceiving')->with('message', 'Operation Successful');

    } catch (Exception $e) {
                  
                 DB::rollback();
                return Redirect::to('returnReceiving')->with('errorMsg', 'Something is wrong in return receiving.');
        }
   }
    
//quick sending system//
   public function itemAddForQuickSending()
    {
        $branch_id = Session::get('branch_id');
      //print_r(Session::get("sending")); exit;  
                    $data=DB::table('godownitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'godownitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'godownitems.price_id')
                        ->select('godownitems.godown_item_id','godownitems.item_id','godownitems.price_id','godownitems.available_quantity', 'iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','iteminfos.upc_code')
                        ->where('godownitems.status', '=', 1)
                        ->get();
            // $upcCodes = [];
            foreach($data as $value){
            $upc_code=$value->upc_code;
            
            $data=DB::table('godownitems')
                        ->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'godownitems.item_id')
                        ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'godownitems.price_id')
                        ->select('godownitems.godown_item_id','godownitems.item_id','godownitems.price_id','godownitems.available_quantity', 'iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price')
                        ->where('godownitems.status', '=', 1)
                        ->where('iteminfos.upc_code', '=', $upc_code)
                        ->get();
            // $upcCodes[] = $data;
            
            foreach($data as $value){
                $item_info=array();
                $item_info['branch_id']=$branch_id;
                $item_info['godown_item_id']=$value->godown_item_id;
                $godown_item_id=$item_info['godown_item_id'];
                $item_info['item_id']=$value->item_id;
                $item_info['item_name']=$value->item_name;
                $item_info['price_id']=$value->price_id;
                $item_info['purchase_price']=$value->purchase_price;
                $item_info['sale_price']=$value->sale_price;
                $item_info['available_quantity']=$value->available_quantity;
                
                if($value->available_quantity>0)
                $item_info['now_send']=$value->available_quantity;
                else
                continue;
                
                $item_info['total_price']=$value->sale_price*$item_info['now_send'];

                    Session::put("sending.$godown_item_id", $item_info);

          }

      }

        $this->saveSendingQuickMode();
        return Redirect::to('sending');
    }
public function saveSendingQuickMode()
    {
        $flag=0;
         foreach(Session::get("sending") as $eachItem):
            $isAvailableQuantity = DB::table('godownitems')
                        ->where('godownitems.godown_item_id', '=', $eachItem['godown_item_id'])
                        ->where('godownitems.available_quantity', '<', $eachItem['now_send'])
                        ->where('godownitems.status', '=',1)
                        ->first();
            if($isAvailableQuantity)
                $flag=1;
         endforeach;   
         if($flag==1)
             return Redirect::back()->with('errorMsg', 'There are item quantity contradiction occured.<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Please remove items and again select items.');
         

        DB::beginTransaction();

        try {
            $success=0;
            $fail=0;
            $sendingDateTime=date('Y-m-d')." ".date('h:i:s');
            foreach(Session::get("sending") as $eachItem){
                    $sendingData=array();
                    $sendingData['branch_id']=$eachItem['branch_id'];
                    $sendingData['item_id']=$eachItem['item_id'];
                    $sendingData['price_id']=$eachItem['price_id'];
                    $sendingData['quantity']=$eachItem['now_send'];
                    $sendingData['sending_date']=$sendingDateTime;
                    $sendingData['sending_by']=Session::get('emp_id');
                    $sendingData['created_at']=$this->timestamp;
                    $godown_item_id=$eachItem['godown_item_id'];
                    //echo'<pre>';print_r($sendingData);exit;
                    $insertSendItem= DB::table('receivingitems')->insert($sendingData);
                       if($insertSendItem){
                       $decreaseItem=DB::table('godownitems')
                                ->where('godownitems.godown_item_id', '=', $godown_item_id)
                                ->decrement('available_quantity', $sendingData['quantity'],
                                        array('sending_by' => $sendingData['sending_by'], 'sending_at' => $sendingDateTime));


                       Session::forget("sending.$godown_item_id");
                       $success++;
                       }
                       else{
                          $fail++;
                       }
                 }
                  DB::table('godownitems')
                    ->where('available_quantity', '<=', 0)
                    ->update(array('status' => 0));
              DB::commit();
             return Redirect::to('sending')->with('success', $success)->with('fail', $fail);

            } catch (Exception $e) {
                    //return  DB::getQueryLog();
                         DB::rollback();
            return Redirect::to('sending')->with('errorMsg', 'Something is wrong in save sending.');
        }
    }
} //end of class
