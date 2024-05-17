<?php
class ControllerExtensionMazaStartupRedirect extends Controller {
	public function index(): void {
        if (isset($this->request->get['_route_'])) {
            $path = $this->request->get['_route_'];

            $url_data = $_GET;

            unset($url_data['_route_']);
            unset($url_data['route']);

            $url = '';

            if ($url_data) {
                $url = '?' . http_build_query($url_data, '', '&');
            }
            
            $query = $this->db->query("SELECT `to` FROM `" . DB_PREFIX . "mz_redirect_url` WHERE store_id = '" . $this->config->get('config_store_id') . "' AND ('" . $this->db->escape($path) . "' LIKE `from` OR `from` = '" . $this->db->escape($path . $url) . "' OR `from` = '" . $this->db->escape(HTTPS_SERVER . $path . $url) . "') LIMIT 1");
            
            if ($query->row) {
                if (strpos($query->row['to'], 'http') === 0) {
                    $this->response->redirect($query->row['to']);
                } elseif(strpos($query->row['to'], '?') === false) {
                    $this->response->redirect(HTTPS_SERVER . $query->row['to'] . $url);
                } else {
                    $this->response->redirect(HTTPS_SERVER . $query->row['to']);
                }
            }
        }
	}
}
