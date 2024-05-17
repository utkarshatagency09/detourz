<?php
class ControllerExtensionMazaStartupDocument extends Controller {
    public function index(): void {
		// Default OGP
        $this->mz_document->addOGP('og:type', 'website');
        $this->mz_document->addOGP('og:locale', str_replace('-', '_', $this->session->data['language']));

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();
        foreach ($languages as $language){
            if ($language['code'] !== $this->session->data['language']) {
                $this->mz_document->addOGP('og:locale:alternate', str_replace('-', '_', $language['code']));
            }
        }
    }
}
