<?php
class ControllerExtensionMazaEventControllerCommonFooter extends Controller {
	public function before(): void {
		// header content
        $this->mz_cache->setVar('top_header', $this->load->controller('extension/maza/layout_builder', ['group' => 'top_header', 'group_owner' => $this->mz_skin_config->get('skin_header_id')]));
        $this->mz_cache->setVar('main_header', $this->load->controller('extension/maza/layout_builder', ['group' => 'main_header', 'group_owner' => $this->mz_skin_config->get('skin_header_id')]));
        $this->mz_cache->setVar('main_navigation', $this->load->controller('extension/maza/layout_builder', ['group' => 'main_navigation', 'group_owner' => $this->mz_skin_config->get('skin_header_id')]));
        $this->mz_cache->setVar('header_component', $this->load->controller('extension/maza/layout_builder', ['group' => 'header_component', 'group_owner' => $this->mz_skin_config->get('skin_header_id')]));

        // Page components for default layout buider
        // Popup | Sticky | Drawer
        if($this->config->get('mz_layout_type') === 'default' && !$this->mz_cache->isVar('page_component')){
			$this->mz_cache->setVar('page_component', $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]));
        }
	}
}
