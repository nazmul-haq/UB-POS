@if (Session::has('message'))
	<div class="alert alert-success fade in" >
	  <i class="icon-ok"></i>&nbsp;&nbsp;<strong>{{ Session::get('message') }}</strong>
	  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#735;</button>
	</div>
@endif

@if (Session::has('errorMsg'))
	<div class="alert alert-warning fade in" >
	  <i class="icon-warning-sign"></i>&nbsp;&nbsp;<strong>{{ Session::get('errorMsg') }}</strong>
	  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#735;</button>
	</div>
@endif

@if ( ! empty( $errors ) )
    @foreach ( $errors->all() as $error )
		<div class="alert alert-warning fade in" >
		  <i class="icon-warning-sign"></i>&nbsp;&nbsp;<strong>{{ $error }}</strong>
		  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#735;</button>
		</div>
	@endforeach
@endif


@if (Session::has('insert'))
	<div class="alert alert-success fade in" >
	  <strong>{{ Session::get('insert') }} Permission has been permitted for the employee</strong>
	  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#735;</button>
	</div>
@endif
@if (Session::has('delete'))
	<div class="alert alert-success fade in" >
	  <strong>{{ Session::get('delete') }}  Permission has been canceled for the employee</strong>
	  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#735;</button>
	</div>
@endif
@if (Session::has('quantityError'))
	<div class="alert alert-success fade in" >
	  <strong>{{ Session::get('quantityError') }}</strong>
	  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#735;</button>
	</div>
@endif


@if (Session::has('success'))
    @if(Session::get('success')>0)
	<div class="alert alert-success fade in" >
	  <i class="icon-ok"></i>&nbsp;&nbsp;<strong>{{ Session::get('success') }} Item has been send</strong>
	  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#735;</button>
	</div>
    @endif
@endif

@if (Session::has('fail'))
    @if(Session::get('fail')>0)
	<div class="alert alert-warning fade in" >
	  <i class="icon-ok"></i>&nbsp;&nbsp;<strong>{{ Session::get('fail') }} Item send Faild</strong>
	  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&#735;</button>
	</div>
    @endif
@endif