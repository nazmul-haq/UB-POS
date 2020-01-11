<?php
/* godowndifferent price edit for temporary...*/
/* temporary delete godown item */
	Route::get('deleteGodownProduct','ItemController@deleteGodownProduct');
/*end of temporary delte godwon item*/

	Route::get('updateGodownItemPrice/{itemId}', array(
		'as'	=> 'updateGodownItemPrice',
		'uses'	=> 'ItemController@itemGodownPriceEdit'
	));
	Route::post('saveGodownPriceEdit', array(
		'as' 	=>  'admin.saveGodownPriceEdit.post',
		'uses' 	=>  'ItemController@itemGodownPriceEditSave'
	));
	Route::get('updateGodownItemDifferentPrice/{itemId}', array(
		'as'	=> 'updateGodownItemDifferentPrice',
		'uses'	=> 'ItemController@selectDifferentPriceItemFromGodown'
	));
	Route::post('godownDifferentPriceEdit', array(
		'as' 	=>  'godownDifferentPriceEdit.post',
		'uses' 	=>  'ItemController@udpateDifferentPriceItemToGodown'
	));

 /**********login and logout maintainance start**********/
	Route::get('/', array(
		'as' 	=>  'admin.login',
		'uses' 	=>  'EmpInfoController@login'
	));
	Route::get('sessionLogout/{empId}', array(
		'as' 	=>  'admin.sessionLogout',
		'uses' 	=>  'EmpInfoController@sessionLogout'
	));
	Route::post('login', array(
		'as' 	=>  'admin.login.post',
		'uses' 	=>  'EmpInfoController@isLoggedIn'
	));
	Route::get('logout', array(
		'as' 	=>  'admin.logout',
		'uses' 	=>  'EmpInfoController@doLogout'
	));
/**********login and logout maintainance end**********/
        
