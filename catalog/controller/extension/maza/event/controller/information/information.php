<?php
class ControllerExtensionMazaEventControllerInformationInformation extends Controller {
	public function before(): void {
		if (isset($this->request->get['information_id'])) {
			// canonical
			$this->document->addLink($this->url->link('information/information', 'information_id=' .  $this->request->get['information_id']), 'canonical');
		}

		// Twig template of this page from layout builder, must call before header
		$mz_content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_content', $mz_content);

		$mz_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_component', $mz_component);
	}
}
