<?php
class ControllerExtensionMazaAccountWishList extends Controller {
	public function remove(): void {
		$json = array();
                
		$this->load->language('account/wishlist');
                
		if ($this->customer->isLogged() && isset($this->request->post['product_id'])) {
			$this->load->model('account/wishlist');
                        
			// Remove Wishlist
			$this->model_account_wishlist->deleteWishlist($this->request->post['product_id']);
                        
			$json['total'] = $this->model_account_wishlist->getTotalWishlist();
			
			$json['success'] = $this->language->get('text_remove');
		}
                
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