/**********Routes for employees start**********/
Route::group(array('prefix' => 'admin', 'before' => 'auth'), function(){
	

	Route::get('index', array(
		'as'	=> 	'admin.dashboard',
		'uses'	=>  'EmpInfoController@dashboard'
	));


 /*
  * Database Backup Routes
 */	
	Route::get('dbBackup', array(
		'as'	=> 	'backup.dbBackup',
		'uses'	=>  'BackupController@dbBackup'
	));
	        
/**********Setup Routes**********/
	Route::get('setup', array(
		'as'	=>	'admin.setup',
		'uses'	=>	'SetupController@index'
	));
  /*
 * 	Offer Setup routes
  */
	Route::get('createOffer', array(
		'as'	=>	'admin.addOffer',
		'uses'	=>	'SetupController@addOffer'
	));
	Route::get('offer/getItemWiseDatatable', array(
		'as'	=>	'admin.getItemWiseData',
		'uses'	=>	'SetupController@getItemWiseData'
	));
	Route::get('itemOfferCreate/{itemId}', array(
		'as'	=>	'admin.createItemOffer',
		'uses'	=>	'SetupController@createItemOffer'
	));
	Route::get('itemOfferReset/{itemId}', array(
		'as'	=>	'admin.resetItemOffer',
		'uses'	=>	'SetupController@resetItemOffer'
	));
	
	Route::get('offer/getBrandWiseDatatable', array(
		'as'	=>	'admin.getBrandWiseData',
		'uses'	=>	'SetupController@getBrandWiseData'
	));
	Route::get('itemBrandCreate/{brandId}', array(
		'as'	=>	'admin.createBrandOffer',
		'uses'	=>	'SetupController@createBrandOffer'
	));
	Route::get('itemBrandReset/{brandId}', array(
		'as'	=>	'admin.resetBrandOffer',
		'uses'	=>	'SetupController@resetBrandOffer'
	));
	
	Route::get('offer/getCategoryWiseDatatable', array(
		'as'	=>	'admin.getCategoryWiseData',
		'uses'	=>	'SetupController@getCategoryWiseData'
	));
	Route::get('itemCategoryCreate/{categoryId}', array(
		'as'	=>	'admin.createCategoryOffer',
		'uses'	=>	'SetupController@createCategoryOffer'
	));
	Route::get('itemCategoryReset/{categoryId}', array(
		'as'	=>	'admin.resetCategoryOffer',
		'uses'	=>	'SetupController@resetCategoryOffer'
	));

	//Supplier Wise Offer
	
	Route::get('offer/getSupplierWiseItemData', array(
		'as'	=>	'admin.getSupplierWiseItemData',
		'uses'	=>	'SetupController@getSupplierWiseItemData'
	));
	Route::get('supplierItemOfferCreate/{suppId}', array(
		'as'	=>	'admin.supplierItemOfferCreate',
		'uses'	=>	'SetupController@supplierItemOfferCreate'
	));
	Route::get('supplierItemOfferReset/{suppId}', array(
		'as'	=>	'admin.supplierItemOfferReset',
		'uses'	=>	'SetupController@supplierItemOfferReset'
	));

	//Supplier Wise Offer End


	Route::get('viewOffer', array(
		'as'	=>	'admin.offerView',
		'uses'	=>	'SetupController@offerView'
	));
	
	Route::post('offerShow', array(
		'as'	=>	'admin.showOffer.post',
		'uses'	=>	'SetupController@showOffer'
	));
	
	
		

	/**********___**********/
		
/***************Employee Routes******************/

	Route::get('addEmployee', array(
		'as'	=>	'admin.addEmployee',
		'uses'	=>	'EmpInfoController@addEmployee'
	));
	Route::post('addEmployee', array(
		'as'	=> 'admin.saveEmployee.post',
		'uses'	=> 'EmpInfoController@saveEmployee'
	));
	Route::get('employee', array(
		'before' => array('auth','module'),
		'as'	=> 'admin.EmployeeView',
		'uses'	=> 'EmpInfoController@viewEmployee'
	));
	Route::get('employee/viewEmployee', array(
		'as'	=> 'admin.viewEmployee',
		'uses'	=> 'EmpInfoController@getEmployeeData'
	));
	Route::get('editEmployee/{empId}', array(
		'as'	=> 'admin.editEmployee',
		'uses'	=> 'EmpInfoController@getEmpInfoById'
	))->where('empId', '[1-9][0-9]*');
	Route::post('editEmployee', array(
		'as'	=> 'admin.updateEmployee.post',
		'uses'	=> 'EmpInfoController@updateEmployee'
	));
	Route::get('deleteEmployee/{empId}', array(
		'as'	=> 'admin.deleteEmployee',
		'uses'	=> 'EmpInfoController@deleteEmployee'
	));
/*=================***=======================*/

/*=========Customer Routes==============*/
	Route::get('customers', array(
		'before' => array('auth','module'),
		'as'	=> 'admin.customer',
		'uses'	=> 'CustomerInfoController@index'
	));
	Route::get('customers/viewCustomer', array(
		'as'	=> 'admin.viewCustomer',
		'uses'	=> 'CustomerInfoController@getCustomerData'
	));
	
	Route::post('customers', array(
		'as'	=> 'admin.addCustomerType.post',
		'uses'	=> 'CustomerInfoController@addCustomerType'
	));        
        
    Route::get('customers/TypeRemove/{cus_type_id}', array(
		'as'	=> 'admin.DeleteCustomerType.post',
		'uses'	=> 'CustomerInfoController@deleteCusType'
	));
	
    Route::get('customers/{cus_type_id}/edit', array(
		'as'	=> 'admin.cusTypeEdit',
		'uses'	=> 'CustomerInfoController@cusTypeEdit'
	));
	
        
	Route::post('saveCustomer', array(
		'as'	=> 'admin.saveCustomer.post',
		'uses'	=> 'CustomerInfoController@store'
	));
        
	Route::get('customers/{id}', array(
		'as'	=> 'admin.get.show',
		'uses'	=> 'CustomerInfoController@show'
	));
        

	Route::get('customers/update/{id}', array(
		'as'	=> 'admin.get.edit',
		'uses'	=> 'CustomerInfoController@edit'
	));
	Route::post('customers/updateCustomer', array(
		'as'	=> 'admin.updateCustomer.post',
		'uses'	=> 'CustomerInfoController@update'
	));
	Route::get('customer/destroy/{cus_id}', array(
		'as'	=> 'admin.customer.destroy',
		'uses'	=> 'CustomerInfoController@destroy'
	));
	// Customer payment routes
	Route::get('customer/paymentCus/{supp_id}', array(
		'as'	=> 'admin.customer.payment',
		'uses'	=> 'CustomerInfoController@paymentCus'
	))->where('cus_id', '[1-9][0-9]*');
	
	Route::post('customer/savePayment', array(
		'as'	=> 'customer.saveSupplierPayment.post',
		'uses'	=> 'CustomerInfoController@paymentSaveCustomer'
	));
	Route::post('customer/searchPaymentTransaction/{cus_id}', array(
		'as'	=> 'customer.searchPayTransaction.post',
		'uses'	=> 'CustomerInfoController@paymentCus'
	)); 
       
    //customer Transaction Details
	Route::get('customer/transactionDetails/{cus_id}', array(
		'as'	=> 'admin.cusTransaction',
		'uses'	=> 'CustomerInfoController@transactionDetails'
	))->where('cus_id', '[1-9][0-9]*');
	Route::post('customer/transactionDetails/{cus_id}', array(
		'as'	=> 'admin.cusTransaction',
		'uses'	=> 'CustomerInfoController@transactionDetails'
	))->where('cus_id', '[1-9][0-9]*');
    //End of Transaction Details

	/*
	  * Get Membership Routes
	*/
	Route::get('customer/getMembership', array(
		'as'	=> 'admin.getMembership',
		'uses'	=> 'CustomerInfoController@getMembership'
	)); 
	Route::get('customer/getMembershipData', array(
		'as'	=> 'admin.getMembershipData',
		'uses'	=> 'CustomerInfoController@getMemberShipData'
	));
    Route::get('customer/getMembership/{cus_id}/{type_id}', array(
		'as'	=> 'admin.confirmMembership',
		'uses'	=> 'CustomerInfoController@confirmMembership'
	));

/*=========***==============*/

/*=========Supplier Routes==============*/
	Route::get('suppliers', array(
		'before' => array('auth','module'),
		'as'	=> 'admin.suppliers',
		'uses'	=> 'SupplierInfoController@index'
	));
	Route::get('suppliers/viewSupplierAll', array(
		'as'	=> 'admin.viewSupplierAll',
		'uses'	=> 'SupplierInfoController@getSupplierData'
	));
        Route::post('suppliers/saveSupplier', array(
		'as'	=> 'admin.saveSupplier.post',
		'uses'	=> 'SupplierInfoController@store'
	));
        Route::get('suppliers/{id}', array(
		'as'	=> 'admin.get.show',
		'uses'	=> 'SupplierInfoController@show'
	));

        Route::get('suppliers/update/{id}', array(
		'as'	=> 'admin.get.edit',
		'uses'	=> 'SupplierInfoController@edit'
	));
        Route::post('suppliers/updateSupplier', array(
		'as'	=> 'admin.updateSupplier.post',
		'uses'	=> 'SupplierInfoController@update'
	));
        Route::get('suppliers/destroy/{supp_id}', array(
		'as'	=> 'admin.destroy',
		'uses'	=> 'SupplierInfoController@destroy'
	));
     Route::get('supplier/items/{supp_id}', array(
		'as'	=> 'admin.supplier.items',
		'uses'	=> 'SupplierInfoController@viewSupplierItems'
	))->where('supp_id', '[1-9][0-9]*');
     //purchase order //
	    Route::get('supplier/purchaseOrder/{supp_id}', array(
			'as'	=> 'admin.supplier.purchaseOrder',
			'uses'	=> 'SupplierInfoController@viewSupplierItemsForPurchase'
		))->where('supp_id', '[1-9][0-9]*');
     //purchase order //
	Route::post('addItemToPurchaseOrder', array(
		'as'	=> 'admin.supplier.addItemToPurchaseOrder',
		'uses'	=> 'SupplierInfoController@addItemToPurchaseOrder'
	));
	// Supplier payment routes
	Route::get('supplier/payment/{supp_id}', array(
		'as'	=> 'admin.supplier.payment',
		'uses'	=> 'SupplierInfoController@payment'
	))->where('supp_id', '[1-9][0-9]*');
	Route::post('supplier/savePayment', array(
		'as'	=> 'supplier.saveSupplierPayment.post',
		'uses'	=> 'SupplierInfoController@paymentSaveSupplier'
	));
	Route::post('supplier/searchPaymentTransaction/{supp_id}', array(
		'as'	=> 'supplier.searchPayTransaction.post',
		'uses'	=> 'SupplierInfoController@payment'
	));

  	//Supplier Transaction Details
	Route::get('supplier/transactionDetails/{cus_id}', array(
		'as'	=> 'admin.supplierTransaction',
		'uses'	=> 'SupplierInfoController@transactionDetails'
	))->where('cus_id', '[1-9][0-9]*');
	Route::post('supplier/transactionDetails/{cus_id}', array(
		'as'	=> 'admin.cusTransaction',
		'uses'	=> 'SupplierInfoController@transactionDetails'
	))->where('cus_id', '[1-9][0-9]*');
    //End of Transaction Details



/*=========***==============*/


/*=========Item Category Routes============*/
	Route::get('allItemView', array(
		'before' => array('auth','module'),
		'as'	=> 'admin.allItemView',
		'uses'	=> 'ItemController@allItemView'
	));
        Route::get('viewAllItem', array(
		'as'	=> 'admin.getAllItemData',
		'uses'	=> 'ItemController@getAllItemData'
	));

        Route::get('viewAllItem', array(
		'as'	=> 'viewAllItem.view',
		'uses'	=> 'ItemController@viewData'
	));
        Route::get('items', array(
		'as'	=> 'admin.itemView',
		'uses'	=> 'ItemController@index'
	));
	Route::get('item/viewItem', array(
		'as'	=> 'admin.viewItem',
		'uses'	=> 'ItemController@getItemData'
	));
	Route::get('item/viewAllItem', array(
		'as'	=> 'admin.viewAllItem',
		'uses'	=> 'ItemController@getAllItem'
	));

	//Multiple Item Add

	Route::get('multipleItemAdd', array(
		'as'	=> 'admin.multipleItemAdd',
		'uses'	=> 'ItemController@multipleItemAdd'
	));
	Route::post('saveMultipleItem', array(
		'as'	=> 'admin.saveMultipleItem.post',
		'uses'	=> 'ItemController@saveMultipleItem'
	));
	
	//Multiple Item Add
	
	Route::get('ItemForm', array(
		'as'	=> 'admin.itemAddForm',
		'uses'	=> 'ItemController@addItemForm'
	));
	Route::post('ItemFormSave', array(
		'as'	=> 'admin.itemAddFormSave.post',
		'uses'	=> 'ItemController@saveItem'
	));
	Route::get('itemEditForm/{itemId}', array(
		'as'	=> 'admin.itemForm',
		'uses'	=> 'ItemController@editItemForm'
	));
	Route::get('viewItemSuppliers/{itemId}', array(
		'as'	=> 'admin.viewItemSuppliers',
		'uses'	=> 'ItemController@viewItemSuppliers'
	));
        Route::get('itemPriceEdit/{itemId}', array(
		'as'	=> 'admin.itemPriceEdit',
		'uses'	=> 'ItemController@itemPriceEdit'
	));
         Route::post('PriceEditSave', array(
		'as'	=> 'admin.savePriceEdit.post',
		'uses'	=> 'ItemController@savePriceEdit'
	));
       
         
        Route::get('itemQtyEdit/{stockId}', array(
		'as'	=> 'admin.itemQtyEdit',
		'uses'	=> 'ItemController@itemQtyEdit'
	));
        Route::post('saveItemQty', array(
		'as'	=> 'admin.saveQtyEdit.post',
		'uses'	=> 'ItemController@saveQtyEdit'
	));
	
	Route::post('ItemFormEdit', array(
		'as'	=> 'admin.editItemSave.post',
		'uses'	=> 'ItemController@editItemSave'
	));
	Route::get('itemInactive/{itemId}', array(
		'as'	=> 'admin.itemRemove',
		'uses'	=> 'ItemController@itemRemove'
	));
// item Category Setup routes admin.setUp.itemCategorySetup		
	Route::get('ItemCategory', array(
		'as'	=> 'admin.itemCategoryForm',
		'uses'	=> 'ItemController@itemCategoryForm'
	));
	Route::post('save', array(
		'as'	=> 'admin.addItemCategory.post',
		'uses'	=> 'ItemController@saveItemCategory'
	));
	Route::get('viewCategoryItem', array(
		'as'	=> 'admin.itemCategoryView',
		'uses'	=> 'ItemController@itemCategoryView'
	));
	Route::get('itemCategoryEdit/{categoryId}', array(
		'as'	=> 'admin.editItemCategory.post',
		'uses'	=> 'ItemController@editItemCategory'
	));
	Route::get('itemCategoryRemove/{categoryId}', array(
		'as'	=> 'admin.removeItemCategory.post',
		'uses'	=> 'ItemController@deleteItemCategory'
	));


	//Inventory Dialog Box//
	Route::get('inventory', array(
		'as'	=> 'admin.inventory',
		'uses'	=> 'InventoryDialogController@index'
	));
	Route::get('autoInventoryItemSuggest', array(
		'as'	=> 'admin.autoInventoryItemSuggest',
		'uses'	=> 'InventoryDialogController@autoInventoryItemSuggest'
	));
	Route::get('inventoryDialogItem', array(
		'as'	=> 'admin.inventoryDialogItem',
		'uses'	=> 'InventoryDialogController@viewAllItemForInventory'
	));
	Route::get('emptyCart', array(
		'as'	=> 'admin.emptyCart',
		'uses'	=> 'InventoryDialogController@emptyCart'
	));
	Route::post('editDeleteItem', array(
		'as'	=> 'admin.editDeleteItem',
		'uses'	=> 'InventoryDialogController@saleEditDeleteItem'
	));
	Route::post('inventoryItemToChart', array(
        'as'	=> 'admin.inventoryItemToChart',
        'uses'	=> 'InventoryDialogController@inventoryItemAddToChart'
    ));
	Route::post('inventoryDialogSave', array(
		'as'	=> 'admin.inventoryDialogSave',
		'uses'	=> 'InventoryDialogController@inventoryDialogSave'
	));
//Inventory Dialog Box//
// item Brand Setup routes admin.setUp.itemBrandSetup	
	Route::get('ItemBrand', array(
		'as'	=> 'admin.itemBrandForm',
		'uses'	=> 'ItemController@itemBrandForm'
	));
	Route::post('saveBrand', array(
		'as'	=> 'admin.addItemBrand.post',
		'uses'	=> 'ItemController@saveItemBrand'
	));
	Route::get('viewCategoryBrand', array(
		'as'	=> 'admin.itemBrandView',
		'uses'	=> 'ItemController@itemBrandView'
	));
	Route::get('itemBrandEdit/{brandId}', array(
		'as'	=> 'admin.editItemBrand.post',
		'uses'	=> 'ItemController@editItemBrand'
	));
	Route::get('itemBrandRemove/{brandId}', array(
		'as'	=> 'admin.removeItemBrand.post',
		'uses'	=> 'ItemController@deleteItemBrand'
	));
// item Location Setup routes admin.setUp.itemLocationSetup		
	Route::get('ItemLocation', array(
		'as'	=> 'admin.itemLocationForm',
		'uses'	=> 'ItemController@itemLocationForm'
	));
	Route::post('saveLocation', array(
		'as'	=> 'admin.addItemLocation.post',
		'uses'	=> 'ItemController@saveItemLocation'
	));
	Route::get('viewItemLocation', array(
		'as'	=> 'admin.itemLocationView',
		'uses'	=> 'ItemController@itemLocationView'
	));
	Route::get('itemLocationEdit/{LocationId}', array(
		'as'	=> 'admin.editItemLocation.post',
		'uses'	=> 'ItemController@editItemLocation'
	));
	Route::get('itemLocationRemove/{locationId}', array(
		'as'	=> 'admin.removeItemLocation.post',
		'uses'	=> 'ItemController@deleteItemLocation'
	));
	Route::get('differentPriceItem/{itemId}', array(
		'as'	=> 'admin.diffPriceItem',
		'uses'	=> 'ItemController@differentPricesItem'
	));
  /*
   * item Company Setup routes admin.setUp.itemCompanySetup	
  */ 
	Route::get('ItemCompany', array(
		'as'	=> 'admin.itemCompanyForm',
		'uses'	=> 'ItemController@itemCompanyForm'
	));
	Route::post('saveCompany', array(
		'as'	=> 'admin.addItemCompany.post',
		'uses'	=> 'ItemController@saveItemCompany'
	));
	Route::get('viewCategoryCompany', array(
		'as'	=> 'admin.itemCompanyView',
		'uses'	=> 'ItemController@itemCompanyView'
	));
	Route::get('itemCompanyEdit/{companyId}', array(
		'as'	=> 'admin.editItemCompany.post',
		'uses'	=> 'ItemController@editItemCompany'
	));
	Route::get('itemCompanyRemove/{companyId}', array(
		'as'	=> 'admin.removeItemCompany.post',
		'uses'	=> 'ItemController@deleteItemCompany'
	));
	/*
	 * Income/Expense Type setup Routes 
	*/	
	Route::get('IncExpType', array(
		'as'	=> 'admin.incExpTypeForm',
		'uses'	=> 'SetupController@incExpTypeForm'
	));
	Route::post('saveIncExpType', array(
		'as'	=> 'admin.addIncExpType.post',
		'uses'	=> 'SetupController@addIncExpType'
	));
	Route::get('getIncExpType', array(
		'as'	=> 'admin.getIncExpType',
		'uses'	=> 'SetupController@getIncExpType'
	));
	Route::get('incExpTypeEdit/{typeId}', array(
		'as'	=> 'admin.editIncExpType',
		'uses'	=> 'SetupController@editIncExpType'
	));
	Route::get('incExpTypeRemove/{typeId}', array(
		'as'	=> 'admin.distroyIncExpType',
		'uses'	=> 'SetupController@distroyIncExpType'
	));
	
	/*
	 *	Godown Routes
	*/
	Route::get('godownItem', array(
		'as'	=> 'admin.godownItem',
		'uses'	=> 'ItemController@godownItem'
	));
	Route::get('godownItemDatatable', array(
		'as'	=> 'admin.getGodownItemData',
		'uses'	=> 'ItemController@getGodownItemData'
	));
	Route::get('viewRecentItem', array(
		'as'	=> 'admin.getRecentItems',
		'uses'	=> 'ItemController@getRecentItems'
	));
	Route::get('recentItems', array(
		'as'	=> 'admin.recentItemsDatable',
		'uses'	=> 'ItemController@recentItemsDatable'
	));	
	Route::get('godDifferentPriceItem/{itemId}', array(
		'as'	=> 'admin.godDiffPriceItem',
		'uses'	=> 'ItemController@goDownDifferentPricesItem'
	));
    Route::get('item/godownLowInventory', array(
		'as'	=> 'admin.godownLowInventory',
		'uses'	=> 'ItemController@godownLowInventory'
	));
    Route::get('item/getGLInventory', array(
		'as'	=> 'admin.getGLInventory',
		'uses'	=> 'ItemController@getGLInventory'
	));
    Route::get('stockLowInventory', array(
		'as'	=> 'admin.stockLowInventory',
		'uses'	=> 'ItemController@stockLowInventory'
	));
    Route::get('item/getSLInventory', array(
		'as'	=> 'admin.getSLInventory',
		'uses'	=> 'ItemController@getSLInventory'
	));
    Route::get('returnQtyFromGodown', array(
		'as'	=> 'admin.returnQtyFromGodown',
		'uses'	=> 'TempReturn@returnQtyFromGodown'
	));
    Route::get('returnAutoSuggestGodown', array(
        'as'	=> 'admin.godownItemAutoSuggestion',
        'uses'	=> 'TempReturn@godownItemAutoSuggestion'
    ));
    Route::post('selectDeleteSupplierGodown', array(
		'as'	=> 'admin.selectDeleteSupplierGodown',
		'uses'	=> 'TempReturn@selectDeleteSupplierGodown'
	));
 	Route::post('addReturnQtyFromGodown', array(
		'as'	=> 'admin.addReturnQtyFromGodown',
		'uses'	=> 'TempReturn@addReturnQtyFromGodown'
	));
    Route::post('returnGodownEditDeleteItem', array(
		'as'	=> 'admin.returnGodownEditDeleteItem',
		'uses'	=> 'TempReturn@returnGodownEditDeleteItem'
	));
	Route::post('invoiceAndGodownReturn', array(
		'as'	=> 'admin.invoiceAndGodownReturn',
		'uses'	=> 'TempReturn@invoiceAndGodownReturn'
	)); 
 	Route::get('returnGodownReceipt', array(
        'as'	=> 'admin.returnGodownReceipt',
        'uses'	=> 'TempReturn@returnGodownReceipt'
    )); 


    Route::get('returnReplace', array(
        'as'	=> 'admin.returnReplace',
        'uses'	=> 'TempReturn@returnReplace'
    )); 
         
         
         
    Route::get('returnQtyFromStock', array(
		'as'	=> 'admin.returnQtyFromStock',
		'uses'	=> 'TempReturn@returnQtyFromStock'
	));
    Route::get('returnAutoSuggest', array(
        'as'	=> 'admin.stockItemAutoSuggestion',
        'uses'	=> 'TempReturn@stockItemAutoSuggestion'
    ));
    Route::post('selectDeleteSupplier', array(
		'as'	=> 'admin.selectDeleteSupplier',
		'uses'	=> 'TempReturn@selectDeleteSupplier'
	));
    Route::post('addReturnQtyFromStock', array(
		'as'	=> 'admin.addReturnQtyFromStock',
		'uses'	=> 'TempReturn@addReturnQtyFromStock'
	));
    Route::post('returnStockEditDeleteItem', array(
		'as'	=> 'admin.returnStockEditDeleteItem',
		'uses'	=> 'TempReturn@returnStockEditDeleteItem'
	));
    Route::post('invoiceAndStockReturn', array(
		'as'	=> 'admin.invoiceAndStockReturn',
		'uses'	=> 'TempReturn@invoiceAndStockReturn'
	));
    Route::get('returnStockReceipt', array(
        'as'	=> 'admin.returnStockReceipt',
        'uses'	=> 'TempReturn@returnStockReceipt'
    ));
        
    
    Route::get('returnQtyFromCustomer', array(
		'as'	=> 'admin.returnQtyFromCustomer',
		'uses'	=> 'TempReturn@returnQtyFromCustomer'
	));
    Route::get('AutoSugForCusReturn', array(
        'as'	=> 'admin.itemAutoSugForCusReturn',
        'uses'	=> 'TempReturn@itemAutoSugForCusReturn'
    ));
    Route::post('selectDeleteCustomer', array(
		'as'	=> 'admin.selectDeleteCustomer',
		'uses'	=> 'TempReturn@selectDeleteCustomer'
	));
        Route::post('addItemFromCustomer', array(
		'as'	=> 'admin.addItemFromCustomer',
		'uses'	=> 'TempReturn@addItemFromCustomer'
	));
    Route::post('editDeleteItemFromCus', array(
		'as'	=> 'admin.editDeleteItemFromCus',
		'uses'	=> 'TempReturn@editDeleteItemFromCus'
	));
    Route::post('iWiseReturnInvoice', array(
		'as'	=> 'admin.invoiceAndSaleReturnItemWise',
		'uses'	=> 'TempReturn@invoiceAndSaleReturnItemWise'
	));
    Route::get('itemWisereturnReceipt', array(
        'as'	=> 'admin.itemWiseReturnReceipt',
        'uses'	=> 'TempReturn@itemWiseReturnReceipt'
    ) );
         
/*
*	Reports Routes
*/
    Route::get('reports', array(
		'before' => array('auth','module'),
		'as'	=>'reports.index',
		'uses' 	=>  'ReportController@index'
    ));
	
    Route::get('sending/report', array(
		'as'	=> 'send.report',
		'uses'	=> 'ReportController@sendReport'
	));
    Route::post('sending/viewReport', array(
		'as'	=> 'send.viewReceivingReport',
		'uses'	=> 'ReportController@viewSendReport'
	));
	
    Route::get('receiving/report', array(
		'as'	=> 'receive.report',
		'uses'	=> 'ReportController@receiveReport'
	));
    Route::post('receiving/viewReport', array(
		'as'	=> 'receive.viewReceivingReport',
		'uses'	=> 'ReportController@viewReceivingReport'
	));

   Route::get('sale/report', array(
		'as'	=> 'sale.report',
		'uses'	=> 'ReportController@saleReport'
	));
   Route::post('sale/viewReport', array(
		'as'	=> 'sale.viewSaleReport',
		'uses'	=> 'ReportController@viewSaleReport'
	));
	
	Route::get('sale/saleReportDetails/{saleInvoiceId}', array(
		'as'	=> 'sale.saleDetailsReport',
		'uses'	=> 'ReportController@saleDetailsReport'
	));

	Route::get('sale/saleReportDetailsSuppWise/{saleInvoiceId}/{supplierId}', array(
		'as'	=> 'sale.saleReportDetailsSuppWise',
		'uses'	=> 'ReportController@saleReportDetailsSuppWise'
	));
	
	Route::get('sale/saleReportReceipt/{saleInvoiceId}', array(
		'as'	=> 'sale.saleReportReceipt',
		'uses'	=> 'ReportController@saleReportReceipt'
	));

	//del invoices Section //

	Route::get('deletePurchaseReport', array(
		'as'	=> 'deletePurchaseReport',
		'uses'	=> 'DeleteInvoiceController@deletePurchaseReport'
	));
    Route::post('deletePurchaseReportView', array(
		'as'	=> 'deletePurchaseReportView',
		'uses'	=> 'DeleteInvoiceController@deletePurchaseReportView'
	));

    Route::post('deletePurchaseInvoice', array(
		'as'	=> 'deletePurchaseInvoice.post',
		'uses'	=> 'DeleteInvoiceController@deletePurchaseInvoice'
	));

	Route::get('deleteSaleReport', array(
		'as'	=> 'deleteSaleReport',
		'uses'	=> 'DeleteInvoiceController@deleteSaleReport'
	));
   Route::post('deleteSaleReportView', array(
		'as'	=> 'deleteSaleReportView',
		'uses'	=> 'DeleteInvoiceController@deleteSaleReportView'
	));

	Route::post('deleteSaleInvoice', array(
		'as'	=> 'deleteSaleInvoice.post',
		'uses'	=> 'DeleteInvoiceController@deleteSaleInvoice'
	));
	

	Route::get('viewDelSaleInvoices', array(
		'as'	=> 'viewDelSaleInvoices',
		'uses'	=> 'DeleteInvoiceController@viewDelSaleInvoices'
	));
	//del invoices Section //
    Route::get('purchase/report', array(
		'as'	=> 'purchase.report',
		'uses'	=> 'ReportController@purchaseReport'
	));

    Route::post('purchase/viewReport', array(
		'as'	=> 'purchase.viewPurchaseReport',
		'uses'	=> 'ReportController@viewPurchaseReport'
	));
    Route::get('purchase/purchaseReportDetails/{purchaseInvoiceId}', array(
		'as'	=> 'purchase.purchaseDetailsReport',
		'uses'	=> 'ReportController@purchaseDetailsReport'
	));
	//purchase order
	
	Route::get('purchaseOrder/report', array(
		'as'	=> 'purchaseOrder.report',
		'uses'	=> 'ReportController@purchaseOrderReport'
	));
	Route::post('purchase/viewPurchaseOrderReport', array(
		'as'	=> 'purchase.viewPurchaseOrderReport',
		'uses'	=> 'ReportController@viewPurchaseOrderReport'
	));
	Route::get('purchase/purchaseOrderDetailsReport/{purchaseInvoiceId}', array(
		'as'	=> 'purchase.purchaseOrderDetailsReport',
		'uses'	=> 'ReportController@purchaseOrderDetailsReport'
	));
	//purchase order end
	
    Route::get('saleReturn/report', array(
		'as'	=> 'saleReturn.report',
		'uses'	=> 'ReportController@saleReturnReport'
	));
    Route::post('saleReturn/viewReport', array(
		'as'	=> 'saleReturn.viewSaleReturnReport',
		'uses'	=> 'ReportController@viewSaleReturnReport'
	));
    Route::get('saleReturn/saleReturnReportDetails/{saleReturnInvoiceId}', array(
		'as'	=> 'saleReturn.saleReturnDetailsReport',
		'uses'	=> 'ReportController@saleReturnDetailsReport'
	));
	
    Route::get('otherIncomeReport/report', array(
		'as'	=> 'income.report',
		'uses'	=> 'ReportController@getIncomeReport'
	));
	Route::post('otherIncomeReport/viewReport', array(
		'as'	=> 'income.viewIncomeReport',
		'uses'	=> 'ReportController@viewIncomeReport'
	));
	
    Route::get('otherExpenseReport/report', array(
		'as'	=> 'expense.report',
		'uses'	=> 'ReportController@getExpenseReport'
	));
	Route::post('otherExpenseReport/viewReport', array(
		'as'	=> 'expense.viewExpenseReport',
		'uses'	=> 'ReportController@viewExpenseReport'
	));
	
    Route::get('damageProducts/report', array(
		'as'	=> 'damage.report',
		'uses'	=> 'ReportController@getDamageReport'
	));	
    Route::post('damageProducts/report', array(
		'as'	=> 'damage.report',
		'uses'	=> 'ReportController@getDamageReport'
	));	
	Route::get('damageReportDetails/{damageInvoiceId}', array(
		'as'	=> 'damage.damageDetailsReport',
		'uses'	=> 'ReportController@getDamageDetailsReport'
	));
	
    Route::get('returntogodowon/report', array(
		'as'	=> 'returntogodowon.report',
		'uses'	=> 'ReportController@getReturnToGodowonReport'
	));
	Route::post('returntogodowon/viewReport', array(
		'as'	=> 'returntogodowon.viewReturnGodownReport',
		'uses'	=> 'ReportController@viewReturnToGodowonReport'
	));
	
    Route::get('returnreceiving/report', array(
		'as'	=> 'returnreceiving.report',
		'uses'	=> 'ReportController@getReturnReceivingReport'
	));
	Route::post('returnreceiving/viewReport', array(
		'as'	=> 'returnreceiving.viewReturnReceivingReport',
		'uses'	=> 'ReportController@viewReturnReceivingReport'
	));
	
    Route::get('purchasereturn/report', array(
		'as'	=> 'purchasereturn.report',
		'uses'	=> 'ReportController@getReturnPurchaseReport'
	));
	Route::post('purchasereturn/viewReport', array(
		'as'	=> 'purchasereturn.viewPurchaseReturnReport',
		'uses'	=> 'ReportController@viewPurchaseReturnReport'
	));
    Route::get('purchasereturn/purchaseReturnReportDetails/{purchaseReturnInvoiceId}', array(
		'as'	=> 'purchasereturn.purchaseReturnDetailsReport',
		'uses'	=> 'ReportController@purchaseReturnDetailsReport'
	));
    Route::get('viewAllItem', array(
		'as'	=> 'viewAllItem.report',
		'uses'	=> 'ReportController@viewAllItem'
	));
    Route::get('viewCategoryWise', array(
		'as'	=> 'viewAllItemCategoryWise.report',
		'uses'	=> 'ReportController@viewAllItemCategoryWise'
	));
	
    Route::get('summary/fullReports', array(
		'as'	=> 'summary.fullReports',
		'uses'	=> 'ReportController@getSummaryFullReport'
	));
    Route::post('summary/viewfullReports', array(
            'as'	=> 'summary.viewFullReports',
            'uses'	=> 'ReportController@getSummaryFullReport'
    ));

    Route::get('summary/backDateStockReports', array(
		'as'	=> 'summary.backDateStockReports',
		'uses'	=> 'ReportController@getBackDateStockReports'
	));
    Route::post('summary/viewBackDateStockReports', array(
            'as'	=> 'summary.viewBackDateStockReports',
            'uses'	=> 'ReportController@getBackDateStockReports'
    ));

    Route::get('summary/sales', array(
		'as'	=> 'summary.sales',
		'uses'	=> 'ReportController@getSummarySales'
	));
        Route::post('summary/viewsalesReport', array(
                    'as'	=> 'summary.viewSalesReport',
                    'uses'	=> 'ReportController@getSummarySales'
            ));
    Route::get('summary/salesReturn', array(
		'as'	=> 'summary.salesReturn',
		'uses'	=> 'ReportController@getSummarySalesReturn'
	));
        Route::post('summary/viewSalesReturn', array(
                    'as'	=> 'summary.viewSalesReturnReport',
                    'uses'	=> 'ReportController@getSummarySalesReturn'
            ));
    Route::get('summary/purchases', array(
		'as'	=> 'summary.purchaseReports',
		'uses'	=> 'ReportController@getSummaryPurchaseReports'
	));
        Route::post('summary/viewPurchaseReports', array(
                    'as'	=> 'summary.viewPurchaseReports',
                    'uses'	=> 'ReportController@getSummaryPurchaseReports'
            ));
    Route::get('summary/purchasesReturn', array(
		'as'	=> 'summary.purchaseReturnReports',
		'uses'	=> 'ReportController@getSummaryPurchaseRetrunReports'
	));
        Route::post('summary/viewPurchasesReturn', array(
                    'as'	=> 'summary.viewPurchaseReturnReports',
                    'uses'	=> 'ReportController@getSummaryPurchaseRetrunReports'
            ));
   Route::get('summary/otherIncome', array(
		'as'	=> 'summary.otherIncomeReports',
		'uses'	=> 'ReportController@getSummaryOtherIncomeReports'
	));
	Route::post('summary/viewOtherIncome', array(
		'as'	=> 'summary.viewOtherIncomeReports',
		'uses'	=> 'ReportController@getSummaryOtherIncomeReports'
	));
	Route::get('summary/otherExpense', array(
		'as'	=> 'summary.otherExpenseReports',
		'uses'	=> 'ReportController@getSummaryOtherExpenseReports'
	));
	Route::post('summary/viewOtherExpense', array(
		'as'	=> 'summary.viewOtherExpenseReports',
		'uses'	=> 'ReportController@getSummaryOtherExpenseReports'
	));
		
	Route::get('summary/getEmpSalesReports', array(
		'as'	=> 'summary.EmpSalesReports',
		'uses'	=> 'ReportController@getEmpSaleReports'
	));
	Route::post('summary/viewEmpSalesReports', array(
		'as'	=> 'summary.getEmpSaleReports',
		'uses'	=> 'ReportController@getEmpSaleReports'
	));

	Route::get('summary/EmpDetailSaleReport', array(
		'as'	=> 'summary.EmpDetailSaleReport',
		'uses'	=> 'ReportController@EmpDetailSaleReport'
	));
	Route::post('summary/viewEmpDtlSalesReport', array(
		'as'	=> 'summary.viewEmpDtlSalesReport',
		'uses'	=> 'ReportController@viewEmpDtlSalesReport'
	));

	
	
	Route::get('summary/damageProducts', array(
		'as'	=> 'summary.damageProductReports',
		'uses'	=> 'ReportController@getSummaryDamageReports'
	));
	Route::post('summary/damageProducts', array(
		'as'	=> 'summary.damageProductReports',
		'uses'	=> 'ReportController@getSummaryDamageReports'
	));

	Route::get('summary/itemWiseSalesReport', array(
		'as'	=> 'itemWiseSalesReport.report',
		'uses'	=> 'ReportController@itemWiseSalesReport'
	));

	Route::post('summary/itemWiseSalesReport', array(
		'as'	=> 'itemWiseSalesReport.report',
		'uses'	=> 'ReportController@itemWiseSalesReport'
	));

	Route::get('summary/categoryWiseSalesReport', array(
		'as'	=> 'categoryWiseSalesReport.report',
		'uses'	=> 'ReportController@categoryWiseSalesReport'
	));
	Route::post('summary/categoryWiseSalesReport', array(
		'as'	=> 'categoryWiseSalesReport.report',
		'uses'	=> 'ReportController@categoryWiseSalesReport'
	));






/*
* End Reports Routes
*/
 /*
 * Others Routes
 */
    Route::get('others', array(
                'before'=> array('auth','module'),
		'as'	=> 'other.index',
		'uses'	=> 'OtherController@otherIndex'
	));
    Route::get('otherIncome', array(
		'as'	=> 'others.getOtherIncome',
		'uses'	=> 'OtherController@getOtherIncome'
	));
    Route::get('incomeAutoSuggest', array(
		'as'	=> 'other.incomeAutoSuggest',
		'uses'	=> 'OtherController@autoIncomeSuggest'
	));
    Route::get('otherIncomeDatable', array(
		'as'	=> 'others.getIncomeByDatable',
		'uses'	=> 'OtherController@getIncomeByDatable'
	));
    Route::post('saveIncomeData', array(
		'as'	=> 'saveOtherIncome.post',
		'uses'	=> 'OtherController@saveOtherIncome'
	));
	Route::get('otherIncomeInactive/{incomeId}', array(
		'as'	=> 'inactiveOtherIncome',
		'uses'	=> 'OtherController@inactiveOtherIncome'
	));
	Route::get('otherIncomeEdit/{incomeId}', array(
		'as'	=> 'getOtherIncomeForm.get',
		'uses'	=> 'OtherController@getOtherIncomeForm'
	));
	Route::post('otherIncomeEditSave', array(
		'as'	=> 'editOtherIncome.post',
		'uses'	=> 'OtherController@editOtherIncomeSave'
	));
	//Expense Route
    Route::get('otherExpense', array(
		'as'	=> 'others.getOtherExpense',
		'uses'	=> 'OtherController@getOtherExpense'
	));
    Route::get('expenseAutoSuggest', array(
		'as'	=> 'other.expenseAutoSuggest',
		'uses'	=> 'OtherController@autoExpenseSuggest'
	));
    Route::get('otherExpenseDatable', array(
		'as'	=> 'others.getExpenseByDatable',
		'uses'	=> 'OtherController@getExpenseByDatable'
	));
    Route::post('saveExpenseData', array(
		'as'	=> 'saveOtherExpense.post',
		'uses'	=> 'OtherController@saveOtherExpense'
	));
	Route::get('otherExpenseInactive/{expenseId}', array(
		'as'	=> 'inactiveOtherExpense',
		'uses'	=> 'OtherController@inactiveOtherExpense'
	));
	Route::get('otherExpenseEdit/{expenseId}', array(
		'as'	=> 'getOtherExpenseForm.get',
		'uses'	=> 'OtherController@getOtherExpenseForm'
	));
	Route::post('otherExpenseEditSave', array(
		'as'	=> 'editOtherExpense.post',
		'uses'	=> 'OtherController@editOtherExpenseSave'
	));
	
 /*
 * End Others Routes
 */

/**||--permission module start--**/	
	Route::get('permission', array(
                'before'=>      array('auth','module'),
		'as'	=>	'admin.permission',
		'uses'	=>	'EmpInfoController@permissionEmp'
	));
    Route::post('viewPermission', array(
		'as'	=> 'admin.viewPermission.post',
		'uses'	=> 'EmpInfoController@viewPermissionEmp'
	));
    Route::post('savePermission', array(
		'as'	=> 'admin.savePermissionSubModule.post',
		'uses'	=> 'EmpInfoController@savePermissionEmp'
	));
/**permission module end**/

 /*
  * URL Permission routes
 */	
	Route::get('permissionEmpUrl', array(
		'as'	=>	'admin.urlPermissionEmp',
		'uses'	=>	'EmpInfoController@urlPermissionEmp'
	));
    Route::post('viewEmpUrlPermission', array(
		'as'	=> 'admin.viewEmpUrlPermission.post',
		'uses'	=> 'EmpInfoController@getEmpUrlPermission'
	));
    Route::post('EmpUrlPermissionSave', array(
		'as'	=> 'admin.saveEmpUrlPermission.post',
		'uses'	=> 'EmpInfoController@saveEmpUrlPermission'
	));
 /*
  * URL Permission End
 */	
	
	Route::get('users', array(
		'as'	=>	'admin.user',
		'uses' 	=>  'EmpInfoController@users'
	));
	Route::get('datatable/users', array(
		'as' 	=> 	'admin.datatable.users',
		'uses' 	=>  'EmpInfoController@getDatatable'
	));

});

