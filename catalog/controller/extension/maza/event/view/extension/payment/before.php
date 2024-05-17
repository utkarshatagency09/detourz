<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaEventViewExtensionPaymentBefore extends Controller {
    public function index(string $route, array &$data): void {
        $data['mz_back'] = $this->url->link('checkout/checkout');
    }
}
