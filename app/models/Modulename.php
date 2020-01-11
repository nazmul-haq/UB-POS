<?php

class Modulename extends \Eloquent {
	protected $fillable = [];

        public function Submodulenames(){
            return $this->hasMany('Submodulename');
        }

}