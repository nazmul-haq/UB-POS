<?php

class Submodulename extends \Eloquent {
	protected $fillable = [];

        public function ModuleNamess(){
        return $this->belongsTo('Modulename');
    }
    
}