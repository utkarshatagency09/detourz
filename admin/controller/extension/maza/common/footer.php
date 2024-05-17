<?php
class ControllerExtensionMazaCommonFooter extends Controller {
	public function index() {
                $this->load->language('extension/maza/common/footer');
                
                $this->load->model('extension/maza/asset');
                
                $data['styles'] = array();
                
                // Font icon manager css files
                // add font css
                $icon_packages = $this->model_extension_maza_asset->getFontIconPackages();
                foreach ($icon_packages as $package) {
                    $data['styles'][$package['file']] = array(
                        'href' => $this->config->get('mz_store_url') . $package['file']
                    );
                }
            
		return $this->load->view('extension/maza/common/footer', $data);
	}
}
