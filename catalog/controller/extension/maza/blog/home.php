<?php
class ControllerExtensionMazaBlogHome extends Controller {
	public function index() {
		$this->document->addLink($this->url->link('extension/maza/blog/home'), 'canonical');

		if ($this->config->get('maza_ogp')) {
			$this->mz_document->addOGP('og:type', 'website');
			$this->mz_document->addOGP('og:url', $this->url->link('extension/maza/blog/home'));
			$this->mz_document->addOGP('og:image', maza\getImageURL($this->config->get('config_logo')));
		}
		
		// Content
		$data['mz_content'] = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]);

		$page_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
		$data['mz_component'] = $this->mz_load->view($page_component, $data);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
                
		$this->response->setOutput($this->load->view('extension/maza/blog/home', $data));
	}
}