/**********- Routes for Purchases -**********/

Route::group(array('prefix' => 'purchase', 'before' => 'auth'), function(){

    Route::get('purchases', array(
                    'before'    =>array('auth','module'),
                    'as'	=> 'perchase.index',
                    'uses'	=> 'PurchaseController@index'
            ));
    Route::get('purchasesExcel', array(
                    // 'before'    =>array('auth','module'),
                    'as'	=> 'purchasesExcel.index',
                    'uses'	=> 'PurchaseController@itemAddTochartExcel'
            ));
    Route::get('autoSuggest', array(
                    'as'	=> 'purchase.itemAutoSuggest',
                    'uses'	=> 'PurchaseController@autoItemSuggest'
            ));
    Route::post('itemAddTochart', array(
                    'as'	=> 'purchase.addItemToChart',
            'uses'	=> 'PurchaseController@itemAddChart'
    ));

    Route::post('discountPermission', array(
        'as'	=> 'sale.discountPermission.post',
        'uses'	=> 'SaleController@discountPermission'
    ));


    Route::post('editDeleteItem', array(
		'as'	=> 'purchase.editDeleteItem',
		'uses'	=> 'PurchaseController@editDeleteItem'
	));
    Route::get('supplierAutoSugg', array(
		'as'	=> 'purchase.autoSupplierSuggest',
		'uses'	=> 'PurchaseController@autoSupplierSuggest'
	));
    Route::post('selectDeleteSupplier', array(
		'as'	=> 'purchase.selectDeleteSupplier',
		'uses'	=> 'PurchaseController@selectDeleteSupplier'
	));
    Route::post('invoiceAndPurchase', array(
		'as'	=> 'purchase.invoiceAndPurchase',
		'uses'	=> 'PurchaseController@invoiceAndPurchase'
	));
   Route::get('receipt', array(
                    'as'	=> 'receipt',
                    'uses'	=> 'PurchaseController@purchaseReceipt'
        ));
   Route::get('orderReceipt', array(
                    'as'	=> 'orderReceipt',
                    'uses'	=> 'PurchaseController@orderReceipt'
        ));
   Route::get('sendOrderToPurchase/{purchaseInvoiceId}', array(
		'as'	=> 'purchase.sendOrderToPurchase',
		'uses'	=> 'PurchaseController@sendOrderToPurchase'
	));
   Route::get('returns', array(
		'as'	=> 'purchase.returnToSupplier',
		'uses'	=> 'PurchaseReturnController@index'
	));
    Route::get('returnAutoSuggest', array(
                    'as'	=> 'purchaseReturn.invoiceAutoSuggest',
                    'uses'	=> 'PurchaseReturnController@autoInvoiceSuggest'
            ));
    Route::post('returnItemAddTochart', array(
                    'as'	=> 'purchaseReturn.returnItemAddTochart',
                    'uses'	=> 'PurchaseReturnController@returnItemAddTochart'
            ));
    Route::post('returnEditDeleteItem', array(
		'as'	=> 'purchaseReturn.editDeleteItem',
		'uses'	=> 'PurchaseReturnController@purchaseReturnEditDeleteItem'
	));
   Route::post('invoiceAndPurchaseReturn', array(
		'as'	=> 'purchaseReturn.invoiceAndPurchaseReturn',
		'uses'	=> 'PurchaseReturnController@invoiceAndPurchaseReturn'
	));

  Route::get('returnReceipt', array(
                    'as'	=> 'purchaseReturn.returnReceipt',
                    'uses'	=> 'PurchaseReturnController@purchaseReturnReceipt'
            ));
});

