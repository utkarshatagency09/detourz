<?php
class ControllerExtensionMazaEventControllerCheckoutCheckout extends Controller {
	public function before(&$route): void {
		if ($this->mz_skin_config->get('catalog_checkout_status')) {
			$route = 'extension/maza/checkout';
		}
	}
}
