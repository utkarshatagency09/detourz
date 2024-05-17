<?php
class ControllerExtensionMazaEventControllerAccountWishlist extends Controller {
	public function addAfter(): void {
		$json = json_decode($this->response->getOutput(), true);

		if(isset($json['success'])){
			$data = $json;
			$data['login'] = $this->url->link('account/login');
			$data['register'] = $this->url->link('account/register');
			$data['wishlist'] = $this->url->link('account/wishlist');

			$image = $this->model_extension_maza_catalog_product->getImage($this->request->post['product_id']);

			$image_width = 50;
			if(is_file(DIR_IMAGE . $image)){
				$data['thumb'] = $this->model_tool_image->resize($image, $image_width, ($this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height') * $image_width) / $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'));
			} else {
				$data['thumb'] = $this->model_tool_image->resize('mz_no_image.png', $image_width, ($this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height') * $image_width) / $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'));
			}

			$this->load->language('extension/module/account');

			$data['logged'] = $this->customer->isLogged();

			$json['toast'] = $this->load->view('extension/maza/event/account/wishlist_add', $data);

			$this->response->setOutput(json_encode($json));
		}
	}
}