/*=========- End of Purchases Routes -==============*/

/*
 * Damage Product Routes
*/
Route::group(array('prefix' => 'damage', 'before' => 'auth'), function(){
    Route::get('damageProducts', array(
		'as'	=> 'damage.index',
		'uses'	=> 'DamageController@index'
	));
	
    Route::get('autoSuggest', array(
		'as'	=> 'damage.itemAutoSuggest',
		'uses'	=> 'DamageController@autoItemSuggest'
	));
    Route::post('itemAddTochart', array(
		'as'	=> 'damage.addItemToChart',
		'uses'	=> 'DamageController@itemAddChart'
	));
    Route::post('editDeleteItem', array(
		'as'	=> 'damage.editDeleteItem',
		'uses'	=> 'DamageController@editDeleteItem'
	));
	Route::post('invoiceAndDamaged', array(
		'as'	=> 'damage.invoiceAndDamaged',
		'uses'	=> 'DamageController@invoiceAndDamaged'
	));
	Route::get('receipt', array(
		'as'	=> 'damage.receipt',
		'uses'	=> 'DamageController@damageReceipt'
	));
});

/**********- Routes for Sending and Receiving -**********/

Route::group(array('before' => 'auth'), function(){
	Route::get('sending', array(
                'before'    =>array('auth','module'),
		'as'	=> 'send',
		'uses'	=> 'SendingReceivingController@index'
	));
        Route::get('autoSuggest', array(
                    'as'	=> 'sending.itemAutoSuggest',
                    'uses'	=> 'SendingReceivingController@autoItemSuggest'
            ));
       Route::post('sending/complete', array(
		'as'	=> 'send.saveSending',
		'uses'	=> 'SendingReceivingController@saveSending'
	));
//        Route::get('sending/report', array(
//		'as'	=> 'send.report',
//		'uses'	=> 'SendingReceivingController@sendReport'
//	));
//        Route::post('sending/viewReport', array(
//		'as'	=> 'send.viewReceivingReport',
//		'uses'	=> 'SendingReceivingController@viewSendReport'
//	));
	Route::post('itemAddForsending', array(
		'as'	=> 'send.itemAddForsending',
		'uses'	=> 'SendingReceivingController@itemAddForSending'
	));
        Route::post('editDeleteSending', array(
		'as'	=> 'send.editDeleteSending',
		'uses'	=> 'SendingReceivingController@editDeleteItemSending'
	));

    Route::get('receiving', array(
                'before'    =>array('auth','module'),
		'as'	=> 'receive',
		'uses'	=> 'SendingReceivingController@receive'
	));
    Route::post('receiving/complete', array(
		'as'	=> 'receive.saveReceiveItem',
		'uses'	=> 'SendingReceivingController@saveReceiveItem'
	));
	Route::get('returnToGodown', array(
		'as'	=> 'send.returnToGodown',
		'uses'	=> 'SendingReceivingController@returnToGodown'
	));
    Route::get('returnToGodownAutoSuggest', array(
                    'as'	=> 'returnToGodown.itemAutoSuggest',
                    'uses'	=> 'SendingReceivingController@returnToGodownAutoSuggest'
            ));
    Route::post('returnItemAddForsending', array(
		'as'	=> 'returnToGodown.itemAddForsending',
		'uses'	=> 'SendingReceivingController@returnItemAddForsending'
	));
       Route::post('returnEditDeleteSending', array(
		'as'	=> 'returnToGodown.returnEditDeleteSending',
		'uses'	=> 'SendingReceivingController@returnEditDeleteSending'
	));
       Route::post('returnToGodown/complete', array(
		'as'	=> 'returnToGodown.saveReturnToGodown',
		'uses'	=> 'SendingReceivingController@saveReturnToGodown'
	));
       Route::get('returnReceiving', array(
		'as'	=> 'returnReceive',
		'uses'	=> 'SendingReceivingController@returnReceive'
	));
        Route::post('returnReceiving/complete', array(
		'as'	=> 'returnReceive.savereturnReceiveItem',
		'uses'	=> 'SendingReceivingController@savereturnReceiveItem'
	));
/*
*	End of Routes for Sending and Receiving 
*/
/**********- Routes for Quick Sending and Receiving -**********/
       Route::get('quickSending', array(
		'as'	=> 'quickSending',
		'uses'	=> 'SendingReceivingController@itemAddForQuickSending'
	));
/**********- Routes End of Quick Sending and Receiving -**********/


/*
*	Routes for Point Report
*/
	Route::get('pointIncreasing/report', array(
		'as'	=> 'pointIncrease.report',
		'uses'	=> 'SaleController@pointIncreaseReport'
	));
    Route::post('pointIncreasing/viewReport', array(
		'as'	=> 'pointIncrease.PointIncreasingReport',
		'uses'	=> 'SaleController@viewPointIncreasingReport'
	));
    Route::get('pointUsing/report', array(
		'as'	=> 'pointUsing.report',
		'uses'	=> 'SaleController@pointUsingReport'
	));
    Route::post('pointUsing/viewReport', array(
		'as'	=> 'pointUse.PointUsingReport',
		'uses'	=> 'SaleController@viewPointUsingReport'
	));
/*
* End of Routes for Point Report
*/

});


