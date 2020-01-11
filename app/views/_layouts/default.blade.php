<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ $title or '::=:: Dashboard - POS ::=::' }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">

{{ HTML::style('css/bootstrap.min.css') }}
{{ HTML::style('css/bootstrap-responsive.min.css') }}
{{ HTML::style('css/font-google.css') }}
{{ HTML::style('css/font-awesome.css') }}
{{ HTML::style('css/style.css') }}
{{ HTML::style('css/clock.css') }}
{{ HTML::style('css/dashboard.css') }}
{{ HTML::style('css/jquery-sticklr-1.4.css') }}
{{ HTML::style('css/jquery.dataTables.css') }}
{{ HTML::style('css/wizard/jquery.wizard.css') }}
{{ HTML::style('css/custom.css') }}
{{ HTML::style('css/jquery-ui.css') }}
{{ HTML::style('css/datepicker.css') }}
{{ HTML::style('css/print.css') }}



<!-- javascript
================================================== --> 
{{ HTML::script('js/jquery-1.7.2.min.js') }}
{{ HTML::script('js/bootstrap.js') }}
<!-- Bootstrap DatePicker-->
{{ HTML::script('js/bootstrap-datepicker.js') }}
<!-- dataTable-->
{{ HTML::script('js/jquery.dataTables.min.js') }}
<!-- validation -->
{{ HTML::script('js/jquery.validate.min.js') }}
<!-- Shortcut lib js -->
{{ HTML::script('js/shortcut.lib.js') }}

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<noscript>
  <META HTTP-EQUIV="Refresh" CONTENT="0;URL={{URL::to('noscript')}}">
  <body style='display:none'>
</noscript>

<body>

@if(Auth::check()) 

@include('_layouts.navigation')

	<div class="main">
	  <div class="main-inner">
		<div class="container">	
			<div class="content-wrapper">
				@yield('content')
			</div>
		</div>
		<!-- /container --> 
	  </div>
	  <!-- /main-inner --> 
	</div>

@include('_layouts.footer')
<?php
	Request::segment(1);
?>
<script type="text/javascript">
	var segment = "<?php echo Request::segment(2); ?>";
	if(segment == 'index'){
		$('.Dashboard').addClass('active btn btn-fill');
	}else if(segment == 'items'){
		$('.Items').addClass('active btn btn-fill');
	}else if(segment == 'sales'){
		$('.Sales').addClass('active btn btn-fill');
	}else if(segment == 'suppliers'){
		$('.Suppliers').addClass('active btn btn-fill');
	}else if(segment == 'customers'){
		$('.Customers').addClass('active btn btn-fill');
	}else if(segment == 'reports'){
		$('.Reports').addClass('active btn btn-fill');
	}else if(segment == 'setup'){
		$('.Setup').addClass('active btn btn-fill');
	}else if(segment == 'purchases'){
		$('.Purchase').addClass('active btn btn-fill');
	}else{
		var segment = "<?php echo Request::segment(1); ?>";
		if(segment == 'sending'){
			$('.Sending').addClass('active btn btn-fill');
		}else if(segment == 'receiving'){
			$('.Receiving').addClass('active btn btn-fill');
		}else if(segment == 'return'){
			$('.Return').addClass('active btn btn-fill');
		}
	}
</script>
@else 
	{{ URL::to('admin/login') }}
@endif
</body>
</html>