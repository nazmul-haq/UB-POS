<script>
	function loadingImg(){
		$('#loading').ajaxStart(function() {
			$(this).show();
		}).ajaxComplete(function() {
			$(this).hide();
		});
	}
	$().ready(function(){
	 //=============Item Category==============		
		$("#addItemCategory").click(function(){
			loadingImg();
			$("#AdditemCategorybody").load('{{ route("admin.itemCategoryForm") }}');
		});		
		
		$("#viewitemCategory").click(function(){
			loadingImg();
			$("#viewItemCategorybody").load('{{ route("admin.itemCategoryView") }}');
		});
		
	 //=========Item Brand==========
		$("#addItemBrand").click(function(){
			loadingImg();
			$("#addItemBrandBody").load('{{ route("admin.itemBrandForm") }}');
		});	
		
		$("#viewItemBrand").click(function(){
			loadingImg();
			$("#viewItemBrandBody").load('{{ route("admin.itemBrandView") }}');
		});	
	
	 //=============Item Location==============		
		$("#addItemLocationModal").click(function(){
			loadingImg();
			$("#AdditemLocationbody").load('{{ route("admin.itemLocationForm") }}');
		});		
		
		$("#viewItemLocation").click(function(){
			loadingImg();
			$("#viewItemLocationbody").load('{{ route("admin.itemLocationView") }}');
		});
		
	 //=========Item Company==========
		$("#addItemCompany").click(function(){
			loadingImg();
			$("#addItemCompanyBody").load('{{ route("admin.itemCompanyForm") }}');
		});	
		
		$("#viewItemCompany").click(function(){
			loadingImg();
			$("#viewItemCompanyBody").load('{{ route("admin.itemCompanyView") }}');
		});	
	
	 //=============Income/Expense==============		
		$("#addIncExpModal").click(function(){
			loadingImg();
			$("#AddIncExpbody").load('{{ route("admin.incExpTypeForm") }}');
		});		
		
		$("#viewIncExp").click(function(){
			loadingImg();
			$("#viewIncExpbody").load('{{ route("admin.getIncExpType") }}');
		});
		
		
	});
</script>