/**********- Routes for Sale -**********/

Route::group(array('prefix' => 'sale', 'before' => 'auth'), function(){
    Route::get('sales', array(
                    'before'    =>array('auth','module'),
                    'as'	=> 'sale.index',
                    'uses'	=> 'SaleController@index'
            ));
    Route::get('autoSuggest', array(
                    'as'	=> 'sale.itemAutoSuggest',
                    'uses'	=> 'SaleController@autoItemSuggest'
            ));
    Route::post('itemAddTochart', array(
                    'as'	=> 'sale.addItemToChart',
                    'uses'	=> 'SaleController@saleItemAddChart'
            ));
    Route::post('editDeleteItem', array(
		'as'	=> 'sale.editDeleteItem',
		'uses'	=> 'SaleController@saleEditDeleteItem'
	));
    Route::get('customerAutoSugg', array(
		'as'	=> 'sale.autoCustomerSuggest',
		'uses'	=> 'SaleController@autoCustomerSuggest'
	));
    Route::get('emptyCart', array(
		'as'	=> 'sale.emptyCart',
		'uses'	=> 'SaleController@emptyCart'
	));
    Route::post('selectDeleteCustomer', array(
		'as'	=> 'sale.selectDeleteCustomer',
		'uses'	=> 'SaleController@selectDeleteCustomer'
	));

	Route::get('addInvoiceToQueue', array(
		'as'	=> 'sale.addInvoiceToQueue',
		'uses'	=> 'SaleController@addInvoiceToQueue'
	));
	Route::post('addInvoiceToQueue', array(
		'as'	=> 'sale.addInvoiceToQueue.post',
		'uses'	=> 'SaleController@addInvoiceToQueue'
	));
	Route::post('reloadDeleteInvoiceQueueElement', array(
		'as'	=> 'sale.reloadDeleteInvoiceQueueElement',
		'uses'	=> 'SaleController@reloadDeleteInvoiceQueueElement'
	));

    Route::post('invoiceAndSale', array(
		'as'	=> 'sale.invoiceAndSale',
		'uses'	=> 'SaleController@invoiceAndSale'
	));

	


    Route::get('receipt', array(
                    'as'	=> 'sale.receipt',
                    'uses'	=> 'SaleController@saleReceipt'
            ));
     

    Route::get('returns', array(
                    
                    'as'	=> 'saleReturn.index',
                    'uses'	=> 'SaleReturnController@index'
            ));
    Route::get('returnAutoSuggest', array(
                    'as'	=> 'saleReturn.invoiceAutoSuggest',
                    'uses'	=> 'SaleReturnController@autoInvoiceSuggest'
            ));
    Route::post('returnItemAddTochart', array(
                    'as'	=> 'saleReturn.returnItemAddTochart',
                    'uses'	=> 'SaleReturnController@returnItemAddTochart'
            ));
    Route::post('returnEditDeleteItem', array(
		'as'	=> 'saleReturn.editDeleteItem',
		'uses'	=> 'SaleReturnController@saleReturnEditDeleteItem'
	));
   Route::post('invoiceAndSaleReturn', array(
		'as'	=> 'saleReturn.invoiceAndSaleReturn',
		'uses'	=> 'SaleReturnController@invoiceAndSaleReturn'
	));

  Route::get('returnReceipt', array(
                    'as'	=> 'saleReturn.returnReceipt',
                    'uses'	=> 'SaleReturnController@saleReturnReceipt'
            ));
  
});

