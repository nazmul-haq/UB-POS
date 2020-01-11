<?php 
/*
====Developed by Unitech IT
*/
	class Helper{
		public static function dateFormat($date){
			return date("d-m-Y h:i:s A", strtotime($date));
		}
		public static function onlyDMY($date){
			return date("d-m-Y", strtotime($date));
		}
	}