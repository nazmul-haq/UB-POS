<?php 

class InstallationController extends BaseController {
	
	public $timestamp;
	
	function __construct(){
		$this->timestamp = date('Y-m-d H:i:s');
	}
	
	public function installation() {
		$install_check = DB::table('companyprofiles')->select('install_complete')->where('install_complete', '1')->first();
		if(isset($install_check->install_complete) &&($install_check->install_complete == 1)):
			return Redirect::route('admin.login')->with('message', 'Already Software Installed! Please Logged In');
		endif;
		
		$modules	= DB::table('modulenames')->select('module_id', 'module_name','status')->orderBy('module_name', 'asc')->get();		
		return View::make('installation.setup', compact('modules'));
	}
	
	public function save(){
		
		DB::beginTransaction();

		try {
			$validator = Validator::make($data = Input::all(), Installation::$rules);
			
			if($validator->fails()){
				return Redirect::back()->withErrors($validator)->withInput();			
			} 			
			$company_profile = array(
				'company_name'			=> Input::get('company_name'),
				'address'				=> Input::get('address'),
				'mobile'				=> Input::get('mobile'),
				'web_address'			=> Input::get('web_address'),
				'print_recipt_a_sale'   => Input::get('print_recipt_a_sale'),
				'language'				=> Input::get('language'),
				'time_zone'				=> Input::get('time_zone'),
				'return_policy'			=> Input::get('return_policy'),
				'theme'					=> Input::get('theme'),
                'year'					=> Input::get('year'),
				'install_complete'		=> 1,
				'created_at'			=> $this->timestamp,
			);
			DB::table('companyprofiles')->insert($company_profile);
			$last_insert_id =  DB::getPdo()->lastInsertId();

			$emp_info = array(
				'emp_id'		=> 1,
                                'f_name'		=> Input::get('f_name'),
				'l_name'		=> Input::get('l_name'),
				'mobile'		=> Input::get('mobile'),
				'email'			=> Input::get('email'),
				'user_name'		=> Input::get('user_name'),
				'password'		=> Hash::make(Input::get('password')),
				'role'		 	=> 2,
                                'year'			=> Input::get('year'),
				'created_by'		=> 1,
				'created_at'            => $this->timestamp,
			);

			DB::table('empinfos')->insert($emp_info);
			$emp_last_insert_id = DB::getPdo()->lastInsertId(); 
    //			DB::table('empinfos')->update($emp_info_update);
			$module_ids = Input::get('module_id');
			
			foreach($module_ids as $key => $module_id){
                        //this module permission used for company
				$module_permission = array(
					'module_id'     => $module_id,
					'company_id'	=> $last_insert_id ,
                                        'year'		=> Input::get('year'),
                                        'created_at'	=> $this->timestamp,
				);
				DB::table('modulepermissions')->insert($module_permission);
			}
                        
                        for($i=1;$i<=3;$i++){
                            
                        //this is default module setup for super admin
                            $module_permission1 = array(
					'module_id'     => $i,
					'emp_id'	=> 1,
                                        'created_at'	=> $this->timestamp,
                                        'created_by'    => 1
				);
                            
				DB::table('moduleemppermissions')->insert($module_permission1);
                        }
                        
			DB::commit();
			return Redirect::to('/')->with('message', 'Software Install Successfully. You can Logged In');
			
		} catch (Exception $e) {
                    //return  DB::getQueryLog();
                         DB::rollback();
			return Redirect::route('install.setup')->with('errorMsg', 'Something is wrong in Installation! Please try again.');
		}	
		
	}
	public function editInstall(){
		$editInfo = DB::table('companyprofiles')->where('install_complete', '1')->first();
		return View::make('installation.editSetup', compact('editInfo'));
	}
	public function updateInstall($company_id){
		try{
			$data = Input::all();
			$data['back_date_purchase']=(empty($data['back_date_purchase']))?0:$data['back_date_purchase'];
			$data['back_date_sales']=(empty($data['back_date_sales']))?0:$data['back_date_sales'];
			$data['back_date_return']=(empty($data['back_date_return']))?0:$data['back_date_return'];
			$validator = Validator::make($data, Installation::$updateRules);
			if($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			unset($data['_token']);
			$company_id = Input::get('company_id');
			$insert = DB::table('companyprofiles')->where('company_id', $company_id)->update($data);
			if($insert) {
				return Redirect::to("editInstallation")->with('message', 'Update Successfully');
			}
			return Redirect::to("editInstallation")->with('errorMsg', 'No new action occured!');
		} catch(Exception $e){
                        Session::flash('mySqlError',$e->errorInfo[2]); // session set only once
                        $err_msg=Lang::get("mysqlError.".$e->errorInfo[1]);
			return Redirect::to("editInstallation")->with('errorMsg', $err_msg)->withInput();
		}

    }
}