/*=========- End of Sale Routes -==============*/




	/****** barcode generator  *****/

Route::group(array('before' => 'auth'), function(){

	// ******* Barcode Generate  ***********//

  	Route::get('barcode', array(
        'as'	=> 'barcode',
		'uses'	=> 'ItemController@generateBarcode'
            ));
	Route::post('barcode', array(
		'as'	=> 'barcode.post',
		'uses'	=> 'ItemController@generateBarcode'
	));
 	Route::post('barcodePrint', array(
		'as'	=> 'barcode.print',
		'uses'	=> 'ItemController@barcodePrint'
	));
	Route::post('barcodeQueueAll', array(
        'as' => 'barcode.queue.all',
        'uses' => 'ItemController@barcodeQueueAll'
    ));
    Route::get('barcodeQueueEmpty', array(
        'as' => 'barcodeQueueEmpty',
        'uses' => 'ItemController@barcodeQueueEmpty'
    ));
    Route::get('barcodeQueueItemDelete/{id}', array(
        'as' => 'barcodeQueueItemDelete',
        'uses' => 'ItemController@barcodeQueueItemDelete'
    ));

	/*******end of Barcode generate*******/

	/****** Software Configuration*****/

  Route::get('editInstallation', array(
                  'as'	=> 'editInstall',
				  'uses'	=> 'InstallationController@editInstall'
            ));
			
 Route::post('updateInstallation/{id}', array(
                  'as'	=> 'updateInstall.post',
				  'uses'	=> 'InstallationController@updateInstall'
            ));
});

	/*******end of Softwre Configureation*******/
 Route::group(array('before' => 'auth'), function(){
		  Route::get('return', array(
                          'before'    =>array('auth','module'),
			  'as'	=> 'return.index',
                          'uses'=> 'ReturnController@index'
					));
  
});

