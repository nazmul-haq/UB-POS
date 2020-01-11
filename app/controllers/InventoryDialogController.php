<?php
class InventoryDialogController extends \BaseController {

    public $timestamp;
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->timestamp = date('Y-m-d H:i:s');
    }
    public function index()
    {
        // Session::forget("inventoryItems");
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
        return View::make('inventory.inventoryDialog',compact('items','customers','payment_type'));
    }

    public function autoInventoryItemSuggest()
    {
    $term = Input::get('q');
        $search_items = DB::table('stockitems')
        ->join('iteminfos', 'iteminfos.item_id', '=', 'stockitems.item_id')
        ->join('priceinfos', 'iteminfos.item_id', '=', 'priceinfos.item_id')
        // ->where('stockitems.status', '=', 1)
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

    public function inventoryItemAddToChart()
    {
        $item_id = Input::get('item_id');
        $datas = DB::table('stockitems as s')
            ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.upc_code','p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
            ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
            ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
            ->where('s.status', '=', 1)
            ->where('i.upc_code', '=', $item_id)
            ->groupBy('s.stock_item_id')
            ->get();
        if(!$datas){
            $datas = DB::table('stockitems as s')
                ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.upc_code','p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
                ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
                ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
                ->where('i.upc_code', '=', $item_id)
                ->groupBy('s.stock_item_id')
                ->get();
            if(!$datas){
                return Redirect::to('admin/inventory')->with('errorMsg', "This product are not available in the stock");
            }
        }
        foreach($datas as $data){
            $item_info=array();
            $item_info['stock_item_id'] =$data->stock_item_id;
            $stock_item_id              = $item_info['stock_item_id'];
            $item_info['row_no']        = $data->row_no;
            $item_info['item_id']       = $data->item_id;
            $item_info['item_name']     = $data->item_name;
            $item_info['upc_code']     = $data->upc_code;
            $key = $item_info['key']    = $item_info['item_id'].'-'.$item_info['stock_item_id'];
           $item_info['purchase_price']=$data->purchase_price;
            $item_info['sale_price']    = $data->sale_price;
            $item_info['available_quantity']=$data->available_quantity;
            $item_info['new_quantity']=0;
            $item_id=$data->item_id;
            //echo'<pre>';print_r(Session::get("saleItems.$key"));exit;
            if(Session::get("inventoryItems.$key")){
                return Redirect::to('admin/inventory');
            }
            Session::put("inventoryItems.$key", $item_info);
        }
      //  echo'<pre>';print_r(Session::get('saleItems'));exit;
        return Redirect::to('admin/inventory');
    }
    public function emptyCart()
    {
        if(Session::get('inventoryItems')){
            Session::forget('inventoryItems');
        }
        return Redirect::to('admin/inventory');
    }

    public function saleEditDeleteItem()
    {
        $vdata = Input::all();
        $key = $vdata['key'];
        $new_quantity = $vdata['new_quantity'];
            $parameters = explode("-",$key);
            $item_id = $parameters[0];
            $stock_item_id = $parameters[1];
            $data = DB::table('stockitems as s')
                ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.upc_code', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
                ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
                ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
                ->where('s.status', '=', 1)
                ->where('s.item_id', '=', $item_id)
                ->where('s.stock_item_id', '=', $stock_item_id)
                ->groupBy('s.stock_item_id')
                ->first();
            if(!$data){
                $data = DB::table('stockitems as s')
                    ->select('s.stock_item_id', 's.item_id', 's.price_id', 'i.item_name','i.upc_code', 'p.purchase_price','p.sale_price', DB::raw('SUM(s.available_quantity) as available_quantity, count(*) as row_no'))
                    ->leftjoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
                    ->leftJoin('priceinfos as p', 'p.price_id', '=', 's.price_id')
                    // ->where('s.status', '=', 1)
                    ->where('s.item_id', '=', $item_id)
                    ->where('s.stock_item_id', '=', $stock_item_id)
                    ->groupBy('s.stock_item_id')
                    ->first();
            }
        if($vdata['edit_delete']=='edit'){
            $item_info=array();
            $item_info['stock_item_id']=$data->stock_item_id;
            //$stock_item_id=$item_info['stock_item_id'];
            $item_info['row_no']=$data->row_no;
            $item_info['item_id']=$data->item_id;
            $item_info['key']=$key;
            $item_info['item_name']=$data->item_name;
            $item_info['upc_code']=$data->upc_code;
           $item_info['purchase_price']=$data->purchase_price;
            $item_info['sale_price']=$data->sale_price;
            $item_info['available_quantity']=$data->available_quantity;
            $item_info['new_quantity']=$new_quantity;
            Session::put("inventoryItems.$key", $item_info);
        }
        else{
          Session::forget("inventoryItems.$key");
        }
        return Redirect::to('admin/inventory');
   }
    
    public function inventoryDialogSave()
    {
        if(!Session::has("inventoryItems")){
            return Redirect::to('admin/inventory')->with('errorMsg','Cart is empty.');
        }
        $data = Session::get("inventoryItems");
        $date = Input::get('date');
        $inventory_invoice_id = date('ymdhis');
        $receipt_item_infos = array();
        $receipt_info = [];
        $success = 0;
        DB::beginTransaction();
        try {
            foreach($data as $key => $value){
                if($value['available_quantity'] != $value['new_quantity']){
                    $receiptItem = [];
                    $update = DB::table('stockitems')
                        ->where('stock_item_id',$value['stock_item_id'])
                        ->where('available_quantity',$value['available_quantity'])
                        ->update([
                            'status' => 1,
                            'available_quantity' => $value['new_quantity'],
                            'updated_by' => Session::get('emp_id'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    if($update){
                        $invoice = DB::table('inventorydialog')
                            ->insert([
                                'stock_item_id'     => $value['stock_item_id'],
                                'dialog_invoice_id' => $inventory_invoice_id,
                                'quantity_ex'       => $value['available_quantity'],
                                'quantity_new'      => $value['new_quantity'],
                                'created_by'        => Session::get('emp_id'),
                                'created_at'        => date('Y-m-d H:i:s')
                            ]);
                    }
                    $item_info = DB::table('stockitems')
                        ->join('iteminfos','stockitems.item_id','=','iteminfos.item_id')
                        ->where('stockitems.stock_item_id',$value['stock_item_id'])
                        ->first();
                    $receiptItem['item_name']    = $item_info->item_name;
                    $receiptItem['upc_code']     = $item_info->upc_code;
                    $receiptItem['ex_quantity']  = $value['available_quantity'];
                    $receiptItem['new_quantity'] = $value['new_quantity'];
                    $receipt_item_infos[] = $receiptItem;
                    $success++;
                }
                if($success < 1){
                    return Redirect::to('admin/inventory')->with('errorMsg', 'No Change Occured !');
                }
            }
            $emp_info=DB::table('empinfos')
                ->where('empinfos.emp_id', '=', Session::get('emp_id'))
                ->first();
            $receipt_info['date']                 = date('Y-m-d');
            $receipt_info['created_at']           = date('Y-m-d h:i:s a');
            $receipt_info['inventory_invoice_id'] = $inventory_invoice_id;
            $receipt_info['emp_name']             = $emp_info->user_name;
            $company_info=DB::table('companyprofiles')
                ->first();
            Session::forget("inventoryItems");
            DB::commit();
            return View::make('inventory.inventoryDialogReceipt',compact('receipt_item_infos','receipt_info','company_info'));
        } catch (Exception $e) {
            // return $e;
            DB::rollback();
        return Redirect::to('admin/inventory')->with('errorMsg', 'Something is wrong !');
      }
    }
    
}
