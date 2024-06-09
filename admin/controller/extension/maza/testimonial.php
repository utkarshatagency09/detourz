<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaTestimonial extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/maza/testimonial');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/testimonial');

		$this->getList();
	}

	/**
	 * Add testimonial
	 */
	public function add() {
		$this->load->language('extension/maza/testimonial');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/testimonial');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_testimonial->addTestimonial($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_rating'])) {
				$url .= '&filter_rating=' . $this->request->get['filter_rating'];
			}
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	/**
	 * Edit testimonial
	 */
	public function edit() {
		$this->load->language('extension/maza/testimonial');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/testimonial');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_testimonial->editTestimonial($this->request->get['testimonial_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_rating'])) {
				$url .= '&filter_rating=' . $this->request->get['filter_rating'];
			}
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	/**
	 * Delete individual testimonial
	 */
	public function delete() {
		$this->load->language('extension/maza/testimonial');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/testimonial');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $testimonial_id) {
				$this->model_extension_maza_testimonial->deleteTestimonial($testimonial_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_rating'])) {
				$url .= '&filter_rating=' . $this->request->get['filter_rating'];
			}
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}
			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}


	/**
	 * Get list of testimonial
	 */
	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}
		if (isset($this->request->get['filter_rating'])) {
			$filter_rating = $this->request->get['filter_rating'];
		} else {
			$filter_rating = null;
		}
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		// Header
		$header_data                 = array();
		$header_data['title']        = $this->language->get('text_list');
		$header_data['theme_select'] = $header_data['skin_select'] = false;
		// $header_data['menu']   = array();
		// $header_data['menu'][] = array('name' => $this->language->get('tab_testimonial'), 'id' => 'tab-mz-testimonial', 'href' => false);
		// $header_data['menu'][] = array('name' => $this->language->get('tab_setting'), 'id' => 'tab-mz-setting', 'href' => $this->url->link('extension/maza/testimonial/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));

		// $header_data['menu_active'] = 'tab-mz-testimonial';

		$header_data['buttons'][]      = array(
			'id' => 'button-add',
			'name' => '',
			'class' => 'btn-primary',
			'tooltip' => $this->language->get('button_add'),
			'icon' => 'fa-plus',
			'href' => $this->url->link('extension/maza/testimonial/add', 'user_token=' . $this->session->data['user_token'], true),
			'target' => false,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-delete',
			'name' => '',
			'tooltip' => $this->language->get('button_delete'),
			'icon' => 'fa-trash',
			'class' => 'btn-danger',
			'href' => FALSE,
			'target' => FALSE,
			'confirm' => $this->language->get('text_confirm'),
			'form_target_id' => 'form-mz-testimonial',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-testimonial',
			'target' => '_blank'
		);
		$header_data['form_target_id'] = 'form-mz-testimonial';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Testimonial list
		$url = '';
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['add']    = $this->url->link('extension/maza/testimonial/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/maza/testimonial/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$this->load->model('tool/image');

		$data['testimonials'] = array();

		$filter_data = array(
			'filter_name' => $filter_name,
			'filter_date_added' => $filter_date_added,
			'filter_rating' => $filter_rating,
			'filter_status' => $filter_status,
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$testimonial_total = $this->model_extension_maza_testimonial->getTotalTestimonials($filter_data);

		$results = $this->model_extension_maza_testimonial->getTestimonials($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['testimonials'][] = array(
				'testimonial_id' => $result['testimonial_id'],
				'name' => $result['name'],
				'image' => $image,
				'rating' => $result['rating'],
				'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'sort_order' => $result['sort_order'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit' => $this->url->link('extension/maza/testimonial/edit', 'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $result['testimonial_id'] . $url, true),
			);
		}

		if (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array) $this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		// Sort order
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name']       = $this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_rating']     = $this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=rating' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);
		$data['sort_status']     = $this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);

		$data['sort']  = $sort;
		$data['order'] = $order;

		// Pagination
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination        = new Pagination();
		$pagination->total = $testimonial_total;
		$pagination->page  = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url   = $this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['filter_name']       = $filter_name;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_rating']     = $filter_rating;
		$data['filter_status']     = $filter_status;

		$data['results'] = sprintf($this->language->get('text_pagination'), ($testimonial_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($testimonial_total - $this->config->get('config_limit_admin'))) ? $testimonial_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $testimonial_total, ceil($testimonial_total / $this->config->get('config_limit_admin')));

		$data['default_url'] = '&user_token=' . $this->session->data['user_token'];
		if (isset($this->request->get['mz_theme_code'])) {
			$data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		$data['user_token'] = $this->session->data['user_token'];

		// Columns
		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/testimonial/list', $data));
	}

	/**
	 * Form to add or edit Testimonial
	 */
	protected function getForm() {
		$this->load->model('localisation/language');
		$this->load->model('setting/store');
		$this->load->model('tool/image');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . urlencode(html_entity_decode($this->request->get['filter_date_added'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_rating'])) {
			$url .= '&filter_rating=' . $this->request->get['filter_rating'];
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data = array();

		// Header
		$header_data                 = array();
		$header_data['title']        = !isset($this->request->get['testimonial_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$header_data['theme_select'] = $header_data['skin_select'] = false;
		$header_data['menu'] = array(
			array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
			array('name' => $this->language->get('tab_data'), 'id' => 'tab-mz-data', 'href' => false),
		);

		$header_data['menu_active']    = 'tab-mz-general';
		$header_data['buttons'][]      = array(
			'id' => 'button-save',
			'name' => '',
			'class' => 'btn-primary',
			'tooltip' => $this->language->get('button_save'),
			'icon' => 'fa-save',
			'href' => false,
			'target' => false,
			'form_target_id' => 'form-mz-testimonial',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-cancel',
			'name' => '',
			'tooltip' => $this->language->get('button_cancel'),
			'icon' => 'fa-reply',
			'class' => 'btn-default',
			'href' => $this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-testimonial-add',
			'target' => '_blank'
		);
		$header_data['form_target_id'] = 'form-mz-testimonial';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		// Setting
		$setting                            = array();
		$setting['testimonial_description'] = array();
		$setting['testimonial_store']       = array(0);
		$setting['image']                   = '';
		$setting['email']                   = '';
		$setting['rating']                  = 5;
		$setting['date_added']              = date('Y-m-d');
		$setting['sort_order']              = 0;
		$setting['status']                  = true;

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		} elseif (isset($this->request->get['testimonial_id'])) {
			$setting                            = array_merge($setting, $this->model_extension_maza_testimonial->getTestimonial($this->request->get['testimonial_id']));
			$setting['testimonial_description'] = $this->model_extension_maza_testimonial->getTestimonialDescriptions($this->request->get['testimonial_id']);
			$setting['testimonial_store']       = $this->model_extension_maza_testimonial->getTestimonialStores($this->request->get['testimonial_id']);
		}

		// Data
		$data = array_merge($data, $setting);

		if (!isset($this->request->get['testimonial_id'])) {
			$data['action'] = $this->url->link('extension/maza/testimonial/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/testimonial/edit', 'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $this->request->get['testimonial_id'] . $url, true);
		}

		// Stores
		$data['stores']   = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name' => $this->language->get('text_default')
		);
		$stores           = $this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name' => $store['name']
			);
		}

		// Image
		if (is_file(DIR_IMAGE . $setting['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($setting['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);


		// General
		$data['languages']  = $this->model_localisation_language->getLanguages();
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		if (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}

		foreach ($this->error as $key => $val) {
			$data['err_' . $key] = $val;
		}

		// Columns
		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/testimonial/form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/maza/testimonial')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['testimonial_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 50)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if (utf8_strlen($value['extra']) > 50) {
				$this->error['extra'][$language_id] = $this->language->get('error_extra');
			}

			if (utf8_strlen($value['description']) < 10) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}
		}

		if (($this->request->post['rating'] < 0) || ($this->request->post['rating'] > 5)) {
			$this->error['rating'] = $this->language->get('error_rating');
		}

		if (!empty($this->request->post['email']) && !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (empty($this->request->post['date_added'])) {
			$this->request->post['date_added'] = date('Y-m-d');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/maza/testimonial')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function setting() {
		$this->load->language('extension/maza/testimonial');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}
		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		// Header
		$header_data         = array();
		$header_data['menu'] = array();

		// $header_data['menu'][] = array('name' => $this->language->get('tab_testimonial'), 'id' => 'tab-mz-testimonial', 'href' => $this->url->link('extension/maza/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
		$header_data['menu'][] = array('name' => $this->language->get('tab_setting'), 'id' => 'tab-mz-setting', 'href' => false);
		$header_data['menu'][] = array('name' => $this->language->get('tab_form'), 'id' => 'tab-mz-form', 'href' => false);
		$header_data['menu'][] = array('name' => $this->language->get('tab_page'), 'id' => 'tab-mz-page', 'href' => false);
		$header_data['menu'][] = array('name' => $this->language->get('tab_listing'), 'id' => 'tab-mz-listing', 'href' => false);
		// $header_data['menu'][] = array('name' => $this->language->get('tab_layout'), 'id' => 'tab-mz-layout', 'href' => false);


		$header_data['menu_active']    = 'tab-mz-setting';
		$header_data['buttons'][]      = array(
			'id' => 'button-import',
			'name' => false,
			'tooltip' => $this->language->get('button_import'),
			'icon' => 'fa-upload',
			'class' => 'btn-info',
			'href' => false,
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-export',
			'name' => false,
			'tooltip' => $this->language->get('button_export'),
			'icon' => 'fa-download',
			'class' => 'btn-info',
			'href' => $this->url->link('extension/maza/testimonial/export', 'user_token=' . $this->session->data['user_token'] . $url, true),
			'target' => FALSE,
			'form_target_id' => false,
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-save',
			'name' => false,
			'tooltip' => $this->language->get('button_save'),
			'icon' => 'fa-save',
			'class' => 'btn-primary',
			'href' => FALSE,
			'target' => FALSE,
			'form_target_id' => 'form-mz-setting',
		);
		$header_data['buttons'][]      = array(
			'id' => 'button-docs',
			'name' => null,
			'tooltip' => $this->language->get('button_docs'),
			'icon' => 'fa-info',
			'class' => 'btn-default',
			'href' => 'https://docs.pocotheme.com/#page-testimonial-setting',
			'target' => '_blank'
		);
		$header_data['form_target_id'] = 'form-mz-setting';

		$data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);

		$this->load->model('extension/maza/asset');

		// Submit form
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateSetting()) {
			$this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'testimonial', $this->request->post);
			// clear asset files for new settings
			$this->mz_document->clear();

			$data['success'] = $this->language->get('text_success');
		}

		if (isset($this->error['warning'])) {
			$data['warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		foreach ($this->error as $key => $val) {
			$data['err_' . $key] = $val;
		}

		$data['import'] = $this->url->link('extension/maza/testimonial/import', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action'] = $this->url->link('extension/maza/testimonial/setting', 'user_token=' . $this->session->data['user_token'] . $url, true);

		// Setting
		$setting = array();

		// Testimonial page
		// $setting['testimonial_meta_title']        = '';
		// $setting['testimonial_meta_description']  = '';
		// $setting['testimonial_meta_keyword']      = '';

		// Testimonial submit form 
		$setting['testimonial_submit_status']           = 1;
		$setting['testimonial_submit_require_approval'] = 1;

		// Testimonial thank you mail
		$setting['testimonial_mail_status']   = 0;
		$setting['testimonial_mail_template'] = array();

		// Testimonial form
		$setting['testimonial_form_rating']  = 1;
		$setting['testimonial_form_email']   = 1;
		$setting['testimonial_form_extra']   = 1;
		$setting['testimonial_form_image']   = 1;
		$setting['testimonial_form_captcha'] = 1;

		// Testimonial listing
		$setting['testimonial_list_rating']    = 1;
		$setting['testimonial_list_timestamp'] = 1;
		$setting['testimonial_list_extra']     = 1;
		$setting['testimonial_list_image']     = 1;

		// Testimonial listing
		$setting['testimonial_limit']             = 15;
		$setting['testimonial_thumb_width']       = 50;
		$setting['testimonial_thumb_height']      = 50;
		$setting['testimonial_quote_icon_image']  = array();
		$setting['testimonial_quote_icon_svg']    = array();
		$setting['testimonial_quote_icon_font']   = array();
		$setting['testimonial_quote_icon_size']   = null;
		$setting['testimonial_quote_icon_width']  = null;
		$setting['testimonial_quote_icon_height'] = null;

		// Testimonial grid per row
		$setting['testimonial_column_xs'] = 1;
		$setting['testimonial_column_sm'] = 1;
		$setting['testimonial_column_md'] = 2;
		$setting['testimonial_column_lg'] = 3;
		$setting['testimonial_column_xl'] = 3;

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$setting = array_merge($setting, $this->request->post);
		} else {
			$setting = array_merge($setting, $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'testimonial'));
		}


		// Data
		$data = array_merge($data, $setting);

		$this->load->model('tool/image');

		// Form
		$data['list_field_status'] = array(
			array('id' => 1, 'text' => $this->language->get('text_enabled')),
			array('id' => 0, 'text' => $this->language->get('text_disabled')),
			array('id' => -1, 'text' => $this->language->get('text_optional')),
		);

		// Image
		$data['placeholder_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['placeholder_svg']   = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
		$data['placeholder_font']  = 'fa fa-font';

		// quote icon image
		$data['thumb_testimonial_quote_icon_image'] = array();

		foreach ($data['testimonial_quote_icon_image'] as $language_id => $image) {
			if ($image) {
				$data['thumb_testimonial_quote_icon_image'][$language_id] = $this->model_tool_image->resize($image, 100, 100);
			} else {
				$data['thumb_testimonial_quote_icon_image'][$language_id] = $this->model_tool_image->resize('no_image.png', 100, 100);
			}
		}

		// quote icon svg
		$data['thumb_testimonial_quote_icon_svg'] = array();

		foreach ($data['testimonial_quote_icon_svg'] as $language_id => $image_svg) {
			if ($image_svg) {
				$data['thumb_testimonial_quote_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/' . $image_svg;
			} else {
				$data['thumb_testimonial_quote_icon_svg'][$language_id] = $this->config->get('mz_store_url') . 'image/catalog/maza/svg/no_image.svg';
			}
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header']         = $this->load->controller('extension/maza/common/header/main');
		$data['column_left']    = $this->load->controller('common/column_left');
		$data['mz_footer']      = $this->load->controller('extension/maza/common/footer');
		$data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');

		$this->response->setOutput($this->load->view('extension/maza/testimonial/setting', $data));
	}

	protected function validateSetting() {
		if (!$this->user->hasPermission('modify', 'extension/maza/testimonial')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		// Page
		// if(empty($this->request->post['testimonial_meta_title'])){
		//     $this->error['meta_title'] = $this->language->get('error_meta_title');
		// }

		// testimonial listing
		if ($this->request->post['testimonial_limit'] <= 0) {
			$this->error['testimonial_limit'] = $this->language->get('error_positive_number');
		}

		if ($this->request->post['testimonial_thumb_width'] <= 0 || $this->request->post['testimonial_thumb_height'] <= 0) {
			$this->error['testimonial_thumb_size'] = $this->language->get('error_positive_number');
		}

		if (!isset($this->error['warning']) && $this->error) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/testimonial');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort' => 'name',
				'order' => 'ASC',
				'start' => 0,
				'limit' => 5
			);

			$results = $this->model_extension_maza_testimonial->getTestimonials($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'testimonial_id' => $result['testimonial_id'],
					'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	/**
	 * Export setting
	 */
	public function export() {
		$this->load->model('extension/maza/skin');
		$this->load->language('extension/maza/testimonial');

		$setting = $this->model_extension_maza_skin->getSetting($this->mz_skin_config->get('skin_id'), 'testimonial');

		if ($setting) {
			header('Content-Type: application/json; charset=utf-8');
			header('Content-disposition: attachment; filename="maza.setting.testimonial.' . $this->mz_skin_config->get('skin_code') . '.json"');

			echo json_encode(['type' => 'maza', 'code' => 'testimonial', 'setting' => $setting]);
		} else {
			$this->session->data['warning'] = $this->language->get('error_no_setting');

			$url = '';

			if (isset($this->request->get['mz_theme_code'])) {
				$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
			}

			if (isset($this->request->get['mz_skin_id'])) {
				$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
			}

			$this->response->redirect($this->url->link('extension/maza/testimonial/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
	}

	/**
	 * Import setting
	 */
	public function import() {
		$this->load->language('extension/maza/testimonial');

		$warning = '';

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/maza/testimonial')) {
			$warning = $this->language->get('error_permission');
		} else {
			if (isset($this->request->files['file']['name'])) {
				if (substr($this->request->files['file']['name'], -4) != 'json') {
					$warning = $this->language->get('error_filetype');
				}

				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$warning = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$warning = $this->language->get('error_upload');
			}
		}

		if (!$warning) {
			$file = $this->request->files['file']['tmp_name'];

			if (is_file($file)) {
				$data = json_decode(file_get_contents($file), true);

				if ($data && $data['type'] == 'maza' && $data['code'] == 'testimonial') {
					$this->load->model('extension/maza/skin');

					$this->model_extension_maza_skin->editSetting($this->mz_skin_config->get('skin_id'), 'testimonial', $data['setting']);

					$this->session->data['success'] = $this->language->get('text_success_import');
				} else {
					$warning = $this->language->get('error_import_file');
				}
			} else {
				$warning = $this->language->get('error_file');
			}
		}

		if ($warning) {
			$this->session->data['warning'] = $warning;
		}

		$url = '';

		if (isset($this->request->get['mz_theme_code'])) {
			$url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
		}

		if (isset($this->request->get['mz_skin_id'])) {
			$url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
		}

		$this->response->redirect($this->url->link('extension/maza/testimonial/setting', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}
}