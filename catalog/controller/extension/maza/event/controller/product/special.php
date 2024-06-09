<?php
class ControllerExtensionMazaEventControllerProductSpecial extends Controller {
	public function before(): void {
		// Twig template of this page from layout builder, must call before header
		$mz_content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_content', $mz_content);

		$mz_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_component', $mz_component);
	}
}
