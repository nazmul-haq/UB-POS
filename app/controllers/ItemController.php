<?php

class ItemController extends \BaseController
{

    public $timestamp;

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
        $this->timestamp = date('Y-m-d H:i:s');
    }
    public function getAllItemJsonData()
    {
        $all_items = DB::select("select iteminfos.item_id,iteminfos.item_name,iteminfos.upc_code, priceinfos.purchase_price,priceinfos.sale_price,iteminfos.price_id, COALESCE(all_item.g_qty, 0)as g_qty, COALESCE(all_item.s_qty, 0)as s_qty,sum(COALESCE(all_item.s_qty, 0) + COALESCE(all_item.g_qty, 0)) as total_qty
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
            order by iteminfos.item_name asc");

        return Response::json(['data' => $all_items]);
    }

    public function allItemView()
    {
        $sql = DB::select("select stk.item_id,stk.item_name,stk.upc_code,stk.s_qty,COALESCE(gdn.g_qty, 0)as g_qty, sum(COALESCE(stk.s_qty, 0) + COALESCE(gdn.g_qty, 0)) as total_qty 
                from(
                        select iteminfos.item_id,iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(s.available_quantity), 0) as s_qty
                        from iteminfos
                        left outer join stockitems as s on s.item_id=iteminfos.item_id
                        where s.`status`=1
                        group by iteminfos.item_id
                        ) as stk
                        left outer join(
                                select iteminfos.item_id,iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(g.available_quantity), 0)  as g_qty
                                from iteminfos
                                right outer join godownitems as g on g.item_id=iteminfos.item_id
                                where g.`status`=1
                                group by iteminfos.item_id
                        ) as gdn on gdn.item_id=stk.item_id
                group by stk.item_id");
        return View::make('admin.items.viewAllItem');
    }

    public function getAllItemData()
    {


//        echo 'hi';exit;
//        return Datatable::query(DB::select("select stk.item_id,stk.item_name,stk.upc_code,stk.s_qty,COALESCE(gdn.g_qty, 0)as g_qty, sum(COALESCE(stk.s_qty, 0) + COALESCE(gdn.g_qty, 0)) as total_qty 
//                from(
//                        select iteminfos.item_id,iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(s.available_quantity), 0) as s_qty
//                        from iteminfos
//                        left outer join stockitems as s on s.item_id=iteminfos.item_id
//                        where s.`status`=1
//                        group by iteminfos.item_id
//                        ) as stk
//                        left outer join(
//                                select iteminfos.item_id,iteminfos.upc_code,iteminfos.item_name,COALESCE(sum(g.available_quantity), 0)  as g_qty
//                                from iteminfos
//                                right outer join godownitems as g on g.item_id=iteminfos.item_id
//                                where g.`status`=1
//                                group by iteminfos.item_id
//                        ) as gdn on gdn.item_id=stk.item_id
//                group by stk.item_id")  
//                )
//                        
//                        ->showColumns('upc_code', 'item_name', 'g_qty', 's_qty', 'total_qty')
//                        ->searchColumns('upc_code', 'item_name','# ID')
//                        ->setSearchWithAlias()
//                        ->orderColumns('upc_code', 'item_name','# ID')
//                        ->make();


//        return Datatable::query(DB::table('stockitems as s')
//                                ->select('s.stock_item_id', 'i.item_id', 'com.company_name', 'i.upc_code', 'i.item_name', 'p.purchase_price', 'p.sale_price', 'i.tax_amount', 'i.offer', DB::raw('SUM(s.available_quantity) as available_qty'), DB::raw('count(s.item_id) as differentPrice'))
//                                ->leftJoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
//                                ->leftJoin('companynames as com', 'com.company_id', '=', 'i.item_company_id')
//                                ->leftJoin('itembrands as b', 'i.brand_id', '=', 'b.brand_id')
//                                ->leftJoin('itemlocations as l', 'i.location_id', '=', 'l.location_id')
//                                ->leftJoin('priceinfos as p', 's.price_id', '=', 'p.price_id')
//                                ->where('s.status', '=', '1')
//                                ->groupBy('s.item_id'))
//                        ->addColumn('#', function($model) {
//                            return '<input type="checkbox" name="barcodeInfo[]" value="' . $model->item_id . '">';
//                        })
//                        ->showColumns('upc_code', 'item_name', 'company_name', 'purchase_price', 'sale_price', 'tax_amount', 'offer', 'available_qty')
//                        ->addColumn('action', function($model) {
//                            $html = '<div class="btn-group">';
//                            $html .= '<a class="btn btn-primary btn-small" href="javascript:;" data-toggle="dropdown"><i class="icon-user icon-white"></i> Action</a>';
//                            $html .= '<a class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown" href="javascript:;"><span class="caret"></span></a>';
//                            $html .= '<ul class="dropdown-menu">';
//                            $html .= '<li><a onclick="ItemEdit(' . $model->item_id . ')" href="#editItemModal"  role="button" data-toggle="modal"><i class="icon-edit"></i>&nbsp; Item info Edit</a></li>';
//                            $html .= '<li><a href="#" title="Inactive" onclick="return deleteConfirm(' . $model->item_id . ')" id="' . $model->item_id . '"><i class="icon-trash"></i> Delete</a></li>';
//
//                            if ($model->differentPrice > 1) {
//                                
//                            } else {
//                                $html .= '<li><a onclick="editPrice(' . $model->stock_item_id . ')" href="#editPriceModal"  role="button" data-toggle="modal"><i class="icon-edit"></i>&nbsp; Price Edit</a></li>';
//                                $html .= '<li><a onclick="editQuantity(' . $model->stock_item_id . ')" href="#editQuantityModal"  role="button" data-toggle="modal"><i class="icon-pencil"></i>&nbsp; Quantity Edit</a></li>';
//                            }
//                            $html .= '</ul> ';
//
//                            if ($model->differentPrice > 1) {
//                                $html.='<a class="btn btn-success btn-small" style="margin-left: 3px;" title="Multiple prices are here" onclick="differentPrice(' . $model->item_id . ')" href="#diffPrice"  role="button" data-toggle="modal">Diff.Price</a>';
//                            }
//                            $html.='</div>';
//                            return $html;
//                        })
//                        ->searchColumns('upc_code', 'item_name', 'company_name')
//                        ->setSearchWithAlias()
//                        ->orderColumns('upc_code', 'item_name', 'company_name', 'purchase_price', 'sale_price', 'tax_amount', 'offer', 'available_qty')
//                        ->make();
    }
    public function index()
    {
        return View::make('admin.items.viewItem');
    }
//=======Item Category========
    public function itemCategoryForm()
    {
        return View::make('admin.setup.itemCategorySetup.addItemCategory');
    }
    public function saveItemCategory()
    {
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $item_category = array(
                'category_name' => Input::get('category_name'),
                'created_by' => Session::get('emp_id'),
                'created_at' => $this->timestamp
            );
            $insert = DB::table('itemcategorys')->insert($item_category);
            if ($insert) {
                return Redirect::to('admin/setup')->with('message', 'Added Successfully');
            }
            return Redirect::to('admin/setup')->with('errorMsg', 'Something must be wrong! Please check');
        } catch (\Exception $e) {
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Redirect::to('admin/setup')->with('errorMsg', $err_msg)->withInput();
        }
    }
    public function itemCategoryView()
    {
        $item_categorys = DB::table('itemcategorys')->where('status', 1)->get();
        return View::make('admin.setup.itemCategorySetup.itemCategory', compact('item_categorys'));
    }
    public function editItemCategory($categoryId)
    {
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$rules);
            if ($validator->fails()) {
                return Response::json(['status' => 'Validation Error occurred']);
            }
            $item_category = array(
                'category_name' => Input::get('category_name'),
                'updated_by' => Session::get('emp_id'),
                'updated_at' => $this->timestamp
            );

            $item_category = DB::table('itemcategorys')
                ->where('category_id', $categoryId)
                ->update($item_category);
            if ($item_category) {
                return Response::json(['status' => 'success']);
            }
            return Response::json(['status' => 'error']);
        } catch (\Exception $e) {
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Response::json(['status' => $err_msg]);
        }
    }
    public function deleteItemCategory($categoryId)
    {
        $categoryItemDelete = DB::table('itemcategorys')
            ->where('category_id', $categoryId)
            ->update(array('status' => 0));
        if ($categoryItemDelete) {
            return Response::json(['status' => 'success']);
        }
        return Response::json(['status' => 'error']);
    }
