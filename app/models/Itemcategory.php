<?php

class Itemcategory extends \Eloquent {
	
	protected $fillable = ['category_name', 'created_by'];
		
	public static $rules = [
		'category_name'	=> 'required'
	];
	public static $brand_rules = [
		'brand_name'	=> 'required'
	];
	public static $location_rules = [
		'location_name'	=> 'required'
	];
	public static $company_rules = [
		'company_name'	=> 'required'
	];
	
	public static $item_rules = [
		'item_name'			=> 'required',
		'category_id'		=> 'required',
		'carton'		    => 'required',
	];
}