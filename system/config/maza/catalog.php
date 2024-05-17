<?php
// Startup
$_['mz_startup'] = array(
	'extension/maza/startup/redirect',
	'extension/maza/startup/seo_url',
	'extension/maza/startup/pagecache',
	'extension/maza/startup/setting',
	'extension/maza/startup/event',
	'extension/maza/startup/hook',
	'extension/maza/startup/product_label',
	'extension/maza/startup/catalog',
	'extension/maza/startup/document',
);

// Apply full page cache to route
$_['mz_cache_route'] = array(
	'common/home',
	'product/category',
	'product/manufacturer',
	'product/manufacturer/info',
	'product/search',
	'product/product',
	'product/special',
	'extension/maza/products',
	'extension/maza/page',
	'extension/maza/product/quick_view',
	'extension/maza/blog/article',
	'extension/maza/blog/author',
	'extension/maza/blog/category',
	'extension/maza/blog/home',
	'extension/maza/blog/all',
);

// Autoload Model
$_['mz_model_autoload']       = array(
	'extension/maza/opencart',
	'extension/maza/common',
	'extension/maza/asset',
	'extension/maza/catalog/product',
	'extension/maza/image',
	'tool/image',
);

// Action Events
$_['mz_action_event'] = array(
	'controller/common/header/before' => array(
		'extension/maza/event/controller/common/header/before'
	),
	'controller/common/header/after' => array(
		'extension/maza/event/document/after',
	),
	'controller/common/footer/before' => array(
		'extension/maza/event/document/before',
		'extension/maza/event/controller/common/footer/before',
	),
	'controller/common/home/before' => array(
		'extension/maza/event/controller/common/home/before'
	),
	'controller/checkout/checkout/before' => array(
		'extension/maza/event/controller/checkout/checkout/before'
	),
	'controller/checkout/cart/add/after' => array(
		'extension/maza/event/controller/checkout/cart/addAfter'
	),
	'controller/account/wishlist/add/after' => array(
		'extension/maza/event/controller/account/wishlist/addAfter'
	),
	'controller/product/compare/add/after' => array(
		'extension/maza/event/controller/product/compare/addAfter'
	),
	'controller/product/category/before' => array(
		'extension/maza/event/controller/product/category/before',
	),
	'controller/product/search/before' => array(
		'extension/maza/event/controller/product/search/before',
	),
	'controller/product/special/before' => array(
		'extension/maza/event/controller/product/special/before',
	),
	'controller/product/product/before' => array(
		'extension/maza/event/recent_viewed/beforeController',
		'extension/maza/event/controller/product/product/before',
	),
	'controller/product/manufacturer/info/before' => array(
		'extension/maza/event/controller/product/manufacturer/infoBefore',
	),
	'controller/information/information/before' => array(
		'extension/maza/event/controller/information/information/before',
	),
	'controller/extension/maza/blog/article/before' => array(
		'extension/maza/event/recent_viewed/beforeController'
	),
	'model/*/before' => array(
		'extension/maza/event/model/cache/before'
	),
	'model/*/after' => array(
		999 => 'extension/maza/event/model/cache/after'
	),
	'model/tool/image/resize/before' => array(
		'extension/maza/event/model/tool/image/resizeBefore'
	),
	'model/catalog/product/getProduct/after' => array(
		'extension/maza/event/model/catalog/product/getProductAfter'
	),
	'model/catalog/product/getProducts/before' => array(
		'extension/maza/event/model/catalog/product/getProductsBefore'
	),
	'model/catalog/product/getTotalProducts/before' => array(
		'extension/maza/event/model/catalog/product/getTotalProductsBefore'
	),
	'model/catalog/product/getProductAttributes/before' => array(
		'extension/maza/event/model/catalog/product/getProductAttributesBefore'
	),
	'model/account/customer/addCustomer/after' => array(
		'extension/maza/event/model/account/customer/addCustomerAfter'
	),
	'model/account/customer/editNewsletter/after' => array(
		'extension/maza/event/model/account/customer/editNewsletterAfter'
	),
	'view/common/header/before' => array(
		'extension/maza/event/view/common/header/before'
	),
	'view/common/footer/before' => array(
		'extension/maza/event/view/common/footer/before'
	),
	'view/common/home/before' => array(
		'extension/maza/event/view/common/home/before'
	),
	'view/*/before' => array(
		'extension/maza/event/view/before'
	),
	// 'view/*/after' => array(
	// 	'extension/maza/event/view/after'
	// ),
	'view/common/column_left/before' => array(
		'extension/maza/event/view/common/column_left/before'
	),
	'view/common/column_right/before' => array(
		'extension/maza/event/view/common/column_right/before'
	),
	'view/common/content_top/before' => array(
		'extension/maza/event/view/common/content_top/before'
	),
	'view/common/content_bottom/before' => array(
		'extension/maza/event/view/common/content_bottom/before'
	),
	'view/extension/module/account/before' => array(
		'extension/maza/event/view/extension/module/account/before'
	),
	'view/extension/payment/*/before' => array(
		'extension/maza/event/view/extension/payment/before'
	),
	'view/information/information/before' => array(
		'extension/maza/event/view/information/information/before'
	),
	'view/information/contact/before' => array(
		'extension/maza/event/view/information/contact/before'
	),
	'view/product/manufacturer_info/before' => array(
		999 => 'extension/maza/event/view/product/manufacturer_info/before'
	),
	'view/product/category/before' => array(
		999 => 'extension/maza/event/view/product/category/before'
	),
	'view/product/special/before' => array(
		999 => 'extension/maza/event/view/product/special/before'
	),
	'view/product/search/before' => array(
		999 => 'extension/maza/event/view/product/search/before'
	),
	'view/product/product/before' => array(
		999 => 'extension/maza/event/view/product/product/before'
	),
	// 'view/product/product/button/after' => array(
	// 	'extension/maza/event/view/debug'
	// ),
);

// Action hook
$_['mz_action_hook'] = array(
	'gallery' => array(
		'extension/maza/hooks/gallery/default'
	),
);