/* empty purchase cart*/
  Route::get('purchaseEmptyCart', array(
                  'as'	=> 'purchase.emptyCart',
				  'uses'	=> 'PurchaseController@emptyCart'
            ));


	
/*
* 	Installation Routes
*/
Route::get('install', array('as' => 'install.setup', 'uses' => 'InstallationController@installation'));
Route::post('install/store', array('as' => 'install.save', 'uses' => 'InstallationController@save'));

//if javascript disabled then this code will execute//
Route::get('noscript', function()
        {
            return View::make('noscript');
        });

Route::get('summary/spplierWisePurchase', array(
		'as'	=> 'summary.spplierWisePurchase',
		'uses'	=> 'ReportController@spplierWisePurchase'
	));

Route::get('summary/getspplierWisePurchaseData/{from}/{to}/{supplierId}', array(
		'as'	=> 'summary.getspplierWisePurchaseData',
		'uses'	=> 'ReportController@getspplierWisePurchaseData'
	)); 
Route::post('summary/spplierWisePurchase', array(
		'as'	=> 'spplierWisePurchase.report',
		'uses'	=> 'ReportController@spplierWisePurchase'
	)); 


Route::get('summary/spplierWiseSale', array(
		'as'	=> 'summary.spplierWiseSale',
		'uses'	=> 'ReportController@spplierWiseSale'
	));

