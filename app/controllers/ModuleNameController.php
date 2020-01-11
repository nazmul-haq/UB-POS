<?php

class ModuleNameController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

        $ModuleSubModule=DB::table('modulenames')->join('submodulenames','modulenames.module_id','=','submodulenames.module_id')->get();

            
        //$ModuleSubModule = DB::table('modulenames')->where('module_id', 2)->get();
           // $b = new Modulename;
            //$ModuleSubModule=$b->find(1);


             
            //$ModuleSubModule=Modulename::find($id)->get();

           // $ModuleSubModule=Modulename::find($id);
            //$ModuleSubModule = Modulename::where('module_id', '=', $id);
            //$ModuleSubModule = Modulename::with('Submodulename');

           // $ModuleSubModule = Modulename::with('Submodulename')->find($id)->Submodulename;
            
            //$ModuleSubModule = Modulename::where('module_id', '=', $id)->take(10)->get();

            //dd($ModuleSubModule->Submodulenames);
//        $queries = DB::getQueryLog();
//        $last_query = end($queries);
//        var_dump($last_query);
//        exit();

           

            echo '<pre>';
            dd($ModuleSubModule);
            exit();

            
                
             return View::make('app.users',compact('ModuleSubModule'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
