<?php

class Abc extends \Eloquent {
	protected $fillable = ['id','name'];

        public function alphas(){
            return $this->hasMany('Def');
        }
}