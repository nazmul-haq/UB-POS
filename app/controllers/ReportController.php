<?php

class ReportController extends \BaseController {
	
	public function index(){
		return View::make('reports.reports');
	}	
	/*
	*	Sending Reports
	*/
	public function sendReport()
	{
		return View::make('reports.details.sendingReport');
	} 
    public function viewSendReport()
	{
            $input	=	Input::all();
            $from	=	$input['from'];
            $to		=	$input['to'];

			$reports= DB::table('receivingitems')
					->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'receivingitems.item_id')
					->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'receivingitems.price_id')
					->leftjoin('empinfos', 'empinfos.emp_id', '=', 'receivingitems.sending_by')
					->select('receivingitems.receiving_item_id','receivingitems.item_id','receivingitems.quantity','receivingitems.sending_date', 'receivingitems.status','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id','empinfos.user_name as sent_by')
					->whereBetween('receivingitems.sending_date', array($input['from'], $input['to']))
					->orderBy('receivingitems.sending_date', 'desc')
					->get();

			return View::make('reports.details.sendingReport',compact('reports','from','to'));
	}

	public function receiveReport()
	{
		return View::make('reports.details.receivingReport');
	}

	public function viewReceivingReport()
	{
		$input	=	Input::all();
		$from	=	$input['from'];
		$to		=	$input['to'];

		$reports= DB::table('receivingitems')
				->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'receivingitems.item_id')
				->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'receivingitems.price_id')
				->leftjoin('empinfos', 'empinfos.emp_id', '=', 'receivingitems.receive_cancel_by')
				->select('receivingitems.receiving_item_id','receivingitems.item_id','receivingitems.quantity','receivingitems.receive_cancel_date','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id','empinfos.user_name as receiving_by')
				->where('receivingitems.status', '=', 0)
				->whereBetween('receivingitems.receive_cancel_date', array($input['from'], $input['to']))
				->orderBy('receivingitems.receive_cancel_date', 'desc')
				->get();
		return View::make('reports.details.receivingReport',compact('reports','from','to'));
	}
    public function saleReport()
	{
		$from=date('Y-m-d');
        $to=date('Y-m-d');
        return View::make('reports.details.saleReport',compact('from','to'));
	}
    public function viewSaleReport()
	{
	    $input=Input::all();
	    //echo '<pre>';print_r($input);exit;
	    $from=$input['from'];
	    $to=$input['to'];
	    $reports= DB::select("select customerinfos.user_name as customer_name,customerinfos.full_name as customer_full_name,empinfos.user_name as invoiced_employee, each_item.*,(sum(each_item.item_profit) - (each_item.discount)) as profit  
				from
				 (select saleinvoices.sale_invoice_id,saleinvoices.cus_id,saleinvoices.created_by,saleinvoices.discount,saleinvoices.point_use_taka,saleinvoices.amount,saleinvoices.pay,saleinvoices.due,saleinvoices.date,saleinvoices.status,saleinvoices.created_at as invoiced_datetime,
				 			(sum((itemsales.amount/itemsales.quantity) - priceinfos.purchase_price) * itemsales.quantity)-itemsales.discount as item_profit,
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
        return View::make('reports.details.saleReport',compact('reports','from','to'));
	}
	public function saleDetailsReport($saleInvoiceId){
		$company_info = DB::table('companyprofiles')
						->select('company_name', 'address', 'mobile')
						->first();
		
		$receipt_info = DB::table('saleinvoices as si')
						->leftjoin('empinfos as ei', 'ei.emp_id', '=', 'si.created_by')
						->leftJoin('customerinfos as ci', 'ci.cus_id', '=', 'si.cus_id')
						->leftJoin('customertypes as ct', 'ct.cus_type_id', '=', 'ci.cus_type_id')
						->leftJoin('paymenttypes as pt', 'pt.payment_type_id', '=', 'si.payment_type_id')
						->select('si.sale_invoice_id', 'si.discount', 'si.point_use_taka', 'si.amount', 'si.pay', 'si.due', 'si.pay_note', 'si.date', 'si.created_at', 'ci.user_name as customer_name', 'ct.point_unit', 'pt.payment_type_name', 'ei.user_name as invoiced_employee')
						->where('si.status', 1)
						->where('si.sale_invoice_id', $saleInvoiceId)
						->first();
                
      ### No delete this commentted code ###      
                
						
//		$receipt_item_infos = DB::table('itemsales as is')
//							->join('saleinvoices as si', 'is.sale_invoice_id', '=', 'si.sale_invoice_id')
//							->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'is.item_id')
//							->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'is.price_id')
//							->select('is.quantity', 'is.discount', 'is.amount', 'ii.item_name', 'is.tax', 'pi.sale_price')
//							->where('is.status', 1)
//							->where('is.sale_invoice_id', $saleInvoiceId)
//							->get();
                
                $receipt_item_infos = DB::table('itemsales')
					->select('i.item_name','itemsales.tax','itemsales.discount','p.sale_price', DB::raw('SUM(itemsales.quantity) as quantity, SUM(itemsales.amount) as amount'))
                                        ->join('saleinvoices', 'saleinvoices.sale_invoice_id', '=', 'itemsales.sale_invoice_id')
                                        ->leftjoin('iteminfos as i', 'itemsales.item_id', '=', 'i.item_id')
                                        ->leftJoin('priceinfos as p', 'p.price_id', '=', 'itemsales.price_id')
                                        ->where('itemsales.status', '=', 1)
                                        ->where('itemsales.sale_invoice_id', '=', $saleInvoiceId)
                                        ->groupBy('p.sale_price')
                                        ->groupBy('itemsales.item_id')
                                        ->orderBy('itemsales.i_sale_id','asc')
                                        ->get();
		return View::make('reports.details.saleReportDetails', compact('company_info', 'receipt_info', 'receipt_item_infos'));
	}
	/*sale Receipt*/
	public function saleReportReceipt($saleInvoiceId){
		$company_info = DB::table('companyprofiles')
						->select('company_name', 'address', 'mobile')
						->first();
		
		$receipt_info = DB::table('saleinvoices as si')
						->leftjoin('empinfos as ei', 'ei.emp_id', '=', 'si.created_by')
						->leftJoin('customerinfos as ci', 'ci.cus_id', '=', 'si.cus_id')
						->leftJoin('customertypes as ct', 'ct.cus_type_id', '=', 'ci.cus_type_id')
						->leftJoin('paymenttypes as pt', 'pt.payment_type_id', '=', 'si.payment_type_id')
						->select('si.sale_invoice_id', 'si.discount', 'si.point_use_taka', 'si.amount', 'si.pay', 'si.due', 'si.pay_note', 'si.date', 'si.created_at', 'ci.present_address','ci.full_name as customer_name', 'ct.point_unit', 'pt.payment_type_name', 'ei.user_name as invoiced_employee')
						->where('si.status', 1)
						->where('si.sale_invoice_id', $saleInvoiceId)
						->first();
						
		$receipt_item_infos = DB::table('itemsales as is')
							->join('saleinvoices as si', 'is.sale_invoice_id', '=', 'si.sale_invoice_id')
							->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'is.item_id')
							->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'is.price_id')
							->select('is.quantity', 'is.discount', 'is.amount', 'ii.item_name', 'is.tax', 'pi.sale_price')
							->where('is.status', 1)
							->where('is.sale_invoice_id', $saleInvoiceId)
							->get();
		return View::make('reports.details.saleReportReceipt', compact('company_info', 'receipt_info', 'receipt_item_infos'));
	}
    public function purchaseReport(){
        return View::make('reports.details.purchaseReport');
	}
	public function viewPurchaseReport()
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
			return View::make('reports.details.purchaseReport',compact('reports','from','to'));
	}
	public function purchaseDetailsReport($purchaseInvoiceId){
		$company_info = DB::table('companyprofiles')
			->select('company_name', 'address', 'mobile')
			->first();
		
		$receipt_info = DB::table('supinvoices')
			->leftjoin('empinfos', 'empinfos.emp_id', '=', 'supinvoices.created_by')
			->leftjoin('supplierinfos', 'supplierinfos.supp_id', '=', 'supinvoices.supp_id')
			->leftjoin('paymenttypes as pt', 'pt.payment_type_id', '=', 'supinvoices.payment_type_id')
			->select('supinvoices.sup_invoice_id', 'supinvoices.sup_memo_no', 'supinvoices.amount', 'supinvoices.discount', 'supinvoices.pay', 'supinvoices.due', 'supinvoices.transaction_date', 'supinvoices.status',  'supplierinfos.supp_or_comp_name as supplier_name', 'pt.payment_type_name', 'empinfos.user_name as invoiced_employee', 'supinvoices.created_at')
			->where('supinvoices.sup_invoice_id', $purchaseInvoiceId)
			->where('supinvoices.status', '=', 1)
			->orderBy('supinvoices.transaction_date', 'desc')
			->first();
						
		$receipt_item_infos = DB::table('itempurchases as itempurchases')
			->join('supinvoices', 'itempurchases.sup_invoice_id', '=', 'supinvoices.sup_invoice_id')
			->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'itempurchases.item_id')
			->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'itempurchases.price_id')
			->select('itempurchases.quantity', 'itempurchases.discount', 'itempurchases.amount', 'ii.item_name', 'pi.purchase_price', 'pi.sale_price')
			->where('itempurchases.sup_invoice_id', $purchaseInvoiceId)
			->where('itempurchases.status', 1)
			->get(); 
		return View::make('reports.details.purchaseReportDetails', compact('company_info', 'receipt_info', 'receipt_item_infos'));
	}
	public function purchaseOrderReport(){
        return View::make('reports.details.purchaseOrderReport');
	}
	public function viewPurchaseOrderReport()
	{
		$input=Input::all();
		$from=$input['from'];
		$to=$input['to'];
			$reports= DB::table('supinvoices_order')
					->leftjoin('empinfos', 'empinfos.emp_id', '=', 'supinvoices_order.created_by')
					->leftjoin('supplierinfos', 'supplierinfos.supp_id', '=', 'supinvoices_order.supp_id')
					->leftjoin('paymenttypes', 'paymenttypes.payment_type_id', '=', 'supinvoices_order.payment_type_id')
					->select('supinvoices_order.sup_invoice_id','supinvoices_order.supp_id','supplierinfos.supp_or_comp_name as supplier_name','supinvoices_order.payment_type_id','paymenttypes.payment_type_name','supinvoices_order.discount','supinvoices_order.amount','supinvoices_order.pay','supinvoices_order.due','supinvoices_order.transaction_date','supinvoices_order.status','empinfos.user_name as invoiced_employee','supinvoices_order.created_at as invoiced_datetime')
					->where('supinvoices_order.status', '=', 1)
					->whereBetween('supinvoices_order.transaction_date', array($from, $to))
					->orderBy('supinvoices_order.transaction_date', 'desc')
					->get();
	//echo '<pre>';print_r($reports);exit;
			return View::make('reports.details.purchaseOrderReport',compact('reports','from','to'));
	}
	public function purchaseOrderDetailsReport($purchaseInvoiceId){
		$company_info = DB::table('companyprofiles')
			->select('company_name', 'address', 'mobile')
			->first();
		
		$receipt_info = DB::table('supinvoices_order')
			->leftjoin('empinfos', 'empinfos.emp_id', '=', 'supinvoices_order.created_by')
			->leftjoin('supplierinfos', 'supplierinfos.supp_id', '=', 'supinvoices_order.supp_id')
			->leftjoin('paymenttypes as pt', 'pt.payment_type_id', '=', 'supinvoices_order.payment_type_id')
			->select('supinvoices_order.sup_invoice_id', 'supinvoices_order.sup_memo_no', 'supinvoices_order.amount', 'supinvoices_order.discount', 'supinvoices_order.pay', 'supinvoices_order.due', 'supinvoices_order.transaction_date', 'supinvoices_order.status',  'supplierinfos.supp_or_comp_name as supplier_name', 'pt.payment_type_name', 'empinfos.user_name as invoiced_employee', 'supinvoices_order.created_at')
			->where('supinvoices_order.sup_invoice_id', $purchaseInvoiceId)
			->where('supinvoices_order.status', '=', 1)
			->orderBy('supinvoices_order.transaction_date', 'desc')
			->first();
						
		$receipt_item_infos = DB::table('itempurchases_order as itempurchases_order')
			->join('supinvoices_order', 'itempurchases_order.sup_invoice_id', '=', 'supinvoices_order.sup_invoice_id')
			->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'itempurchases_order.item_id')
			->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'itempurchases_order.price_id')
			->select('itempurchases_order.quantity', 'itempurchases_order.discount', 'itempurchases_order.amount', 'ii.item_name', 'pi.purchase_price', 'pi.sale_price')
			->where('itempurchases_order.sup_invoice_id', $purchaseInvoiceId)
			->where('itempurchases_order.status', 1)
			->get(); 
		return View::make('reports.details.purchaseOrderReportDetails', compact('company_info', 'receipt_info', 'receipt_item_infos'));
	}
	
	public function saleReturnReport(){
        return View::make('reports.details.saleReturnReport');
	}	
	public function viewSaleReturnReport(){
		$input=Input::all();
		//echo '<pre>';print_r($input);exit;
		$from	=	$input['from'];
		$to		=	$input['to'];
		$reports= DB::select("select customerinfos.user_name as customer_name,empinfos.user_name as invoiced_employee,paymenttypes.payment_type_name, 
								each_item.*,(sum(each_item.item_loss_profit) - (each_item.less_amount)) as loss_profit  
								from
								 (select salereturninvoices.sale_r_invoice_id,salereturninvoices.cus_id,salereturninvoices.payment_type_id,salereturninvoices.created_by,salereturninvoices.less_amount,salereturninvoices.amount,salereturninvoices.transaction_date,salereturninvoices.status,salereturninvoices.created_at as return_invoiced_datetime,
								 			(sum(priceinfos.sale_price - priceinfos.purchase_price) * salereturntostocks.quantity)-salereturntostocks.discount as item_loss_profit,
										priceinfos.sale_price,priceinfos.purchase_price,salereturntostocks.quantity
									from salereturntostocks
								   left join salereturninvoices on salereturninvoices.sale_r_invoice_id=salereturntostocks.sale_r_invoice_id
								   left join priceinfos on priceinfos.price_id=salereturntostocks.price_id
								   WHERE salereturninvoices.transaction_date
								   BETWEEN('$from')AND('$to')
								   group by salereturntostocks.i_sale_return_id) as each_item
								left join customerinfos on customerinfos.cus_id=each_item.cus_id
								left join empinfos on empinfos.emp_id=each_item.created_by
								left join paymenttypes on paymenttypes.payment_type_id=each_item.payment_type_id
								group by each_item.sale_r_invoice_id");

		return View::make('reports.details.saleReturnReport',compact('reports','from','to'));
	}
	
	public function saleReturnDetailsReport($saleReturnInvoiceId){
		$company_info = DB::table('companyprofiles')
						->select('company_name', 'address', 'mobile')
						->first();
		
		$receipt_info = DB::table('salereturninvoices as sri')
					->leftjoin('empinfos', 'empinfos.emp_id', '=', 'sri.created_by')
					->leftjoin('customerinfos as ci', 'ci.cus_id', '=', 'sri.cus_id')
					->leftjoin('paymenttypes as pt', 'pt.payment_type_id', '=', 'sri.payment_type_id')
					->select('sri.sale_r_invoice_id', 'sri.amount', 'sri.less_amount', 'sri.transaction_date', 'sri.created_at',  'ci.user_name as customer_name', 'pt.payment_type_name', 'empinfos.user_name as invoiced_employee')
					->where('sri.sale_r_invoice_id', $saleReturnInvoiceId)
					->where('sri.status', '=', 1)
					->orderBy('sri.transaction_date', 'desc')
					->first();
						
		$receipt_returnItem_infos = DB::table('salereturntostocks as srs')
					->join('salereturninvoices as sri', 'srs.sale_r_invoice_id', '=', 'sri.sale_r_invoice_id')
					->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'srs.item_id')
					->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'srs.price_id')
					->select('srs.quantity', 'srs.discount', 'srs.amount', 'srs.tax', 'ii.item_name', 'pi.sale_price')
					->where('srs.sale_r_invoice_id', $saleReturnInvoiceId)
					->where('srs.status', 1)
					->get(); 
		return View::make('reports.details.saleReturnReportDetails', compact('company_info', 'receipt_info', 'receipt_returnItem_infos'));
	}
	
    public function getIncomeReport() {
		$title = ':: POSv2 :: - Other Income Reports';
        return View::make('reports.details.incomeReportDetails', compact('title'));
	}
    public function viewIncomeReport(){
		$title = ':: POSv2 :: - Other Income Reports';
		$input	=	Input::all();
		$from	=	$input['from'];
		$to		=	$input['to'];
		$reports= DB::table('otherincomes')
				->leftjoin('empinfos', 'empinfos.emp_id', '=', 'otherincomes.created_by')
				->leftJoin('incomeexpensetype', 'incomeexpensetype.type_id', '=', 'otherincomes.income_type_id')
				->select('incomeexpensetype.type_name', 'otherincomes.amount', 'otherincomes.comment', 'otherincomes.date', 'empinfos.user_name as employee_name')
				->where('otherincomes.status', 1)
				->where('incomeexpensetype.used_for', 1)
				->whereBetween('otherincomes.date', array($from, $to))
				->orderBy('otherincomes.date', 'desc')
				->get();
		return View::make('reports.details.incomeReportDetails',compact('reports','from','to', 'title'));
	}
	
    public function getExpenseReport() {
		$title = ':: POSv2 :: - Other Expense Reports';
        return View::make('reports.details.expenseReportDetails', compact('title'));
	}
    public function viewExpenseReport(){
		$title = ':: POSv2 :: - Other Expense Reports';
		$input	=	Input::all();
		$from	=	$input['from'];
		$to		=	$input['to'];
		$reports= DB::table('otherexpenses')
				->leftjoin('empinfos', 'empinfos.emp_id', '=', 'otherexpenses.created_by')
				->leftJoin('incomeexpensetype', 'incomeexpensetype.type_id', '=', 'otherexpenses.expense_type_id')
				->select('incomeexpensetype.type_name', 'otherexpenses.amount','otherexpenses.other_expense_id', 'otherexpenses.comment', 'otherexpenses.date', 'empinfos.user_name as employee_name')
				->where('otherexpenses.status', 1)
				->where('incomeexpensetype.used_for', 2)
				->whereBetween('otherexpenses.date', array($from, $to))
				->orderBy('otherexpenses.date', 'desc')
				->get();
		return View::make('reports.details.expenseReportDetails',compact('reports','from','to', 'title'));
	}

	/*
	*	Return To Godown Reports
	*/
	 public function getReturnToGodowonReport(){
		$title = ':: POSv2 :: - Return To Godown Reports';
		return View::make('reports.details.returnToGodownReport', compact('title'));
	}

    public function viewReturnToGodowonReport() {
		$input	=	Input::all();
		$from	=	$input['from'];
		$to		=	$input['to'];

		$reports= DB::table('returnreceivingitems')
				->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'returnreceivingitems.item_id')
				->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'returnreceivingitems.price_id')
				->leftjoin('empinfos', 'empinfos.emp_id', '=', 'returnreceivingitems.returning_by')
				->select('returnreceivingitems.r_receiving_item_id','returnreceivingitems.item_id','returnreceivingitems.quantity','returnreceivingitems.returning_date', 'returnreceivingitems.status','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id','empinfos.user_name as sent_by')
				->whereBetween('returnreceivingitems.returning_date', array($input['from'], $input['to']))
				->orderBy('returnreceivingitems.returning_date', 'desc')
				->get();

		return View::make('reports.details.returnToGodownReport',compact('reports','from','to'));
	}
	
	/*
	 * Return Receiving Reports
	*/
	public function getReturnReceivingReport()	{
		return View::make('reports.details.returnReceivingReport');
	}
	
	public function viewReturnReceivingReport(){
		$input	=	Input::all();
		$from	=	$input['from'];
		$to		=	$input['to'];

		$reports= DB::table('returnreceivingitems')
			->leftjoin('iteminfos', 'iteminfos.item_id', '=', 'returnreceivingitems.item_id')
			->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'returnreceivingitems.price_id')
			->leftjoin('empinfos', 'empinfos.emp_id', '=', 'returnreceivingitems.receive_cancel_by')
			->select('returnreceivingitems.r_receiving_item_id','returnreceivingitems.item_id','returnreceivingitems.quantity','returnreceivingitems.receive_cancel_date','iteminfos.item_name','priceinfos.sale_price','priceinfos.purchase_price','priceinfos.price_id','empinfos.user_name as receiving_by')
			->where('returnreceivingitems.status', '=', 0)
			->whereBetween('returnreceivingitems.receive_cancel_date', array($input['from'], $input['to']))
			->orderBy('returnreceivingitems.receive_cancel_date', 'desc')
			->get();

		return View::make('reports.details.returnReceivingReport',compact('reports','from','to'));
	}
	
	/*
	 *  Purchase Return Reports
	*/
	
	public function getReturnPurchaseReport(){
        return View::make('reports.details.purchaseReturnReport');
	}	
	public function viewPurchaseReturnReport(){
		$input=Input::all();
		$from	=	$input['from'];
		$to		=	$input['to'];
		$reports= DB::table('supplierreturninvoices as srri')
			->leftjoin('empinfos', 'empinfos.emp_id', '=', 'srri.created_by')
			->leftjoin('supplierinfos', 'supplierinfos.supp_id', '=', 'srri.supp_id')
			->leftjoin('paymenttypes as pt', 'pt.payment_type_id', '=', 'srri.payment_type_id')
			->select('srri.sup_r_invoice_id', 'srri.amount', 'srri.less_amount', 'srri.transaction_date', 'srri.status',  'supplierinfos.supp_or_comp_name as supplier_name', 'pt.payment_type_name', 'empinfos.user_name as invoiced_employee', 'srri.created_at as return_purchase_datetime')
			->where('srri.status', '=', 1)
			->whereBetween('srri.transaction_date', array($from, $to))
			->orderBy('srri.transaction_date', 'desc')
			->get();
		return View::make('reports.details.purchaseReturnReport',compact('reports','from','to'));
	}
	
	public function purchaseReturnDetailsReport($purchaseReturnInvoiceId){
		$company_info = DB::table('companyprofiles')
			->select('company_name', 'address', 'mobile')
			->first();
		
		$receipt_info = DB::table('supplierreturninvoices as srri')
			->leftjoin('empinfos', 'empinfos.emp_id', '=', 'srri.created_by')
			->leftjoin('supplierinfos', 'supplierinfos.supp_id', '=', 'srri.supp_id')
			->leftjoin('paymenttypes as pt', 'pt.payment_type_id', '=', 'srri.payment_type_id')
			->select('srri.sup_r_invoice_id', 'srri.amount', 'srri.less_amount', 'srri.transaction_date', 'srri.status',  'supplierinfos.supp_or_comp_name as supplier_name', 'pt.payment_type_name', 'empinfos.user_name as invoiced_employee', 'srri.created_at')
			->where('srri.sup_r_invoice_id', $purchaseReturnInvoiceId)
			->where('srri.status', '=', 1)
			->orderBy('srri.transaction_date', 'desc')
			->first();
						
		$receipt_return_item_infos = DB::table('purchasereturntosupplier as prts')
			->join('supplierreturninvoices as srri', 'prts.sup_r_invoice_id', '=', 'srri.sup_r_invoice_id')
			->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'prts.item_id')
			->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'prts.price_id')
			->select('prts.quantity', 'prts.discount', 'prts.amount', 'ii.item_name', 'pi.purchase_price')
			->where('prts.sup_r_invoice_id', $purchaseReturnInvoiceId)
			->where('prts.status', 1)
			->get(); 
		return View::make('reports.details.purchaseReturnReportDetails', compact('company_info', 'receipt_info', 'receipt_return_item_infos'));
	}
	/*
	 * Damage Products Details Reports
	*/
	public function getDamageReport(){
		$title = ':: POSv2 :: - Damage Products Report';
		$input = Input::all();

		if(!$input){
			$from	=	date('Y-m-d');
			$to		=	date('Y-m-d');
		}else{
			$from	=	$input['from'];
			$to		=	$input['to'];
		}
		$reports =  DB::table('damageinvoices')							
						->leftjoin('empinfos', 'empinfos.emp_id', '=', 'damageinvoices.created_by')
						->whereBetween('damageinvoices.date', array($from, $to))
						->where('damageinvoices.status', 1)
						->get([
							'damageinvoices.damage_invoice_id',
							'damageinvoices.amount',
							'damageinvoices.date',
							'empinfos.user_name'
						]);
		return View::make('reports.details.damageProductReport', compact('title','from','to','reports'));
	}
	
	public function getDamageDetailsReport($damageInvoiceId){
		$company_info = DB::table('companyprofiles')
						->select('company_name', 'address', 'mobile')
						->first();
		
		$receipt_info = DB::table('damageinvoices')
						->leftjoin('empinfos', 'empinfos.emp_id', '=', 'damageinvoices.created_by')
						->select('damageinvoices.damage_invoice_id','damageinvoices.amount', 'damageinvoices.date', 'damageinvoices.created_at', 'empinfos.user_name')
						->where('damageinvoices.status', 1)
						->where('damageinvoices.damage_invoice_id', $damageInvoiceId)
						->first();
						
		$receipt_item_infos = DB::table('itemdamages')
							->join('damageinvoices', 'itemdamages.damage_invoice_id', '=', 'damageinvoices.damage_invoice_id')
							->leftJoin('iteminfos', 'iteminfos.item_id', '=', 'itemdamages.item_id')
							->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'itemdamages.price_id')
							->select('itemdamages.quantity', 'itemdamages.amount', 'iteminfos.item_name', 'priceinfos.purchase_price')
							->where('itemdamages.status', 1)
							->where('itemdamages.damage_invoice_id', $damageInvoiceId)
							->get();
		return View::make('reports.details.damageReportDetails', compact('company_info', 'receipt_info', 'receipt_item_infos'));
	}
	public function viewAllItem() {
        return View::make('reports.details.viewAllItem');
    }
	 public function viewAllItemDataJsonFormat(){
    	$all_items=DB::select("select iteminfos.item_id,iteminfos.item_name,iteminfos.upc_code, priceinfos.purchase_price,priceinfos.sale_price,iteminfos.price_id, COALESCE(all_item.g_qty, 0)as g_qty, COALESCE(all_item.s_qty, 0)as s_qty,sum(COALESCE(all_item.s_qty, 0) + COALESCE(all_item.g_qty, 0)) as total_qty
					from iteminfos
					left join priceinfos on priceinfos.price_id=iteminfos.price_id
					left join (
						select stk.item_id,stk.price_id,stk.purchase_price,stk.sale_price,stk.item_name,stk.upc_code,stk.s_qty,COALESCE(gdn.g_qty, 0)as g_qty, sum(COALESCE(stk.s_qty, 0) + COALESCE(gdn.g_qty, 0)) as total_qty 
						 from(
						         select iteminfos.item_id,iteminfos.price_id,p.purchase_price,p.sale_price, iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(s.available_quantity), 0) as s_qty
						         from iteminfos
						         left outer join stockitems as s on s.item_id=iteminfos.item_id
						         left join priceinfos as p on p.price_id=iteminfos.price_id
						         where s.`status`=1
						         group by iteminfos.item_id
						         ) 
						         as stk
						         left outer join(
						                 select iteminfos.item_id,iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(g.available_quantity), 0)  as g_qty
						                 from iteminfos
						                 right outer join godownitems as g on g.item_id=iteminfos.item_id
						                 where g.`status`=1
						                 group by iteminfos.item_id
						         ) 
						         as gdn on gdn.item_id=stk.item_id
						 group by stk.item_id
						 order by stk.item_name asc
						 ) as all_item on iteminfos.item_id=all_item.item_id
					where iteminfos.status=1
					group by iteminfos.item_id
					order by iteminfos.item_name asc;");
		return Response::json(['data'=>$all_items]);
    }
    
    public function viewAllItemCategoryWise() {
            $all_items=DB::select("select c.category_id,c.category_name, sum(result.s_qty) as t_s_qty, sum(result.s_q_amount) as t_s_q_amount,sum(result.g_qty) as t_g_qty,sum(result.g_q_amount) as t_g_q_amount,sum(result.total_qty) as total_qty
from(
		select stk.item_id, stk.category_id, stk.price_id,stk.purchase_price,stk.sale_price,stk.item_name,stk.upc_code,stk.s_qty,(stk.s_qty*stk.purchase_price) as s_q_amount ,COALESCE(gdn.g_qty, 0)as g_qty,(COALESCE(gdn.g_qty, 0)*stk.purchase_price) as g_q_amount, sum(COALESCE(stk.s_qty, 0) + COALESCE(gdn.g_qty, 0)) as total_qty 
		    from(
		            select iteminfos.item_id, iteminfos.category_id, iteminfos.price_id,p.purchase_price,p.sale_price, iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(s.available_quantity), 0) as s_qty
		            from iteminfos
		            left outer join stockitems as s on s.item_id=iteminfos.item_id
		            left join priceinfos as p on p.price_id=iteminfos.price_id
		            where s.`status`=1
		            group by iteminfos.item_id
		            ) 
		            as stk
		            left outer join(
		                    select iteminfos.item_id,iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(g.available_quantity), 0)  as g_qty
		                    from iteminfos
		                    right outer join godownitems as g on g.item_id=iteminfos.item_id
		                    where g.`status`=1
		                    group by iteminfos.item_id
		            ) 
		            as gdn on gdn.item_id=stk.item_id
		    group by stk.item_id
		    order by stk.item_id asc
			 ) as result
			 left join itemcategorys as c on c.category_id=result.category_id
			 group by result.category_id");
