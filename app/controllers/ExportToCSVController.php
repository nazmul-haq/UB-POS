<?php 

class ExportToCSVController extends  BaseController{
	public function index(){
		return View::make('admin.export');
	}
	public function exportToCsv($keyword = null,$subkey = null){
		$con = new mysqli("localhost","mbtradeb_mbt","mbtradeb_mbt!@!#!@","mbtradeb_db");
		$tables = array();
		$query = mysqli_query($con, 'SHOW TABLES');
		while($row = mysqli_fetch_row($query)){
			$tables[] = $row[0];
		}
		$keyword = base64_decode(base64_decode($keyword));
		$subkey = ($subkey != null) ? base64_decode(base64_decode($subkey)) : $subkey;
		if(!in_array($keyword, $tables)){
			return Redirect::to('admin/viewAllItem')->with('errorMsg', "Something Went Wrong !!");
		}
		$datas = [];
		$datas = self::getAllData($keyword,$subkey);
		$filename = $datas['title']."-downloads-".date('dmy').".csv";
	    $handle = fopen($filename, 'w+');
	    fputcsv($handle, $datas['header']);
	    foreach($datas['body'] as $value) {
	        fputcsv($handle, $value);
	    }
	    fclose($handle);
	    $headers = array(
	        'Content-Type' => 'text/csv',
	    );
	    return Response::download($filename, $filename, $headers);
	}
	public static function getAllData($keyword,$subkey=null){
		if($keyword == 'iteminfos'){
			if($subkey == 'allItem'){
				$header = ['SL No.','UPC Code','Item Name','Category Name','Last Purchase Price','Last Sale Price','Company Name','Stock Quantity','Total Quantity'];
				$items = DB::select("select com.company_name,itemcategorys.category_name,iteminfos.item_id,iteminfos.item_name,iteminfos.upc_code, priceinfos.purchase_price,priceinfos.sale_price,iteminfos.price_id, COALESCE(all_item.g_qty, 0)as g_qty, COALESCE(all_item.s_qty, 0)as s_qty,sum(COALESCE(all_item.s_qty, 0) + COALESCE(all_item.g_qty, 0)) as total_qty
						from iteminfos
						left join itemcategorys on iteminfos.category_id=itemcategorys.category_id
						left join priceinfos on priceinfos.price_id=iteminfos.price_id
						left join companynames as com on com.company_id = iteminfos.item_company_id
						left join (
							select stk.item_id,stk.price_id,stk.purchase_price,stk.sale_price,stk.item_name,stk.upc_code,stk.s_qty,COALESCE(gdn.g_qty, 0)as g_qty, sum(COALESCE(stk.s_qty, 0) + COALESCE(gdn.g_qty, 0)) as total_qty 
							 from(
							         select iteminfos.item_id,iteminfos.price_id,p.purchase_price,p.sale_price, iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(s.available_quantity), 0) as s_qty
							         from iteminfos
							         left outer join stockitems as s on s.item_id = iteminfos.item_id
							         left join priceinfos as p on p.price_id = iteminfos.price_id
							         where s.`status`=1
							         group by iteminfos.item_id
							         ) 
							         as stk
							         left outer join(
							                 select iteminfos.item_id,iteminfos.upc_code,
							                 iteminfos.item_name,COALESCE(sum(g.available_quantity),0)  as g_qty
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
				foreach($items as $key => $item) {
					$body[] = [++$key,$item->upc_code,ltrim($item->item_name),$item->category_name,$item->purchase_price,$item->sale_price,$item->company_name,$item->s_qty,$item->total_qty];
				}
				$title = 'AllItemInfo';
			}elseif($subkey == 'stockItem'){
				$header = ['SL No.','UPC Code','Item Name','Company Name','Category Name','Purchase Price','Sale Price','Category','Offer','Available Quantity'];
				$items=DB::table('iteminfos')
		            ->select([
		                'companynames.company_name',
		                'iteminfos.item_id','iteminfos.item_name',
		                'iteminfos.upc_code','iteminfos.offer',
		                'itemcategorys.category_name','priceinfos.purchase_price',
		                'priceinfos.sale_price','iteminfos.price_id',
		                'stockitems.available_quantity',
		            ])
		            ->leftJoin('companynames','iteminfos.item_company_id','=','companynames.company_id')
		            ->leftJoin('itemcategorys','iteminfos.category_id','=','itemcategorys.category_id')
		            ->leftJoin('priceinfos','iteminfos.item_id','=','priceinfos.item_id')
		            ->leftJoin('stockitems','iteminfos.item_id','=','stockitems.item_id')
		            ->where('itemInfos.status',1)
		            ->where('priceinfos.status',1)
		            ->where('stockitems.available_quantity','>',0)
		            ->get(); 
	            foreach($items as $key => $item) {
					$body[] = [++$key,$item->upc_code,ltrim($item->item_name),$item->company_name,$item->category_name,$item->purchase_price,$item->sale_price,$item->category_name,$item->offer,$item->available_quantity];
				}
				$title = 'StockItemInfo';
			}elseif($subkey == 'catWiseItem'){
				$header = ['SL No.','UPC Code','Item Name','Category Name','Last Purchase Price','Last Sale Price','Company Name','Stock Quantity','Total Quantity'];
				$items = DB::select("select com.company_name,itemcategorys.category_name,iteminfos.item_id,iteminfos.item_name,iteminfos.upc_code, priceinfos.purchase_price,priceinfos.sale_price,iteminfos.price_id, COALESCE(all_item.g_qty, 0)as g_qty, COALESCE(all_item.s_qty, 0)as s_qty,sum(COALESCE(all_item.s_qty, 0) + COALESCE(all_item.g_qty, 0)) as total_qty
						from iteminfos
						left join itemcategorys on iteminfos.category_id=itemcategorys.category_id
						left join priceinfos on priceinfos.price_id=iteminfos.price_id
						left join companynames as com on com.company_id = iteminfos.item_company_id
						left join (
							select stk.item_id,stk.price_id,stk.purchase_price,stk.sale_price,stk.item_name,stk.upc_code,stk.s_qty,COALESCE(gdn.g_qty, 0)as g_qty, sum(COALESCE(stk.s_qty, 0) + COALESCE(gdn.g_qty, 0)) as total_qty 
							 from(
							         select iteminfos.item_id,iteminfos.price_id,p.purchase_price,p.sale_price, iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(s.available_quantity), 0) as s_qty
							         from iteminfos
							         left outer join stockitems as s on s.item_id = iteminfos.item_id
							         left join priceinfos as p on p.price_id = iteminfos.price_id
							         where s.`status`=1
							         group by iteminfos.item_id
							         ) 
							         as stk
							         left outer join(
							                 select iteminfos.item_id,iteminfos.upc_code,
							                 iteminfos.item_name,COALESCE(sum(g.available_quantity),0)  as g_qty
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
						order by itemcategorys.category_name asc;");
				foreach($items as $key => $item) {
					$body[] = [++$key,$item->upc_code,ltrim($item->item_name),$item->category_name,$item->purchase_price,$item->sale_price,$item->company_name,$item->s_qty,$item->total_qty];
				}
				$title = 'CategoryWiseItemInfo';
			}
		}elseif($keyword == 'customerinfos'){
			$header = ['SL No.','Customer ID','Full Name','User Name','Mobile','Due','Register Date'];
			$customers = DB::table('customerinfos')
				->where('status', 1)
	            ->get(); 
            foreach($customers as $key => $customer) {
				$body[] = [++$key,$customer->cus_id,$customer->full_name,$customer->user_name,$customer->mobile,$customer->due,date('Y-m-d', strtotime($customer->created_at))];
			}
			$title = 'CustomerInfo';
		}elseif($keyword == 'supplierinfos'){
			$header = ['SL No.','Supplier ID','Supplier/Company Name','User Name','Mobile','Due'];
			$supliers = DB::table('supplierinfos')
				->where('status', 1)
	            ->get(); 
            foreach($supliers as $key => $supplier) {
				$body[] = [++$key,$supplier->supp_id,$supplier->supp_or_comp_name,$supplier->user_name,$supplier->mobile,$supplier->due];
			}
			$title = 'SuplierInfo';
		}elseif($keyword == 'empinfos'){
			$header = ['SL No.','Name','User Name','Father\'s Name','Mother\'s Name','Mobile','Email','Permanent Address','Present Address','National Id','Fixed Salary(Till '.date('F').')','Advance Salary(Till '.date('F').')','Due Salary(Till '.date('F').')'];
			$employees = DB::table('empinfos')
				->where('status', 1)
	            ->get(); 
            foreach($employees as $key => $employee) {
				$body[] = [++$key,$employee->f_name.' '.$employee->l_name,$employee->user_name,$employee->father_name,$employee->mother_name,$employee->mobile,$employee->email,$employee->permanent_address,$employee->present_address,$employee->national_id,$employee->fixed_salary,$employee->advance_salary,$employee->due_salary];
			}
			$title = 'EmployeeInfo';
		}
		return $finalArray[] = ['header' => $header,'body' => $body,'title' => $title];
	}
}