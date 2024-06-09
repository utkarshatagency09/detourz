<?php
class ControllerExtensionMazaAccountSocialAuth extends Controller
{
    public function index(array $config = []): string
    {
        $this->load->language('extension/maza/account/socialauth');

        $default_config = ['size' => 'md', 'inline' => false];

        $data = array_merge($default_config, $config);

        $url = '';

        if(!isset($this->request->get['route']) || strpos($this->request->get['route'], 'account/') === false){
            // Redirect
            if (!isset($this->request->get['route'])) {
                $redirect = $this->url->link('common/home');
            } else {
                $url_data = $this->request->get;

                unset($url_data['_route_']);

                $route = $url_data['route'];

                unset($url_data['route']);

                $url_query = '';

                if ($url_data) {
                    $url_query = '&' . urldecode(http_build_query($url_data, '', '&'));
                }

                $redirect = $this->url->link($route, $url_query, $this->request->server['HTTPS']);
            }

            $url .= '&redirect=' . $redirect;
        }

        // Providers
        $providers = ['google', 'apple', 'facebook', 'instagram', 'twitter', 'linkedin', 'paypal', 'amazon', 'discord'];

        $data['providers'] = [];

        foreach ($providers as $provider) {
            if ($this->config->get('maza_socialauth_' . $provider . '_status')) {
                $data['providers'][$provider] = [
                    'name'  => $this->language->get('text_' . $provider),
                    'title' => sprintf($this->language->get('button_provider'), $this->language->get('text_' . $provider)),
                    'href'  => $this->url->link('extension/maza/account/socialauth/authenticate', 'provider=' . $provider . $url),
                ];
            }
        }

        return $this->load->view('extension/maza/account/socialauth', $data);
    }

