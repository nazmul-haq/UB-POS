<?php

class Def extends \Eloquent {
	protected $fillable = ['id','abc_id','alpha'];

        public function abc(){
        return $this->belongsTo('Abc');
    }

}