<?php

class SetupController extends \BaseController {
	
	public $timestamp;

    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
		$this->timestamp = date('Y-m-d H:i:s');
    }

	public function index()
	{
		return View::make('admin.setup.setUp');
	}
	
	public function addOffer(){
		$data = Input::all();
		$title = ':: POS :: - Create Offer';		
		return View::make('admin.setup.offerSetup.addOffer', compact('title', 'data'));
	}
	//Item Offer
    public function getItemWiseData() {		
        return Datatable::query(DB::table('iteminfos')
				->leftJoin('itembrands', 'iteminfos.brand_id', '=', 'itembrands.brand_id')
				->leftJoin('itemcategorys', 'iteminfos.category_id', '=', 'itemcategorys.category_id')
				->select('iteminfos.item_id','iteminfos.upc_code', 'iteminfos.item_name','itembrands.brand_name', 'itemcategorys.category_name', 'iteminfos.offer')
				->where('iteminfos.status', 1))
                ->showColumns('upc_code', 'item_name', 'brand_name', 'category_name')
				->addColumn('offer', function($model){
					return '<input type="text" class="input-small" id="offer'.$model->item_id.'" name="offer" value="'.$model->offer.'" />';
				})
                ->addColumn('action', function($model) {
					$html = '<button class="btn btn-info btn-small" onclick="itemOffer('.$model->item_id.')"><i class="icon-edit"></i> Create Offer</button> '.
                            '<a class="btn btn-warning btn-small" href="#" onclick="resetItemOffer('.$model->item_id.')" id="'.$model->item_id.'"><i class="icon-undo"></i> Reset Offer</a>';
                    return $html;
                })
                ->searchColumns('upc_code','item_name','category_name')
                ->setSearchWithAlias()
                ->orderColumns('item_name','category_name')
                ->make();
    }
	
	public function createItemOffer($itemId) {
		//print_r(Input::all());exit;
		try{
			$data = Input::all();
			$validator = Validator::make($data, Setup::$itemOffer_rules);
			if($validator->fails()) {
				return Response::json(['status' =>'Validation Error occurred']);
			}
			$item_offer = array(
				'offer' 		=>  Input::get('offer'),
				'offer_type' 	=>  Input::get('offer_type')
			);
			
			$item_offer_up = DB::table('iteminfos')		
				->where('item_id', $itemId)
				->update($item_offer);
			if($item_offer_up){	
				return Response::json(['status' => 'success']);
			} 
			return Response::json(['status' => 'No operation occur! You can\'t assign offer less than current offer.']);
		} catch(\Exception $e){
			//Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            //$err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Response::json(['status' => 'Something Wrong!']);
		}
	}
	
	public function resetItemOffer($itemId) {
		try{
			$data = Input::all();
			$validator = Validator::make($data, Setup::$brandOffer_rules);
			if($validator->fails()) {
				return Response::json(['status' =>'Validation Error occurred']);
			}
			
			$resetOffer = DB::table('iteminfos')
				->where('item_id', $itemId)
				->update(array('offer' => Input::get('offer'), 'offer_type' => 0));
			if($resetOffer){	
				return Response::json(['status' => 'success']);
			} 
			return Response::json(['status' => 'No operation occur! You can\'t assign offer less than current offer.']);
		} catch(\Exception $e){
			return Response::json(['status' => 'Something Wrong!']);
		}
	}
	//Brand Offer
    public function getBrandWiseData() {		
        return Datatable::query(DB::table('itembrands as b')
				->select('b.brand_id', 'b.brand_name', 'b.offer')
				->leftJoin('iteminfos as i', 'b.brand_id', '=', 'i.brand_id')
				->where('i.status', 1)
				->where('b.status', 1)
				->groupBy('i.brand_id'))
                ->showColumns('brand_id', 'brand_name')
				->addColumn('offer', function($model){
					return '<input type="text" class="input-small" id="offerBrand'.$model->brand_id.'" name="offer[]" value="'.$model->offer.'" />';
				})
                ->addColumn('action', function($model) {
					$html = '<button class="btn btn-info btn-small" onclick="brandOffer('.$model->brand_id.')"><i class="icon-edit"></i> Create Offer</button> ' .
                            '<a class="btn btn-warning btn-small" href="#" onclick="resetBrandOffer('.$model->brand_id.')" id="'.$model->brand_id.'"><i class="icon-undo"></i> Reset Offer</a>';
                    return $html;
                })
                ->searchColumns('item_id','brand_name')
                ->setSearchWithAlias()
                ->orderColumns('brand_name')
                ->make();
    }
	
	public function createBrandOffer($brandId) {
		//print_r(Input::all());exit;
		try{
			$data = Input::all();
			$validator = Validator::make($data, Setup::$brandOffer_rules);
			if($validator->fails()) {
				return Response::json(['status' =>'Validation Error occurred']);
			}			
			DB::table('itembrands')
				->where('brand_id', $brandId)
				->update(array('offer' => Input::get('offer')));
				
			$offer = Input::get('offer');
			$offer_type = Input::get('offer_type');
			$brand_offer_up = DB::table('iteminfos')
				->whereRaw("brand_id = $brandId AND ($offer > offer OR ($offer < offer AND offer_type = $offer_type))")
				->update(array('offer_type'=>$offer_type, 'offer'=> $offer));
			if($brand_offer_up){	
				return Response::json(['status' => 'success']);
			} 
			return Response::json(['status' => 'No operation occur! You can\'t assign offer less than current offer.']);
		} catch(\Exception $e){
			//Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            //$err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Response::json(['status' => 'Something Wrong!']);
		}
	}
	
	public function resetBrandOffer($brandId) {
		try{
			$data = Input::all();
			$validator = Validator::make($data, Setup::$brandOffer_rules);
			if($validator->fails()) {
				return Response::json(['status' =>'Validation Error occurred']);
			}
			DB::table('itembrands')
				->where('brand_id', $brandId)
				->update(array('offer' => 0));
			
			$resetOffer = DB::table('iteminfos')
				->where('offer_type', Input::get('offer_type'))
				->where('brand_id', $brandId)
				->update(array('offer' => Input::get('offer'), 'offer_type' => 0));
			if($resetOffer){	
				return Response::json(['status' => 'success']);
			} 
			return Response::json(['status' => 'No operation occur! You can\'t assign offer less than current offer.']);
		} catch(\Exception $e){
			return Response::json(['status' => 'Something Wrong!']);
		}
	}
	//Category Wise Offer
    public function getCategoryWiseData() {		
        return Datatable::query(DB::table('itemcategorys as c')
				->select('c.category_id', 'c.category_name', 'c.offer')
				->leftJoin('iteminfos as i', 'c.category_id', '=', 'i.category_id')
				->where('i.status', 1)
				->where('c.status', 1)
				->groupBy('i.category_id'))
                ->showColumns('category_id', 'category_name')
				->addColumn('offer', function($model){
					return '<input type="text" class="input-small" id="offerCategory'.$model->category_id.'" name="offer[]" value="'.$model->offer.'" />';
				})
                ->addColumn('action', function($model) {
					$html = '<button class="btn btn-info btn-small" onclick="categoryOffer('.$model->category_id.')"><i class="icon-edit"></i> Create Offer</button> '.
                            '<a class="btn btn-warning btn-small" href="#" onclick="resetCategoryOffer('.$model->category_id.')" id="'.$model->category_id.'"><i class="icon-undo"></i> Reset Offer</a>';
                    return $html;
                })
                ->searchColumns('item_id','category_name')
                ->setSearchWithAlias()
                ->orderColumns('category_name')
                ->make();
    }
	
	public function createCategoryOffer($categoryId) {
		//print_r(Input::all());exit;
		try{
			$data = Input::all();
			$validator = Validator::make($data, Setup::$cateogryOffer_rules);
			if($validator->fails()) {
				return Response::json(['status' =>'Validation Error occurred']);
			}			
			DB::table('itemcategorys')
				->where('category_id', $categoryId)
				->update(array('offer' => Input::get('offer')));
			
			$offer = Input::get('offer');
			$offer_type = Input::get('offer_type');
			$category_offer_up = DB::table('iteminfos')
				->whereRaw("category_id = $categoryId AND ($offer > offer OR ($offer < offer AND offer_type = $offer_type))")
				->update(array('offer_type'=>$offer_type, 'offer'=> $offer));
				
			if($category_offer_up){	
				return Response::json(['status' => 'success']);
			} 
			return Response::json(['status' => 'No operation occur! You can\'t assign offer less than current offer.']);
		} catch(\Exception $e){
			//Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            //$err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Response::json(['status' => 'Something Wrong!']);
		}
	}
	
	public function resetCategoryOffer($categoryId) {
		try{
			$data = Input::all();
			$validator = Validator::make($data, Setup::$cateogryOffer_rules);
			if($validator->fails()) {
				return Response::json(['status' =>'Validation Error occurred']);
			}			
			DB::table('itemcategorys')
				->where('category_id', $categoryId)
				->update(array('offer' => 0));
				
			$offerReset = DB::table('iteminfos')
				->where('offer_type', Input::get('offer_type'))
				->where('category_id', $categoryId)
				->update(array('offer' => Input::get('offer'), 'offer_type' => 0));
			if($offerReset){	
				return Response::json(['status' => 'success']);
			} 
			return Response::json(['status' => 'No operation occur! You can\'t assign offer less than current offer.']);
		} catch(\Exception $e){
			return Response::json(['status' => 'Something Wrong!']);
		}
	}
	
	public function offerView(){
		$title = ':: POS :: - View Offer';
		return View::make('admin.setup.offerSetup.viewOffer', compact('title'));
	}

	public function showOffer(){
		$data = Input::all();
		array_shift($data);
		//print_r($data);exit;
		$getOfferType = array();
		if($data['OfferType'] == 1){
			$itemOffer = DB::table('iteminfos')
								->select('item_name', 'offer')
								->where('offer_type', 1)
								->where('status',1)
								->get();
			$getOfferType['itemOffers'] = $itemOffer;
		}
		if($data['OfferType'] == 2){
			$categoryOffer = DB::table('itemcategorys as c')
								->select('c.category_id', 'c.category_name', DB::raw('MAX(i.offer) as offer'))
								->leftJoin('iteminfos as i', 'c.category_id', '=', 'i.category_id')
								->where('i.offer_type', 2)
								->where('i.status', 1)
								->where('c.status', 1)
								->groupBy('i.category_id')
								->get();
			$getOfferType['categoryOffers'] = $categoryOffer;		
		}
		if($data['OfferType'] == 3){
			$brandOffer =DB::table('itembrands as b')
							->select('b.brand_id', 'b.brand_name', DB::raw('MAX(i.offer) as offer'))
							->leftJoin('iteminfos as i', 'b.brand_id', '=', 'i.brand_id')
							->where('i.offer_type', 3)
							->where('i.status', 1)
							->where('b.status', 1)
							->groupBy('i.brand_id')
							->get();
			$getOfferType['brandOffers'] = $brandOffer;
		}
		//echo '<pre>';print_r($getOfferType['itemOffers']);exit;
		return View::make('admin.setup.offerSetup.viewOffer', compact('data', 'getOfferType'));
	}
	
	/*
	  * Income/Expense Type Setup
	*/
	public function incExpTypeForm(){
		return View::make('admin.setup.incExpTypeSetup.addIncExpType');
	}

    public function addIncExpType() {
		try{
			$data = Input::all();
			$validator = Validator::make($data, Setup::$type_rules);
			if($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			$incExp = array(
					'type_name' 	=>  Input::get('type_name'),
					'used_for' 		=>  Input::get('used_for'),
					'created_by' 	=>  Session::get('emp_id'),
					'created_at' 	=>  $this->timestamp
			);
			$insert = DB::table('incomeexpensetype')->insert($incExp);
			if($insert) {
				return Redirect::to('admin/setup')->with('message', 'Added Successfully');
			}
			return Redirect::to('admin/setup')->with('errorMsg', 'Something must be wrong! Please check');
		} catch(\Exception $e){
			Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Redirect::to('admin/setup')->with('errorMsg', $err_msg)->withInput();
		}
    }
	public function getIncExpType(){
		$incExps = DB::table('incomeexpensetype')
								->where('status', 1)
								->get();
		return View::make('admin.setup.incExpTypeSetup.incExpType', compact('incExps'));
	}

	public function editIncExpType($typeId)
	{
		try{
			$data = Input::all();
			$validator = Validator::make($data, Setup::$type_rules);
			if($validator->fails()) {
				return Response::json(['status' =>'Validation Error occurred']);
			}
			$incExpType = array(
					'type_name' 	=>  Input::get('type_name'),
					'updated_by' 	=>  Session::get('emp_id'),
					'updated_at' 	=>  $this->timestamp
			);
			$incExpType = DB::table('incomeexpensetype')
								->where('type_id', $typeId)
								->update($incExpType);
			if($incExpType){
				return Response::json(['status' => 'success']);
			}
			return Response::json(['status' => 'error']);
		} catch(\Exception $e){
			Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
            $err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Response::json(['status' => $err_msg]);
		}
	}

	public function distroyIncExpType($typeId){
		$inactiveIncExp = DB::table('incomeexpensetype')
								->where('type_id', $typeId)
								->update(array('status' => 0));
		if($inactiveIncExp){
			return Response::json(['status' => 'success']);
		}
		return Response::json(['status' => 'error']);
	}
	

}
