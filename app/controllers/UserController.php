<?php 

class UserController extends BaseController{
	protected $layout = '_layouts.default';
	public function index(){		
		return View::make('index');
	}
	
	public function login(){
		
		return View::make('login');
	}
	
	public function users(){
		
		$table = Datatable::table()
		  ->addColumn('User Name')
		  ->setUrl(URL::to('api/users'))
		  ->noScript();
		$this->layout->content = View::make('app.users', array('table' => $table));
	}
	
	public function getUsersDataTable(){

		$query = User::select('username')->get();	  
		return Datatable::collection($query)
			->showColumns('username')

			->searchColumns('username')
			->orderColumns('username')
			->make();
	}
	
	
}
