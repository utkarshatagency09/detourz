<?php
class ControllerExtensionMazaEventLocalisationCurrency extends Controller {
        public function refresh(string $route, array $param): bool {
                if ($param && $param[0]) {
                        $force = true;
                } else {
                        $force = false;
                }

                $currency_code = array();
                
                if ($force) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code != '" . $this->db->escape($this->config->get('config_currency')) . "'");
		} else {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code != '" . $this->db->escape($this->config->get('config_currency')) . "' AND date_modified < '" .  $this->db->escape(date('Y-m-d H:i:s', strtotime('-1 day'))) . "'");
		}

                foreach ($query->rows as $result) {
                        $currency_code[] = strtoupper($result['code']);
		}

                if ($currency_code && $this->config->get('maza_api_exchangerate_key')) {
                        $default = strtoupper($this->config->get('config_currency'));

                        $curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, 'https://api.apilayer.com/exchangerates_data/latest?base=' . $default . '&symbols=' . implode(',', $currency_code));
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                "Content-Type: text/plain",
                                "apikey: " . $this->config->get('maza_api_exchangerate_key')
                        ));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);

			$response = curl_exec($curl);

			curl_close($curl);

			$response_info = json_decode($response, true);

                        if ($response_info && $response_info['success']) {
				foreach ($response_info['rates'] as $code => $rate) {
					$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '" . (float)$rate . "', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($code) . "'");
				}

                                $this->cache->delete('currency');

                                return true;
                        }
                }

                return false;
        }
}
