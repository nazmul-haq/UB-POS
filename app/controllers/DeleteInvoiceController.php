<?php

class DeleteInvoiceController extends \BaseController {

	public function DB2(){
		return DB::connection('mysql2');
	}
	
	public function deleteSaleReport()
	{
		$from=date('Y-m-d');
        $to=date('Y-m-d');
        return View::make('reports.details.deleteSaleReport',compact('from','to'));
	}

    public function deleteSaleReportView()
	{
        
	    $input=Input::all();
	    //echo '<pre>';print_r($input);exit;
	    $from=$input['from'];
	    $to=$input['to'];
	    $reports= DB::select("select customerinfos.user_name as customer_name,empinfos.user_name as invoiced_employee, each_item.*,(sum(each_item.item_profit) - (each_item.discount+each_item.point_use_taka)) as profit  
								from
								 (select saleinvoices.sale_invoice_id,saleinvoices.cus_id,saleinvoices.created_by,saleinvoices.discount,saleinvoices.point_use_taka,saleinvoices.amount,saleinvoices.pay,saleinvoices.due,saleinvoices.date,saleinvoices.status,saleinvoices.created_at as invoiced_datetime,
								 			(sum(priceinfos.sale_price - priceinfos.purchase_price) * itemsales.quantity)-itemsales.discount as item_profit,
										priceinfos.sale_price,priceinfos.purchase_price,itemsales.quantity
									from itemsales
								   left join saleinvoices on saleinvoices.sale_invoice_id=itemsales.sale_invoice_id
								   left join priceinfos on priceinfos.price_id=itemsales.price_id
								   WHERE  date
								   BETWEEN('$from')AND('$to')
								   group by itemsales.i_sale_id) as each_item
								left join customerinfos on customerinfos.cus_id=each_item.cus_id
								left join empinfos on empinfos.emp_id=each_item.created_by
								group by each_item.sale_invoice_id");

	        return View::make('reports.details.deleteSaleReport',compact('reports','from','to'));
	}



    public function deleteSaleInvoice()
	{
        
        try {
        DB::beginTransaction();
	        $data = Input::all();
	        array_forget($data,'_token');
	        array_forget($data,'datatable_length');
	    	for ($i=1; $i <= $data['index']; $i++) { 
	        	if (isset($data['saleInvoiceId_'.$i])) {
		            $getSaleInvoices = DB::table('saleinvoices')
		            	->where('sale_invoice_id',$data['saleInvoiceId_'.$i])
		            	->get();
		            foreach($getSaleInvoices as $saleInvoice){
		            	$insertSaleInvoice = $this->DB2()->table('del_saleinvoices')
		            	->insert([
	            			'id' 				=> $saleInvoice->id,
	            			'sale_invoice_id' 	=> $saleInvoice->sale_invoice_id,
	            			'cus_id' 			=> $saleInvoice->cus_id,
	            			'payment_type_id' 	=> $saleInvoice->payment_type_id,
	            			'discount' 			=> $saleInvoice->discount,
	            			'point_use_taka' 	=> $saleInvoice->point_use_taka,
	            			'amount' 			=> $saleInvoice->amount,
	            			'pay' 				=> $saleInvoice->pay,
	            			'due' 				=> $saleInvoice->due,
	            			'pay_note' 			=> $saleInvoice->pay_note,
	            			'date' 				=> $saleInvoice->date,
	            			'status' 			=> $saleInvoice->status,
	            			'year' 				=> $saleInvoice->year,
	            			'created_by' 		=> $saleInvoice->created_by,
	            			'updated_by' 		=> $saleInvoice->updated_by,
	            			'created_at' 		=> $saleInvoice->created_at,
	            			'updated_at' 		=> $saleInvoice->updated_at
		            	]);
		            	$getItemSales = DB::table('itemsales')
		            		->where('sale_invoice_id',$saleInvoice->sale_invoice_id)
		            		->get();
		            	foreach($getItemSales as $invoice){
		            		$insertItemSales = $this->DB2()->table('del_itemsales')
			            	->insert([
		            			'i_sale_id' 			=> $invoice->i_sale_id,
		            			'del_sale_invoice_id' 	=> $invoice->sale_invoice_id,
		            			'item_id' 				=> $invoice->item_id,
		            			'price_id' 				=> $invoice->price_id,
		            			'quantity' 				=> $invoice->quantity,
		            			'discount' 				=> $invoice->discount,
		            			'tax' 					=> $invoice->tax,
		            			'amount' 				=> $invoice->amount,
		            			'status' 				=> $invoice->status,
		            			'year' 					=> $invoice->year,
		            			'created_by' 			=> $invoice->created_by,
		            			'updated_by' 			=> $invoice->updated_by,
		            			'created_at' 			=> $invoice->created_at,
		            			'updated_at' 			=> $invoice->updated_at
			            	]);
				            $deleteItemSales = DB::table('itemsales')
				            	->where('sale_invoice_id',$invoice->sale_invoice_id)
				            	->delete();
		            	}

		            	$getSaleReturnInvoices = DB::table('salereturninvoices')
		            	->where('sale_invoice_id',$invoice->sale_invoice_id)
		            	->get();
		            	if(count($getSaleReturnInvoices) > 0){

		            	foreach($getSaleReturnInvoices as $saleReturnInvoice){
		            		
			            	$insertSaleReturnInvoice = $this->DB2()->table('del_salereturninvoices')
				            	->insert([
			            			'id' 					=> $saleReturnInvoice->id,
			            			'sale_r_invoice_id' 	=> $saleReturnInvoice->sale_r_invoice_id,
			            			'del_sale_invoice_id' 	=> $saleReturnInvoice->sale_invoice_id,
			            			'cus_id' 				=> $saleReturnInvoice->cus_id,
			            			'payment_type_id' 		=> $saleReturnInvoice->payment_type_id,
			            			'amount' 				=> $saleReturnInvoice->amount,
			            			'less_amount' 			=> $saleReturnInvoice->less_amount,
			            			'transaction_date' 		=> $saleReturnInvoice->transaction_date,
			            			'status' 				=> $saleReturnInvoice->status,
			            			'year' 					=> $saleReturnInvoice->year,
			            			'created_by' 			=> $saleReturnInvoice->created_by,
			            			'updated_by' 			=> $saleReturnInvoice->updated_by,
			            			'created_at' 			=> $saleReturnInvoice->created_at,
			            			'updated_at' 			=> $saleReturnInvoice->updated_at
				            	]);
			            	$getSaleReturnToStocks = DB::table('salereturntostocks')
			            		->where('sale_r_invoice_id',$saleReturnInvoice->sale_r_invoice_id)
			            		->get();
			            	
			            	foreach($getSaleReturnToStocks as $saleReturnToStocks){
			            		$insertSaleReturnToStocks = $this->DB2()->table('del_salereturntostocks')
					            	->insert([
				            			'i_sale_return_id' 		=> $saleReturnToStocks->i_sale_return_id,
				            			'del_sale_r_invoice_id' => $saleReturnToStocks->sale_r_invoice_id,
				            			'item_id' 				=> $saleReturnToStocks->item_id,
				            			'price_id' 				=> $saleReturnToStocks->price_id,
				            			'quantity' 				=> $saleReturnToStocks->quantity,
				            			'discount' 				=> $saleReturnToStocks->discount,
				            			'tax' 					=> $saleReturnToStocks->tax,
				            			'amount' 				=> $saleReturnToStocks->amount,
				            			'status' 				=> $saleReturnToStocks->status,
				            			'year' 					=> $saleReturnToStocks->year,
				            			'created_by' 			=> $saleReturnToStocks->created_by,
				            			'updated_by' 			=> $saleReturnToStocks->updated_by,
				            			'created_at' 			=> $saleReturnToStocks->created_at,
				            			'updated_at' 			=> $saleReturnToStocks->updated_at
					            	]);
					            $deleteSaleReturnToStocks = DB::table('salereturntostocks')
					            	->where('sale_r_invoice_id',$saleReturnToStocks->sale_r_invoice_id)
					            	->delete();
			            	}
			            	$deleteSaleReturnInvoice = DB::table('salereturninvoices')
			            		->where('sale_invoice_id',$saleReturnInvoice->sale_invoice_id)
			            		->delete();
		            	}
		            }
		            	$deleteSaleInvoice = DB::table('saleinvoices')
		            		->where('sale_invoice_id',$saleInvoice->sale_invoice_id)
		            		->delete();
		            }
	        	}
	    	}
	        DB::commit();
	    	return Redirect::to('admin/deleteSaleReport')->with('message', 'Invoice Deleted Successfully');

        }catch(\Exception $e) {
            DB::rollback();
            //return $e;
            Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError.".$e->errorInfo[2]);
            return Redirect::to('admin/deleteSaleReport')->with('errorMsg', $err_msg);
        }
    }
    
    public function viewDelSaleInvoices(){
        $result = $this->DB2()->table('del_salereturntostocks')->get();
        echo "<pre>";
        print_r($result);
        return;
	}
	
	/* mysqlError.Cannot add or update a child row: a foreign key constraint fails (`del_asiabazar`.`del_saleinvoices`, CONSTRAINT `del_saleinvoices_ibfk_3` FOREIGN KEY (`payment_type_id`) REFERENCES `paymenttypes` (`payment_type_id`))*/
//=========@@ Purchase Section =============

    public function deletePurchaseReport(){
        return View::make('reports.details.deletePurchaseReport');
	}

	public function deletePurchaseReportView()
	{
		$input=Input::all();
		$from=$input['from'];
		$to=$input['to'];
			$reports= DB::table('supinvoices')
					->leftjoin('empinfos', 'empinfos.emp_id', '=', 'supinvoices.created_by')
					->leftjoin('supplierinfos', 'supplierinfos.supp_id', '=', 'supinvoices.supp_id')
					->leftjoin('paymenttypes', 'paymenttypes.payment_type_id', '=', 'supinvoices.payment_type_id')
					->select('supinvoices.sup_invoice_id','supinvoices.supp_id','supplierinfos.supp_or_comp_name as supplier_name','supinvoices.payment_type_id','paymenttypes.payment_type_name','supinvoices.discount','supinvoices.amount','supinvoices.pay','supinvoices.due','supinvoices.transaction_date','supinvoices.status','empinfos.user_name as invoiced_employee','supinvoices.created_at as invoiced_datetime')
					->where('supinvoices.status', '=', 1)
					->whereBetween('supinvoices.transaction_date', array($from, $to))
					->orderBy('supinvoices.transaction_date', 'desc')
					->get();
	//echo '<pre>';print_r($reports);exit;
			return View::make('reports.details.deletePurchaseReport',compact('reports','from','to'));
	}



	public function deletePurchaseInvoice()
	{
		DB::beginTransaction();
        try {
            
	        $data = Input::all();
	        array_forget($data,'_token');
	        array_forget($data,'datatable_length');
	    	for ($i=1; $i <= $data['index']; $i++) { 
	        	if (isset($data['supInvoiceId_'.$i])) {
		            $getSupInvoices = DB::table('supinvoices')
		            	->where('sup_invoice_id',$data['supInvoiceId_'.$i])
		            	->get();
		            foreach($getSupInvoices as $supInvoice){
		            	$insertSupInvoice = DB::table('del_supinvoices')
			            	->insert([
		            			'id' 				=> $supInvoice->id,
		            			'sup_invoice_id' 	=> $supInvoice->sup_invoice_id,
		            			'sup_memo_no' 		=> $supInvoice->sup_memo_no,
		            			'supp_id' 			=> $supInvoice->supp_id,
		            			'payment_type_id' 	=> $supInvoice->payment_type_id,
		            			'discount' 			=> $supInvoice->discount,
		            			'amount' 			=> $supInvoice->amount,
		            			'pay' 				=> $supInvoice->pay,
		            			'due' 				=> $supInvoice->due,
		            			'transaction_date' 	=> $supInvoice->transaction_date,
		            			'status' 			=> $supInvoice->status,
		            			'year' 				=> $supInvoice->year,
		            			'created_by' 		=> $supInvoice->created_by,
		            			'updated_by' 		=> $supInvoice->updated_by,
		            			'created_at' 		=> $supInvoice->created_at,
		            			'updated_at' 		=> $supInvoice->updated_at
			            	]);
		            	$getItemPurchase = DB::table('itempurchases')
		            		->where('sup_invoice_id',$supInvoice->sup_invoice_id)
		            		->get();
		            	foreach($getItemPurchase as $itemPurchase){
		            		$insertItemPurchase = DB::table('del_itempurchases')
				            	->insert([
			            			'i_purchase_id' 		=> $itemPurchase->i_purchase_id,
			            			'del_sup_invoice_id' 	=> $itemPurchase->sup_invoice_id,
			            			'item_id' 				=> $itemPurchase->item_id,
			            			'price_id' 				=> $itemPurchase->price_id,
			            			'quantity' 				=> $itemPurchase->quantity,
			            			'discount' 				=> $itemPurchase->discount,
			            			'amount' 				=> $itemPurchase->amount,
			            			'status' 				=> $itemPurchase->status,
			            			'year' 					=> $itemPurchase->year,
			            			'created_by' 			=> $itemPurchase->created_by,
			            			'updated_by' 			=> $itemPurchase->updated_by,
			            			'created_at' 			=> $itemPurchase->created_at,
			            			'updated_at' 			=> $itemPurchase->updated_at
				            	]);
				            $deleteItemPurchases = DB::table('itempurchases')
				            	->where('sup_invoice_id',$itemPurchase->sup_invoice_id)
				            	->delete();
		            	}
		            	$getSupplierReturnInvoices = DB::table('supplierreturninvoices')
		            		->where('sup_invoice_id',$supInvoice->sup_invoice_id)
		            		->get();
		            	if(count($getSupplierReturnInvoices) > 0){

		            	foreach($getSupplierReturnInvoices as $supplierReturnInvoice){
			            	$insertSupplierReturnInvoice = DB::table('del_supplierreturninvoices')
				            	->insert([
			            			'id' 					=> $supplierReturnInvoice->id,
			            			'sup_r_invoice_id' 		=> $supplierReturnInvoice->sup_r_invoice_id,
			            			'del_sup_invoice_id' 	=> $supplierReturnInvoice->sup_invoice_id,
			            			'supp_id' 				=> $supplierReturnInvoice->supp_id,
			            			'payment_type_id' 		=> $supplierReturnInvoice->payment_type_id,
			            			'amount' 				=> $supplierReturnInvoice->amount,
			            			'less_amount' 			=> $supplierReturnInvoice->less_amount,
			            			'transaction_date' 		=> $supplierReturnInvoice->transaction_date,
			            			'status' 				=> $supplierReturnInvoice->status,
			            			'year' 					=> $supplierReturnInvoice->year,
			            			'created_by' 			=> $supplierReturnInvoice->created_by,
			            			'updated_by' 			=> $supplierReturnInvoice->updated_by,
			            			'created_at' 			=> $supplierReturnInvoice->created_at,
			            			'updated_at' 			=> $supplierReturnInvoice->updated_at
				            	]);
			            	$getPurchaseReturnToSupplier = DB::table('purchasereturntosupplier')
			            		->where('sup_r_invoice_id',$supplierReturnInvoice->sup_r_invoice_id)
			            		->get();
			            		//echo "<pre>";
			            	foreach($getPurchaseReturnToSupplier as $purchaseReturnToSupplier){
			            		$insertPurchaseReturnToSupplier = DB::table('del_purchasereturntosupplier')
					            	->insert([
				            			'purchase_return_id' 	=> $purchaseReturnToSupplier->purchase_return_id,
				            			'del_sup_r_invoice_id' 	=> $purchaseReturnToSupplier->sup_r_invoice_id,
				            			'item_id' 				=> $purchaseReturnToSupplier->item_id,
				            			'price_id' 				=> $purchaseReturnToSupplier->price_id,
				            			'quantity' 				=> $purchaseReturnToSupplier->quantity,
				            			'discount' 				=> $purchaseReturnToSupplier->discount,
				            			'amount' 				=> $purchaseReturnToSupplier->amount,
				            			'status' 				=> $purchaseReturnToSupplier->status,
				            			'year' 					=> $purchaseReturnToSupplier->year,
				            			'created_by' 			=> $purchaseReturnToSupplier->created_by,
				            			'updated_by' 			=> $purchaseReturnToSupplier->updated_by,
				            			'created_at' 			=> $purchaseReturnToSupplier->created_at,
				            			'updated_at' 			=> $purchaseReturnToSupplier->updated_at
					            	]);
					            $deletePurchaseReturnToSupplier = DB::table('purchasereturntosupplier')
					            	->where('sup_r_invoice_id',$purchaseReturnToSupplier->sup_r_invoice_id)
					            	->delete();
			            	}
			            	$deleteSupplierReturnInvoice = DB::table('supplierreturninvoices')
			            		->where('sup_invoice_id',$supplierReturnInvoice->sup_invoice_id)
			            		->delete();
		            	}
		            }

		            	$deleteSupInvoice = DB::table('supinvoices')
		            		->where('sup_invoice_id',$supInvoice->sup_invoice_id)
		            		->delete();
		            }
	        	}
	    	}
            DB::commit();
    		return Redirect::to('admin/deletePurchaseReport')->with('message', 'Invoice Deleted Successfully');
        }catch(\Exception $e) {
            DB::rollback();
            //return $e;
            Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError.".$e->errorInfo[2]);
            return Redirect::to('admin/deletePurchaseReport')->with('errorMsg', $err_msg);
        }
    }
}

/*mysqlError.Cannot delete or update a parent row: a foreign key constraint fails (`asiabazar`.`purchasereturntosupplier`, CONSTRAINT `FK_purchasereturntosupplier_supplierreturninvoices` FOREIGN KEY (`sup_r_invoice_id`) REFERENCES `supplierreturninvoices` (`sup_r_invoice_id`)*/