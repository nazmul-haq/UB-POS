<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>:::POS Login System:::</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
		{{ HTML::style('css/bootstrap.min.css') }}
		{{ HTML::style('css/bootstrap-responsive.min.css') }}
		{{ HTML::style('css/font-google.css') }}
		{{ HTML::style('css/signin.css') }}

		{{ HTML::script('js/jquery-1.7.2.min.js') }}
		{{ HTML::script('js/bootstrap.js') }}
    </head>

    <body>
		@include('_sessionMessage')
		@if(count($data) > 0)
        <div class="account-container">
            <div class="content clearfix">
                <div class="login-wrapper">
                    <div class="logo">
					{{ HTML::image('img/unitech.png', 'Unitech IT', array('class' => 'login-img')) }}
                        <p class="head-text"><strong class="unitech">UNITECH IT</strong><br/><strong>Technology for humanity</strong></p>
                    </div>
                    <div class="login-heading">
                        <p>{{ HTML::image('img/logo-pos.png', 'Point of sale') }}</p>
                        <p style="float:right; margin: -5px 7px 0 0"><strong>Point Of Sale</strong></p>
                    </div>
                </div>

                <div class="login-fields">
                    <br>
                    <p><span style="font-size: 14px;">You Have an active session in : </span> 
                    	<span style="color: red; font-weight: bold; font-size: 20px;">{{$data->last_logged_ip}}</span> 
                    </p>
                    <br><br><br>
                </div>
                <div class="login-actions">
                	<div class="" style="text-align: center;">
						<a class="btn btn main-btn btn-large btn-primary" href="{{URL::to('sessionLogout')}}/{{base64_encode($data->emp_id)}}">Log Out</a>	
                	</div>
                </div> <!-- .actions -->
            </div> <!-- /content -->

        </div> <!-- /account-container -->
        @endif
        <script>
            //alert message
            $(window).load(function(){
                setTimeout(function(){ $('.alert').fadeOut() }, 6000);
            });
        </script>
    </body>
</html>
