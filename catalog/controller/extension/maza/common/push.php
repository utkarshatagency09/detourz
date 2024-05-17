<?php
class ControllerExtensionMazaCommonPush extends Controller {
    public function index(): string {
        $this->load->language('extension/maza/common/push');

        $data['public_key'] = $this->config->get('maza_notification_push_public_key');
        $data['subscribe']  = $this->url->link('extension/maza/common/push/subscribe');
        $data['rebuild']    = $this->mz_document->isRoute('common/home') && $this->customer->isLogged();

        return $this->load->view('extension/maza/common/push', $data);
    }

    public function subscribe(): void {
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && !empty($this->request->post['endpoint'])) {
            $subscription = [
                'endpoint' => $this->request->post['endpoint'],
                'expirationTime' => $this->request->post['expirationTime']??'',
                'keys' => $this->request->post['keys'],
            ];

            $this->load->model('extension/maza/tool/push');

            $subscriber = $this->model_extension_maza_tool_push->getSubscriber($subscription['endpoint']);

            if ($subscriber) {
                $this->model_extension_maza_tool_push->editSubscriber($subscriber['subscriber_id'], array_merge($subscriber, $subscription));
            } else {
                $this->model_extension_maza_tool_push->addSubscriber($subscription);
            }
        }
    }
}