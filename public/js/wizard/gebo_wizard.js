/* [ ---- Gebo Admin Panel - wizard ---- ] */

	$(document).ready(function() {
		//* simple wizard
		gebo_wizard.simple();
		//* wizard with validation
		gebo_wizard.validation();
		//* add step numbers to titles
		gebo_wizard.steps_nb();
	});

	gebo_wizard = {
		simple: function(){
			$('#simple_wizard').stepy({
				titleClick	: true,
				nextLabel:      'Next &raquo;',
				backLabel:      '<i class="glyphicon glyphicon-chevron-left"></i> Back'
			});
		},
		
		validation: function(){
			jQuery.validator.addMethod("lettersonly", function(value, element) {
			  return this.optional(element) || /^[a-z]+$/i.test(value);
			}, "<span class='error'>Only alphabetical characters</span>");
			
			jQuery.validator.addMethod("alphaspace", function(value, element) {
			  return this.optional(element) || /^[a-z\ \s]+$/i.test(value);
			}, "<span class='error'>Only alphabetical characters</span>");
			
			jQuery.validator.addMethod("alphanumber", function(value, element) {
			   return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_]+$/);
			}, "Only letters, Numbers & underscore Allowed.");

			$('#validate_wizard').stepy({
				nextLabel:      'Next  &raquo;',
				backLabel:      '<i class="icon-chevron-left"></i> Backward',
				block		: true,
				errorImage	: true,
				titleClick	: true,
				validate	: true
			});
			stepy_validation = $('#validate_wizard').validate({
				onfocusout: false,
				errorPlacement: function(error, element) {
					error.appendTo( element.closest("div.controls") );
				},
				highlight: function(element) {
					$(element).closest("div.form-control").addClass("error f_error");
					var thisStep = $(element).closest('form').prev('ul').find('.current-step');
					thisStep.addClass('error-image');
				},
				unhighlight: function(element) {
					$(element).closest("div.form-control").removeClass("error f_error");
					if(!$(element).closest('form').find('div.error').length) {
						var thisStep = $(element).closest('form').prev('ul').find('.current-step');
						thisStep.removeClass('error-image');
					};
				},				
				rules: {
					'role'		: {
						required	: true
					},
					'f_name'		: {
						required	: true,
						lettersonly	: true
					},
					'l_name'		: {
						required	: true,
						lettersonly	: true
					},
					'father_name'	: {
						required	: false,
						alphaspace	: true
					},
					'mother_name'	: {
						required	: false,
						alphaspace	: true
					},
					'user_name'		: {
						required	: true,
						minlength	: 3,
						alphanumber : true
					},
					'password'		: {
						required	: true,
						minlength	: 4
					},
					'mobile'		: {
						required	: true,
						digits		: true,
						minlength	: 10,
						maxlength	: 14
					},
					'email'		: {
						required	: false,
						email		: true
					},
				}, messages: {
					'v_f_name'		: { required:  '<span class="error">First Name field is required!</span>' },
					'v_l_name'		: { required:  '<span class="error">Last Name field is required!</span>' },
					'v_father_name'	: { required:  '<span class="error">Father Name field is required!</span>' },
					'v_mother_name'	: { required:  '<span class="error">Mother Name field is required!</span>' },
					'v_username'	: { required:  '<span class="error">Username field is required!</span>'},
					'v_password'	: { required:  '<span class="error">Password field is requerid!</span>' },
					'v_mobile'		: { required:  '<span class="error">Mobile field is requerid!</span>' },
					'v_email'		: { email	:  '<span class="error">Invalid e-mail!</span>' }
				},
				ignore				: ':hidden'
			});
		},
		
		//* add numbers to step titles
		steps_nb: function(){
			$('.stepy-titles').each(function(){
				$(this).children('li').each(function(index){
					var myIndex = index + 1
					$(this).append('<span class="stepNb">'+myIndex+'</span>');
				})
			})
		}
	};