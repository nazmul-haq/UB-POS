@extends('_layouts.default')

@section('content')
	<div class="row">
	  <div class="span12">
	  <h3>Users</h3>
		  {{ Datatable::table()
				->addColumn('id','Name','First Name', 'Last Name', 'Action')      
				->setUrl(route('api.users'))   
				->render() }}
	  </div>
	</div>

@stop

