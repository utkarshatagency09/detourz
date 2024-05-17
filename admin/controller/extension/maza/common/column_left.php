<?php
class ControllerExtensionMazaCommonColumnLeft extends Controller {
	public function index() {
		$this->load->language('extension/maza/common/column_left');

		// Menu
		$menus = array();

		// Skin
		if ($this->user->hasPermission('access', 'extension/maza/skin')) {
			$menus[] = array(
				'id' => 'mz-menu-skin',
				'icon' => 'fa-desktop',
				'name' => $this->language->get('text_skin'),
				'route' => 'extension/maza/skin',
			);
		}

		// style
		if ($this->user->hasPermission('access', 'extension/maza/style')) {
			$menus[] = array(
				'id' => 'mz-menu-style',
				'icon' => 'fa-paint-brush',
				'name' => $this->language->get('text_style'),
				'route' => 'extension/maza/style',
			);
		}

		// catalog
		if ($this->user->hasPermission('access', 'extension/maza/catalog')) {
			$menus[] = array(
				'id' => 'mz-menu-catalog',
				'icon' => 'fa-file-text',
				'name' => $this->language->get('text_catalog'),
				'route' => 'extension/maza/catalog',
			);
		}

		// layout builder
		if ($this->user->hasPermission('access', 'extension/maza/layout_builder')) {
			$menus[] = array(
				'id' => 'mz-menu-layout-builder',
				'icon' => 'fa-object-group',
				'name' => $this->language->get('text_layout_builder'),
				'route' => 'extension/maza/layout',
			);
		}

		// content builder
		if ($this->user->hasPermission('access', 'extension/maza/content_builder')) {
			$menus[] = array(
				'id' => 'mz-menu-content-builder',
				'icon' => 'fa-table',
				'name' => $this->language->get('text_content_builder'),
				'route' => 'extension/maza/content_builder',
			);
		}

		// page builder
		if ($this->user->hasPermission('access', 'extension/maza/page_builder')) {
			$menus[] = array(
				'id' => 'mz-menu-page-builder',
				'icon' => 'fa-columns',
				'name' => $this->language->get('text_page_builder'),
				'route' => 'extension/maza/page_builder',
			);
		}

		// Module
		if ($this->user->hasPermission('access', 'extension/maza/module')) {
			$menus[] = array(
				'id' => 'mz-menu-module',
				'icon' => 'fa-plug',
				'name' => $this->language->get('text_module'),
				'route' => 'extension/maza/module',
			);
		}

		// Menu
		if ($this->user->hasPermission('access', 'extension/maza/menu')) {
			$menus[] = array(
				'id' => 'mz-menu-menu',
				'icon' => 'fa-bars',
				'name' => $this->language->get('text_menu'),
				'route' => 'extension/maza/menu',
			);
		}

		// Filter
		if ($this->user->hasPermission('access', 'extension/maza/filter')) {
			$menus[] = array(
				'id' => 'mz-menu-filter',
				'icon' => 'fa-filter',
				'name' => $this->language->get('text_filter'),
				'route' => 'extension/maza/filter',
			);
		}

		// Newsletter
		$newsletter = [];

		if ($this->user->hasPermission('access', 'extension/maza/newsletter')) {
			$newsletter[] = array(
				'id' => 'mz-menu-newsletter-mail',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_send_mail'),
				'route' => 'extension/maza/newsletter/mail',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/newsletter')) {
			$newsletter[] = array(
				'id' => 'mz-menu-newsletter-subscriber',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_subscriber'),
				'route' => 'extension/maza/newsletter/subscriber',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/newsletter')) {
			$newsletter[] = array(
				'id' => 'mz-menu-newsletter-setting',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_setting'),
				'route' => 'extension/maza/newsletter',
			);
		}

		if ($newsletter) {
			$menus[] = array(
				'id' => 'mz-menu-newsletter',
				'icon' => 'fa-envelope',
				'name' => $this->language->get('text_newsletter'),
				'children' => $newsletter,
			);
		}

		// Testimonial
		$testimonial = [];

		if ($this->user->hasPermission('access', 'extension/maza/testimonial')) {
			$testimonial[] = array(
				'id' => 'mz-menu-testimonial',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_testimonial'),
				'route' => 'extension/maza/testimonial',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/testimonial')) {
			$testimonial[] = array(
				'id' => 'mz-menu-testimonial-setting',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_setting'),
				'route' => 'extension/maza/testimonial/setting',
			);
		}

		if ($testimonial) {
			$menus[] = array(
				'id' => 'mz-menu-testimonial',
				'icon' => 'fa-smile-o',
				'name' => $this->language->get('text_testimonial'),
				'children' => $testimonial,
			);
		}

		// Notification
		$notification = [];

		if ($this->user->hasPermission('access', 'extension/maza/notification/send')) {
			$notification[] = array(
				'id' => 'mz-menu-notification-send',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_send'),
				'route' => 'extension/maza/notification/send',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/notification')) {
			$notification[] = array(
				'id' => 'mz-menu-notification-subscriber',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_subscriber'),
				'route' => 'extension/maza/notification',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/notification/channel')) {
			$notification[] = array(
				'id' => 'mz-menu-notification-channel',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_channel'),
				'route' => 'extension/maza/notification/channel',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/notification/push')) {
			$notification[] = array(
				'id' => 'mz-menu-notification-push',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_push'),
				'route' => 'extension/maza/notification/push',
			);
		}

		if ($notification) {
			$menus[] = array(
				'id' => 'mz-menu-notification',
				'icon' => 'fa-bell',
				'name' => $this->language->get('text_notification'),
				'children' => $notification,
			);
		}

		// Form
		$form = [];

		if ($this->user->hasPermission('access', 'extension/maza/form')) {
			$form[] = array(
				'id' => 'mz-menu-form',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_form'),
				'route' => 'extension/maza/form',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/form/field')) {
			$form[] = array(
				'id' => 'mz-menu-form-field',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_field'),
				'route' => 'extension/maza/form/field',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/form/record')) {
			$form[] = array(
				'id' => 'mz-menu-form-record',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_record'),
				'route' => 'extension/maza/form/record',
			);
		}

		if ($form) {
			$menus[] = array(
				'id' => 'mz-menu-form',
				'icon' => 'fa-wpforms',
				'name' => $this->language->get('text_form'),
				'children' => $form,
			);
		}

		// Gallery
		if ($this->user->hasPermission('access', 'extension/maza/gallery')) {
			$menus[] = array(
				'id' => 'mz-menu-gallery',
				'icon' => 'fa-image',
				'name' => $this->language->get('text_gallery'),
				'route' => 'extension/maza/gallery',
			);
		}

		// Extra
		$extra = [];

		if ($this->user->hasPermission('access', 'extension/maza/catalog/product')) {
			$extra[] = array(
				'id' => 'mz-menu-extra-product',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_product'),
				'route' => 'extension/maza/catalog/product',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/catalog/manufacturer')) {
			$extra[] = array(
				'id' => 'mz-menu-extra-manufacturer',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_manufacturer'),
				'route' => 'extension/maza/catalog/manufacturer',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/catalog/product_label')) {
			$extra[] = array(
				'id' => 'mz-menu-extra-product-label',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_product_label'),
				'route' => 'extension/maza/catalog/product_label',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/catalog/data')) {
			$extra[] = array(
				'id' => 'mz-menu-extra-data',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_data'),
				'route' => 'extension/maza/catalog/data',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/catalog/document')) {
			$extra[] = array(
				'id' => 'mz-menu-extra-document',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_document'),
				'route' => 'extension/maza/catalog/document',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/catalog/redirect')) {
			$extra[] = array(
				'id' => 'mz-menu-extra-redirect',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_redirect'),
				'route' => 'extension/maza/catalog/redirect',
			);
		}

		if ($extra) {
			$menus[] = array(
				'id' => 'mz-menu-extra',
				'icon' => 'fa-tags',
				'name' => $this->language->get('text_extra'),
				'children' => $extra
			);
		}

		// Blog
		$blog = [];

		if ($this->user->hasPermission('access', 'extension/maza/blog')) {
			$blog[] = array(
				'id' => 'mz-menu-blog-dashboard',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_dashboard'),
				'route' => 'extension/maza/blog',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/blog/category')) {
			$blog[] = array(
				'id' => 'mz-menu-blog-category',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_category'),
				'route' => 'extension/maza/blog/category',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/blog/article')) {
			$blog[] = array(
				'id' => 'mz-menu-blog-article',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_article'),
				'route' => 'extension/maza/blog/article',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/blog/author')) {
			$blog[] = array(
				'id' => 'mz-menu-blog-author',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_author'),
				'route' => 'extension/maza/blog/author',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/blog/comment')) {
			$blog[] = array(
				'id' => 'mz-menu-blog-comment',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_comment'),
				'route' => 'extension/maza/blog/comment',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/blog/report')) {
			$blog[] = array(
				'id' => 'mz-menu-blog-report',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_report'),
				'route' => 'extension/maza/blog/report',
			);
		}

		if ($this->user->hasPermission('access', 'extension/maza/blog/setting')) {
			$blog[] = array(
				'id' => 'mz-menu-blog-setting',
				'icon' => 'fa-angle-double-right',
				'name' => $this->language->get('text_setting'),
				'route' => 'extension/maza/blog/setting',
			);
		}

		if ($blog) {
			$menus[] = array(
				'id' => 'mz-menu-blog',
				'icon' => 'fa-newspaper-o',
				'name' => $this->language->get('text_blog'),
				'children' => $blog
			);
		}

		// Code
		if ($this->user->hasPermission('access', 'extension/maza/code')) {
			$menus[] = array(
				'id' => 'mz-menu-code',
				'icon' => 'fa-code',
				'name' => $this->language->get('text_code'),
				'route' => 'extension/maza/code',
			);
		}

		// System
		if ($this->user->hasPermission('access', 'extension/maza/system')) {
			$menus[] = array(
				'id' => 'mz-menu-system',
				'icon' => 'fa-cog',
				'name' => $this->language->get('text_system'),
				'route' => 'extension/maza/system',
			);
		}

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}

		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		$data['menus'] = [];

		$active_menus = $this->getActiveMenus($menus, $this->request->get['route']);
		
		if (!$active_menus) {
			$active_menus = $this->getActiveMenus($menus, dirname($this->request->get['route']));
		}

		foreach ($active_menus as $menu) {
			if (!empty($menu['children'])) {
				$route = $menu['children'][0]['route'];
			} else {
				$route = $menu['route'];
			}

			if (!isset($menu['href'])) {
				$menu['href'] 	= $this->url->link($route, 'user_token=' . $this->session->data['user_token'] . $url, true);
			}

			$data['menus'][] = $menu;
		}

		// First link should be Home link
		foreach ($menus as $menu) {
			if (empty($menu['children'])) {
				$data['home'] = $this->url->link($menu['route'], 'user_token=' . $this->session->data['user_token'] . $url, true);
				break;
			}
		}

		$url_data = $this->request->get;
		unset($url_data['_route_']);
		unset($url_data['route']);
		if ($url_data) {
			$redirect = $this->url->link($this->request->get['route'], urldecode(http_build_query($url_data, '', '&')), $this->request->server['HTTPS']);
		} else {
			$redirect = '';
		}

		$data['clear']             = $this->url->link('extension/maza/common/clear', 'user_token=' . $this->session->data['user_token'] . $url . '&redirect=' . urlencode($redirect), true);
		$data['mazatheme_version'] = $this->mz_theme_config->get('version');

		return $this->load->view('extension/maza/common/column_left', $data);
	}

	private function getActiveMenus(array $data, string $route): array {
		$items = [];

		if (in_array($route, array_column($data, 'route'))) {
			$items = $data;
		} else {
			foreach ($data as $menu) {
				if (empty($menu['children'])) {
					continue;
				}

				$items = $this->getActiveMenus($menu['children'], $route);

				if ($items) {
					$back = $data[0];
					$back['name'] = $this->language->get('button_back');
					$back['icon'] = 'fa-long-arrow-left';

					array_unshift($items, $back);

					break;
				}
			}
		}

		// Set active flag
		foreach ($items as &$item) {
			$item['active'] = isset($item['route']) && $item['route'] == $route;

			if ($item['active']) {
				break;
			}
		}

		return $items;
	}

	public function module($code) {
		$this->load->language('extension/maza/common/column_left');
		$this->load->model('extension/maza/opencart');

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}

		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		$data['home'] = $this->url->link('extension/maza/module', 'user_token=' . $this->session->data['user_token'] . $url, true);

		// Menu
		$data['menus'] = array();

		$data['menus'][] = array(
			'id' => 'mz-menu-add',
			'icon' => 'fa-plus-circle',
			'name' => $this->language->get('text_add_module'),
			'active' => !isset($this->request->get['module_id']) ? TRUE : FALSE,
			'href' => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . $url, true),
			'children' => array()
		);

		// module list
		$modules = $this->model_extension_maza_opencart->getModulesByCode($code);
		foreach ($modules as $module) {
			$data['menus'][] = array(
				'id' => 'mz-menu-edit-' . $module['module_id'],
				'icon' => 'fa-pencil',
				'name' => $module['name'],
				'active' => (isset($this->request->get['module_id']) && $this->request->get['module_id'] === $module['module_id']) ? TRUE : FALSE,
				'href' => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'] . $url, true),
				'children' => array()
			);
		}

		$url_data = $this->request->get;
		unset($url_data['_route_']);
		unset($url_data['route']);
		if ($url_data) {
			$redirect = $this->url->link($this->request->get['route'], urldecode(http_build_query($url_data, '', '&')), $this->request->server['HTTPS']);
		} else {
			$redirect = '';
		}

		$data['clear']             = $this->url->link('extension/maza/common/clear', 'user_token=' . $this->session->data['user_token'] . $url . '&redirect=' . urlencode($redirect), true);
		$data['mazatheme_version'] = $this->mz_theme_config->get('version');

		return $this->load->view('extension/maza/common/column_left', $data);
	}
}