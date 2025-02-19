
	<!-- /Sticky sidebar -->

	<div id="sticky">        
		<ul id="example-3" class="sticklr">
			<li>
				<a href="javascript;" onclick='history.go(-1)' title="">{{ HTML::image('img/nav_icon/back.png', 'title', array('class' => 'sticky_icon')) }}</a>  
			</li>
		</ul>       
	</div>
	@yield('stickyInfo')
	<!-- /extra -->
	<div class="footer">
	  <div class="footer-inner">
		<div class="container">
		  <div class="row">
			<div class="span12"> 
				<strong> &copy; {{ date('Y') }} <a target="_blank" href="http://uit.unitechengr.com.com/">Unitech IT</a>.</strong> 
				<div style="float:right;">
					<span class="shortcut">F1. Sales</span>
					<span class="shortcut">F2. Purchase</span>
					<span class="shortcut">F3. Sending</span>
					<span class="shortcut">F4. Receiving</span>
					<span class="shortcut">F7. Reports</span>
				</div>
			</div>
			<!-- /span12 --> 
		  </div>
		  <!-- /row --> 
		</div>
		<!-- /container --> 
	  </div>
	  <!-- /footer-inner --> 
	</div>
	<!-- /footer --> 
 
<!--Javascript library-->

<!-- Sticy Sidebar-->
{{ HTML::script('js/sticky/jquery-sticklr-1.4.pack.js') }}
{{ HTML::script('js/sticky/jquery.localscroll-min.js') }}

{{ HTML::script('js/base.js') }}

<!-- 
================================================== --> 
<script>	
	$(document).ready(function () {
	  /*
		* Datepicker Function
	  */	 			
		$('.datepicker').datepicker().on('changeDate', function(ev){
			$(this).datepicker('hide');
		});
		$('.datepicker').on('keyup',function(e) {
		  if (e.keyCode == 27) {
			$(this).datepicker('hide');
			}
		});
	  /*
	   *	scroll to fixed top menu bar
	  */
		var subnavbar = $('.subnavbar');
		var origOffsetY = subnavbar.offset().top;
		function scroll() {
			if ($(window).scrollTop() >= origOffsetY) {
				//$('.subnavbar').css({"position": "fixed", "z-index": "99999", "width": "100%","top":"0"});
			} else {
				//$('.subnavbar').css({"position": "absolute","z-index": "99999","width": "100%"});
				//$('.content-wrapper').css({"margin-top":"20px"});
			}
		}
		document.onscroll = scroll;
		
	  /*
		*  sticky Sidebar
	  */		
	  	$('#example-1').sticklr({
			showOn		: 'click',
			stickTo     : 'left',
            size        : 32
		});

		$('#example-3').sticklr({
			animate     : true,
			relativeTo  : 'top',
			showOn		: 'hover',
			stickTo     : 'right',
			size        : 24
		});
		$.localScroll();
		
	  /*
		* fode on menu bar
	  */
		$('.subnavbar .nav-main').click(function() {
			$('.sub-navbar', this).slideToggle("slow");
			
		});
		$('body').click(function() {
			$('.sub-navbar').fadeOut("slow");		
		});
	 /*
	  *	Animation anchor for Go to Top
	  *	Add class when scroll down
	 */
		$(window).scroll(function(event){
			var scroll = $(window).scrollTop();
			if (scroll >= 50) {
				$(".go-top").addClass("show");
			} else {
				$(".go-top").removeClass("show");
			}
		});
		$('#scroll_top').click(function(){
			$('html, body').animate({
				scrollTop: $( $(this).attr('href') ).offset().top
			}, 1000);
		});
	  /*
	   *	Boostrap modal close problem
	  */
		$('.modal').modal({
		  show: false,
		  backdrop: 'static'
		});
	  /*
	   *	Bootstrap tooltip 
	  */
		$('[data-toggle="tooltip"]').tooltip();
		
	});	
	/*
	 *	Shortcut for quick access
	*/
	$(window).load(function(){
		init();
	});
	function init() {
		shortcut.add("F1", function() {
			window.location = "{{ URL::to('sale/sales') }}";
		});
		shortcut.add("F2", function() {
			window.location = "{{ URL::to('purchase/purchases') }}";
		});
		shortcut.add("F3", function() {
			window.location = "{{ URL::to('sending') }}";
		});
		shortcut.add("F4", function() {
			window.location = "{{ URL::to('receiving') }}";
		});
		shortcut.add("F7", function() {
			window.location = "{{ URL::to('admin/reports') }}";
		});
	}
	
	/*
	 *	alert message remove within 6 sec
	*/
	 $(window).load(function(){
		setTimeout(function(){ $('.alert').fadeOut() }, 6000);		
	});
</script>