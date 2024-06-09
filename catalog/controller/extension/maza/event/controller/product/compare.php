<?php
class ControllerExtensionMazaEventControllerProductCompare extends Controller {
	public function addAfter(): void {
		$json = json_decode($this->response->getOutput(), true);

		if(isset($json['success'])){
			$data = $json;
			$data['compare'] = $this->url->link('product/compare');

			$image = $this->model_extension_maza_catalog_product->getImage($this->request->post['product_id']);

			$image_width = 50;
			if(is_file(DIR_IMAGE . $image)){
				$data['thumb'] = $this->model_tool_image->resize($image, $image_width, ($this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height') * $image_width) / $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'));
			} else {
				$data['thumb'] = $this->model_tool_image->resize('mz_no_image.png', $image_width, ($this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height') * $image_width) / $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'));
			}

			$json['toast'] = $this->load->view('extension/maza/event/product/compare_add', $data);

			$this->response->setOutput(json_encode($json));
		}
	}
}
