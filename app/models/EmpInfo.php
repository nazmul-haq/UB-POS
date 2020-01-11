<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class EmpInfo extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $primaryKey = 'emp_id';
	protected $fillable = ['user_name','password','role'];

	public static $auth_rules = [
		'user_name' => 'required',
		'password' 	=> 'required',
	];

	protected $fillable_user = ['f_name','l_name','user_name', 'password', 'mobile', 'email', 'present_address', 'present_address', 'national_id'];

	public static $user_rules = [
		'f_name'		=> 'required|Alpha',
		'l_name'		=> 'required|Alpha',
		'user_name'		=> 'required|min:3',
		'password'		=> 'required|min:4',
		'mobile'		=> 'required|between: 10,14',
		'email'			=> 'email'
	];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'empinfos';

	public $timestamps = true;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

}
