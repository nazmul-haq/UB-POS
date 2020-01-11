<?php

class Supplierinfo extends \Eloquent {

	protected $primaryKey = 'supp_id';
	protected $fillable = [];

	public static $supp_rules = [
		'supp_or_comp_name'	=> 'required|alpha_space',
		'user_name'             => 'required|alpha_dash'
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
	protected $table = 'supplierinfos';

	public $timestamps = true;

}