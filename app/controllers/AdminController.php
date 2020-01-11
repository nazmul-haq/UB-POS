<?php 

class AdminController extends  BaseController{
	
	protected $layout = '_layouts.default';
	
	public function users(){
		
		$table = Datatable::table()
		  ->addColumn('id', 'User Name')
		  ->setUrl(route('api.users'))
		  ->noScript();

		$this->layout->content = View::make('app.users', array('table' => $table));
	}
	
	public function getUsersDataTable(){

    $query = User::all();
	//dd($query); exit();
    return Datatable::collection($query)
        ->searchColumns('username')
        ->orderColumns('username')
        ->make();
	}



}