    public function authenticate(): void
    {
        if (isset($this->request->request['state'])) {
            $this->session->start($this->request->request['state']);
            setcookie($this->config->get('session_name'), $this->session->getId(), ini_get('session.cookie_lifetime'), ini_get('session.cookie_path'), ini_get('session.cookie_domain'));	
        }
        
        if (isset($this->request->get['provider'])) {
            $provider = $this->request->get['provider'];

            $this->session->data['provider'] = $provider;
        } elseif (isset($this->session->data['provider'])) {
            $provider = $this->session->data['provider'];

            unset($this->session->data['provider']);
        } else {
            $this->response->redirect($this->url->link('account/login'));
        }

        if (isset($this->request->get['redirect'])) {
            $redirect = $this->request->get['redirect'];

            $this->session->data['redirect'] = $redirect;
        } elseif (isset($this->session->data['redirect'])) {
            $redirect = $this->session->data['redirect'];

            unset($this->session->data['redirect']);
        } else {
            $redirect = $this->url->link('account/account');
        }

        $config = [
            'callback' => $this->url->link('extension/maza/account/socialauth/authenticate'),
            'providers' => [
                'Apple'     =>  ['enabled' => $this->config->get('maza_socialauth_apple_status'), 'authorize_url_parameters' => ['state' => $this->session->getId()], 'keys' => ['id' => $this->config->get('maza_socialauth_apple_id'), 'team_id' => $this->config->get('maza_socialauth_apple_team_id'), 'key_id' => $this->config->get('maza_socialauth_apple_key_id'), 'key_content' => $this->config->get('maza_socialauth_apple_key_content')]],
                'Google'    =>  ['enabled' => $this->config->get('maza_socialauth_google_status'), 'authorize_url_parameters' => ['state' => $this->session->getId()], 'keys' => ['id' => $this->config->get('maza_socialauth_google_id'), 'secret' => $this->config->get('maza_socialauth_google_secret')]],
                'Facebook'  =>  ['enabled' => $this->config->get('maza_socialauth_facebook_status'), 'authorize_url_parameters' => ['state' => $this->session->getId()], 'keys' => ['id' => $this->config->get('maza_socialauth_facebook_id'), 'secret' => $this->config->get('maza_socialauth_facebook_secret')]],
                'Instagram' =>  ['enabled' => $this->config->get('maza_socialauth_instagram_status'), 'authorize_url_parameters' => ['state' => $this->session->getId()], 'keys' => ['id' => $this->config->get('maza_socialauth_instagram_id'), 'secret' => $this->config->get('maza_socialauth_instagram_secret')]],
                'Twitter'   =>  ['enabled' => $this->config->get('maza_socialauth_twitter_status'), 'authorize_url_parameters' => ['state' => $this->session->getId()], 'keys' => ['key' => $this->config->get('maza_socialauth_twitter_key'), 'secret' => $this->config->get('maza_socialauth_twitter_secret')]],
                'LinkedIn'  =>  ['enabled' => $this->config->get('maza_socialauth_linkedin_status'), 'authorize_url_parameters' => ['state' => $this->session->getId()], 'keys' => ['key' => $this->config->get('maza_socialauth_linkedin_key'), 'secret' => $this->config->get('maza_socialauth_linkedin_secret')]],
                'PayPal'    =>  ['enabled' => $this->config->get('maza_socialauth_paypal_status'), 'authorize_url_parameters' => ['state' => $this->session->getId()], 'keys' => ['id' => $this->config->get('maza_socialauth_paypal_id'), 'secret' => $this->config->get('maza_socialauth_paypal_secret')]],
                'Amazon'    =>  ['enabled' => $this->config->get('maza_socialauth_amazon_status'), 'authorize_url_parameters' => ['state' => $this->session->getId()], 'keys' => ['id' => $this->config->get('maza_socialauth_amazon_id'), 'secret' => $this->config->get('maza_socialauth_amazon_secret')]],
                'Discord'   =>  ['enabled' => $this->config->get('maza_socialauth_discord_status'), 'authorize_url_parameters' => ['state' => $this->session->getId()], 'keys' => ['id' => $this->config->get('maza_socialauth_discord_id'), 'secret' => $this->config->get('maza_socialauth_discord_secret')]],
            ],
        ];

        try {
            $storage = new maza\socialAuth\Storage($this->registry);

            $hybridauth = new Hybridauth\Hybridauth($config, null, $storage);

            $adapter = $hybridauth->authenticate($provider);

            if ($adapter->isConnected()) {
                $user = $adapter->getUserProfile();

                // Login
                $this->load->model('account/customer');

                $customer = $this->model_account_customer->getCustomerByEmail($user->emailVerified?:$user->email);

                if ($customer && $customer['status']) {
                    $this->session->data['customer_id'] = $customer['customer_id'];
                } elseif(!$customer && $user->displayName && ($user->emailVerified?:$user->email)) {
                    $display_name = explode(' ', $user->displayName);

                    if($user->firstName){
                        $firstname = $user->firstName;
                    } else {
                        $firstname = array_shift($display_name);
                    }

                    if($user->lastName){
                        $lastname = $user->lastName;
                    } else {
                        $lastname = implode(' ', $display_name)?:$firstname;
                    }

                    $customer_data = [
                        'firstname' => $firstname,
                        'lastname'  => $lastname,
                        'email'     => $user->emailVerified?:$user->email,
                        'telephone' => $user->phone,
                        'password'  => token(),
                    ];

                    $this->session->data['customer_id'] = $this->model_account_customer->addCustomer($customer_data);
                } else {
                    $this->log->write('Unsuccessfull login attempt by ' . $provider . ' base on ' . print_r($user, true));
                }

                if (isset($this->session->data['customer_id'])) {
                    $customer_info = $this->model_account_customer->getCustomer($this->session->data['customer_id']);
                } else {
                    $customer_info = [];
                }

                if ($customer_info) {
                    $this->customer = new \Cart\Customer($this->registry);

                    $this->model_account_customer->deleteLoginAttempts($customer_info['email']);

                    // Unset guest
                    unset($this->session->data['guest']);

                    // Default Shipping Address
                    $this->load->model('account/address');

                    if ($this->config->get('config_tax_customer') == 'payment') {
                        $this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                    }

                    if ($this->config->get('config_tax_customer') == 'shipping') {
                        $this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
                    }

                    // Wishlist
                    if (isset($this->session->data['wishlist']) && is_array($this->session->data['wishlist'])) {
                        $this->load->model('account/wishlist');

                        foreach ($this->session->data['wishlist'] as $key => $product_id) {
                            $this->model_account_wishlist->addWishlist($product_id);

                            unset($this->session->data['wishlist'][$key]);
                        }
                    }
                }

                $adapter->disconnect();

                $this->response->redirect($redirect);
            }
        } catch (\Exception$e) {
            $this->log->write('Oops, we ran into an issue! ' . $e->getMessage());
        }
    }
}
