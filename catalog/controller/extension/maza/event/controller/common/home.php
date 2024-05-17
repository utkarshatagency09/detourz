<?php
class ControllerExtensionMazaEventControllerCommonHome extends Controller {
	public function before(): void {
		if ($this->config->get('maza_ogp')) {
			$this->mz_document->addOGP('og:type', 'website');
			$this->mz_document->addOGP('og:title', $this->config->get('config_meta_title'));
			$this->mz_document->addOGP('og:description', $this->config->get('config_meta_description'));
			$this->mz_document->addOGP('og:image', maza\getImageURL($this->config->get('config_logo')));
			$this->mz_document->addOGP('og:url', $this->url->link('common/home'));
		}

		// Twig template of this page from layout builder, must call before header
		$mz_content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_content', $mz_content);

		$mz_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_component', $mz_component);
	}
}