//            echo'<pre>';print_r($all_items);
//            echo 'hi';exit;
            return View::make('reports.summary.viewAllItemCategoryWise', compact('all_items'));
 
    }
    
        
        
	
	/*
	 *  Summary Reports
	*/
	public function getSummaryFullReport(){
		$title = ':: POSv2 :: - Full Summary Reports';
                $input=Input::all();

                if(!$input){
                    $from=date('Y-m-d');
                    $to=date('Y-m-d');
                }
                else{
                   $from=$input['from'];
                    $to=$input['to'];
                }
               // $sale_raw = DB::select("select sum(saleinvoices.discount) as sale_discount,
               //                            sum(saleinvoices.point_use_taka) as sale_point_use_taka,
               //                            sum(saleinvoices.amount) as sale_amount,
               //                            sum(saleinvoices.pay) as sale_pay,
               //                            sum(saleinvoices.due) as sale_due
               //                      from saleinvoices
               //                      WHERE  date
               //                              BETWEEN('2015-04-05')AND('2015-04-05')");

                $sale=  DB::select("select sum(total_item.discount) as sale_discount,sum(total_item.point_use_taka) as sale_point_use_taka,sum(total_item.amount) as sale_amount,sum(total_item.pay) as sale_pay,sum(total_item.due) as sale_due,sum(total_item.invoice_profit)as total_sale_profit
									from(
										select customerinfos.user_name as customer_name,empinfos.user_name as invoiced_employee,
												each_item.*,(sum(each_item.item_profit) - (each_item.discount+each_item.point_use_taka)) as invoice_profit  
										from
										 (select saleinvoices.sale_invoice_id,saleinvoices.cus_id,saleinvoices.created_by,saleinvoices.discount,saleinvoices.point_use_taka,saleinvoices.amount,saleinvoices.pay,saleinvoices.due,saleinvoices.date,saleinvoices.status,saleinvoices.created_at as invoiced_datetime,
										 			(sum((itemsales.amount/itemsales.quantity) - priceinfos.purchase_price) * itemsales.quantity)-itemsales.discount as item_profit,
												priceinfos.sale_price,priceinfos.purchase_price,itemsales.quantity
											from itemsales
										   left join saleinvoices on saleinvoices.sale_invoice_id=itemsales.sale_invoice_id
										   left join priceinfos on priceinfos.price_id=itemsales.price_id
										   WHERE  date
										   BETWEEN('$from')AND('$to')
										   group by itemsales.i_sale_id) as each_item
										left join customerinfos on customerinfos.cus_id=each_item.cus_id
										left join empinfos on empinfos.emp_id=each_item.created_by
										group by each_item.sale_invoice_id
										) as total_item;")[0];
                        //  $reports= DB::table('saleinvoices')
                        // ->leftjoin('empinfos', 'empinfos.emp_id', '=', 'saleinvoices.created_by')
                        // ->leftjoin('customerinfos', 'customerinfos.cus_id', '=', 'saleinvoices.cus_id')
                        // ->leftjoin('itemsales', 'itemsales.sale_invoice_id', '=', 'saleinvoices.sale_invoice_id')
                        // ->leftjoin('priceinfos', 'priceinfos.price_id', '=', 'itemsales.price_id')
                        // ->select('saleinvoices.sale_invoice_id','saleinvoices.cus_id','customerinfos.user_name as customer_name','saleinvoices.discount',
                        //         'saleinvoices.point_use_taka','saleinvoices.amount','saleinvoices.pay','saleinvoices.due',
                        //         'saleinvoices.date','saleinvoices.status','empinfos.user_name as invoiced_employee',
                        //         'saleinvoices.created_at as invoiced_datetime',
                        //         DB::raw('(sum((priceinfos.sale_price - priceinfos.purchase_price)*itemsales.quantity) - (saleinvoices.discount + saleinvoices.point_use_taka)) as profit')
                        //         )
                        // ->where('saleinvoices.status', '=', 1)
                        // ->whereBetween('saleinvoices.date', array($from, $to))
                        // ->groupBy('itemsales.sale_invoice_id')
                        // ->orderBy('saleinvoices.sale_invoice_id', 'desc')
                        // ->get();


						$salereturn=  DB::select("select sum(total_item.amount) as salereturn_amount,sum(total_item.less_amount) as salereturn_less,sum(total_item.loss_profit) as total_loss_profit
												from(
													select customerinfos.user_name as customer_name,empinfos.user_name as invoiced_employee,paymenttypes.payment_type_name, 
														each_item.*,(sum(each_item.item_loss_profit) - (each_item.less_amount)) as loss_profit  
														from
														 (select salereturninvoices.sale_r_invoice_id,salereturninvoices.cus_id,salereturninvoices.payment_type_id,salereturninvoices.created_by,salereturninvoices.less_amount,salereturninvoices.amount,salereturninvoices.transaction_date,salereturninvoices.status,salereturninvoices.created_at as return_invoiced_datetime,
														 			(sum(priceinfos.sale_price - priceinfos.purchase_price) * salereturntostocks.quantity)-salereturntostocks.discount as item_loss_profit,
																priceinfos.sale_price,priceinfos.purchase_price,salereturntostocks.quantity
															from salereturntostocks
														   left join salereturninvoices on salereturninvoices.sale_r_invoice_id=salereturntostocks.sale_r_invoice_id
														   left join priceinfos on priceinfos.price_id=salereturntostocks.price_id
														   WHERE salereturninvoices.transaction_date
														   BETWEEN('$from')AND('$to')
														   group by salereturntostocks.i_sale_return_id) as each_item
														left join customerinfos on customerinfos.cus_id=each_item.cus_id
														left join empinfos on empinfos.emp_id=each_item.created_by
														left join paymenttypes on paymenttypes.payment_type_id=each_item.payment_type_id
														group by each_item.sale_r_invoice_id) as total_item;")[0];

                // $salereturn=  DB::table('salereturninvoices')
                //          ->select(DB::raw('sum(salereturninvoices.amount) as salereturn_amount,
                //                            sum(salereturninvoices.less_amount) as salereturn_less'))
                //          ->whereBetween('salereturninvoices.transaction_date', array($from, $to))
                //          ->first();




//                $purchase_raw = DB::select("select sum(supinvoices.discount) as purchase_discount,
//                                                   sum(supinvoices.amount) as purchase_amount,
//                                                   sum(supinvoices.pay) as 	purchase_pay,
//                                                   sum(supinvoices.due) as purchase_due
//                                            from supinvoices
//                                            WHERE  transaction_date
//                                            BETWEEN('2015-04-05')AND('2015-04-05')");

                $purchase=  DB::table('supinvoices')
                         ->select(DB::raw('sum(supinvoices.discount) as purchase_discount,
                                           sum(supinvoices.amount) as purchase_amount,
                                           sum(supinvoices.pay) as purchase_pay,
                                           sum(supinvoices.due) as purchase_due'))
                         ->whereBetween('supinvoices.transaction_date', array($from, $to))
                         ->first();

//                $purchasereturn_raw = DB::select("select sum(supplierreturninvoices.less_amount) as purchasereturn_less,
//                                                         sum(supplierreturninvoices.amount) as purchasereturn_amount
//                                                    from supplierreturninvoices
//                                                    WHERE  transaction_date
//                                                    BETWEEN('2015-04-05')AND('2015-04-05')");

                $purchasereturn=  DB::table('supplierreturninvoices')
                         ->select(DB::raw('sum(supplierreturninvoices.less_amount) as purchasereturn_less,
                                           sum(supplierreturninvoices.amount) as purchasereturn_amount'))
                         ->whereBetween('supplierreturninvoices.transaction_date', array($from, $to))
                         ->first();



                $other_income=  DB::table('otherincomes')
                                ->where('otherincomes.status', 1)
				->whereBetween('otherincomes.date', array($from, $to))
                                ->sum('amount');
                $other_expense=  DB::table('otherexpenses')
                                ->where('otherexpenses.status', 1)
				->whereBetween('otherexpenses.date', array($from, $to))
                                ->sum('amount');
                $salary_given=  DB::table('empwisesalary')
                    ->where('empwisesalary.status', 1)
				    ->whereBetween('empwisesalary.date', array($from, $to))
                    ->sum('amount');
								
//                $damage_raw = DB::select("select sum(damageinvoices.amount) as damage_amount
//					 from damageinvoices
//               WHERE  damageinvoices.date
  //             BETWEEN('2015-04-03')AND('2015-04-30')");
//
                $damage=  DB::table('damageinvoices')
                         ->select(DB::raw('sum(damageinvoices.amount) as damage_amount'))
                         ->whereBetween('damageinvoices.date', array($from, $to))
                         ->first();
	
				$totalGodwonTk = DB::table('godownitems') 
									->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'godownitems.price_id')
									->select(DB::raw('sum(priceinfos.purchase_price * godownitems.available_quantity) as total_amount'))
									->where('godownitems.status', 1)
									->first();
									
				$totalStockTk = DB::table('stockitems') 
									->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
									->select(DB::raw('sum(priceinfos.purchase_price * stockitems.available_quantity) as total_amount'))
									->where('stockitems.status', 1)
									->first();
				/*  echo'<pre>';
				print_r($totalStockTk);exit; */  
                       $cusDuePayment	= DB::table('cusduepayments')
							->select(DB::raw('SUM(amount) as total_amount'))
							->whereBetween('date', array($from, $to))
							->where('status', 1)
							->first();
					$cusDueAmount = DB::table('customerinfos')
						->select(DB::raw('SUM(due) as total_cus_due'))
						->first();
					$supDueAmount = DB::table('supplierinfos')
						->select(DB::raw('SUM(due) as total_supp_due'))
						->first();
							
                    $supDuePayment	= DB::table('supduepayments')
                        ->select(DB::raw('SUM(amount) as total_amount'))
                        ->whereBetween('date', array($from, $to))
                        ->where('status', 1)
                        ->first();



		return View::make('reports.summary.fullReport', compact('title','from','to','other_expense','other_income','damage','purchasereturn','purchase','salereturn','sale', 'totalGodwonTk', 'totalStockTk', 'cusDueAmount', 'supDueAmount', 'salary_given', 'cusDuePayment', 'supDuePayment'));
	}
	public function dailyledger(){
		$title = ':: POSv2 :: - Full Summary Reports';
                $input=Input::all();
                if(!$input){
                    $from=date('Y-m-d');
                    $to=date('Y-m-d');
                }
                else{
                   $from=$input['from'];
                   $to=$input['from'];
                }
                $sale=  DB::select("select sum(total_item.discount) as sale_discount,sum(total_item.point_use_taka) as sale_point_use_taka,sum(total_item.amount) as sale_amount,sum(total_item.pay) as sale_pay,sum(total_item.due) as sale_due,sum(total_item.invoice_profit)as total_sale_profit
						from(
							select customerinfos.user_name as customer_name,empinfos.user_name as invoiced_employee,
									each_item.*,(sum(each_item.item_profit) - (each_item.discount+each_item.point_use_taka)) as invoice_profit  
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
							group by each_item.sale_invoice_id
							) as total_item;")[0];

					$salereturn=  DB::select("select sum(total_item.amount) as salereturn_amount,sum(total_item.less_amount) as salereturn_less,sum(total_item.loss_profit) as total_loss_profit
							from(
								select customerinfos.user_name as customer_name,empinfos.user_name as invoiced_employee,paymenttypes.payment_type_name, 
									each_item.*,(sum(each_item.item_loss_profit) - (each_item.less_amount)) as loss_profit  
									from
									 (select salereturninvoices.sale_r_invoice_id,salereturninvoices.cus_id,salereturninvoices.payment_type_id,salereturninvoices.created_by,salereturninvoices.less_amount,salereturninvoices.amount,salereturninvoices.transaction_date,salereturninvoices.status,salereturninvoices.created_at as return_invoiced_datetime,
									 			(sum(priceinfos.sale_price - priceinfos.purchase_price) * salereturntostocks.quantity)-salereturntostocks.discount as item_loss_profit,
											priceinfos.sale_price,priceinfos.purchase_price,salereturntostocks.quantity
										from salereturntostocks
									   left join salereturninvoices on salereturninvoices.sale_r_invoice_id=salereturntostocks.sale_r_invoice_id
									   left join priceinfos on priceinfos.price_id=salereturntostocks.price_id
									   WHERE salereturninvoices.transaction_date
									   BETWEEN('$from')AND('$to')
									   group by salereturntostocks.i_sale_return_id) as each_item
									left join customerinfos on customerinfos.cus_id=each_item.cus_id
									left join empinfos on empinfos.emp_id=each_item.created_by
									left join paymenttypes on paymenttypes.payment_type_id=each_item.payment_type_id
									group by each_item.sale_r_invoice_id) as total_item;")[0];

                $purchase=  DB::table('supinvoices')
                    ->select(DB::raw('sum(supinvoices.discount) as purchase_discount,
                                       sum(supinvoices.amount) as purchase_amount,
                                       sum(supinvoices.pay) as purchase_pay,
                                       sum(supinvoices.due) as purchase_due'))
                    ->whereBetween('supinvoices.transaction_date', array($from, $to))
                    ->first();
                $purchasereturn=  DB::table('supplierreturninvoices')
                    ->select(DB::raw('sum(supplierreturninvoices.less_amount) as purchasereturn_less,
                                       sum(supplierreturninvoices.amount) as purchasereturn_amount'))
                    ->whereBetween('supplierreturninvoices.transaction_date', array($from, $to))
                    ->first();
                $other_income=  DB::table('otherincomes')
	                ->where('otherincomes.status', 1)
					->whereBetween('otherincomes.date', array($from, $to))
	                ->sum('amount');
                $other_expense=  DB::table('otherexpenses')
		            ->where('otherexpenses.status', 1)
					->whereBetween('otherexpenses.date', array($from, $to))
		            ->sum('amount');
                $damage=  DB::table('damageinvoices')
                     ->select(DB::raw('sum(damageinvoices.amount) as damage_amount'))
                     ->whereBetween('damageinvoices.date', array($from, $to))
                     ->first();
	
				$totalGodwonTk = DB::table('godownitems') 
					->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'godownitems.price_id')
					->select(DB::raw('sum(priceinfos.purchase_price * godownitems.available_quantity) as total_amount'))
					->where('godownitems.status', 1)
					->first();
									
				$totalStockTk = DB::table('stockitems') 
					->leftJoin('priceinfos', 'priceinfos.price_id', '=', 'stockitems.price_id')
					->select(DB::raw('sum(priceinfos.purchase_price * stockitems.available_quantity) as total_amount'))
					->where('stockitems.status', 1)
					->first();
				/*  echo'<pre>';
				print_r($totalStockTk);exit; */  
                   $cusDuePayment	= DB::table('cusduepayments')
						->select(DB::raw('SUM(amount) as total_amount'))
						->whereBetween('date', array($from, $to))
						->where('status', 1)
						->first();
					$cusDueAmount = DB::table('customerinfos')
						->select(DB::raw('SUM(due) as total_cus_due'))
						->first();
					$supDueAmount = DB::table('supplierinfos')
						->select(DB::raw('SUM(due) as total_supp_due'))
						->first();
                    $supDuePayment	= DB::table('supduepayments')
                        ->select(DB::raw('SUM(amount) as total_amount'))
                        ->whereBetween('date', array($from, $to))
                        ->where('status', 1)
                        ->first();
		return View::make('reports.summary.dailyLedger', compact('title','from','to','other_expense','other_income','damage','purchasereturn','purchase','salereturn','sale', 'totalGodwonTk', 'totalStockTk', 'cusDueAmount', 'supDueAmount', 'cusDuePayment', 'supDuePayment'));
	}
    public function getSummarySales(){
		$title = ':: POSv2 :: - Sales Summary Reports';
                $input=Input::all();
                if(!$input){
                    $from=date('Y-m-d');
                    $to=date('Y-m-d');
                }else{
                    $from=$input['from'];
                    $to=$input['to'];
                }
                $reports= DB::select("select each_invoice_item.date,sum(each_invoice_item.discount) as sale_discount,sum(each_invoice_item.point_use_taka) as sale_point_use_taka,sum(each_invoice_item.amount) as sale_amount,sum(each_invoice_item.pay) as sale_pay,sum(each_invoice_item.due) as sale_due,sum(each_invoice_item.profit) as total_profit
									from(
										select customerinfos.user_name as customer_name,empinfos.user_name as invoiced_employee, each_item.*,(sum(each_item.item_profit) - (each_item.discount+each_item.point_use_taka)) as profit  
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
										group by each_item.sale_invoice_id) as each_invoice_item
									group by each_invoice_item.date;");
                        	

		return View::make('reports.summary.salesReport', compact('title','from','to','reports'));
	}
        public function getSummarySalesReturn(){
		$title = ':: POSv2 :: - Sales Return Summary Reports';
                $input=Input::all();
           // echo'<pre>';print_r($input);exit;
                if(!$input){
                    $from=date('Y-m-d');
                    $to=date('Y-m-d');
                }else{
                    $from=$input['from'];
                    $to=$input['to'];
                }

      $saleReturns= DB::select("select each_invoice_item.transaction_date as date ,sum(each_invoice_item.less_amount) as salereturn_less,sum(each_invoice_item.amount) as salereturn_amount,sum(each_invoice_item.loss_profit) as total_loss_profit
							from(
								select customerinfos.user_name as customer_name,empinfos.user_name as invoiced_employee,paymenttypes.payment_type_name, 
									each_item.*,(sum(each_item.item_loss_profit) - (each_item.less_amount)) as loss_profit  
									from
									 (select salereturninvoices.sale_r_invoice_id,salereturninvoices.cus_id,salereturninvoices.payment_type_id,salereturninvoices.created_by,salereturninvoices.less_amount,salereturninvoices.amount,salereturninvoices.transaction_date,salereturninvoices.status,salereturninvoices.created_at as return_invoiced_datetime,
									 			(sum(priceinfos.sale_price - priceinfos.purchase_price) * salereturntostocks.quantity)-salereturntostocks.discount as item_loss_profit,
											priceinfos.sale_price,priceinfos.purchase_price,salereturntostocks.quantity
										from salereturntostocks
									   left join salereturninvoices on salereturninvoices.sale_r_invoice_id=salereturntostocks.sale_r_invoice_id
									   left join priceinfos on priceinfos.price_id=salereturntostocks.price_id
									   WHERE salereturninvoices.transaction_date
									   BETWEEN('$from')AND('$to')
									   group by salereturntostocks.i_sale_return_id) as each_item
									left join customerinfos on customerinfos.cus_id=each_item.cus_id
									left join empinfos on empinfos.emp_id=each_item.created_by
									left join paymenttypes on paymenttypes.payment_type_id=each_item.payment_type_id
									group by each_item.sale_r_invoice_id
								) as each_invoice_item
								group by each_invoice_item.transaction_date");


		return View::make('reports.summary.salesReturnReport', compact('title','from','to','saleReturns'));
	}


         public function getSummaryPurchaseReports(){
			$title = ':: POSv2 :: - Purchases Summary Reports';
                $input=Input::all();
                
                if(!$input){
                    $from=date('Y-m-d');
                    $to=date('Y-m-d');
                }else{
                    $from=$input['from'];
                    $to=$input['to'];
                }

//                $purchases_raw = DB::select("select supinvoices.transaction_date as date,    sum(supinvoices.discount) as purchase_discount,
//                                                   sum(supinvoices.amount) as purchase_amount,
//                                                   sum(supinvoices.pay) as 	purchase_pay,
//                                                   sum(supinvoices.due) as purchase_due
//                                            from supinvoices
//                                            WHERE  transaction_date
//                                            BETWEEN('2015-04-01')AND('2015-04-07')
//                                            group by transaction_date;");

                $purchases=  DB::table('supinvoices')
                         ->select(DB::raw('supinvoices.transaction_date as date,    sum(supinvoices.discount) as purchase_discount,
                                                   sum(supinvoices.amount) as purchase_amount,
                                                   sum(supinvoices.pay) as 	purchase_pay,
                                                   sum(supinvoices.due) as purchase_due'))
                         ->whereBetween('supinvoices.transaction_date', array($from, $to))
                         ->groupBy('transaction_date')
                         ->get();

//                echo '<pre>';print_r($purchases_raw);
//                echo '<pre>';print_r($purchases);exit;


		return View::make('reports.summary.purchasesReport', compact('title','from','to','purchases'));
	}

        public function getSummaryPurchaseRetrunReports(){
		$title = ':: POSv2 :: - Purchases return Summary Reports';
                $input=Input::all();
                if(!$input){
                    $from=date('Y-m-d');
                    $to=date('Y-m-d');
                }else{
                    $from=$input['from'];
                    $to=$input['to'];
                }

//                $purchases_return_raw = DB::select("select supplierreturninvoices.transaction_date as date, sum(supplierreturninvoices.less_amount) as purchasereturn_less,
//                                                           sum(supplierreturninvoices.amount) as purchasereturn_amount
//                                                    from supplierreturninvoices
//                                                    WHERE  transaction_date
//                                                      BETWEEN('2015-04-01')AND('2015-04-06')
//                                                    group by transaction_date");

                $purchases_return=  DB::table('supplierreturninvoices')
                         ->select(DB::raw('supplierreturninvoices.transaction_date as date,
                                                           sum(supplierreturninvoices.less_amount) as purchasereturn_less,
                                                           sum(supplierreturninvoices.amount) as purchasereturn_amount'))
                         ->whereBetween('supplierreturninvoices.transaction_date', array($from, $to))
                         ->groupBy('transaction_date')
                         ->get();

               // echo '<pre>';print_r($purchases_return_raw);
               // echo '<pre>';print_r($purchases_return);exit;


		return View::make('reports.summary.purchasesReturnReport', compact('title','from','to','purchases_return'));
	}

        public function getSummaryOtherIncomeReports(){
		$title = ':: POSv2 :: - Other Income Summary Reports';
                $input=Input::all();
                if(!$input){
                    $from=date('Y-m-d');
                    $to=date('Y-m-d');
                }else{
                    $from=$input['from'];
                    $to=$input['to'];
                }

//                $otherIncome_raw = DB::select("select supplierreturninvoices.transaction_date as date, sum(supplierreturninvoices.less_amount) as purchasereturn_less,
//                                                           sum(supplierreturninvoices.amount) as purchasereturn_amount
//                                                    from supplierreturninvoices
//                                                    WHERE  transaction_date
//                                                      BETWEEN('2015-04-01')AND('2015-04-06')
//                                                    group by transaction_date");

              
                $otherIncome=  DB::table('otherincomes')
                         ->select(DB::raw('otherincomes.date, sum(otherincomes.amount) as incomeTotal'))
                         ->whereBetween('otherincomes.date', array($from, $to))
                         ->groupBy('date')
                         ->get();

               // echo '<pre>';print_r($otherIncome_raw);
                //echo '<pre>';print_r($other_income);exit;


		return View::make('reports.summary.otherIncome', compact('title','from','to','otherIncome'));
	}
	 public function getSummaryOtherExpenseReports(){
		$title = ':: POSv2 :: - Other Expense Summary Reports';
                $input=Input::all();
                if(!$input){
                    $from=date('Y-m-d');
                    $to=date('Y-m-d');
                }else{
                    $from=$input['from'];
                    $to=$input['to'];
                }
  
                $otherExpense=  DB::table('otherexpenses')
                         ->select(DB::raw('otherexpenses.date, sum(otherexpenses.amount) as expenseTotal'))
                         ->whereBetween('otherexpenses.date', array($from, $to))
                         ->groupBy('date')
                         ->get();

               // echo '<pre>';print_r($otherIncome_raw);
                //echo '<pre>';print_r($other_income);exit;


		return View::make('reports.summary.otherExpense', compact('title','from','to','otherExpense'));
	}
   /*  public function getEmpSaleReports(){
		$title = ':: POSv2 :: - Employees Sales Report';
		$input=Input::all();
		if(!$input){
			$from=date('Y-m-d');
			$to=date('Y-m-d');
		}else{
			$from=$input['from'];
			$to=$input['to'];
		}
		$empSales =  DB::table('saleinvoices')
				 ->leftjoin('empinfos', 'empinfos.emp_id', '=', 'saleinvoices.created_by')
				 ->select('empinfos.user_name',DB::raw('sum(saleinvoices.discount) as total_discount,sum(saleinvoices.point_use_taka) as total_point_use_taka,sum(saleinvoices.amount) as total_amount,sum(saleinvoices.pay) as total_pay,sum(saleinvoices.due) as total_due'))
				 ->whereBetween('saleinvoices.date', array($from, $to))
				 ->where('saleinvoices.status', 1)
				 ->groupBy('saleinvoices.created_by')
				 ->get();
		
		$empSalesReturn =  DB::table('salereturninvoices')
				 ->leftjoin('empinfos', 'empinfos.emp_id', '=', 'salereturninvoices.created_by')
				 ->select('empinfos.user_name',DB::raw('sum(salereturninvoices.amount) as sr_amount'))
				 ->whereBetween('salereturninvoices.transaction_date', array($from, $to))
				 ->where('salereturninvoices.status', 1)
				 ->groupBy('salereturninvoices.created_by')
				 ->get();
                        
		$datas = array();
		$j	   = count($empSalesReturn);	
		
		foreach($empSales as $sale){			
			$temp = array();
			$temp['user_name']		=	$sale->user_name;
			$temp['total_discount'] =	$sale->total_discount;
			$temp['total_point_use_taka'] = $sale->total_point_use_taka;
			$temp['total_amount']	=	$sale->total_amount;
			$temp['total_pay']		=	$sale->total_pay;
			$temp['total_due']		=	$sale->total_due;
			$temp['sr_amount']		=	0;
			
			for($i = $j-1; $i >= 0; $i--){
				 $cmp = strcmp(@$empSalesReturn[$i]->user_name,$temp['user_name']);
				
				if(!$cmp){
					$temp['sr_amount'] = $empSalesReturn[$i]->sr_amount;
					unset($empSalesReturn[$i]);
					$i--;
				} 
			}
			array_push($datas, $temp);			
		}
		foreach($empSalesReturn as $saleReturn){
			$temp = array();
			$temp['user_name']		= $saleReturn->user_name;
			$temp['total_discount'] = 0;
			$temp['total_point_use_taka'] = 0;
			$temp['total_amount'] 	= 0;
			$temp['total_pay'] 		= 0 ;
			$temp['total_due'] 		= 0 ;
			$temp['sr_amount']  	= $saleReturn->sr_amount;
			array_push($datas, $temp);
		}
                       
		return View::make('reports.summary.empSalesReport', compact('title','from','to','datas'));
	} */
	
	public function saleOrderReport(){
        return View::make('reports.details.saleOrderReport');
	}
	public function viewSaleOrderReport()
	{
		$input=Input::all();
		$from=$input['from'];
		$to=$input['to'];
		$reports= DB::table('saleinvoices_order')
				->leftJoin('empinfos', 'empinfos.emp_id', '=', 'saleinvoices_order.created_by')
				->leftJoin('customerinfos', 'saleinvoices_order.cus_id', '=', 'customerinfos.cus_id')
				->select([
					'saleinvoices_order.sale_order_invoice_id',
					'saleinvoices_order.amount',
					'saleinvoices_order.date',
					'saleinvoices_order.created_at as invoice_time',
					'empinfos.user_name as invoice_by',
					'customerinfos.full_name as customer_name',
				])
				->where('saleinvoices_order.status', '=', 1)
				->whereBetween('saleinvoices_order.date', array($from, $to))
				->orderBy('saleinvoices_order.date', 'desc')
				->get();
	//echo '<pre>';print_r($reports);exit;
			return View::make('reports.details.saleOrderReport',compact('reports','from','to'));
	}
	public function saleOrderDetailsReport($saleReportInvoiceId){
		$company_info = DB::table('companyprofiles')
			->select('company_name', 'address', 'mobile')
			->first();
		// return $saleReportInvoiceId;
		$receipt_info= DB::table('saleinvoices_order')
			->leftJoin('empinfos', 'empinfos.emp_id', '=', 'saleinvoices_order.created_by')
			->leftJoin('customerinfos', 'saleinvoices_order.cus_id', '=', 'customerinfos.cus_id')
			->select([
				'saleinvoices_order.sale_order_invoice_id',
				'saleinvoices_order.amount',
				'saleinvoices_order.date',
				'saleinvoices_order.created_at as invoice_time',
				'empinfos.user_name as invoice_by',
				'customerinfos.full_name as customer_name',
			])
			->where('saleinvoices_order.status', '=', 1)
			->where('saleinvoices_order.sale_order_invoice_id', $saleReportInvoiceId)
			->first();
		// dd($receipt_info);
		$receipt_item_infos = DB::table('itemsales_order')
			->join('saleinvoices_order', 'itemsales_order.sale_order_invoice_id', '=', 'saleinvoices_order.sale_order_invoice_id')
			->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'itemsales_order.item_id')
			->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'itemsales_order.price_id')
			->select('itemsales_order.quantity', 'itemsales_order.amount', 'ii.item_name', 'pi.sale_price')
			->where('itemsales_order.sale_order_invoice_id', $saleReportInvoiceId)
			->where('itemsales_order.status', 1)
			->get(); 
		return View::make('reports.details.saleOrderReportDetails', compact('company_info', 'receipt_info', 'receipt_item_infos'));
	}

	public function saleOrderReportReceipt($saleOrderInvoiceId){
		$company_info = DB::table('companyprofiles')
			->select('company_name', 'address', 'mobile')
			->first();
		$receipt_info = DB::table('saleinvoices_order as sio')
			->leftjoin('empinfos as ei', 'ei.emp_id', '=', 'sio.created_by')
			->leftJoin('customerinfos as ci', 'ci.cus_id', '=', 'sio.cus_id')
			->leftJoin('customertypes as ct', 'ct.cus_type_id', '=', 'ci.cus_type_id')
			->leftJoin('paymenttypes as pt', 'pt.payment_type_id', '=', 'sio.payment_type_id')
			->select('sio.sale_order_invoice_id', 'sio.discount', 'sio.point_use_taka', 'sio.amount', 'sio.pay', 'sio.due', 'sio.pay_note', 'sio.date', 'sio.created_at', 'ci.present_address','ci.full_name as customer_name', 'ct.point_unit', 'pt.payment_type_name', 'ei.user_name as invoiced_employee')
			->where('sio.status', 1)
			->where('sio.sale_order_invoice_id', $saleOrderInvoiceId)
			->first();
						
		$receipt_item_infos = DB::table('itemsales_order as iso')
			->join('saleinvoices_order as sio', 'iso.sale_order_invoice_id', '=', 'sio.sale_order_invoice_id')
			->leftJoin('iteminfos as ii', 'ii.item_id', '=', 'iso.item_id')
			->leftJoin('priceinfos as pi', 'pi.price_id', '=', 'iso.price_id')
			->select(['iso.quantity', 'iso.discount', 'iso.amount', 'ii.item_name', 'iso.tax', 'pi.sale_price'])
			->where('iso.status', 1)
			->where('iso.sale_order_invoice_id', $saleOrderInvoiceId)
			->get();
		return View::make('reports.details.saleOrderDetailsReceipt', compact('company_info', 'receipt_info', 'receipt_item_infos'));
	}
//@@==============Sale order report end @@============ //

    public function getEmpSaleReports(){
		$title = ':: POSv2 :: - Employees Sales Report';
		$input=Input::all();
		if(!$input){
			$from	=	date('Y-m-d');
			$to	=date('Y-m-d');
		}else{
			$from=$input['from'];
			$to=$input['to'];
		}
		
		$emp_id   = Input::get('emp_id');
		
		$empList = array(
			'' => 'Please Select Employee') + DB::table('empinfos')
								->where('status', 1)
								->orderBy('user_name', 'asc')
								->lists('user_name', 'emp_id');	
		
		//$emp_name = 	isset($input['user_name']);	
		
		$emp_name = DB::table('empinfos')
						->where('status', 1)
						->where('emp_id', $emp_id)
						->first(['user_name']);
		//echo '<pre>'; print_r($input); exit;	
		$sales =  DB::table('saleinvoices')
					->select(DB::raw('sum(saleinvoices.point_use_taka) as sale_point_use_taka,
										sum(saleinvoices.discount) as sale_discount,
										sum(saleinvoices.amount) as sale_amount,
										sum(saleinvoices.pay) as sale_pay,
										sum(saleinvoices.due) as sale_due'))
					->where('saleinvoices.status', 1)
					->where('saleinvoices.created_by', $emp_id)
					->whereBetween('saleinvoices.date', array($from, $to))
					->first();		

		$salereturn=  DB::table('salereturninvoices')
						->select(DB::raw('sum(salereturninvoices.amount) as salereturn_amount,
											sum(salereturninvoices.less_amount) as salereturn_less'))
						->where('salereturninvoices.status', 1)
						->where('salereturninvoices.created_by', $emp_id)
						->whereBetween('salereturninvoices.transaction_date', array($from, $to))
						->first();

		$purchase=  DB::table('supinvoices')
						->select(DB::raw('sum(supinvoices.discount) as purchase_discount,
											sum(supinvoices.amount) as purchase_amount,
											sum(supinvoices.pay) as purchase_pay,
											sum(supinvoices.due) as purchase_due'))
						->where('supinvoices.status', 1)
						->where('supinvoices.created_by', $emp_id)
						->whereBetween('supinvoices.transaction_date', array($from, $to))
						->first();

		$purchasereturn=  DB::table('supplierreturninvoices')
							->select(DB::raw('sum(supplierreturninvoices.less_amount) as purchasereturn_less,
												sum(supplierreturninvoices.amount) as purchasereturn_amount'))
							->where('supplierreturninvoices.status', 1)
							->where('supplierreturninvoices.created_by', $emp_id)
							->whereBetween('supplierreturninvoices.transaction_date', array($from, $to))
							->first();

		$other_income=  DB::table('otherincomes')
							->where('otherincomes.status', 1)
							->where('otherincomes.created_by', $emp_id)
							->whereBetween('otherincomes.date', array($from, $to))
							->sum('amount');
							
		$other_expense=  DB::table('otherexpenses')
							->where('otherexpenses.status', 1)
							->where('otherexpenses.created_by', $emp_id)
							->whereBetween('otherexpenses.date', array($from, $to))
							->sum('amount');

		$damage=  DB::table('damageinvoices')
							->select(DB::raw('sum(damageinvoices.amount) as damage_amount'))
							->where('damageinvoices.status', 1)
							->where('damageinvoices.created_by', $emp_id)
							->whereBetween('damageinvoices.date', array($from, $to))
							->first();
		
		$cusDuePayment	= DB::table('cusduepayments')
							->select(DB::raw('SUM(amount) as total_amount'))
							->where('created_by', $emp_id)
							->whereBetween('date', array($from, $to))
							->where('status', 1)
							->first();
							
		$supDuePayment	= DB::table('supduepayments')
							->select(DB::raw('SUM(amount) as total_amount'))
							->where('created_by', $emp_id)
							->whereBetween('date', array($from, $to))
							->where('status', 1)
							->first();
					
		return View::make('reports.summary.empSalesReport', compact('title','from','to', 'empList','other_expense','other_income','damage','purchasereturn','purchase','salereturn','sales','emp_name', 'cusDuePayment', 'supDuePayment'));
	}//


	public function EmpDetailSaleReport()
	{	

		$from=date("Y-m-d");
		$to=date("Y-m-d");
		$emp_name = DB::table('empinfos')
						->select(['empinfos.user_name','empinfos.emp_id'])
						->where('status', 1)
						->get();
		return View::make('reports.summary.empDetailSalesReport', compact('emp_name','from','to'));
	}

	public function viewEmpDtlSalesReport()
	{
			$input=Input::all();

			$emp_name = DB::table('empinfos')
						->select(['empinfos.user_name','empinfos.emp_id'])
						->where('status', 1)
						->get();

		 	$from=$input['from'];
            $to=$input['to'];
            $emp_id=$input['emp_id'];
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
										AND saleinvoices.created_by =$emp_id
									   group by itemsales.i_sale_id) as each_item
									left join customerinfos on customerinfos.cus_id=each_item.cus_id
									left join empinfos on empinfos.emp_id=each_item.created_by
									group by each_item.sale_invoice_id 
									");
                        
			return View::make('reports.summary.empDetailSalesReport',compact('reports','emp_name','from','to'));
	}
    
	/*
	 * Damage Products Summary Report
	*/ 
	public function getSummaryDamageReports(){
		$title = ':: POSv2 :: - Damage Products Summary Report';
		$input=Input::all();
		if(!$input){
			$from = date('Y-m-d');
			$to	  = date('Y-m-d');
		}else{
			$from =	$input['from'];
			$to	  =	$input['to'];
		}
		$damageReports = DB::table('damageinvoices')
							->select(DB::raw('damageinvoices.date,
										   sum(damageinvoices.amount) as damage_amount'))
							->whereBetween('damageinvoices.date', array($from, $to))
							->groupBy('date')
							->get();
		return View::make('reports.summary.damageProductsReport', compact('title','from','to','damageReports'));
	}



	
	public function itemWiseSalesReport(){
		$title = ':: POSv2 :: - Item wise sales report';
        $input=Input::all();
        if(!$input){
            $from = date('Y-m-d');
            $to   =date('Y-m-d');
        }else{
            $from = $input['from'];
            $to = $input['to'];
        }                      		
		return View::make('reports.summary.itemWiseSalesReport', compact('title','from','to'));
	}

	public function getItemWiseSalesReportJsonFormat($from = null, $to = null){
		$items =  DB::table('itemsales')
                ->leftjoin('iteminfos as i', 'i.item_id', '=', 'itemsales.item_id')
                ->leftjoin('priceinfos', 'i.price_id', '=', 'priceinfos.price_id')
                ->leftjoin('saleinvoices as si', 'si.sale_invoice_id', '=', 'itemsales.sale_invoice_id')
                ->select(DB::raw('i.upc_code,i.item_name,itemsales.item_id,sum(itemsales.discount) as total_discount, sum(itemsales.quantity) as total_quantity, sum(itemsales.amount) as total_amount,sum(((priceinfos.sale_price-priceinfos.purchase_price)-itemsales.discount)*itemsales.quantity)as profit'))
                 ->where('itemsales.status', '=', 1)
				 ->whereBetween('si.date', array($from, $to))
				 ->groupBy('itemsales.item_id')
				 ->orderBy('i.item_name','ASC')
                 ->get();
        return Response::json(['data'=>$items]);
	}



	public function categoryWiseSalesReport(){
		$title = ':: POSv2 :: - Category Wise Sales Reports';
        $input=Input::all();
        if(!$input){
            $from=date('Y-m-d');
            $to=date('Y-m-d');
        }else{
            $from=$input['from'];
            $to=$input['to'];
        }
        $items = DB::select(
    			"select itemsales.i_sale_id,iteminfos.category_id,itemcategorys.category_name,sum(itemsales.discount) as total_discount,sum(itemsales.amount) as total_sales, 
    			sum(((priceinfos.sale_price-priceinfos.purchase_price)-itemsales.discount)*itemsales.quantity)as profit,SUM((priceinfos.purchase_price*itemsales.quantity)) as purchase_price,priceinfos.sale_price
				from itemsales
				INNER JOIN iteminfos ON  itemsales.item_id = iteminfos.item_id
				INNER JOIN priceinfos on  iteminfos.price_id=priceinfos.price_id
				INNER JOIN itemcategorys on  iteminfos.category_id=itemcategorys.category_id
				INNER JOIN saleinvoices on  itemsales.sale_invoice_id=saleinvoices.sale_invoice_id
				where itemsales.status = 1
				and (saleinvoices.date BETWEEN '$from' and '$to' )
				group by iteminfos.category_id;"
        	);
		return View::make('reports.summary.categoryWiseSalesReport', compact('title','from','to','items'));
	}
	
	
	public function spplierWisePurchase(){
		$title = ':: POSv2 :: - Supplier Wise Purchase Report';
        $input=Input::all();
        if(!$input){
            $from = date('Y-m-d');
            $to = date('Y-m-d');
            $supplierId = 0;
        }else{
            $from = $input['from'];
            $to = $input['to'];
            $supplierId = $input['supplier_id'];
        }
        $suppliers = DB::table('supplierinfos')->where('status',1)->get();
        $totalValue = DB::table('supinvoices')
        		  ->join('supplierinfos','supinvoices.supp_id','=','supplierinfos.supp_id') 
        		  ->join('paymenttypes','supinvoices.payment_type_id','=','paymenttypes.payment_type_id') 
        		  ->whereBetween('supinvoices.transaction_date',[$from,$to])
        		  ->where('supplierinfos.supp_id',$supplierId)
        		  ->groupBy('supinvoices.supp_id')
        		  ->get([
        		  		DB::raw('SUM(supinvoices.discount) as totalDiscount'),
        		  		DB::raw('SUM(supinvoices.amount) as totalAmount'),
	                    DB::raw('SUM(supinvoices.pay) as totalPay'),
	                    DB::raw('SUM(supinvoices.due) as totalDue')
        		  	]);
		return View::make('reports.summary.spplierWisePurchase', compact('title','from','to','items','suppliers','supplierId','totalValue'));
	}

	public function itemWisePurchaseReport(){
		$title = ':: POSv2 :: - Item Wise Purchase Report';
        $input=Input::all();
        if(!$input){
            $from = date('Y-m-d');
            $to = date('Y-m-d');
        }else{
            $from = $input['from'];
            $to = $input['to'];
        }
		return View::make('reports.summary.itemWisePurchaseReport', compact('title','from','to'));
	}

	public function itemWisePurchaseReportJsonFormat($from = null, $to = null){
		$result = DB::table('itempurchases')
				  ->select(
				  	'itempurchases.sup_invoice_id',
				  	'iteminfos.item_name',
				  	'supplierinfos.supp_or_comp_name',
				  	'itempurchases.discount',
				  	'itempurchases.amount',
				  	'empinfos.user_name'
				  	)
				  ->join('iteminfos','itempurchases.item_id','=','iteminfos.item_name')
				  ->join('supinvoices','itempurchases.sup_invoice_id','=','supinvoices.sup_invoice_id')
				  ->join('empinfos','itempurchases.created_by','=','empinfos.emp_id')
				  ->join('supplierinfos','supinvoices.supp_id','=','supplierinfos.supp_id')
				  ->whereBetween('supinvoices.transaction_date',array($from,$to))
				  ->where('supinvoices.status',1)
				  ->where('itempurchases.status',1)
				  ->get();
		return Response::json(['data'=>$result]);
	}

	public function getspplierWisePurchaseData($from = null,$to = null,$supplierId = null){
		return Datatable::query(DB::table('supinvoices')
        		  ->join('supplierinfos','supinvoices.supp_id','=','supplierinfos.supp_id') 
        		  ->join('paymenttypes','supinvoices.payment_type_id','=','paymenttypes.payment_type_id') 
        		  ->whereBetween('supinvoices.transaction_date',[$from,$to])
        		  ->where('supplierinfos.supp_id',$supplierId)
        		  )
					->addColumn('sup_invoice_id', function($model) {
                            return "<a href='#purDetailsModal' onclick='purchaseDetails(".$model->sup_invoice_id.")' data-toggle='modal'>".$model->sup_invoice_id."</a>";
                        })
                  ->showColumns( 'supp_or_comp_name', 'transaction_date', 'payment_type_name', 'discount', 'amount', 'pay','due')
                  ->searchColumns('sup_invoice_id', 'supp_or_comp_name')
                  ->orderColumns('sup_invoice_id', 'transaction_date', 'amount')
                  ->make();
	}

	public function spplierWiseSale(){
		$title = ':: POSv2 :: - Supplier Wise Sale Report';
        $input=Input::all();
        if(!$input){
            $from = date('Y-m-d');
            $to = date('Y-m-d');
            $supplierId = 0;
        }else{
            $from = $input['from'];
            $to = $input['to'];
            $supplierId = $input['supplier_id'];
        }
        $suppliers = DB::table('supplierinfos')->where('status',1)->get();
        $totalValue = DB::table('supinvoices')
        		  ->join('supplierinfos','supinvoices.supp_id','=','supplierinfos.supp_id') 
        		  ->join('paymenttypes','supinvoices.payment_type_id','=','paymenttypes.payment_type_id') 
        		  ->whereBetween('supinvoices.transaction_date',[$from,$to])
        		  ->where('supplierinfos.supp_id',$supplierId)
        		  ->groupBy('supinvoices.supp_id')
        		  ->get([
        		  		DB::raw('SUM(supinvoices.discount) as totalDiscount'),
        		  		DB::raw('SUM(supinvoices.amount) as totalAmount'),
	                    DB::raw('SUM(supinvoices.pay) as totalPay'),
	                    DB::raw('SUM(supinvoices.due) as totalDue')
        		  	]);
		return View::make('reports.summary.supplierWiseSale', compact('title','from','to','items','suppliers','supplierId','totalValue'));
	}

	public function getspplierWiseSaleData($from = null,$to = null,$supplierId = null){

		return Datatable::query(DB::table('saleinvoices')
        		  ->join('itemsales','saleinvoices.sale_invoice_id','=','itemsales.sale_invoice_id') 
        		  ->join('iteminfos','itemsales.item_id','=','iteminfos.item_id') 
        		  ->join('supplierinfos','iteminfos.supplier_id','=','supplierinfos.supp_id')
        		  ->whereBetween('saleinvoices.date',[$from,$to])
        		  ->where('supplierinfos.supp_id',$supplierId)
        		  ->groupBy('saleinvoices.sale_invoice_id')
        		  )
				  ->addColumn('sale_invoice_id', function($model) use ($supplierId) {
                            return "<a href='#saleDetailsModal' onclick='saleDetails(".$model->sale_invoice_id.",".$supplierId.")' data-toggle='modal'>".$model->sale_invoice_id."</a>";
                        })
				  
                  ->showColumns( 'supp_or_comp_name', 'date')
                  ->addColumn('amount', function($model) use ($supplierId) {
                            $amount = DB::table('itemsales')
                            	->join('iteminfos','itemsales.item_id','=','iteminfos.item_id')
	                        	->where('itemsales.sale_invoice_id',$model->sale_invoice_id)
	                        	->where('iteminfos.supplier_id',$supplierId)
                            	->get([
			        		  		DB::raw('SUM(itemsales.amount) as totalAmount')
			        		  	]);
		        		  return $amount[0]->totalAmount;
                    })
				  ->addColumn('pay', function($model) use ($supplierId) {
                        $paid = DB::table('itemsales')
                        	->join('iteminfos','itemsales.item_id','=','iteminfos.item_id')
                        	->where('itemsales.sale_invoice_id',$model->sale_invoice_id)
                        	->where('iteminfos.supplier_id',$supplierId)
                        	->get([
		        		  		DB::raw('SUM(itemsales.amount) as totalAmount')
		        		  	]);
	        		  	return $paid[0]->totalAmount;
                    })
				  ->addColumn('discount', function($model) use ($supplierId) {
                        $discount = DB::table('itemsales')
                        	->join('iteminfos','itemsales.item_id','=','iteminfos.item_id')
                        	->where('itemsales.sale_invoice_id',$model->sale_invoice_id)
                        	->where('iteminfos.supplier_id',$supplierId)
                        	->get([
		        		  		DB::raw('SUM(itemsales.discount) as totalAmount')
		        		  	]);
		        		  return $discount[0]->totalAmount;
                    })
				  ->addColumn( 'due', function($model) use ($supplierId) {
                       return $due = 0;
                    })
                  ->searchColumns('sale_invoice_id', 'supp_or_comp_name')
                  ->orderColumns('date', 'amount')
                  ->make();
	}

	public function saleReportDetailsSuppWise($saleInvoiceId,$supplierId){
		// return ;
		// $saleInvoiceIdArr = explode("_", $saleInvoiceId);
		// return $supplierId = $saleInvoiceIdArr[1];
		$company_info = DB::table('companyprofiles')
						->select('company_name', 'address', 'mobile')
						->first();
		
		$receipt_info = DB::table('saleinvoices as si')
						->leftjoin('empinfos as ei', 'ei.emp_id', '=', 'si.created_by')
						->leftJoin('customerinfos as ci', 'ci.cus_id', '=', 'si.cus_id')
						->leftJoin('customertypes as ct', 'ct.cus_type_id', '=', 'ci.cus_type_id')
						->leftJoin('paymenttypes as pt', 'pt.payment_type_id', '=', 'si.payment_type_id')
						->select('si.sale_invoice_id', 'si.discount', 'si.point_use_taka', 'si.amount', 'si.pay', 'si.due', 'si.pay_note', 'si.date', 'si.created_at', 'ci.user_name as customer_name', 'ct.point_unit', 'pt.payment_type_name', 'ei.user_name as invoiced_employee')
						->where('si.status', 1)
						->where('si.sale_invoice_id', $saleInvoiceId)
						->first();
                
                $receipt_item_infos = DB::table('itemsales')
					->select('i.item_name','itemsales.tax','i.supplier_id','itemsales.discount','p.sale_price', DB::raw('SUM(itemsales.quantity) as quantity, SUM(itemsales.amount) as amount'))
                                        ->join('saleinvoices', 'saleinvoices.sale_invoice_id', '=', 'itemsales.sale_invoice_id')
                                        ->leftjoin('iteminfos as i', 'itemsales.item_id', '=', 'i.item_id')
                                        ->leftJoin('priceinfos as p', 'p.price_id', '=', 'itemsales.price_id')
                                        ->where('itemsales.status', '=', 1)
                                        ->where('itemsales.sale_invoice_id', '=', $saleInvoiceId)
                                        ->groupBy('p.sale_price')
                                        ->groupBy('itemsales.item_id')
                                        ->orderBy('itemsales.i_sale_id','asc')
                                        ->get();
		return View::make('reports.details.saleReportDetailsSuppWise', compact('company_info', 'receipt_info', 'receipt_item_infos','supplierId'));
		// saleReportDetailsSuppWise
	}

	public function otherExpenseReportDetails($expense_id = null){
        $otherExpense = DB::table('otherexpenses as oe')
            ->join('incomeexpensetype as iet','oe.expense_type_id','=','iet.type_id')  
            ->join('empinfos as ei','oe.created_by','=','ei.emp_id')  
            ->select([
                'oe.amount','oe.comment','oe.date',
                'ei.f_name','ei.l_name',
                'oe.created_at', 'iet.type_name'
            ])
            ->where('oe.other_expense_id',$expense_id)
            ->first();
        $receipt_info_array=array();

        $receipt_info_array['transaction_date'] = date('d F, Y h:i a', strtotime($otherExpense->created_at));
        $receipt_info_array['amount']         = $otherExpense->amount;
        $receipt_info_array['comment']        = $otherExpense->comment;
        $receipt_info_array['type_name']      = $otherExpense->type_name;
        $receipt_info_array['invoice_by']     = $otherExpense->f_name.' '.$otherExpense->f_name;
        $receipt_info = (object) $receipt_info_array;
        $company_info=DB::table('companyprofiles')
            ->first();

        return View::make('reports.details.expenseReportReceipt',compact('receipt_info','company_info'));
	}

	public function duepaymentreport()
	{
		$from=date('Y-m-d');
	    $to=date('Y-m-d');
	    $reports= DB::table("cusduepayments")
	    	->select([
	    		'cusduepayments.*',
	    		'cusduediscounts.amount as discount_amount',
	    		'customerinfos.full_name',
	    		'empinfos.f_name',
	    		'empinfos.l_name'
	    	])
	    	->leftJoin('cusduediscounts','cusduepayments.c_due_payment_id','=','cusduediscounts.c_due_payment_id')
	    	->leftJoin('customerinfos','cusduepayments.cus_id','=','customerinfos.cus_id')
	    	->leftJoin('empinfos','cusduepayments.created_by','=','empinfos.created_by')
	    	->whereBetween('cusduepayments.date',[$from,$to])
	    	->groupBy('cusduepayments.c_due_payment_id')
	    	->get();
	    return View::make('reports.details.duePaymentReport',compact('from','to','reports'));
	}
    public function viewDuePaymentReport()
	{
	    $input=Input::all();
	    //echo '<pre>';print_r($input);exit;
	    $from=$input['from'];
	    $to=$input['to'];
	    $reports= DB::table("cusduepayments")
	    	->select([
	    		'cusduepayments.*',
	    		'cusduediscounts.amount as discount_amount',
	    		'customerinfos.full_name',
	    		'empinfos.f_name',
	    		'empinfos.l_name'
	    	])
	    	->leftJoin('cusduediscounts','cusduepayments.c_due_payment_id','=','cusduediscounts.c_due_payment_id')
	    	->leftJoin('customerinfos','cusduepayments.cus_id','=','customerinfos.cus_id')
	    	->leftJoin('empinfos','cusduepayments.created_by','=','empinfos.created_by')
	    	->whereBetween('cusduepayments.date',[$from,$to])
	    	->groupBy('cusduepayments.c_due_payment_id')
	    	->get();
        return View::make('reports.details.duePaymentReport',compact('reports','from','to'));
	}
}