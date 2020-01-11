<?php

class Setup extends \Eloquent {
	
	public static $itemOffer_rules = [
		'offer'	=> 'required|number_dot'
	];
	public static $brandOffer_rules = [
		'offer'	=> 'required|number_dot'
	];
	public static $cateogryOffer_rules = [
		'offer'	=> 'required|number_dot'
	];
	public static $type_rules = [
		'type_name'	=> 'required'
	];
}