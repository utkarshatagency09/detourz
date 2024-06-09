<?php
class ControllerExtensionModuleMzNewsletter extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/mz_newsletter');

		$this->document->setTitle($this->language->get('heading_title'));
                
		$this->document->addStyle('view/stylesheet/maza/mz_stylesheet.css');
		$this->document->addScript('view/javascript/maza/mz_common.js');

		$this->load->model('setting/setting');

		$url = '';
                
		if(isset($this->request->get['mz_theme_code'])){
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		
		if(isset($this->request->get['mz_skin_id'])){
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_mz_newsletter', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/maza/module', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/maza/module', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/mz_newsletter', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/mz_newsletter', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/maza/module', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->post['module_mz_newsletter_status'])) {
			$data['module_mz_newsletter_status'] = $this->request->post['module_mz_newsletter_status'];
		} else {
			$data['module_mz_newsletter_status'] = $this->config->get('module_mz_newsletter_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/mz_newsletter', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/mz_newsletter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
	public function install(){
		// Create Database
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mz_newsletter` (
			`subscriber_id` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
			`email_id` VARCHAR(180) NOT NULL UNIQUE KEY,
			`date_added` DATETIME NOT NULL,
			`is_confirmed` TINYINT(1) NOT NULL DEFAULT 0,
			`status` TINYINT(1) NOT NULL DEFAULT 0,
			`token` CHAR(64) NOT NULL UNIQUE KEY
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
		
		// Event
		$this->load->model('extension/maza/opencart');
		
		$this->addEvents();
	}
        
	public function uninstall(){
		// Delete Database
		$this->db->query("DROP TABLE `" . DB_PREFIX . "mz_newsletter`");
		
		// Event
		$this->load->model('extension/maza/opencart');
		
		$this->deleteEvents();
	}
	
	private function addEvents() {
		$this->deleteEvents();
		
		//** Catalog ***********************************
		$this->model_extension_maza_opencart->addEvent('mz_catalog_newsletter_after_add_customer', 'catalog/model/account/customer/addCustomer/after', 'extension/maza/event/extension/module/mz_newsletter/addCustomerAfter');
		$this->model_extension_maza_opencart->addEvent('mz_catalog_newsletter_after_edit_newsletter', 'catalog/model/account/customer/editNewsletter/after', 'extension/maza/event/extension/module/mz_newsletter/editNewsletter');
	}
	
	private function deleteEvents() {
		
		// ** Catalog ****************************************************************
		$this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_newsletter_after_add_customer');
		$this->model_extension_maza_opencart->deleteEventByCode('mz_catalog_newsletter_after_edit_newsletter');
	}
}