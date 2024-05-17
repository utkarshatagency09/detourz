<?php
class ControllerExtensionMazaStartupPageCache extends Controller {
    private $id = null;

	public function index(): void {
        $this->registry->set('mz_pagecache', $this);

        // Validate for cache
        $status = empty($this->session->data['user_id']) && $this->config->get('maza_cache_page') && in_array($this->mz_document->getRoute(), $this->config->get('mz_cache_route')) && !$this->customer->isLogged() && !$this->cart->hasProducts();

        // Cache id/key
        $this->id = 'page.' . $this->session->data['language'] . '.' . $this->session->data['currency'] . '.' . md5(($this->request->server['HTTPS']?'https':'http') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'] . (int)$this->mz_browser->isSupportedWebp());
        
        // Response from cache
        if($status){
            $output = $this->mz_cache->get($this->id);
        } else {
            $output = false;
        }
        if($output){
            $this->response->setOutput($output);
            $this->response->output();
            exit();
        }

        // Register capture event
        if($status){
            $this->event->register('controller/' . $this->mz_document->getRoute() . '/after', new Action('extension/maza/startup/pagecache/capture'), 99);
        }
	}

    /**
     * Capture current page response
     */
    public function capture(): void {
        $this->mz_cache->set($this->mz_pagecache->id, $this->response->getOutput());
    }
}
