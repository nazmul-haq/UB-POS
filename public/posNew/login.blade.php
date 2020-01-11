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
        <div class="account-container">
            <div class="content clearfix">
			{{ Form::open(array('route' => 'admin.login.post')) }}
                <div class="login-wrapper">
                    <div class="logo">
					{{ HTML::image('img/unitech.png', 'UB Automation', array('class' => 'login-img')) }}
					
					
                    </div>
                    <div class="login-heading">
                        <p>
                        {{ HTML::image('img/unitech.png', 'UB Automation', array('class' => 'login-img', 'style' => 'margin: 13px 0px 0 0px !important;float: left;height: 35px;')) }} 
                        {{ HTML::image('img/posNewLogo.png', 'UB Automation', array('class' => 'login-img', 'style' => 'margin: 0px !important;height: 60px; width: 190px;')) }}
                        </p>
                    </div>
                </div>

                <div class="login-fields">
                    <p>Please provide your details</p>
                    <div class="field">
					{{ Form::label('user_name', 'Username') }}
					{{ Form::text('user_name', null, array('id' => 'username', 'class' => 'login username-field', 'placeholder' => 'Enter Username','autofocus'=>'yes')) }}
					{{$errors->first('user_name','<p class="error">:message</p>')}}
                    </div> <!-- /field -->

                    <div class="field">
					{{ Form::label('password', 'Password') }}
					{{ Form::password('password', array('id' => 'password', 'class' => 'login password-field', 'placeholder' => 'Enter Password')) }}
					{{$errors->first('password','<p class="error">:message</p>')}}
                    </div> <!-- /password -->

                </div> <!-- /login-fields -->

                <div class="login-actions">
                    <span class="login-checkbox">
                        <input id="Field" name="Field" type="checkbox" class="field login-checkbox" value="First Choice" tabindex="4" />
                        <label class="choice" for="Field">Keep me signed in</label>
                    </span>
				{{ Form::submit('Sign In', array('class' => 'button btn main-btn btn-large')) }}				
                </div> <!-- .actions -->
			{{ Form::close() }}
            </div> <!-- /content -->

        </div> <!-- /account-container -->
        <script>
            //alert message
            $(window).load(function(){
                setTimeout(function(){ $('.alert').fadeOut() }, 6000);
            });
        </script>
    </body>
</html>
