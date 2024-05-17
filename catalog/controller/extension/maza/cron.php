<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright   Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ControllerExtensionMazaCron extends Controller {
    public function __construct($registry) {
        parent::__construct($registry);

        if (!$this->config->get('maza_cron_status')) {
            die('<h1>503 Service Unavailable</h1>');
        }

        $this->load->library('cart/user');

        $this->load->model('extension/maza/cron');

        if (!$this->model_extension_maza_cron->login()) {
            die('<h1>401 Unauthorized</h1>');
        }

        ignore_user_abort(true);
        set_time_limit(1800);
    }

    public function index() {
        $this->load->model('extension/maza/extension');

        // Flush expire cache
        $this->mz_cache->flush();

        // delete backup archive files
        foreach (glob(DIR_UPLOAD . 'mz.*.backup.zip') as $file) {
            unlink($file);
        }

        // Task
        $this->tags();
        $this->filter();
        $this->transaction();

        die('<h1>Cron completed</h1>');
    }

    /**
     * Generate product and blog tag
     */
    public function tags(): void {
        $this->load->model('extension/maza/extension');

        if ($this->model_extension_maza_extension->hasInstalled('module', 'mz_tags')) {
            $this->load->controller('extension/module/mz_tags/generateTags');
        }
    }

    /**
     * Sync filter values to products
     */
    public function filter(): void {
        $this->load->model('extension/maza/extension');

        if ($this->model_extension_maza_extension->hasInstalled('module', 'mz_filter')) {
            $this->model_extension_maza_cron->callToAdmin('extension/maza/filter/sync');
        }
    }

    /**
     * Process transactions
     */
    public function transaction(): void {
        $this->load->model('extension/maza/tool/mail');
        $this->load->model('extension/maza/tool/sms');
        $this->load->model('extension/maza/tool/push');

        // Flush mail queue
        $this->model_extension_maza_tool_mail->flush($this->request->get['mail_limit']??50, !isset($this->request->get['mail_limit']));

        // Flush sms queue
        $this->model_extension_maza_tool_sms->flush($this->request->get['sms_limit']??50, !isset($this->request->get['sms_limit']));

        // Flush push notification queue
        $this->model_extension_maza_tool_push->flush();
    }
}