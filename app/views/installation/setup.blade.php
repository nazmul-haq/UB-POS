<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title> POS :: Installation</title>

        <!-- Bootstrap -->
	{{ HTML::style('css/bootstrap.min.css') }}
	{{ HTML::style('css/font-awesome.css') }}
	{{ HTML::style('css/smart_wizard.css') }}

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>

        <!-- Begin page content -->
        <div class="container">
            <div class="row">
                <div class="span12">
                    <table align="center" border="0" cellpadding="0" cellspacing="0">
                        <tr><td>
					{{ Form::open(array('route' => 'install.save', 'id' => 'myForm')) }}
					{{ Form::hidden('issubmit', 1) }}
                                <!-- Tabs -->
                                <div class="form_heading">
					{{ HTML::image('img/Setting-icon.png', 'Setup', array('class' => 'setup_img')) }}<h2 class="heading">Install<span>POS</span><strong style="font-size: 13px;">V2</strong></h2>
                                </div>
                                <div id="wizard" class="swMain">
                                    <ul>
                                        <li>
                                            <a href="#step-1">
                                                <label class="stepNumber">1</label>
                                                <span class="stepDesc">
						   Company Profile<br />
                                                    <small>Fill your Company Profile</small>
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#step-2">
                                                <label class="stepNumber">2</label>
                                                <span class="stepDesc">
											   Configuration<br />
                                                    <small>Fill your configuration</small>
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#step-3">
                                                <label class="stepNumber">3</label>
                                                <span class="stepDesc">
											   Super Admin Info<br />
                                                    <small>Fill your Admin Information</small>
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#step-4">
                                                <label class="stepNumber">4</label>
                                                <span class="stepDesc">
											   Module Setup<br />
                                                    <small>Fill your module setup</small>
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div id="step-1" class="form-group">
                                        <h2 class="StepTitle">Step 1: Company Profile</h2>
									@if (Session::has('message'))
                                        <p class="alert">{{ Session::get('message') }}</p>
									@endif
                                        <div class="tbl_wrp">
                                            <table cellspacing="3" cellpadding="3" align="center">
                                                <tr>
                                                    <td align="center" colspan="3">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Company Name :</td>
                                                    <td align="left">
                            						{{ Form::text('company_name', null, array('id' => 'company', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Company Name')) }}
                                                    </td>
                                                    <td align="left"><span id="msg_company"></span>&nbsp;</td>
												{{$errors->first('company_name','<p class="error">:message</p>')}}
                                                </tr>
                                                <tr>
                                                    <td align="right">Address :</td>
                                                    <td align="left">
													{{ Form::text('address', null, array('id' => 'address', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Address')) }}
                                                    </td>
                                                    <td align="left"><span id="msg_address"></span>&nbsp;</td>
												{{$errors->first('address','<p class="error">:message</p>')}}
                                                </tr>
                                                <tr>
                                                    <td align="right">Mobile No :</td>
                                                    <td align="left">
													{{ Form::text('mobile', null, array('id' => 'mobile', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Mobile Number')) }}
                                                    </td>
                                                    <td align="left"><span id="msg_year"></span>&nbsp;</td>
												{{$errors->first('year','<p class="error">:message</p>')}}
                                                </tr>
                                                <tr>
                                                    <td align="right">Year :</td>
                                                    <td align="left">
													{{ Form::text('year', null, array('id' => 'year', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Year')) }}
                                                    </td>
                                                    <td align="left"><span id="msg_mobile"></span>&nbsp;</td>
												{{$errors->first('mobile','<p class="error">:message</p>')}}
                                                </tr>
                                                <tr>
                                                    <td align="right">Web Address :</td>
                                                    <td align="left">
													{{ Form::url('web_address', null, array('id' => 'web_address', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Web Address')) }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div id="step-2">
                                        <h2 class="StepTitle">Step 2: Configuration</h2>
                                        <table cellspacing="3" cellpadding="3" align="center">
                                            <tr>
                                                <td align="center" colspan="3">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="right">Print Receipt After Sale :</td>
                                                <td align="left">
												{{ Form::select('print_recipt_a_sale', array('0' => 'No', '1' => 'Yes'), null, ['id' => 'receipt', 'class' => 'form-control txtBox']) }}
                                                </td>
                                                <td align="left"><span id="msg_firstname"></span>&nbsp;</td>
											{{$errors->first('print_recipt_a_sale','<p class="error">:message</p>')}}
                                            </tr>
                                            <tr>
                                                <td align="right">Language :</td>
                                                <td align="left">
												{{ Form::select('language', array('0' => 'Bangla', '1' => 'English'), null, ['id' => 'language', 'class' => 'form-control txtBox']) }}
                                                </td>
                                                <td align="left"><span id="msg_firstname"></span>&nbsp;</td>
											{{$errors->first('language','<p class="error">:message</p>')}}
                                            </tr>
                                            <tr>
                                                <td align="right">Time Zone :</td>
                                                <td align="left">
												{{ Form::select('time_zone', array('GMT' => 'GMT +6.00', 'GMT +6.00' => 'GMT +8.00'), null, ['id' => 'timezone', 'class' => 'form-control txtBox']) }}
                                                </td>
                                                <td align="left"><span id="msg_firstname"></span>&nbsp;</td>
											{{$errors->first('time_zone','<p class="error">:message</p>')}}
                                            </tr>
                                            <tr>
                                                <td align="right">Software Theme :</td>
                                                <td align="left">
												{{ Form::select('theme', array('0' => 'Default', '1' => 'Simple'), null, ['id' => 'theme', 'class' => 'form-control txtBox']) }}
                                                </td>
                                                <td align="left"><span id="msg_firstname"></span>&nbsp;</td>
											{{$errors->first('theme','<p class="error">:message</p>')}}
                                            </tr>
                                        </table>
                                    </div>

                                    <div id="step-3">
                                        <h2 class="StepTitle">Step 3: Super Admin Information</h2>
                                        <table cellspacing="3" cellpadding="3" align="center">
                                            <tr>
                                                <td align="center" colspan="3">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="right">First Name :</td>
                                                <td align="right">
												{{ Form::text('f_name', null, array('id' => 'f_name', 'class' => 'form-control txtBox', 'placeholder' => 'Enter First Name')) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Last Name :</td>
                                                <td align="left">
												{{ Form::text('l_name', null, array('id' => 'l_name', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Last Name')) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Mobile :</td>
                                                <td align="left">
												{{ Form::number('mobile', null, array('id' => '', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Mobile Number')) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Email :</td>
                                                <td align="left">
												{{ Form::email('email', null, array('id' => 'email', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Email Address')) }}
                                                </td>
                                                <td align="left"><span id="msg_email"></span>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td align="right">Username :</td>
                                                <td align="left">
												{{ Form::text('user_name', null, array('id' => 'super_admin', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Username')) }}
                                                </td>
                                                <td align="left"><span id="msg_admin"></span>&nbsp;</td>
											{{$errors->first('user_name','<p class="error">:message</p>')}}
                                            </tr>
                                            <tr>
                                                <td align="right">Password :</td>
                                                <td align="left">
												{{ Form::password('password', array('id' => 'password', 'class' => 'form-control txtBox', 'placeholder' => 'Enter Password')) }}
                                                </td>
                                                <td align="left"><span id="msg_password"></span>&nbsp;</td>
											{{$errors->first('password','<p class="error">:message</p>')}}
                                            </tr>
                                            <tr>
                                                <td align="right">Return Policy : </td>
                                                <td align="left">
												{{ Form::select('return_policy', array('0' => 'No', '1' => 'Yes'), null, ['id' => 'return_policy', 'class' => 'form-control txtBox']) }}
                                                </td>
                                                <td align="left"><span id="msg_policy"></span>&nbsp;</td>
											{{$errors->first('return_policy','<p class="error">:message</p>')}}
                                            </tr>
                                        </table>
                                    </div>

                                    <div id="step-4">
                                        <h2 class="StepTitle">Step 4: Module Configuration</h2>
                                        <table cellspacing="3" cellpadding="3" align="center">
                                            <tr>
                                                <td align="center" colspan="3">&nbsp;</td>
                                            </tr>

                                            <tr>
                                                <td align="center"><strong style="border-bottom: 2px solid #EA8511; padding-bottom: 3px; font-size: bolder;">Select Module : </strong></td>
                                            </tr>

										@foreach($modules as $module)
                                            <tr>
                                                <td align="right"></td>
                                                <td align="left">
                                                    <input id="{{{ $module->module_id }}}" name="module_id[]" value="{{{ $module->module_id }}}" type="checkbox" @if($module->status==0) {{"checked='checked'"}} @endif/>
                                                    <label for="{{{ $module->module_id }}}">{{{ $module->module_name }}}</label>
                                                </td>
                                                <td align="left"><span id="msg_phone"></span>&nbsp;</td>
                                            </tr>
										@endforeach

                                        </table>
                                    </div>
                                </div>
                                <!-- End SmartWizard Content -->
					 {{ Form::close() }} 
                            </td></tr>
                    </table>
                </div>
            </div>
        </div>


        <!-- javascript
	================================================== --> 
	{{ HTML::script('js/jquery-1.7.2.min.js') }}
	{{ HTML::script('js/bootstrap.js') }}
        <!-- Sticy Sidebar-->
        <!-- wizard Js-->
	{{ HTML::script('js/jquery.smartWizard.js') }}

        <script type="text/javascript">
            $(document).ready(function(){
                // Smart Wizard
                $('#wizard').smartWizard({transitionEffect:'slideleft',onLeaveStep:leaveAStepCallback,onFinish:onFinishCallback,enableFinishButton:true});
                function leaveAStepCallback(obj){
                    var step_num= obj.attr('rel');
                    return validateSteps(step_num);
                }
                function onFinishCallback(){
                    if(validateAllSteps()){
                        $('#myForm').submit();
                    }
                }
            });

            function validateAllSteps(){
                var isStepValid = true;
                if(validateStep1() == false){
                    isStepValid = false;
                    $('#wizard').smartWizard('setError',{stepnum:1,iserror:true});
                }else{
                    $('#wizard').smartWizard('setError',{stepnum:1,iserror:false});
                }

                if(validateStep3() == false){
                    isStepValid = false;
                    $('#wizard').smartWizard('setError',{stepnum:3,iserror:true});
                }else{
                    $('#wizard').smartWizard('setError',{stepnum:3,iserror:false});
                }

                if(!isStepValid){
                    $('#wizard').smartWizard('showMessage','Please correct the errors in the steps and continue');
                }
                return isStepValid;
            }
            function validateSteps(step){

                var isStepValid = true;
                // validate step 1
                if(step == 1){
                    if(validateStep1() == false ){
                        isStepValid = false;
                        $('#wizard').smartWizard('showMessage','Please correct the errors in step'+step+ ' and click next.');
                        $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});
                    }else{
                        $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
                    }
                }
                // validate step3
                if(step == 3){
                    if(validateStep3() == false ){
                        isStepValid = false;
                        $('#wizard').smartWizard('showMessage','Please correct the errors in step'+step+ ' and click next.');
                        $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});
                    }else{
                        $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
                    }
                }
                return isStepValid;
            }

            function validateStep1(){
                var isValid = true;

                // Validate Company Name
                var cpn = $('#company').val();
                if(!cpn && cpn.length <= 0){
                    isValid = false;
                    $('#msg_company').html('<strong style="color:red">Please fill company name</strong>').show();
                }else{
                    $('#msg_company').html('').hide();
                }

                var add = $('#address').val();
                if(!add && add.length <= 0){
                    isValid = false;
                    $('#msg_address').html('<strong style="color:red">Please fill address</strong>').show();
                }else{
                    $('#msg_address').html('').hide();
                }

                var mbn = $('#mobile').val();
                if(!mbn && mbn.length <= 0){
                    isValid = false;
                    $('#msg_mobile').html('<strong style="color:red">Please fill mobile no.</strong>').show();
                }else{
                    $('#msg_mobile').html('').hide();
                }

                return isValid;
            }

            function validateStep3(){
                var isValid = true;

                //validate email  email
                var email = $('#email').val();
                if(email && email.length > 0){
                    if(!isValidEmailAddress(email)){
                        isValid = false;
                        $('#msg_email').html('Email is invalid').show();
                    }else{
                        $('#msg_email').html('').hide();
                    }
                }

                var spa = $('#super_admin').val();
                if(!spa && spa.length <= 0){
                    isValid = false;
                    $('#msg_admin').html('<strong style="color:red">Please fill Super Admin</strong>').show();
                }else{
                    $('#msg_admin').html('').hide();
                }
                var pass = $('#password').val();
                if(!pass && pass.length <= 0){
                    isValid = false;
                    $('#msg_password').html('<strong style="color:red">Please fill password</strong>').show();
                }else{
                    $('#msg_password').html('').hide();
                }
                var pcy = $('#return_policy').val();
                if(!pcy && pcy.length <= 0){
                    isValid = false;
                    $('#msg_policy').html('<strong style="color:red">Please fill policy</strong>').show();
                }else{
                    $('#msg_policy').html('').hide();
                }


                return isValid;
            }

            // Email Validation
            function isValidEmailAddress(emailAddress) {
                var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
                return pattern.test(emailAddress);
            }
        </script>

    </body>
</html>