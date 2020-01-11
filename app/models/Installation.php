<?php 

class Installation extends Eloquent {
	
	protected $fillable = ['company_name', 'address', 'mobile', 'web_address', 'print_recipt_a_sale', 'language', 'time_zone', 'theme', 'f_name', 'l_name','role', 'mobile', 'email', 'super_admin_name', 'password', 'return_policy', 'module_id','year'];
	
	public static $rules = [
		'company_name'          =>	'required|min:3',
		'address'				=>	'required|min:3',
		'mobile'				=>	'required',
		'print_recipt_a_sale'   =>	'required',
		'language'				=>	'required',
		'time_zone'				=>	'required',
		'theme'					=>	'required',
		'user_name'				=>	'required|min:3',
		'password'				=>	'required|min:5',
		'return_policy'                         =>	'required',
	];
	public static $updateRules = [
		'company_name'          =>	'required|min:3',
		'address'				=>	'required|min:3',
		'mobile'				=>	'required',
		'language'				=>	'required',
		'time_zone'				=>	'required',
		'theme'					=>	'required',
	];
}