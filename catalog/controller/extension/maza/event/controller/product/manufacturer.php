<?php
class ControllerExtensionMazaEventControllerProductManufacturer extends Controller {
	public function infoBefore(): void {
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = (int)$this->request->get['manufacturer_id'];
		} else {
			$manufacturer_id = 0;
		}

		$this->load->model('catalog/manufacturer');

		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);

		if ($this->config->get('maza_ogp') && $manufacturer_info) {
			$this->mz_document->addOGP('og:type', 'website');
			$this->mz_document->addOGP('og:title', $manufacturer_info['name']);
			$this->mz_document->addOGP('og:image', maza\getImageURL($manufacturer_info['image']));
			if ($page == 1) {
				$this->mz_document->addOGP('og:url', $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id']));
			} else {
				$this->mz_document->addOGP('og:url', $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . '&page=' . $page));
			}
		}

		$this->load->model('extension/maza/catalog/manufacturer');

		$manufacturer_description = $this->model_extension_maza_catalog_manufacturer->getManufacturerDescription($manufacturer_id);

		if($manufacturer_description){
			$this->document->setTitle($manufacturer_description['meta_title']);
			$this->document->setDescription($manufacturer_description['meta_description']);
			$this->document->setKeywords($manufacturer_description['meta_keyword']);
		}

		// Load Catalog data 
		$this->load->model('extension/maza/catalog/data');

		$catalog_data = $this->model_extension_maza_catalog_data->getManufacturerDatas($manufacturer_id);

		$this->load->controller('extension/maza/hooks/data', $catalog_data);

		// Twig template of this page from layout builder, must call before header
		$mz_content = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_content', $mz_content);

		$mz_component = $this->load->controller('extension/maza/layout_builder', ['group' => 'layout_component', 'group_owner' => $this->config->get('mz_layout_id')]);
		$this->mz_cache->setVar('mz_component', $mz_component);
	}
}
