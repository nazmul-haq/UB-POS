<?php 

class EmpInfoController extends BaseController {
    public $timestamp;
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->timestamp = date('Y-m-d H:i:s');
    }

    public function login() {
        return View::make('login');
    }
    public function sessionLogout($empId = null){
    	$empId = base64_decode($empId);
    	DB::table('empinfos')
    		->where('emp_id',$empId)
    		->update([
    			'active_session' => 0,
                'last_logged_ip' => 0
    		]);
    		// get current user
    	Auth::logout();
        return Redirect::to('/')->with('message', 'Logout Successfully.');
    }
    public function isLoggedIn() {
        $data = Input::all();
        // echo "<pre>";
        // print_r($_SERVER);
        // return;
        $validation = Validator::make($data, Empinfo::$auth_rules);
        if($validation->fails()) {
            return Redirect::back()->withErrors($validation)->withInput();
        } else {
            $auth = Auth::attempt(array(
                    'user_name'	=> Input::get('user_name'),
                    'password' 	=> Input::get('password'),
                    'status'    =>1
            ));
            if(Auth::check()) {
            	//Azim
            // 	$checkSession = DB::table('empinfos')
            // 		->where('emp_id',Auth::user()->emp_id)
            // 		->first();
            // 	if($checkSession->active_session == 1){
            // 		return View::make('activeSession')->with('data', $checkSession);
            // 	}
				
            // 	DB::table('empinfos')
            // 		->where('emp_id',Auth::user()->emp_id)
            // 		->update([
            // 			'active_session' => 1,
            // 			'last_logged_ip' 	 => $_SERVER['REMOTE_ADDR']
            // 		]);
        		//Azim
                //setup session in browser//
                $company_profile=DB::table('companyprofiles')->first();
				 $urls= DB::table('urlnames')
                        ->join('urlemppermissions', 'urlnames.url_id', '=', 'urlemppermissions.url_id')
                        ->where('urlemppermissions.emp_id', '=', Auth::user()->emp_id)
                        ->where('urlemppermissions.status', '=', 1)
                        ->get();
						foreach($urls as $url){
							$url_address[]=$url->url_address;
						}
                Session::put(array(
                        'emp_id'    => Auth::user()->emp_id,
                        'active_session'    => 1,
                        'user_name' => Auth::user()->user_name,
                        'role'      => Auth::user()->role, 
                        'full_name' => Auth::user()->f_name." ".Auth::user()->l_name,
                        'max_inv_dis_percent'    => $company_profile->max_inv_dis_percent,
						'company_name'=>$company_profile->company_name,
						'isAutoPrintAllow'=>$company_profile->print_recipt_a_sale,
						'backdate_sales'=>$company_profile->back_date_sales,
						'backdate_purchase'=>$company_profile->back_date_purchase,
						'backdate_return'=>$company_profile->back_date_return,
						'quick_sending_receiving'=>$company_profile->quick_sending_receiving,
						'project_url'=>$url_address
                        ));
                
                return Redirect::intended('admin/index'); //Redirect to intended page
            } else {
                return Redirect::to('/')->with('errorMsg', 'Username or Password Wrong! Please try again.');
            }
        }
        return Redirect::to('/')->with('errorMsg', 'Something is wrong! Please check it.');

    }

    public function doLogout() {
    	DB::table('empinfos')
    		->where('emp_id',Auth::user()->emp_id)
    		->update([
    			'active_session' => 0
    		]);
        Auth::logout(); // log the user out of our application
        return Redirect::to('/')->with('message', 'Logout Successfully.'); //that means index route
    }

    public function dashboard() {
    	$modulesWithoutSub=DB::table('moduleemppermissions')
           ->join('modulenames', 'moduleemppermissions.module_id', '=', 'modulenames.module_id')
           ->select('modulenames.*')
           ->where('moduleemppermissions.status', '=',1)
           ->where('emp_id', '=', Session::get('emp_id'))
           ->orderBy('modulenames.sorting', 'asc')
           ->get();
           // dd($modulesWithoutSub);
        return View::make('admin.index',compact('modulesWithoutSub'));
    }
    
    public function addEmployee() {
        return View::make('admin.employee.addEmployee');
    }

    public function saveEmployee() {
		try{
			$data = Input::all();
			$validator = Validator::make($data, Empinfo::$user_rules);
			if($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			$emp_info = array(
					'f_name' 			=>  Input::get('f_name'),
					'l_name' 			=>  Input::get('l_name'),
					'father_name' 		=>  Input::get('father_name'),
					'mother_name'	 	=>  Input::get('mother_name'),
					'user_name' 		=>  Input::get('user_name'),
					'password' 			=>  Hash::make(Input::get('password')),
					'mobile' 			=>  Input::get('mobile'),
					'email' 			=>  Input::get('email'),
					'permanent_address' =>  Input::get('permanent_address'),
					'present_address' 	=>  Input::get('present_address'),
					'national_id'		=>  Input::get('national_id'),
					'fixed_salary' 		=>  Input::get('fixed_salary'),
					'created_by' 		=>  Session::get('emp_id'),
					'role'				=>  Input::get('role'),
					'created_at' 		=>  $this->timestamp
			);
			$insert = DB::table('empinfos')->insert($emp_info);
			if($insert) {
				return Redirect::to('admin/addEmployee')->with('message', 'Added Successfully');
			}
			return Redirect::to('admin/addEmployee')->with('errorMsg', 'Something must be wrong! Please check');
		} catch(Exception $e){
			Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
                        $err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
                        return Redirect::to('admin/addEmployee')->with('errorMsg',$err_msg)->withInput();
		}

    }

    public function viewEmployee(){
        return View::make('admin.employee.viewEmployee');
    }

    public function getEmployeeData() {
        return Datatable::query(DB::table('empinfos')
						->where('status', 1)
						)
						->showColumns('emp_id', 'user_name','f_name','l_name')
						->addColumn('action', function($model) {
						$isDisabled=($model->emp_id==Session::get('emp_id'))?'disabled':'';
						 $html = '<a class="btn btn-info" href="'.URL::to("admin/editEmployee/$model->emp_id").'"><i class="icon-edit"></i> Edit</a>'.' | '. '<a class="btn btn-warning '.$isDisabled.'" href="javascript::;" onclick="return deleteConfirm('.$model->emp_id.')" id="'.$model->emp_id.'">
							<i class="icon-remove"></i> Inactive</a>'; 
						
						return $html;	 
						})
						->searchColumns('user_name','f_name','l_name')
						->setSearchWithAlias()
						->orderColumns('emp_id','user_name')
						->make();
    }
	
	public function getEmpInfoById($empId){
        $empInfo = DB::table('empinfos')->where('emp_id',$empId)->first();
        //$empInfo = DB::table('empinfos')->find($empId);
        return View::make('admin.employee.editEmployee',compact('empInfo'));
	}
	
    public function updateEmployee() {
		try{
			$data = Input::all();
			$validator = Validator::make($data, Empinfo::$user_rules);
			if($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			$emp_id = Input::get('emp_id');
			$updateEmp = array(
					'f_name' 		=>  Input::get('f_name'),
					'l_name' 		=>  Input::get('l_name'),
					'father_name' 		=>  Input::get('father_name'),
					'mother_name'	 	=>  Input::get('mother_name'),
					'user_name' 		=>  Input::get('user_name'),
					'password' 		=>  Hash::make(Input::get('password')),
					'mobile' 		=>  Input::get('mobile'),
					'email' 		=>  Input::get('email'),
					'permanent_address'     =>  Input::get('permanent_address'),
					'present_address' 	=>  Input::get('present_address'),
					'national_id'		=>  Input::get('national_id'),
					'fixed_salary' 		=>  Input::get('fixed_salary'),
					'updated_by' 		=>  Session::get('emp_id'),
					'role'			=>  Input::get('role'),
					'updated_at' 		=>  $this->timestamp
			);
			$insert = DB::table('empinfos')->where('emp_id', $emp_id)->update($updateEmp);
			if($insert) {
				return Redirect::to("admin/editEmployee/$emp_id")->with('message', 'Update Successfully');
			}
			return Redirect::to("admin/editEmployee/$emp_id")->with('errorMsg', 'Something must be wrong! Please check');
		} catch(Exception $e){
                        Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
                        $err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Redirect::to("admin/editEmployee/$emp_id")->with('errorMsg', $err_msg)->withInput();
		}

    }
	public function deleteEmployee($empId){		
		$empDelete = DB::table('empinfos')			
			->where('emp_id', $empId)
			->update(array('status' => 0));
		if($empDelete){	
			return Response::json(['status' => 'success']);
		} 
		return Response::json(['status' => 'error']);
	}
	
	public function permissionEmp() {
		$title = ':: POSv2 :: - Employee Permission';
		$getEmps = array(
			'' => 'Select Employee') + DB::table('empinfos')
								->where('status', 1)
								->orderBy('user_name', 'asc')
								->lists('user_name', 'emp_id');
        return View::make('admin.employee.permissionEmp',compact('title', 'getEmps'));
    }
	
	public function viewPermissionEmp() {		
		$emp_id = Input::get('emp_id');
		if(empty($emp_id)){
			return Redirect::to('admin/permission')->with('errorMsg', 'Please Select Employee');
		}		
		$moduleInfo = DB::table('modulepermissions')
			 ->join('modulenames','modulenames.module_id','=','modulepermissions.module_id')
			 ->get();
		$empInfo = DB::table('empinfos')->where('emp_id', $emp_id)->first();

		return View::make('admin.employee.viewModuleSubModule',compact('moduleInfo','empInfo'));
	}

    public function savePermissionEmp() {
            $unsubmodule_module_all= Input::get('unsubmodule_module_all');
            $unsubmodule_module_new= Input::get('new_unsubmodule_module_ids');
            $unsubmodule_module_old= Input::get('old_unsubmodule_module_ids');

            $mod_sub_new= Input::get('new_permission_ids');
            $mod_sub_old= Input::get('old_permission_ids');
            $mod_sub_module_un= Input::get('permission_id_un');

            $mod_data=array();
            $mod_data['emp_id']=Input::get('emp_id');
            $mod_data['created_by']=Session::get('emp_id');
            
            $sub_mod_data=array();
            $sub_mod_data['emp_id']=Input::get('emp_id');
            $sub_mod_data['created_by']=Session::get('emp_id');

                
//                echo'<pre>';
//                print_r($unsubmodule_module_new);
//                echo'<pre>';
//                print_r($unsubmodule_module_old);
//                echo'<pre>';
//                print_r($unsubmodule_module_all);
//                exit();
                
//                echo'<pre>';
//                print_r($mod_sub_new);
//                echo'<pre>';
//                print_r($mod_sub_old);
//                 echo'<pre>';
//                print_r($mod_sub_module_un);
//                exit();


            if(!$unsubmodule_module_new){
            $unsubmodule_module_new=array();
            }
            if(!$unsubmodule_module_old){
                $unsubmodule_module_old=array();
            }



       $del=0;
       $ins=0;
        foreach($unsubmodule_module_all as $mod){
                $old=0;
                $new=0;
                foreach($unsubmodule_module_new as $mod_new){
                    if($mod_new==$mod)
                        $new=1;
                }
                foreach($unsubmodule_module_old as $mod_old){
                    if($mod_old==$mod)
                        $old=1;
                }

                if(($old==1) && ($new==1)){}
                else{

//                    $exp_val=explode('-', $val);
//                    $module_id3=$exp_val[0];
//                    $sub_module_id3=$val;

                    $mod_data['module_id']=$mod;

                    if($old==1){
                        $old=0;
                        $delete = DB::table('moduleemppermissions')
                                ->where('module_id', '=', $mod_data['module_id'])
                                ->where('emp_id', '=', $mod_data['emp_id'])
                                ->delete();

                        if($delete==true)
                           $del++;
                    }

                    else if($new==1){
                            $new=0;
                        $insert = DB::table('moduleemppermissions')->insert($mod_data);

                        if($insert==true)
                            $ins++;
                    }
                    else{ }

                }
            }
			
            if(!$mod_sub_new){
            $mod_sub_new=array();
            }
            if(!$mod_sub_old){
                $mod_sub_old=array();
            }
            if(!$mod_sub_module_un){
                $mod_sub_module_un=array();
            }

        foreach($mod_sub_module_un as $val){
                $old=0;
                $new=0;
                foreach($mod_sub_new as $mod_sub2){
                    if($mod_sub2==$val)
                        $new=1;
                }
                foreach($mod_sub_old as $mod_sub1){
                    if($mod_sub1==$val)
                        $old=1;
                }

                if(($old==1) && ($new==1)){}
                else{

//                    $exp_val=explode('-', $val);
//                    $module_id3=$exp_val[0];
//                    $sub_module_id3=$val;

                    $sub_mod_data['sub_module_id']=$val;

                    if($old==1){
                        $old=0;
                        $delete = DB::table('smemppermissions')->where('sub_module_id', '=', $sub_mod_data['sub_module_id'])
                                ->where('emp_id', '=', $sub_mod_data['emp_id'])
                                ->delete();

                        if($delete==true)
                           $del++;
                    }

                    else if($new==1){
                            $new=0;
                        $insert = DB::table('smemppermissions')->insert($sub_mod_data);

                        if($insert==true)
                            $ins++;
                    }
                    else{ }

                }
            }
            
        return Redirect::to('admin/permission')->with('insert', $ins)->with('delete', $del);
        
    }
	
	/*
	  *  URL Permission Module
	*/	
	
	 public function urlPermissionEmp() {
		$title = ':: POSv2 :: - Employee Permission URL';
		$getEmps = array(
			'' => 'Select Employee') + DB::table('empinfos')
								->where('status', 1)
								->orderBy('user_name', 'asc')
								->lists('user_name', 'emp_id');
        return View::make('admin.employee.empUrlPermission',compact('getEmps', 'title'));
    } 
	
	public function getEmpUrlPermission() {
		$emp_id = Input::get('emp_id');
		if(empty($emp_id)){
			return Redirect::to('admin/permissionEmpUrl')->with('errorMsg', 'Please Select Employee');
		}
		$urlInfos = DB::table('urlnamepermissions')
			 ->join('urlnames', 'urlnamepermissions.url_id', '=', 'urlnames.url_id')
			 ->get();
		$empInfo = DB::table('empinfos')->where('emp_id', $emp_id)->first();

		return View::make('admin.employee.viewEmpUrlPermission',compact('urlInfos','empInfo'));
	}
	
	public function saveEmpUrlPermission(){
		$un_url_all    		= Input::get('un_url_all');
		$un_url_new    		= Input::get('new_un_url_ids');
		$check_all_url 		= Input::get('old_un_url_ids');
		$single_check_url   = Input::get('single_url_id');
		
	/* 	echo'<pre>';
		print_r($check_all_url);
		echo'<pre>';
		print_r($single_check_url);
		exit(); */
		if(!empty($single_check_url)){
			$un_url_old = array_diff($check_all_url, $single_check_url);		
		} else{
			$un_url_old = $check_all_url;
		}
		/* echo'<pre>';
		print_r($un_url_old);
		exit(); */
		$url_data = array();
		$url_data['emp_id']		=	Input::get('emp_id');
		$url_data['created_by']	=	Session::get('emp_id');
                
		/* echo'<pre>';
		print_r($un_url_new);
		echo'<pre>';
		print_r($un_url_old);
		echo'<pre>';
		print_r($un_url_all);
		exit(); */
		
		if(!$un_url_new){
			$un_url_new	= array();
		}
		if(!$un_url_old){
			$un_url_old	= array();
		}
				
		$del=0;
		$ins=0;
		foreach($un_url_all as $url){
			 print_r($url);
			
			$old=0;
			$new=0;
			foreach($un_url_new as $url_new){
				if($url_new == $url)
					$new = 1;
			}
			foreach($un_url_old as $url_old){
				if($url_old == $url)
				 $old=1;
			}

			if(($old==1) && ($new==1)){}
			else {
				$url_data['url_id'] = $url;

				if($old==1){
					$old=0;
					$delete = DB::table('urlemppermissions')
							->where('url_id', '=', $url_data['url_id'])
							->where('emp_id', '=', $url_data['emp_id'])
							->delete();

					if($delete==true)
					   $del++;
				}

				else if($new==1){
						$new=0;
					$insert = DB::table('urlemppermissions')->insert($url_data);

					if($insert==true)
						$ins++;
				}

			}
		} 	
        return Redirect::to('admin/permission')->with('insert', $ins)->with('delete', $del);	
		
	}
}