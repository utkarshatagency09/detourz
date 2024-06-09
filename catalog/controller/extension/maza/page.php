<?php
class ControllerExtensionMazaPage extends Controller {
	public function index() {
		$this->load->language('extension/maza/page');

		$this->load->model('extension/maza/page');
                
		if(isset($this->request->get['page_id'])){
			$page_info = $this->model_extension_maza_page->getPage($this->request->get['page_id']);
		} else {
			$page_info = array();
		}

		if ($page_info) {
			$this->document->setTitle($page_info['meta_title']);
			$this->document->setDescription($page_info['meta_description']);
			$this->document->setKeywords($page_info['meta_keyword']);
			$this->document->addLink($this->url->link('extension/maza/page', 'page_id=' . $page_info['page_id']), 'canonical');

			if ($this->config->get('maza_ogp')) {
				$this->mz_document->addOGP('og:type', 'website');
				$this->mz_document->addOGP('og:title', $page_info['meta_title']?:$page_info['name']);
				$this->mz_document->addOGP('og:url', $this->url->link('extension/maza/page', 'page_id=' . $page_info['page_id']));
			}

			$data['heading_title'] = $page_info['name'];

			$data['mz_component'] = $this->load->controller('extension/maza/layout_builder', ['group' => 'page_component', 'group_owner' => $this->request->get['page_id']]);      
			$data['mz_content'] = $this->mz_load->view($this->load->controller('extension/maza/layout_builder', ['group' => 'page', 'group_owner' => $this->request->get['page_id']]), $data, 'extension/maza/page');

			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('extension/maza/page', $data));
		} else {
			$this->document->setTitle($this->language->get('text_error'));
                        
			$data['heading_title'] = $this->language->get('error_heading');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}
