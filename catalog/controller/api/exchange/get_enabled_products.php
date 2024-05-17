<?php
/**
 * Class ControllerApiExchangeGetEnabledProducts
 *
 *  Params incoming from POST:
 * `username` - API from admin panel
 * `key` - API from admin panel
 *
 * Returned array: enabled products with all price types
 */

class ControllerApiExchangeGetEnabledProducts extends Controller {

    /**
     * Getting enabled products with all price types
     */
    public function index(){
        $this->load->language('api/exchange/web_exchange');
        $this->load->model('account/api');

        unset($this->session->data['web_exchange']);

        $json = array();

        // Login with API Key
        if(isset($this->request->post['username'])) {
            $api_info = $this->model_account_api->login($this->request->post['username'], $this->request->post['key']);
        } else {
            $api_info = $this->model_account_api->login('Default', $this->request->post['key']);
        }
        $json['success'] = sprintf($this->language->get('error'));

        if ($api_info) {
            // Check if IP is allowed
            $ip_data = array();
            $results = $this->model_account_api->getApiIps($api_info['api_id']);

            foreach ($results as $result) {
                $ip_data[] = trim($result['ip']);
            }

            if (!in_array($this->request->server['REMOTE_ADDR'], $ip_data)) {
                $json['error']['ip'] = sprintf($this->language->get('error_ip'), $this->request->server['REMOTE_ADDR']);
            }else{
                $this->load->model('api/exchange/products');
                $json['success'] = sprintf($this->language->get('error'));

                $tableRow = array();

                $result = $this->model_api_exchange_products->getEnabledProducts();
                if ($result->num_rows > 0) {
                    foreach($result->rows as $row) {
                        array_push($tableRow, array(
                            'product_id' => $row['product_id'],
                            'price_type' => $row['price_type'],
                            'price_value' => $row['price_value'],
                            'special_value' => $row['special_value'],
                            'special_date_end' => $row['special_date_end'],
                        ));
                    }
                }
                $json['products_list'] = $tableRow;
                $json['success'] = sprintf($this->language->get('success'));
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}


