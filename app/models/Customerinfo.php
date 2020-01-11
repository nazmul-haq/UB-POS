<?php

class Customerinfo extends \Eloquent {
	protected $primaryKey = 'cus_id';
	protected $fillable = [];

	public static $customer_rules = [
		'cus_type_id'		=> 'required',

	];

    public static $cus_rules = [
		'cus_type_name'		=> 'required|alpha_dash',
		'discount_percent'	=> 'required|number_dot',
        'point_unit'        => 'required|number_dot'
	];
    public static $cusType_rules = [
		'cus_type_name'		=> 'required|alpha_dash'
	];
	
	public static $rules_payment = [
		'payment_type_id'	=> 'required',
		'amount'			=> 'required|numeric'
	];

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'customerinfos';

	public $timestamps = true;
}