//========Item Brand=============
    public function itemBrandForm()
    {
        return View::make('admin.setup.itemBrandSetup.addNewBrand');
    }
    public function saveItemBrand()
    {
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$brand_rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $item_category = array(
                'brand_name' => Input::get('brand_name'),
                'created_by' => Session::get('emp_id'),
                'created_at' => $this->timestamp
            );
            $insert = DB::table('itembrands')->insert($item_category);
            if ($insert) {
                return Redirect::to('admin/setup')->with('message', 'Added Successfully');
            }
            return Redirect::to('admin/setup')->with('errorMsg', 'Something must be wrong! Please check');
        } catch (\Exception $e) {
            return Redirect::to('admin/setup')->with('errorMsg', 'Duplicate entry found.')->withInput();
        }
    }

    public function itemBrandView()
    {
        $item_brands = DB::table('itembrands')->where('status', 1)->get();
        return View::make('admin.setup.itemBrandSetup.itemBrand', compact('item_brands'));
    }

    public function editItemBrand($brandId)
    {
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$brand_rules);
            if ($validator->fails()) {
                return Response::json(['status' => 'Validation Error occurred']);
            }
            $item_brand = array(
                'brand_name' => Input::get('brand_name'),
                'updated_by' => Session::get('emp_id'),
                'updated_at' => $this->timestamp
            );

            $item_brand = DB::table('itembrands')
                ->where('brand_id', $brandId)
                ->update($item_brand);
            if ($item_brand) {
                return Response::json(['status' => 'success']);
            }
            return Response::json(['status' => 'error']);
        } catch (\Exception $e) {
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Response::json(['status' => $err_msg]);
        }
    }

    public function deleteItemBrand($brandId)
    {
        $BrandItemDelete = DB::table('itembrands')
            ->where('brand_id', $brandId)
            ->update(array('status' => 0));
        if ($BrandItemDelete) {
            return Response::json(['status' => 'success']);
        }
        return Response::json(['status' => 'error']);
    }

//=======Item Location========
    public function itemLocationForm()
    {
        return View::make('admin.setup.itemLocationSetup.addItemLocation');
    }

    public function saveItemLocation()
    {
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$location_rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $item_location = array(
                'location_name' => Input::get('location_name'),
                'created_by' => Session::get('emp_id'),
                'created_at' => $this->timestamp
            );
            $insert = DB::table('itemlocations')->insert($item_location);
            if ($insert) {
                return Redirect::to('admin/setup')->with('message', 'Added Successfully');
            }
            return Redirect::to('admin/setup')->with('errorMsg', 'Something must be wrong! Please check');
        } catch (\Exception $e) {
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Redirect::to('admin/setup')->with('errorMsg', $err_msg)->withInput();
        }
    }

    public function itemLocationView()
    {
        $item_locations = DB::table('itemlocations')->where('status', 1)->get();
        return View::make('admin.setup.itemLocationSetup.itemLocation', compact('item_locations'));
    }

    public function editItemLocation($locationId)
    {
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$location_rules);
            if ($validator->fails()) {
                return Response::json(['status' => 'Validation Error occurred']);
            }
            $item_location = array(
                'location_name' => Input::get('location_name'),
                'updated_by' => Session::get('emp_id'),
                'updated_at' => $this->timestamp
            );

            $item_location = DB::table('itemlocations')
                ->where('location_id', $locationId)
                ->update($item_location);
            if ($item_location) {
                return Response::json(['status' => 'success']);
            }
            return Response::json(['status' => 'error']);
        } catch (\Exception $e) {
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Response::json(['status' => $err_msg]);
        }
    }

    public function deleteItemLocation($locationId)
    {
        $locationItemDelete = DB::table('itemlocations')
            ->where('location_id', $locationId)
            ->update(array('status' => 0));
        if ($locationItemDelete) {
            return Response::json(['status' => 'success']);
        }
        return Response::json(['status' => 'error']);
    }

//========Item Company=============
    public function itemCompanyForm()
    {
        return View::make('admin.setup.itemCompanySetup.addNewCompany');
    }

    public function saveItemCompany()
    {
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$company_rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $item_company = array(
                'company_name' => Input::get('company_name'),
                'created_by' => Session::get('emp_id'),
                'created_at' => $this->timestamp
            );
            $insert = DB::table('companynames')->insert($item_company);
            if ($insert) {
                return Redirect::to('admin/setup')->with('message', 'Added Successfully');
            }
            return Redirect::to('admin/setup')->with('errorMsg', 'Something must be wrong! Please check');
        } catch (\Exception $e) {
            return Redirect::to('admin/setup')->with('errorMsg', 'Duplicate entry found.')->withInput();
        }
    }

    public function itemCompanyView()
    {
        $item_companys = DB::table('companynames')->where('status', 1)->get();
        return View::make('admin.setup.itemCompanySetup.itemCompany', compact('item_companys'));
    }

    public function editItemCompany($companyId)
    {
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$company_rules);
            if ($validator->fails()) {
                return Response::json(['status' => 'Validation Error occurred']);
            }
            $item_company = array(
                'company_name' => Input::get('company_name'),
                'updated_by' => Session::get('emp_id'),
                'updated_at' => $this->timestamp
            );

            $update = DB::table('companynames')
                ->where('company_id', $companyId)
                ->update($item_company);
            if ($update) {
                return Response::json(['status' => 'success']);
            }
            return Response::json(['status' => 'error']);
        } catch (\Exception $e) {
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Response::json(['status' => $err_msg]);
        }
    }

    public function deleteItemCompany($companyId)
    {
        $CompanyItemDelete = DB::table('companynames')
            ->where('company_id', $companyId)
            ->update(array('status' => 0));
        if ($CompanyItemDelete) {
            return Response::json(['status' => 'success']);
        }
        return Response::json(['status' => 'error']);
    }

