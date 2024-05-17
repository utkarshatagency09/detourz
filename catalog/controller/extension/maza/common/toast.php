<?php
class ControllerExtensionMazaCommonToast extends Controller {
	public function login(string $message): string {
        $this->load->language('account/login');

        $data['title'] = $this->language->get('text_account');
        $data['message'] = $message;
        $data['icon'] = 'fas fa-exclamation-triangle';
        $data['color'] = 'danger';

        $data['actions'] = array(
            array(
                'color' => 'danger',
                'href' => $this->url->link('account/login'),
                'name' => $this->language->get('text_login'),
            ),
        );

        return $this->load->view('extension/maza/common/toast', $data);
    }
}