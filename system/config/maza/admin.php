<?php
// Actions
// $_['action_pre_action']  = array(
// 	'startup/startup',
// 	'startup/error',
// 	'startup/event',
// 	'startup/sass',
// 	'startup/login',
// 	'startup/permission',
// 	'extension/maza/startup'
// );

// Action Events
$_['mz_action_event'] = array(
	'model/design/layout/deleteLayout/before' => array(
		'extension/maza/event/design/layout/deleteLayout'
	),
	'model/catalog/category/deleteCategory/before' => array(
		'extension/maza/event/catalog/deleteCategory'
	),
	'model/catalog/manufacturer/deleteManufacturer/before' => array(
		'extension/maza/event/catalog/deleteManufacturer'
	),
	'model/catalog/product/deleteProduct/before' => array(
		'extension/maza/event/catalog/deleteProduct'
	),
	'model/localisation/language/addLanguage/after' => array(
		'extension/maza/event/language/addLanguage'
	),
	'model/localisation/language/deleteLanguage/after' => array(
		'extension/maza/event/language/deleteLanguage'
	),
	'model/localisation/currency/refresh/before' => array(
		'extension/maza/event/localisation/currency/refresh'
	),
	'view/common/column_left/before' => array(
		'extension/maza/event/common/menu'
	),
	'view/catalog/product_form/before' => array(
		'extension/maza/event/view/catalog/product_form/before'
	),
	'view/catalog/manufacturer_form/before' => array(
		'extension/maza/event/view/catalog/manufacturer_form/before'
	),
);