//========Item=============

    public function multipleItemAdd() {
        // return 'hi';

        $company = DB::table('sub_companies')
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->lists('company_name', 'id');

        $suppliers = array(
            '' => 'Select Supplier') + DB::table('supplierinfos')
            ->where('status', 1)
            ->orderBy('supp_id', 'asc')
            ->lists('supp_or_comp_name', 'supp_id');
        $item_company = array(
            '' => 'Select Item Company') + DB::table('companynames')
            ->where('status', 1)
            ->orderBy('company_name', 'asc')
            ->lists('company_name', 'company_id');

        $item_categorys = array(
            '' => 'Select Item Category') + DB::table('itemcategorys')
            ->where('status', 1)
            ->orderBy('category_name', 'asc')
            ->lists('category_name', 'category_id');

        $item_brands = array(
            '' => 'Select Item Brand') + DB::table('itembrands')
            ->where('status', 1)
            ->orderBy('brand_name', 'asc')
            ->lists('brand_name', 'brand_id');
        $item_locations = array(
            '' => 'Select Item Location') + DB::table('itemlocations')
            ->where('status', 1)
            ->orderBy('location_name', 'asc')
            ->lists('location_name', 'location_id');

        return View::make('admin.items.addMultipleItem', compact('suppliers','company','item_categorys', 'item_brands', 'item_locations', 'item_company'));
    }
    public function saveMultipleItem() {
        $data = Input::all();
        DB::beginTransaction();
        try {
            for($i = 0; $i < count($data['item_name']);$i++){
                if($data['category_id'] < 0){
                    return Redirect::to('admin/items')->with('errorMsg', 'Select Category Please');
                }
                $item = array(
                    'item_name' => $data['item_name'][$i],
                    'upc_code' => $data['upc_code'][$i],
                    'item_point' => $data['item_point'][$i],
                    'company_id' => empty($data['company_id']) ? null : $data['company_id'],
                    'supplier_id' => empty($data['supplier_id']) ? null : $data['supplier_id'],
                    'item_company_id' => empty($data['item_company_id']) ? null : $data['item_company_id'],
                    'category_id' => $data['category_id'],
                    'brand_id' => empty($data['brand_id']) ? null : $data['brand_id'],
                    'location_id' => empty($data['location_id']) ? null : $data['location_id'],
                    'tax_amount' => 0,
                    'description' => $data['item_name'][$i],
                    'created_by' => Session::get('emp_id'),
                    'created_at' => $this->timestamp
                );
                $last_item_id = DB::table('iteminfos')->insertGetId($item);
                $price_data = array(
                    'item_id' => $last_item_id,
                    'purchase_price' => 0,
                    'sale_price' => 0,
                    'created_by' => Session::get('emp_id'),
                    'created_at' => $this->timestamp
                );
                $last_price_id = DB::table('priceinfos')->insertGetId($price_data);

                $update_item = array(
                    'price_id' => $last_price_id,
                    'updated_by' => Session::get('emp_id'),
                    'updated_at' => $this->timestamp
                );
                /*
                 * If UPC not provided, item id and y-m-d will make upc code. if upc code length is
                 * less than 7 digit, 0 will be concate to fillup desire range
                 * Example itemId=25, custom_upc_code=2500000, final_upc_code=1503232500000
                 */

                if (empty($item['upc_code'])) {
                    $custom_upc_code = $last_item_id;
                    $max = 7;
                    while (strlen($custom_upc_code) < $max) {
                        $custom_upc_code .= 0;
                    }
                    $update_item['upc_code'] = $data['company_id'] . date('ymd') . $custom_upc_code;
                }
                DB::table('iteminfos')->where('item_id', $last_item_id)
                    ->update($update_item);

            }
            DB::commit();
            return Redirect::to('admin/items')->with('message', ($i).' Items Added Successfully');
        } catch (\Exception $e) {
            // return $e;
            DB::rollback();
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Redirect::to('admin/items')->with('errorMsg', $err_msg)->withInput();
        }
    }
    public function addItemForm()
    {
        $company = array(
            '' => 'Select Company') + DB::table('sub_companies')
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->lists('company_name', 'id');

        //temp code
        // $company = DB::table('sub_companies')
        //     ->where('status', 1)
        //     ->where('id', 1)
        //     ->orderBy('id', 'asc')
        //     ->lists('company_name', 'id');
        //temp code

        $suppliers = array(
            '' => 'Select Supplier') + DB::table('supplierinfos')
            ->where('status', 1)
            ->orderBy('supp_id', 'asc')
            ->lists('supp_or_comp_name', 'supp_id');
        $item_company = array(
            '' => 'Select Item Company') + DB::table('companynames')
            ->where('status', 1)
            ->orderBy('company_name', 'asc')
            ->lists('company_name', 'company_id');
        // $item_company = DB::table('companynames')
        //     ->where('status', 1)
        //     ->where('company_id', 223)
        //     ->orderBy('company_name', 'asc')
        //     ->lists('company_name', 'company_id');
        $item_categorys = array(
            '' => 'Select Item Category') + DB::table('itemcategorys')
            ->where('status', 1)
            ->orderBy('category_name', 'asc')
            ->lists('category_name', 'category_id');
        // $item_categorys = DB::table('itemcategorys')
        //     ->where('status', 1)
        //     ->where('category_id', 46)
        //     ->orderBy('category_name', 'asc')
        //     ->lists('category_name', 'category_id');
        $item_brands = array(
            '' => 'Select Item Brand') + DB::table('itembrands')
            ->where('status', 1)
            ->orderBy('brand_name', 'asc')
            ->lists('brand_name', 'brand_id');
        $item_locations = array(
            '' => 'Select Item Location') + DB::table('itemlocations')
            ->where('status', 1)
            ->orderBy('location_name', 'asc')
            ->lists('location_name', 'location_id');
        // $item_locations = DB::table('itemlocations')
        //     ->where('status', 1)
        //     ->where('location_id', 162)
        //     ->orderBy('location_name', 'asc')
        //     ->lists('location_name', 'location_id');
        return View::make('admin.items.addItem', compact('suppliers','company','item_categorys', 'item_brands', 'item_locations', 'item_company'));
    }

    public function saveItem()
    {
        DB::beginTransaction();
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$item_rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $item = array(
                'item_name' => Input::get('item_name'),
                'upc_code' => Input::get('upc_code'),
                'company_id' => empty(Input::get('company_id')) ? null : Input::get('company_id'),
                'supplier_id' => empty(Input::get('supplier_id')) ? null : Input::get('supplier_id'),
                'item_company_id' => empty(Input::get('item_company_id')) ? null : Input::get('item_company_id'),
                'category_id' => Input::get('category_id'),
                'brand_id' => empty(Input::get('brand_id')) ? null : Input::get('brand_id'),
                'location_id' => empty(Input::get('location_id')) ? null : Input::get('location_id'),
                'unit' => Input::get('unit'),
                'carton' => Input::get('carton'),
                'description' => Input::get('description'),
                'created_by' => Session::get('emp_id'),
                'created_at' => $this->timestamp
            );
            $last_item_id = DB::table('iteminfos')->insertGetId($item);
            $price_data = array(
                'item_id' => $last_item_id,
                'purchase_price' => 0,
                'sale_price' => 0,
                'created_by' => Session::get('emp_id'),
                'created_at' => $this->timestamp
            );
            $last_price_id = DB::table('priceinfos')->insertGetId($price_data);

            $update_item = array(
                'price_id' => $last_price_id,
                'updated_by' => Session::get('emp_id'),
                'updated_at' => $this->timestamp
            );
            /*
             * If UPC not provided, item id and y-m-d will make upc code. if upc code length is
             * less than 7 digit, 0 will be concate to fillup desire range
             * Example itemId=25, custom_upc_code=2500000, final_upc_code=1503232500000
             */

            if (empty($item['upc_code'])) {
                $custom_upc_code = $last_item_id;
                $max = 7;
                while (strlen($custom_upc_code) < $max) {
                    $custom_upc_code .= 0;
                }
                $update_item['upc_code'] = Input::get('company_id') . date('ymd') . $custom_upc_code;
            }
            DB::table('iteminfos')->where('item_id', $last_item_id)
                ->update($update_item);

            DB::commit();
            return Redirect::to('admin/items')->with('message', 'Added Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Redirect::to('admin/items')->with('errorMsg', $err_msg)->withInput();
        }
    }
    public function saveItemCustom()
    {      
        return Session::get('branch_id');
        print "<pre>";
        $allItems = Helper::multipleBarcodeItem();
        $finalArray = [];
        foreach($allItems as $item){
            $barcodeArray = [];
            $objItem['product_name']    = $item['product_name'];
            $objItem['category']        = $item['category'];
            $objItem['carton_qty']      = $item['carton_qty'];
            $objItem['purchase_price']  = $item['purchase_price'];
            $objItem['sale_price']      = $item['sale_price'];
            for($i=0;$i<8;$i++){
                $barcodeArray[$i] = $item["barcode[".$i."]"];
                if ($i == 7) {
                    $objItem['barcode'] = implode(' ', $barcodeArray);
                }
            }
            $catId = DB::table('itemcategorys')
                ->where('category_name',$objItem['category'])
                ->first();
            
            $objItem['category_id'] = $catId->category_id;
            $carton_qty_unit_array = explode(' ', $objItem['carton_qty']);
            $objItem['carton'] = $carton_qty_unit_array[0];
            $objItem['unit'] = $carton_qty_unit_array[1];
            $finalArray[] = $objItem;
        }
        // print_r($finalArray);
        // exit;
        foreach($finalArray as $value){
            $singleItem = (object) $value;
            // print_r($singleItem);
            // exit;
            DB::beginTransaction();
            try {
                $item = array(
                    'item_name' => $singleItem->product_name,
                    'upc_code' => $singleItem->barcode,
                    'company_id' => null,
                    'supplier_id' => null,
                    'item_company_id' => null,
                    'category_id' => $singleItem->category_id,
                    'brand_id' => null,
                    'location_id' => null,
                    'unit' => $singleItem->unit,
                    'carton' => $singleItem->carton,
                    'description' => null,
                    'created_by' => Session::get('emp_id'),
                    'created_at' => $this->timestamp
                );
                $last_item_id = DB::table('iteminfos')->insertGetId($item);
                $price_data = array(
                    'item_id' => $last_item_id,
                    'purchase_price' => $singleItem->purchase_price,
                    'sale_price' => $singleItem->sale_price,
                    'created_by' => Session::get('emp_id'),
                    'created_at' => $this->timestamp
                );
                $last_price_id = DB::table('priceinfos')->insertGetId($price_data);

                $update_item = array(
                    'price_id' => $last_price_id,
                    'updated_by' => Session::get('emp_id'),
                    'updated_at' => $this->timestamp
                );
                /*
                 * If UPC not provided, item id and y-m-d will make upc code. if upc code length is
                 * less than 7 digit, 0 will be concate to fillup desire range
                 * Example itemId=25, custom_upc_code=2500000, final_upc_code=1503232500000
                 */

                if (empty($item['upc_code'])) {
                    $custom_upc_code = $last_item_id;
                    $max = 7;
                    while (strlen($custom_upc_code) < $max) {
                        $custom_upc_code .= 0;
                    }
                    $update_item['upc_code'] = date('ymd') . $custom_upc_code;
                }
                DB::table('iteminfos')->where('item_id', $last_item_id)
                    ->update($update_item);
                DB::commit();
                // return Redirect::to('admin/items')->with('message', 'Added Successfully');
            } catch (\Exception $e) {
                DB::rollback();
                return $e;
                Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
                $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
                return Redirect::to('admin/items')->with('errorMsg', $err_msg)->withInput();
            }
        }
    }
    public function getAllItem()
    {
        return Datatable::query(DB::table('iteminfos as i')
            ->select('i.item_id', 'ic.category_name', 'com.company_name', 'i.upc_code', 'i.item_name', 'p.purchase_price', 'p.price_id', 'p.sale_price', 'i.tax_amount', 'i.offer')
            ->join('itemcategorys as ic', 'ic.category_id', '=', 'i.category_id')
            ->leftJoin('companynames as com', 'com.company_id', '=', 'i.item_company_id')
            ->leftJoin('itembrands as b', 'i.brand_id', '=', 'b.brand_id')
            ->leftJoin('priceinfos as p', 'i.price_id', '=', 'p.price_id')
            ->where('i.status', '=', '1')
            ->groupBy('i.item_id'))
            ->addColumn('#', function ($model) {
                return '<input type="checkbox" name="barcodeInfo[]" value="' . $model->item_id . '-' . $model->sale_price . '">';
            })
            ->showColumns('upc_code', 'item_name', 'company_name', 'category_name', 'purchase_price', 'sale_price')
            ->addColumn('action', function ($model) {
                $html = '<div class="btn-group">';
                $html .= '<a class="btn btn-primary btn-small" href="javascript:;" data-toggle="dropdown"><i class="icon-user icon-white"></i> Action</a>';
                $html .= '<a class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown" href="javascript:;"><span class="caret"></span></a>';
                $html .= '<ul class="dropdown-menu">';
                $html .= '<li><a onclick="ItemEdit(' . $model->item_id . ')" href="#editItemModal"  role="button" data-toggle="modal"><i class="icon-edit"></i>&nbsp; Item info Edit</a></li>';
                $html .= '<li><a onclick="ItemSuppliers(' . $model->item_id . ')" href="#viewItemSuppliers"  role="button" data-toggle="modal"><i class="icon-zoom-in"></i>&nbsp; Item Suppliers</a></li>';
                if (Session::get('role') == 2) {
                    $html .= '<li><a href="#" title="Inactive" onclick="return deleteConfirm(' . $model->item_id . ')" id="' . $model->item_id . '"><i class="icon-trash"></i> Delete</a></li>';
                }

                $html .= '</ul> ';

                $html .= '</div>';
                return $html;
            })
            ->searchColumns('upc_code', 'item_name', 'company_name', 'category_name')
            ->setSearchWithAlias()
            ->orderColumns('upc_code', 'item_name', 'company_name', 'category_name', 'purchase_price', 'sale_price', 'offer')
            ->make();
    }
    public function getItemData()
    {
        return Datatable::query(DB::table('stockitems as s')
            ->select('s.stock_item_id', 'i.item_id', 'ic.category_name', 'com.company_name', 'i.upc_code', 'i.item_name','i.carton', 'p.purchase_price', 'p.price_id', 'p.sale_price', 'i.tax_amount', 'i.offer', DB::raw('SUM(s.available_quantity) as available_qty'), DB::raw('count(s.item_id) as differentPrice'))
            ->leftJoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
            ->leftJoin('itemcategorys as ic', 'ic.category_id', '=', 'i.category_id')
            ->leftJoin('companynames as com', 'com.company_id', '=', 'i.item_company_id')
            ->leftJoin('itembrands as b', 'i.brand_id', '=', 'b.brand_id')
            ->leftJoin('itemlocations as l', 'i.location_id', '=', 'l.location_id')
            ->leftJoin('priceinfos as p', 's.price_id', '=', 'p.price_id')
            ->where('s.status', '=', '1')
            ->groupBy('s.item_id'))
            ->addColumn('#', function ($model) {
                if ($model->differentPrice > 1) {
                    return '';
                } else {
                    return '<input type="checkbox" name="barcodeInfo[]" value="' . $model->item_id . '-' . $model->sale_price . '">';
                }
            })
            ->showColumns('upc_code', 'item_name', 'purchase_price', 'sale_price', 'category_name', 'available_qty')
            ->addColumn('PCS/Carton', function ($model) {
                return '<label class="label label-success" style="font-size:14px; width:60%; height:15px; padding-top:5px; text-align:center;">'.$model->carton. '</label> ';
            })
            ->addColumn('carton_quantity', function ($model) {
                return round($model->available_qty/$model->carton,3);
            })
            ->addColumn('action', function ($model) {
                $html = '<div class="btn-group" style="width:170px;">';
                $html .= '<a class="btn btn-primary btn-small" href="javascript:;" data-toggle="dropdown"><i class="icon-user icon-white"></i> Action</a>';
                $html .= '<a class="btn btn-primary btn-small dropdown-toggle" data-toggle="dropdown" href="javascript:;"><span class="caret"></span></a>';
                $html .= '<ul class="dropdown-menu">';
                $html .= '<li><a onclick="ItemEdit(' . $model->item_id . ')" href="#editItemModal"  role="button" data-toggle="modal"><i class="icon-edit"></i>&nbsp; Item info Edit</a></li>';
                $html .= '<li><a onclick="ItemSuppliers(' . $model->item_id . ')" href="#viewItemSuppliers"  role="button" data-toggle="modal"><i class="icon-zoom-in"></i>&nbsp; Item Suppliers</a></li>';
                if (Session::get('role') == 2) {
                    $html .= '<li><a href="#" title="Inactive" onclick="return deleteConfirm(' . $model->item_id . ')" id="' . $model->item_id . '"><i class="icon-trash"></i> Delete</a></li>';
                }

                if ($model->differentPrice > 1) {

                } else {
                    $html .= '<li><a onclick="editPrice(' . $model->stock_item_id . ')" href="#editPriceModal"  role="button" data-toggle="modal"><i class="icon-edit"></i>&nbsp; Price Edit</a></li>';
                    if (Session::get('role') == 2 || Session::get('role') == 1) {
                        $html .= '<li><a onclick="editQuantity(' . $model->stock_item_id . ')" href="#editQuantityModal"  role="button" data-toggle="modal"><i class="icon-pencil"></i>&nbsp; Quantity Edit</a></li>';
                    }
                }
                $html .= '</ul> ';

                if ($model->differentPrice > 1) {
                    $html .= '<a class="btn btn-success btn-small" style="margin-left: 3px;" title="Multiple prices are here" onclick="differentPrice(' . $model->item_id . ')" href="#diffPrice"  role="button" data-toggle="modal">Diff.Price</a>';
                }
                $html .= '</div>';
                return $html;
            })
            
            ->searchColumns('upc_code', 'item_name', 'company_name', 'category_name')
            ->setSearchWithAlias()
            ->orderColumns('upc_code', 'item_name', 'company_name', 'purchase_price', 'sale_price', 'category_name', 'available_qty')
            ->make();
    }
    public function editItemForm($itemId)
    {
        $iteminfos = DB::table('iteminfos')->where('item_id', $itemId)->first();

        $company = array(
            '' => 'Select Company') + DB::table('sub_companies')
            ->where('status', 1)
            ->orderBy('id', 'asc')
            ->lists('company_name', 'id');
        $suppliers = array(
            '' => 'Select Supplier') + DB::table('supplierinfos')
            ->where('status', 1)
            ->orderBy('supp_id', 'asc')
            ->lists('supp_or_comp_name', 'supp_id');
        $item_company = array(
            '' => 'Select Item Company') + DB::table('companynames')
            ->where('status', 1)
            ->orderBy('company_name', 'asc')
            ->lists('company_name', 'company_id');
        $item_categorys = array(
            '' => 'Please Select Item Category') + DB::table('itemcategorys')
            ->where('status', 1)
            ->orderBy('category_name', 'asc')
            ->lists('category_name', 'category_id');
        $item_brands = array(
            '' => 'Select Item Brand') + DB::table('itembrands')
            ->where('status', 1)
            ->orderBy('brand_name', 'asc')
            ->lists('brand_name', 'brand_id');
        $item_locations = array(
            '' => 'Select Item Location') + DB::table('itemlocations')
            ->where('status', 1)
            ->orderBy('location_name', 'asc')
            ->lists('location_name', 'location_id');

        return View::make('admin.items.editItemModal', compact('suppliers','company','iteminfos', 'item_categorys', 'item_brands', 'item_locations', 'item_company'));
    }
    public function viewItemSuppliers($itemId)
    {
        $supplierInfos = DB::table('itempurchases as ip')
            ->select('ip.i_purchase_id', 'ip.sup_invoice_id', 'ip.item_id', 'si.supp_id', 'spi.user_name', 'spi.supp_or_comp_name', 'spi.mobile', 'spi.present_address')
            ->leftJoin('supinvoices as si', 'si.sup_invoice_id', '=', 'ip.sup_invoice_id')
            ->leftJoin('supplierinfos as spi', 'spi.supp_id', '=', 'si.supp_id')
            ->where('ip.item_id', '=', $itemId)
            ->groupBy('si.supp_id')
            ->get();

        return View::make('admin.items.viewItemSuppliers', compact('supplierInfos'));
    }
    public function itemPriceEdit($stockItemId)
    {
        //echo $stockItemId;exit;
        $itemInfo = DB::table('stockitems')
            ->select('stockitems.stock_item_id', 'i.item_id', 'i.item_name', 'p.purchase_price', 'p.sale_price')
            ->leftJoin('iteminfos as i', 'stockitems.item_id', '=', 'i.item_id')
            ->leftJoin('priceinfos as p', 'stockitems.price_id', '=', 'p.price_id')
            ->where('stockitems.stock_item_id', '=', $stockItemId)
            ->first();
        //echo'<pre>';print_r($itemInfo);exit;
        return View::make('admin.items.editPriceModal', compact('itemInfo'));
    }
    public function savePriceEdit()
    {
        //echo '<pre>'; print_r(Input::all());exit;
        try {

            $item_id = Input::get('item_id');
            $purchase_price = Input::get('purchase_price');
            $sale_price = Input::get('sale_price');
            $stock_item_id = Input::get('stock_item_id');
            //echo $item_id.'&nbsp;'.$purchase_price.'&nbsp;'.$sale_price;exit;
            $priceInfo = DB::table('priceinfos')
                ->where('priceinfos.item_id', '=', $item_id)
                ->where('priceinfos.purchase_price', '=', $purchase_price)
                ->where('priceinfos.sale_price', '=', $sale_price)
                ->first();
            if ($priceInfo) {
                $price_id = $priceInfo->price_id;

            } else {
                $insertData = array(
                    'item_id' => $item_id,
                    'purchase_price' => $purchase_price,
                    'sale_price' => $sale_price,
                    'status' => 0,
                    'created_by' => Session::get('emp_id'),
                    'created_at' => $this->timestamp
                );
                $price_id = DB::table('priceinfos')->insertGetId($insertData);
            }
            $another_stock_item_id = DB::table('stockitems')
                ->where('stockitems.stock_item_id', '!=', $stock_item_id)
                ->where('stockitems.price_id', '=', $price_id)
                ->where('stockitems.item_id', '=', $item_id)
                ->first();
            if ($another_stock_item_id) {

                $disableDuplicate = DB::table('stockitems')
                    ->where('stock_item_id', $another_stock_item_id->stock_item_id)
                    ->update(array(
                        'available_quantity' => 0,
                        'status' => 0,
                    ));
                $updateStockPriceId = DB::table('stockitems')
                    ->where('stock_item_id', '=', $stock_item_id)
                    ->increment('available_quantity', $another_stock_item_id->available_quantity,
                        array(
                            'status' => 1,
                            'updated_by' => Session::get('emp_id'),
                            'updated_at' => $this->timestamp
                        )
                    );
                //check is it same price which you are doing to update?
                $selectOldPriceId = DB::table('stockitems')
                    ->where('stockitems.stock_item_id', $stock_item_id)
                    ->where('stockitems.price_id', '=', $price_id)
                    ->where('stockitems.item_id', '=', $item_id)
                    ->count();
                if ($selectOldPriceId == 0) {
                    //if the new price info dosen't matched to the existing stockItem Id
                    $selectUpdatedQuantity = DB::table('stockitems')
                        ->where('stockitems.stock_item_id', $stock_item_id)
                        ->first();
                    $newQUantity = $selectUpdatedQuantity->available_quantity;
                    $updateStockPriceId = DB::table('stockitems')
                        ->where('stock_item_id', $another_stock_item_id->stock_item_id)
                        ->update(array(
                            'available_quantity' => $newQUantity,
                            'status' => 1
                        ));
                    $resetOldStock = DB::table('stockitems')
                        ->where('stock_item_id', $stock_item_id)
                        ->update(array(
                            'available_quantity' => 0,
                            'status' => 0
                        ));
                }
            } else {
                $updateStockPriceId = DB::table('stockitems')
                    ->where('stock_item_id', $stock_item_id)
                    ->update(array('price_id' => $price_id));
            }
            if ($updateStockPriceId) {

                if (Request::ajax()) {
                    return Response::json(['status' => 'success']);
                }
                return Redirect::to('admin/items')->with('message', 'Updated Successfully');
            } else {
                if (Request::ajax()) {
                    return Response::json(['status' => 'Sorry ! Operation failed']);
                }
                return Redirect::to('admin/items')->with('message', 'Sorry ! Operation failed');
            }
        } catch (\Exception $e) {
            return $e;
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            if (Request::ajax()) {
                return Response::json(['status' => 'Sorry ! Duplicate Entry Found.']);
            }
            return;
            //return Redirect::to('admin/items')->with('errorMsg', $err_msg)->withInput();
        }
    }
    public function editItemSave()
    {
        try {
            $data = Input::all();
            $validator = Validator::make($data, Itemcategory::$item_rules);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            }
            $item_id = Input::get('item_id');
            $item_update = array(
                'item_name' => Input::get('item_name'),
                'upc_code' => Input::get('upc_code'),
                'company_id' => empty(Input::get('company_id')) ? null : Input::get('company_id'),
                'supplier_id' => empty(Input::get('supplier_id')) ? null : Input::get('supplier_id'),
                'item_company_id' => empty(Input::get('item_company_id')) ? null : Input::get('item_company_id'),
                'category_id' => Input::get('category_id'),
                'brand_id' => empty(Input::get('brand_id')) ? null : Input::get('brand_id'),
                'location_id' => empty(Input::get('location_id')) ? null : Input::get('location_id'),
                'description' => Input::get('description'),
                'unit' => Input::get('unit'),
                'carton' => Input::get('carton'),
                'updated_by' => Session::get('emp_id'),
                'updated_at' => $this->timestamp
            );
            //return $tie
            DB::table('iteminfos')->where('item_id', $item_id)->update($item_update);

            return Redirect::back()->with('message', 'Updated Successfully');
        } catch (\Exception $e) {
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Redirect::to('admin/items')->with('errorMsg', $err_msg)->withInput();
        }
    }
    /*
     * Item Qty Eidt 
     */
    public function itemQtyEdit($stockItemId)
    {
        //echo $stockItemId;exit;
        $itemInfo = DB::table('stockitems')
            ->select('stockitems.stock_item_id', 'stockitems.available_quantity', 'i.item_name')
            ->leftJoin('iteminfos as i', 'stockitems.item_id', '=', 'i.item_id')
            ->where('stockitems.stock_item_id', '=', $stockItemId)
            ->first();
        return View::make('admin.items.editQtyModal', compact('itemInfo'));
    }
    public function saveQtyEdit()
    {
        try {
            $available_quantity = Input::get('available_quantity');
            $stock_item_id = Input::get('stock_item_id');

            $updateStockQty = DB::table('stockitems')
                ->where('stock_item_id', $stock_item_id)
                ->update(array(
                        'available_quantity' => $available_quantity,
                        'updated_by' => Session::get('emp_id'),
                        'status' => 1,
                        'updated_at' => $this->timestamp)
                );
            if ($updateStockQty) {
                return Redirect::to('admin/items')->with('message', 'Updated Successfully');
            }
            return Redirect::to('admin/items')->with('message', 'Sorry ! Operation failed');
        } catch (\Exception $e) {
            Session::flash('mySqlError', $e->errorInfo[2]); // session set only once
            $err_msg = Lang::get("mysqlError." . $e->errorInfo[1]);
            return Redirect::to('admin/items')->with('errorMsg', $err_msg)->withInput();
        }
    }

    public function itemRemove($itemId)
    {
        $itemRev = DB::table('iteminfos')
            ->where('item_id', $itemId)
            ->update(array('status' => 0));
        if ($itemRev) {
            return Response::json(['status' => 'success']);
        }
        return Response::json(['status' => 'error']);
    }

    public function differentPricesItem($itemId)
    {
        $itemInfo = DB::table('stockitems as s')
            ->join('priceinfos as p', 's.price_id', '=', 'p.price_id')
            ->where('s.item_id', $itemId)
            ->where('s.status', '=', 1)
            ->get();
        return View::make('admin.items.multipleItemViewModal', compact('itemInfo'));
    }
    /*
     * 	Godown Item
     */
    public function godownItem()
    {
        $title = ':: POSv2 :: - Godown Item List';
        return View::make('admin.items.viewGodownItem', compact('title'));
    }
    public function getGodownItemData()
    {
        return Datatable::query(DB::table('godownitems as gdi')
            ->select('gdi.godown_item_id', 'i.upc_code', 'i.item_id', 'i.item_name', 'com.company_name', 'b.brand_name', 'l.location_name', 'p.price_id', 'p.purchase_price', 'p.sale_price', 'i.tax_amount', DB::raw('SUM(gdi.available_quantity) as available_qty'), DB::raw('count(gdi.item_id) as differentPrice'))
            ->leftJoin('iteminfos as i', 'gdi.item_id', '=', 'i.item_id')
            ->leftJoin('companynames as com', 'com.company_id', '=', 'i.item_company_id')
            ->leftJoin('itembrands as b', 'i.brand_id', '=', 'b.brand_id')
            ->leftJoin('itemlocations as l', 'i.location_id', '=', 'l.location_id')
            ->leftJoin('priceinfos as p', 'gdi.price_id', '=', 'p.price_id')
            ->where('gdi.status', '=', '1')
            ->groupBy('gdi.item_id'))
            ->addColumn('#', function ($model) {
                if ($model->differentPrice > 1) {
                    return '';
                } else {
                    return '<input type="checkbox" name="barcodeInfo[]" value="' . $model->item_id . '-' . $model->sale_price . '">';
                }
            })
            ->showColumns('upc_code', 'item_name', 'company_name', 'brand_name', 'location_name', 'purchase_price', 'sale_price', 'available_qty')
            ->addColumn('Diff.Price', function ($model) {
                if ($model->differentPrice > 1) {
                    return '<a class="btn btn-success btn-small" style="margin-left: 3px;" title="Multiple prices are here" onclick="gdDifferentPrice(' . $model->item_id . ')" href="#diffPrice"  role="button" data-toggle="modal"> Details</a>';
                }
            })
            ->addColumn('Update', function ($model) {
                $html = '<a onclick="ItemEdit(' . $model->item_id . ')" href="#editItemModal"  data-toggle="modal" class="btn btn-warning btn-xs">Item Edit</a>';
                if ($model->differentPrice > 1) {
                    $html .= '<a onclick="differentItemPriceEdit(' . $model->item_id . ')" href="#diffPrice" data-toggle="modal" class="btn btn-primary btn-xs">Price Edit</a>';
                } else {
                    $html .= '<a onclick="ItemPriceEdit(' . $model->godown_item_id . ')" href="#editItemPriceModal" data-toggle="modal" class="btn btn-primary btn-xs">Price Edit</a>';
                }
                return $html;
            })
            ->searchColumns('upc_code', 'item_name', 'company_name')
            ->setSearchWithAlias()
            ->orderColumns('upc_code', 'item_name', 'company_name', 'brand_name', 'location_name', 'purchase_price', 'sale_price', 'available_qty')
            ->make();
    }
    public function goDownDifferentPricesItem($itemId)
    {
        $itemInfos = DB::table('godownitems')
            ->join('priceinfos as p', 'godownitems.price_id', '=', 'p.price_id')
            ->where('godownitems.item_id', $itemId)
            ->where('godownitems.status', '=', 1)
            ->get();
        return View::make('admin.items.multipleGodownItemModal', compact('itemInfos'));
    }
    /*
     * Recent Add Items
     */
    public function getRecentItems()
    {
        $title = ':: POSv2 :: - View Recent Add Items';
        return View::make('admin.items.viewRecentItem', compact('title'));
    }

    public function recentItemsDatable()
    {
        return Datatable::query(DB::table('iteminfos as i')
            ->leftJoin('itembrands as b', 'i.brand_id', '=', 'b.brand_id')
            ->leftJoin('itemlocations as l', 'i.location_id', '=', 'l.location_id')
            ->leftJoin('companynames as com', 'com.company_id', '=', 'i.item_company_id')
            ->leftJoin('itemcategorys as c', 'i.category_id', '=', 'c.category_id')
            ->leftJoin('priceinfos as p', 'i.price_id', '=', 'p.price_id')
            ->select('i.item_id', 'i.upc_code', 'i.item_name', 'com.company_name', 'b.brand_name', 'c.category_name', 'l.location_name', 'i.tax_amount', 'i.offer', 'p.created_at')
            ->where('p.purchase_price', 0)
            ->where('i.status', 1))
            ->showColumns('upc_code', 'item_name', 'company_name', 'brand_name', 'category_name', 'location_name', 'tax_amount', 'offer', 'created_at')
            ->addColumn('action', function ($model) {
                $html = '<div class="span3">
								<a class="btn btn-primary btn-small" onclick="ItemEdit(' . $model->item_id . ')" href="#editItemModal"  role="button" data-toggle="modal"><i class="icon-edit"></i> Edit</a>' . ' | ' .
                    '<a class="btn btn-warning btn-small" href="javascript:;" onclick="return deleteConfirm(' . $model->item_id . ')" id="' . $model->item_id . '"><i class="icon-remove"></i> Inactive</a>
							</div>';
                return $html;
            })
            ->searchColumns('upc_code', 'item_name', 'company_name')
            ->setSearchWithAlias()
            ->orderColumns('upc_code', 'item_name', 'company_name', 'brand_name', 'category_name', 'location_name', 'tax_amount', 'offer', 'created_at')
            ->make();
    }
    public function godownLowInventory()
    {
        return View::make('admin.items.viewGodownInventory');
    }
    public function getGLInventory()
    {
        return Datatable::query(DB::table('godownitems as g')
            ->select('g.godown_item_id', 'i.item_id', 'i.upc_code', 'com.company_name', 'i.item_name', 'p.purchase_price', 'p.sale_price', 'i.tax_amount', 'i.offer', DB::raw('SUM(g.available_quantity) as available_qty'))
            ->leftJoin('iteminfos as i', 'g.item_id', '=', 'i.item_id')
            ->leftJoin('companynames as com', 'com.company_id', '=', 'i.item_company_id')
            ->leftJoin('itembrands as b', 'i.brand_id', '=', 'b.brand_id')
            ->leftJoin('itemlocations as l', 'i.location_id', '=', 'l.location_id')
            ->leftJoin('priceinfos as p', 'g.price_id', '=', 'p.price_id')
            ->where('i.status', '=', '1')
            ->groupBy('g.item_id')
        //->having(DB::raw('sum(g.available_quantity)'), '<', 51)
        )
            ->addColumn('#', function ($model) {

                return '<input type="checkbox" name="barcodeInfo[]" value="' . $model->item_id . '-' . $model->sale_price . '">';

            })
            ->showColumns('upc_code', 'item_name', 'company_name', 'purchase_price', 'sale_price', 'offer', 'available_qty')
            ->searchColumns('upc_code', 'item_name', 'company_name')
            ->setSearchWithAlias()
            ->orderColumns('upc_code', 'item_name', 'company_name', 'purchase_price', 'sale_price', 'offer', 'available_qty')
            ->make();
    }
    public function stockLowInventory()
    {
        return View::make('admin.items.viewStockInventory');
    }
    public function getSLInventory()
    {
        return Datatable::query(DB::table('stockitems as s')
            ->select('s.stock_item_id', 'i.item_id', 'i.upc_code', 'com.company_name', 'i.item_name', 'p.purchase_price', 'p.sale_price', 'i.tax_amount', 'i.offer', DB::raw('SUM(s.available_quantity) as available_qty'))
            ->leftJoin('iteminfos as i', 's.item_id', '=', 'i.item_id')
            ->leftJoin('companynames as com', 'com.company_id', '=', 'i.item_company_id')
            ->leftJoin('itembrands as b', 'i.brand_id', '=', 'b.brand_id')
            ->leftJoin('itemlocations as l', 'i.location_id', '=', 'l.location_id')
            ->leftJoin('priceinfos as p', 's.price_id', '=', 'p.price_id')
            ->where('i.status', '=', '1')
            ->groupBy('s.item_id')
            ->having(DB::raw('sum(s.available_quantity)'), '<', 10)
        )
            ->addColumn('#', function ($model) {

            })
            ->showColumns('upc_code', 'item_name', 'company_name', 'purchase_price', 'sale_price', 'offer', 'available_qty')
            ->searchColumns('item_name', 'company_name', 'upc_code')
            ->setSearchWithAlias()
            ->orderColumns('upc_code', 'item_name', 'company_name', 'purchase_price', 'sale_price', 'offer', 'available_qty')
            ->make();
    }
    public function barcodeQueueAll()
    {
        $barcodeInfo = Input::get('barcodeInfo');
        //Session::forget('BarcodeQueueItems');
        //return Redirect::back();
        // echo'<pre>';print_r(Session::get('BarcodeQueueItems'));exit;

        if (empty($barcodeInfo)) {
            return Redirect::to('admin/items')->with('errorMsg', 'Please at least select one item!');
        }
        foreach ($barcodeInfo as $iteminfo) {
            $temp = "BarcodeQueueItems." . $iteminfo;
            // echo $temp;exit;
            Session::put("$temp", $iteminfo);

        }
        return Redirect::back()->with('message', 'Item added to barcode Queue');
    }
    public function generateBarcode()
    {
        $BarcodeQueueItems = Session::get('BarcodeQueueItems');

        if (empty($BarcodeQueueItems)) {
            $err_msg = "Please at first Item add to the Queue!";
            return Redirect::back()->with('errorMsg', $err_msg);
        } else {

            $itemBarcodeInfos = array();

            if (isset($BarcodeQueueItems)) {

                foreach ($BarcodeQueueItems as $item) {
                    $datas = array();
                    if (is_array($item) && count($item) > 0) {
                        $itemData = explode("-", $item['00']);
                        $datas['key'] = $item['00'];

                    } else {
                        $itemData = explode("-", $item);
                        $datas['key'] = $item;
                    }
                    //echo $itemData[0];   it is item id
                    $itemInfo = DB::table('iteminfos')
                        ->where('item_id', $itemData[0])
                        ->first();

                    $datas['upc_code'] = $itemInfo->upc_code;
                    $datas['item_name'] = $itemInfo->item_name;
                    $datas['sale_price'] = $itemData[1];

                    array_push($itemBarcodeInfos, $datas);
                }

                //echo'<pre>';print_r($itemBarcodeInfos);exit;
            }

            return View::make('admin.items.barcodeGenerator', compact('itemBarcodeInfos'));
        }
    }


    public function barcodePrint()
    {
        $barcode_quantity = Input::get('barcode_quantity');
        $itemInfo = Input::get('itemInfo');

        return View::make('admin.items.barcodePrint', compact('itemInfo', 'barcode_quantity'));
    }


    public function barcodeQueueEmpty()
    {

        Session::forget('BarcodeQueueItems');
        return Redirect::back('admin/items')->with('message', 'The Queue is empty now!');

    }

    public function barcodeQueueItemDelete($key)
    {
        Session::forget("BarcodeQueueItems.$key");
        return Redirect::back()->with('message', 'The item is deleted form Queue.');

    }

    public function itemGodownPriceEdit($godownItemId)
    {
        //echo $stockItemId;exit;
        $itemInfo = DB::table('godownitems')
            ->select('godownitems.godown_item_id', 'i.item_id', 'i.item_name', 'p.purchase_price', 'p.sale_price', 'p.price_id')
            ->leftJoin('iteminfos as i', 'godownitems.item_id', '=', 'i.item_id')
            ->leftJoin('priceinfos as p', 'godownitems.price_id', '=', 'p.price_id')
            ->where('godownitems.godown_item_id', '=', $godownItemId)
            ->first();
        //return $itemInfo;
        //echo'<pre>';print_r($itemInfo);exit;
        return View::make('admin.items.editGodownItemPriceModal', compact('itemInfo'));
    }

    public function itemGodownPriceEditSave()
    {

        $price_id = Input::get('price_id');
        $item_id = Input::get('item_id');
        $purchase_price = Input::get('purchase_price');
        $ex_purchase_price = Input::get('ex_purchase_price');
        $sale_price = Input::get('sale_price');

        $itemInfo = DB::table('itempurchases')
            ->select('itempurchases.*')
            ->where('itempurchases.item_id', '=', $item_id)
            ->where('itempurchases.price_id', '=', $price_id)
            ->get();
        foreach ($itemInfo as $purchaseItemInfo) {
            //multiple invoice found

            $sup_invoice_id = $purchaseItemInfo->sup_invoice_id;
            //check if multiple product purchase by one invoice_id

            $sup_invoice_info = DB::table('supinvoices')
                ->where('supinvoices.sup_invoice_id', '=', $sup_invoice_id)
                ->first();

            $selectItemPurchase = DB::table('itempurchases')
                ->where('itempurchases.sup_invoice_id', '=', $sup_invoice_id)
                ->where('itempurchases.item_id', '=', $item_id)
                ->where('itempurchases.price_id', '=', $price_id)
                ->first();
            $quantity = $selectItemPurchase->quantity;
            $i_purchase_id = $selectItemPurchase->i_purchase_id;
            $ex_amount = $sup_invoice_info->amount;

            $resetAmount = $ex_amount - ($ex_purchase_price * $quantity);
            $newAmount = $resetAmount + ($purchase_price * $quantity);
            DB::table('supinvoices')
                ->where('sup_invoice_id', $sup_invoice_id)
                ->update(array(
                    'amount' => $newAmount,
                    'pay' => $newAmount
                ));


            DB::table('itempurchases')
                ->where('i_purchase_id', $i_purchase_id)
                ->update(array(
                    'amount' => ($purchase_price * $quantity)
                ));


            DB::table('priceinfos')
                ->where('price_id', $price_id)
                ->update(array(
                    'purchase_price' => $purchase_price,
                    'sale_price' => $sale_price,
                    'updated_at' => date('Y-m-d h:i:s')
                ));
        }

        return Redirect::back()->with('message', 'Updated Successfully');

    }

    public function selectDifferentPriceItemFromGodown($itemId)
    {
        $itemInfos = DB::table('godownitems')
            ->join('priceinfos as p', 'godownitems.price_id', '=', 'p.price_id')
            ->where('godownitems.item_id', $itemId)
            ->where('godownitems.status', '=', 1)
            ->get();
        return View::make('admin.items.editGodownDifferentPrice', compact('itemInfos'));
    }

    public function udpateDifferentPriceItemToGodown()
    {
        echo "<pre>";
        $flag = 0;
        for ($j = 0; $j < sizeof(Input::get('purchase_price')); $j++) {
            $price_id = Input::get("price_id$j");
            if (isset($price_id)) {
                ++$flag;
            }
        }
        if ($flag > 1) {
            return "Multiple Checked. Please check only one.";
        }

        for ($i = 0; $i < sizeof(Input::get('purchase_price')); $i++) {
            if (Input::get("price_id$i")) {
                $price_id = Input::get("price_id$i");
                $purchase_price = Input::get('purchase_price')[$i];
                $sale_price = Input::get('sale_price')[$i];
            }

        }
        $total_quantity = Input::get('total_quantity');


        $item_id = Input::get('item_id');

        $purchaseItemInfo = DB::table('itempurchases')
            ->select('itempurchases.*', 'p.sale_price', 'p.purchase_price', 'si.amount as invoice_amount')
            ->join('priceinfos as p', 'itempurchases.price_id', '=', 'p.price_id')
            ->join('supinvoices as si', 'itempurchases.sup_invoice_id', '=', 'si.sup_invoice_id')
            ->where('itempurchases.item_id', '=', $item_id)
            //->where('itempurchases.price_id', '=', $price_id)
            ->get();
        //print_r($purchaseItemInfo);
//                exit;
        $totalRows = sizeof($purchaseItemInfo);
        $i = 1;
        foreach ($purchaseItemInfo as $eachItem) {
            $i_purchase_id = $eachItem->i_purchase_id;
            $sup_invoice_id = $eachItem->sup_invoice_id;
            $ex_amount = ($eachItem->quantity * $eachItem->purchase_price);
            $new_amount = ($total_quantity * $purchase_price); //new amount of all quantity
            $resetAmount = ($eachItem->invoice_amount - $ex_amount);

            DB::table('supinvoices')
                ->where('supinvoices.sup_invoice_id', $sup_invoice_id)
                ->update(array(
                    'amount' => $resetAmount,
                    'pay' => $resetAmount
                ));

            if ($i == $totalRows) {
                DB::table('itempurchases')
                    ->where('itempurchases.i_purchase_id', $i_purchase_id)
                    ->update(array(
                        'quantity' => $total_quantity,
                        'amount' => $new_amount
                    ));
                DB::table('itempurchases')
                    ->where('itempurchases.i_purchase_id', '!=', $i_purchase_id)
                    ->where('itempurchases.item_id', $item_id)
                    ->delete();
                DB::table('godownitems')
                    ->where('godownitems.price_id', $price_id)
                    ->where('godownitems.item_id', $item_id)
                    ->update(array(
                        'available_quantity' => $total_quantity
                    ));
                DB::table('godownitems')
                    ->where('godownitems.price_id', '!=', $price_id)
                    ->where('godownitems.item_id', $item_id)
                    ->delete();
                DB::table('supinvoices')
                    ->where('supinvoices.sup_invoice_id', $sup_invoice_id)
                    ->update(array(
                        'amount' => ($resetAmount + $new_amount),
                        'pay' => ($resetAmount + $new_amount)
                    ));
                DB::table('priceinfos')
                    ->where('priceinfos.price_id', $price_id)
                    ->update(array(
                        'purchase_price' => $purchase_price,
                        'sale_price' => $sale_price,
                        'status' => 1
                    ));
                DB::table('priceinfos')
                    ->where('priceinfos.price_id', '!=', $price_id)
                    ->where('priceinfos.item_id', $item_id)
                    ->update(array(
                        'status' => 0
                    ));

            }
            $i++;
        }

        return;
    }

    public function deleteGodownProduct()
    {
        echo "<pre>";
        $input['from'] = '2015-12-01';
        $input['to'] = '2016-01-20';

        $supInvoiceInfo = DB::table('supinvoices')
            ->whereBetween('supinvoices.transaction_date', array($input['from'], $input['to']))
            //->where('itempurchases.quantity', '>', 1)
            //->limit(20)
            ->get();
        print_r($supInvoiceInfo);
        foreach ($supInvoiceInfo as $eachInvoice) {
            $sup_invoice_id = $eachInvoice->sup_invoice_id;

            $purchaseItemInfo = DB::table('itempurchases')
                ->select('itempurchases.*', 'p.sale_price', 'p.purchase_price', 'si.amount as invoice_amount')
                ->join('priceinfos as p', 'itempurchases.price_id', '=', 'p.price_id')
                ->join('supinvoices as si', 'itempurchases.sup_invoice_id', '=', 'si.sup_invoice_id')
                ->whereBetween('itempurchases.created_at', array($input['from'], $input['to']))
                ->where('itempurchases.sup_invoice_id', '=', $sup_invoice_id)
                //->limit(2)
                ->get();

            foreach ($purchaseItemInfo as $eachPurchaseItem) {

                $item_id = $eachPurchaseItem->item_id;

                $price_id = $eachPurchaseItem->price_id;
                $item_quantity = round($eachPurchaseItem->quantity, 2);

                $eachGodownItem = DB::table('godownitems')
                    ->where('godownitems.item_id', $item_id)
                    ->where('godownitems.price_id', $price_id)
                    ->first();

                $exGodownQuantity = $eachGodownItem->available_quantity;
                $godown_item_id = $eachGodownItem->godown_item_id;
                $newGodownQuantity = ($exGodownQuantity - $item_quantity);
                $status = ($newGodownQuantity < 1) ? 0 : 1;
                DB::table('godownitems')
                    ->where('godownitems.godown_item_id', $godown_item_id)
                    ->update(array(
                        'available_quantity' => $newGodownQuantity,
                        'status' => $status,
                        'updated_at' => date('Y-md-d h:i:s')
                    ));


            } //end of item foreach
            DB::table('itempurchases')
                ->where('itempurchases.sup_invoice_id', $sup_invoice_id)
                ->delete();
            DB::table('supinvoices')
                ->where('supinvoices.sup_invoice_id', $sup_invoice_id)
                ->delete();

        }


        /*
            step1:
                delete from purchaseinfos according to i_purchase_id
                and fetch quantity , amount , sup_invoice_id , item_id ,price_id
            step2: update supinvoices according to sup_invoice_id. which dicrese total amount and pay
            setp3: at first check godowniteminfos by item_id and price_id
                   then  decrese available quantity from godowninfos table.
                   if quantity is 0 then status=0


        */


    }


}//end of class


