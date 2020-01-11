<?php

class abcController extends \BaseController {

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

 //               $user = DB::table('users')->where(array('id'=> 1, 'email'=>'majbah@diu.edu.bd'))->first();

//               echo'<pre>';
//               print_r($user);
//               exit();
//
                $abc=Abc::find($id);
               // $user=User::where('id', '=', $id)->post;

//                $queries = DB::getQueryLog();
//                $last_query = end($queries);
//                var_dump($last_query);
//                exit();




//                echo'<pre>';
//                dd($user);

          //return View::make('user.show',compact('user'));
          return View::make('app.users',compact('abc'));
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
