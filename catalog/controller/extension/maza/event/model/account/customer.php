<?php
class ControllerExtensionMazaEventModelAccountCustomer extends Controller {
	public function addCustomerAfter(string $route, array $param, int $customer_id): void {
        $data = $param[0];

        // Assign default channel
        $this->load->model('extension/maza/account/notification');

        $channels = $this->model_extension_maza_account_notification->getDefaultChannels();
        
        $this->model_extension_maza_account_notification->addChannels($customer_id, $channels);

        // Edit newsletter
        $this->load->model('extension/maza/newsletter');

        $newsletter_subscriber = $this->model_extension_maza_newsletter->getSubscriber($data['email']);

        // Subscribe
        if (!empty($data['newsletter']) && !$newsletter_subscriber) {
            $this->model_extension_maza_newsletter->addSubscriber($data['email']);
        }

        // Unsubscribe
        if (empty($data['newsletter']) && $newsletter_subscriber) {
            $this->model_extension_maza_newsletter->deleteSubscriber($newsletter_subscriber['token']);
        }
    }

    public function editNewsletterAfter(string $route, array $param): void {
        $newsletter = $param[0];

        $this->load->model('extension/maza/newsletter');

        $subscriber = $this->model_extension_maza_newsletter->getSubscriber($this->customer->getEmail());

        // Subscribe
        if ($newsletter && !$subscriber) {
            $this->model_extension_maza_newsletter->addSubscriber($this->customer->getEmail());
        }

        // Unsubscribe
        if (!$newsletter && $subscriber) {
            $this->model_extension_maza_newsletter->deleteSubscriber($subscriber['token']);
        }
    }
}
