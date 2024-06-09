<?php
class ControllerExtensionMazaAccountNotificationManufacturer extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('extension/maza/account/notification/manufacturer', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('extension/maza/account/notification/manufacturer');

		$this->load->model('extension/maza/notification');
		$this->load->model('extension/maza/account/notification');
		$this->load->model('tool/image');

		if (isset($this->request->get['remove'])) {
			$this->model_extension_maza_notification->deleteManufacturerSubscribe($this->request->get['remove']);

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->response->redirect($this->url->link('extension/maza/account/notification/manufacturer'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/maza/account/notification/manufacturer')
		);

		$data['channel'] = $this->url->link('extension/maza/account/notification/channel');
		$data['product'] = $this->url->link('extension/maza/account/notification/product');
		$data['manufacturer'] = $this->url->link('extension/maza/account/notification/manufacturer');

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['manufacturers'] = array();

		$results = $this->model_extension_maza_account_notification->getManufacturers();

		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_wishlist_height'));
			} else {
				$image = false;
			}

			$data['manufacturers'][] = array(
				'manufacturer_id' => $result['manufacturer_id'],
				'thumb'      => $image,
				'name'       => $result['name'],
				'href'       => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
				'remove'     => $this->url->link('extension/maza/account/notification/manufacturer', 'remove=' . $result['manufacturer_id'])
			);
		}

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/maza/account/notification/manufacturer', $data));
	}
}