Route::get('summary/getspplierWiseSaleData/{from}/{to}/{supplierId}', array(
		'as'	=> 'summary.getspplierWiseSaleData',
		'uses'	=> 'ReportController@getspplierWiseSaleData'
	)); 
Route::post('summary/spplierWiseSale', array(
		'as'	=> 'spplierWiseSale.report',
		'uses'	=> 'ReportController@spplierWiseSale'
	)); 
//Company Wise Sale
Route::get('summary/companyWiseSale', array(
		'as'	=> 'summary.companyWiseSale',
		'uses'	=> 'ReportController@companyWiseSale'
	));

Route::get('summary/getcompanyWiseSaleData/{from}/{to}/{companyId}', array(
		'as'	=> 'summary.getcompanyWiseSaleData',
		'uses'	=> 'ReportController@getcompanyWiseSaleData'
	)); 
Route::post('summary/companyWiseSale', array(
		'as'	=> 'companyWiseSale.report',
		'uses'	=> 'ReportController@companyWiseSale'
	));

Route::get('summary/saleReportDetailsCompanyWise/{saleInvoiceId}/{companyId}', array(
		'as'	=> 'summary.saleReportDetailsCompanyWise',
		'uses'	=> 'ReportController@saleReportDetailsCompanyWise'
	));

//Company Wise Sale End

Route::get('summary/viewAllItemDataJsonFormat', array(
		'as'	=> 'summary.viewAllItemDataJsonFormat',
		'uses'	=> 'ReportController@viewAllItemDataJsonFormat'
	));

Route::get('summary/getItemWiseSalesReportJsonFormat/{fromDate}/{toDate}', array(
		'as'	=> 'summary.getItemWiseSalesReportJsonFormat',
		'uses'	=> 'ReportController@getItemWiseSalesReportJsonFormat'
	));

Route::get('summary/itemWisePurchaseReport', array(
		'as'	=> 'summary.itemWisePurchaseReport',
		'uses'	=> 'ReportController@itemWisePurchaseReport'
	));

Route::post('summary/itemWisePurchaseReport', array(
            'as'	=> 'itemWisePurchaseReport.report',
            'uses'	=> 'ReportController@itemWisePurchaseReport'
    ));

Route::get('summary/itemWisePurchaseReportJsonFormat/{fromDate}/{toDate}', array(
		'as'	=> 'summary.itemWisePurchaseReportJsonFormat',
		'uses'	=> 'ReportController@itemWisePurchaseReportJsonFormat'
	));

Route::get('export', array(
        'before'=>   array('auth','module'),
		'as'	=>	'export.index',
		'uses'	=>	'ExportToCSVController@index'
	));

Route::get('exportToCsv/{keyword}/{subkey?}',array(
	'as' 	=> 'exportToCsv',
	'uses' 	=> 'ExportToCSVController@exportToCsv'
	// return Hash::make("123456");
));

Route::get('test','ItemController@manageInvoice');
// Route::get('test',function(){
// 	return Hash::make(12345);
// });

Route::post('dbBackupRestore',[
		'as' 	=> 'dbBackupRestore.post',
		'uses'  => 'ItemController@dbBackupRestore' 
	]);

// Start Bank Deposit
Route::group(array('prefix' => 'admin', 'before' => 'auth'), function(){
	Route::get('bankDeposit',array(
		'as' 	=> 'admin.bank.deposit',
		'uses' 	=> 'BankDepositController@bankDeposit')
	);
	Route::post('getBankDeposit',array(
		'as' 	=> 'admin.bank.deposit.getBankDeposit',
		'uses' 	=> 'BankDepositController@bankDeposit')
	);
	Route::get('getBankDeposit/{from?}/{to?}',array(
		'as' 	=> 'admin.bank.deposit.datatable',
		'uses' 	=> 'BankDepositController@getBankDeposit')
	);
	Route::post('bankDeposit',array(
		'as' 	=> 'admin.bank.deposit.post',
		'uses' 	=> 'BankDepositController@saveBankDeposit')
	);
	Route::get('bankDepositEdit/{id}',array(
		'as' 	=> 'admin.bank.deposit.edit',
		'uses' 	=> 'BankDepositController@bankDepositEdit')
	);
	Route::post('bankDepositEdit',array(
		'as' 	=> 'admin.bank.deposit.edit.post',
		'uses' 	=> 'BankDepositController@saveEditBankDeposit')
	);
	Route::get('bankDepositDelete/{id}',array(
		'as' 	=> 'admin.bank.deposit.delete',
		'uses' 	=> 'BankDepositController@bankDepositDelete')
	);
	Route::get('blue-theme',array(
		'as' 	=> 'admin.blue-theme',
		'uses' 	=> 'SetupController@blueTheme')
	);
	Route::get('red-theme',array(
		'as' 	=> 'admin.red-theme',
		'uses' 	=> 'SetupController@redTheme')
	);
});

// End Bank Deposit