/*
foreach($purchaseItemInfo as $eachItem){
    $sup_invoice_id=$eachItem->sup_invoice_id;
    $quantity=$eachItem->quantity;
    $item_id=$eachItem->item_id;
    $price_id=$eachItem->price_id;
    $i_purchase_id=$eachItem->i_purchase_id;

            $sup_invoice_info= DB::table('supinvoices')
                    ->where('supinvoices.sup_invoice_id', $sup_invoice_id)
                    ->first();


            $eachGodownItem=DB::table('godownitems')
                    ->where('godownitems.item_id', $item_id)
                    ->where('godownitems.price_id', $price_id)
                    ->first();

            $exGodownQuantity=$eachGodownItem->available_quantity;
            $godown_item_id=$eachGodownItem->godown_item_id;
            $newGodownQuantity=($exGodownQuantity-$quantity);
            $status=($newGodownQuantity<1)?0:1;

            DB::table('godownitems')
                    ->where('godownitems.godown_item_id', $godown_item_id)
                    ->update(array(
                        'available_quantity' => $newGodownQuantity,
                        'status'   => $status
                        ));
           DB::table('itempurchases')
                    ->where('itempurchases.i_purchase_id', $i_purchase_id)
                    ->delete();
            echo $sup_invoice_id;
            DB::table('supinvoices')
                    ->where('supinvoices.sup_invoice_id', $sup_invoice_id)
                    ->delete();

            }

return ;*/