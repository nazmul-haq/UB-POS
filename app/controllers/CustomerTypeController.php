<?php

class CustomerTypeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		try{
//			$data = Input::all();
//			$validator = Validator::make($data, EmpInfo::$user_rules);
//			if($validator->fails()) {
//				return Redirect::back()->withErrors($validator)->withInput();
//			}
			$emp_info = array(
					'f_name' 			=>  Input::get('f_name'),
					'l_name' 			=>  Input::get('l_name'),
					'father_name'                   =>  Input::get('father_name'),
					'mother_name'                   =>  Input::get('mother_name'),
					'user_name'                     =>  Input::get('user_name'),
					'password' 			=>  Hash::make(Input::get('password')),
					'mobile' 			=>  Input::get('mobile'),
					'email' 			=>  Input::get('email'),
					'permanent_address'             =>  Input::get('permanent_address'),
					'present_address'               =>  Input::get('present_address'),
					'national_id'                   =>  Input::get('national_id'),
					'fixed_salary'                  =>  Input::get('fixed_salary'),
					'created_by'                    =>  Session::get('emp_id'),
					'role'				=>  1
			);
			$insert = DB::table('empinfos')->insert($emp_info);
			if($insert) {
				return Redirect::to('admin/addEmployee')->with('message', 'Added Successfully');
			}
			return Redirect::to('admin/addEmployee')->with('errorMsg', 'Something must be wrong! Please check');
		} catch(\Exception $e){
			return Redirect::to('admin/addEmployee')->with('errorMsg', 'Duplicate entry found.')->withInput();
		}
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
