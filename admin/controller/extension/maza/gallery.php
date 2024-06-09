<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */

class ControllerExtensionMazaGallery extends Controller {
        private $error = array();
    
        public function index(): void {
		$this->load->language('extension/maza/gallery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/gallery');

		$this->getList();
	}
        
        public function add(): void {
		$this->load->language('extension/maza/gallery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/gallery');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_gallery->addGallery($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
                        if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['mz_theme_code'])){
                                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }
                        if(isset($this->request->get['mz_skin_id'])){
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
                        
			$this->response->redirect($this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
	public function edit(): void {
		$this->load->language('extension/maza/gallery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/gallery');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_gallery->editGallery($this->request->get['gallery_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['mz_theme_code'])){
                                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }
                        if(isset($this->request->get['mz_skin_id'])){
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

			$this->response->redirect($this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
	public function delete(): void {
		$this->load->language('extension/maza/gallery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/gallery');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $gallery_id) {
				$this->model_extension_maza_gallery->deleteGallery($gallery_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['mz_theme_code'])){
                                $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                        }
                        if(isset($this->request->get['mz_skin_id'])){
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

			$this->response->redirect($this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
	public function copy(): void {
		$this->load->language('extension/maza/gallery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/gallery');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $gallery_id) {
				$this->model_extension_maza_gallery->copyGallery($gallery_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
        protected function getList(): void {
                if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
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
                
                if(isset($this->request->get['mz_theme_code'])){
                    $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                    $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                
                // Header
                $header_data = array();
                $header_data['title'] = $this->language->get('text_list');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array();
                
                $header_data['menu_active'] = 'tab-mz-gallery';
                
                $header_data['buttons'][] = array( // Button add
                    'id' => 'button-add',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_add'),
                    'icon' => 'fa-plus',
                    'href' => $this->url->link('extension/maza/gallery/add', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => false,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array( // Button copy
                    'id' => 'button-copy',
                    'name' => '',
                    'tooltip' => $this->language->get('button_copy'),
                    'icon' => 'fa-copy',
                    'class' => 'btn-default',
                    'href' => FALSE,
                    'target' => FALSE,
                    'formaction' => $this->url->link('extension/maza/gallery/copy', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'form_target_id' => 'form-mz-gallery',
                );
                $header_data['buttons'][] = array( // Button delete
                    'id' => 'button-delete',
                    'name' => '',
                    'tooltip' => $this->language->get('button_delete'),
                    'icon' => 'fa-trash',
                    'class' => 'btn-danger',
                    'href' => FALSE,
                    'target' => FALSE,
                    'formaction' => $this->url->link('extension/maza/gallery/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'form_target_id' => 'form-mz-gallery',
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-gallery',
                    'target' => '_blank'
                );
                
                $header_data['form_target_id'] = 'form-mz-gallery';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Filter list
                $url = '';
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
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
                
                $data['add'] = $this->url->link('extension/maza/gallery/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/maza/gallery/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
                
                $this->load->model('tool/image');
                
                $data['galleries'] = array();

		$filter_data = array(
                        'filter_name' => $filter_name,
                        'filter_status' => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$gallery_total = $this->model_extension_maza_gallery->getTotalGalleries($filter_data);

		$results = $this->model_extension_maza_gallery->getGalleries($filter_data);

		foreach ($results as $result) {
			$data['galleries'][] = array(
				'gallery_id'    => $result['gallery_id'],
				'name'          => $result['name'],
                                'status'        => $result['status'],
                                'date_added'    => $result['date_added'],
				'edit'          => $this->url->link('extension/maza/gallery/edit', 'user_token=' . $this->session->data['user_token'] . '&gallery_id=' . $result['gallery_id'] . $url, true),
			);
		}

		if(isset($this->session->data['warning'])){
                        $data['warning'] = $this->session->data['warning'];
                        unset($this->session->data['warning']);
                }elseif(isset($this->error['warning'])){
                        $data['warning'] = $this->error['warning'];
                }
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
                
                // Sort order
		$url = '';
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
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

		$data['sort_name'] = $this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
                $data['sort_status'] = $this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
                $data['sort_date_added'] = $this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url, true);
                
                $data['sort'] = $sort;
		$data['order'] = $order;
                
                // Pagination
		$url = '';
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $url .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }
                if (isset($this->request->get['sort'])) {
                        $url .= '&sort=' . $this->request->get['sort'];
                }
                if (isset($this->request->get['order'])) {
                        $url .= '&order=' . $this->request->get['order'];
                }

		$pagination = new Pagination();
		$pagination->total = $gallery_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
                
                $data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;

		$data['results'] = sprintf($this->language->get('text_pagination'), ($gallery_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($gallery_total - $this->config->get('config_limit_admin'))) ? $gallery_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $gallery_total, ceil($gallery_total / $this->config->get('config_limit_admin')));
                
                $data['default_url'] = '&user_token=' . $this->session->data['user_token'];
                if(isset($this->request->get['mz_theme_code'])){
                        $data['default_url'] .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
                        $data['default_url'] .= '&mz_skin_id=' . $this->request->get['mz_skin_id'];
                }

                $data['user_token'] = $this->session->data['user_token'];
                
                // Columns
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		$this->response->setOutput($this->load->view('extension/maza/gallery_list', $data));
        }
        
        protected function getForm(): void {
                $this->load->model('tool/image');
                $this->load->model('localisation/language');
                
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
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['mz_theme_code'])){
                        $url .= '&mz_theme_code=' . $this->request->get['mz_theme_code'];
                }
                if(isset($this->request->get['mz_skin_id'])){
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
                $header_data = array();
                $header_data['title'] = !isset($this->request->get['gallery_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-mz-general', 'href' => false),
                    array('name' => $this->language->get('tab_image'), 'id' => 'tab-mz-image', 'href' => false),
                    array('name' => $this->language->get('tab_video'), 'id' => 'tab-mz-video', 'href' => false)
                );
                
                $header_data['menu_active'] = 'tab-mz-general';
                $header_data['buttons'][] = array(
                    'id' => 'button-save',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'href' => false,
                    'target' => false,
                    'form_target_id' => 'form-mz-gallery',
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-cancel',
                    'name' => '',
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('extension/maza/gallery', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-docs',
                    'name' => null,
                    'tooltip' => $this->language->get('button_docs'),
                    'icon' => 'fa-info',
                    'class' => 'btn-default',
                    'href' => 'https://docs.pocotheme.com/#page-gallery',
                    'target' => '_blank'
                );
                $header_data['form_target_id'] = 'form-mz-gallery';
                
                $data['mz_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                if (!isset($this->request->get['gallery_id'])) {
			$data['action'] = $this->url->link('extension/maza/gallery/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/gallery/edit', 'user_token=' . $this->session->data['user_token'] . '&gallery_id=' . $this->request->get['gallery_id'] . $url, true);
		}
                
                // Setting
                $setting = array();
                $setting['name'] = '';
                $setting['status'] = 1;
                $setting['image'] = array();
                $setting['video'] = array();
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } elseif(isset($this->request->get['gallery_id'])) {
                    $setting = array_merge($setting, $this->model_extension_maza_gallery->getGallery($this->request->get['gallery_id']));
                }

                // Data
                $data = array_merge($data, $setting);
                
                // Image
                $data['images'] = array();
                
                if(isset($setting['image'])){
                        foreach($setting['image'] as $image){
                                if (is_file(DIR_IMAGE . $image['image'])) {
                                        $thumb = $image['image'];
                                } else {
                                        $thumb = 'no_image.png';
                                }

                                $data['images'][] = array(
                                        'image'      => $image['image'],
                                        'thumb'      => $this->model_tool_image->resize($thumb, 80, 80),
                                        'title'      => $image['title'],
                                        'sort_order' => $image['sort_order']
                                );
                        }
                }

                // Video
                $data['videos'] = array();
                
                if(isset($setting['video'])){
                        foreach($setting['video'] as $video){
                                if (is_file(DIR_IMAGE . $video['image'])) {
                                        $thumb = $video['image'];
                                } else {
                                        $thumb = 'no_image.png';
                                }

                                $data['videos'][] = array(
                                        'image'      => $video['image'],
                                        'thumb'      => $this->model_tool_image->resize($thumb, 80, 80),
                                        'url'        => $video['url'],
                                        'sort_order' => $video['sort_order']
                                );
                        }
                }
                
                $data['languages'] = $this->model_localisation_language->getLanguages();
                $data['user_token'] = $this->session->data['user_token'];

                $data['placeholder']        = $this->model_tool_image->resize('no_image.png', 80, 80);
                
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
                if(isset($this->error['warning'])){
                        $data['warning'] = $this->error['warning'];
                }
                foreach($this->error as $key => $val){
                        $data['err_' . $key] = $val;
                }
                
                // Columns
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['mz_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['mz_column_left'] = $this->load->controller('extension/maza/common/column_left');
                
		$this->response->setOutput($this->load->view('extension/maza/gallery_form', $data));
	}
        
        protected function validateForm(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/gallery')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 100)) {
                        $this->error['name'] = $this->language->get('error_name');
                }
                
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
        
        protected function validateDelete(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/gallery')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        protected function validateCopy(): bool {
		if (!$this->user->hasPermission('modify', 'extension/maza/gallery')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete(): void {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/gallery');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_maza_gallery->getGalleries($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'gallery_id' => $result['gallery_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
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
}
