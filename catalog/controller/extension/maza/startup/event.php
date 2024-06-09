<?php
class ControllerExtensionMazaStartupEvent extends Controller {
	public function index(): void {
        if ($this->config->has('mz_action_event')) {
            foreach ($this->config->get('mz_action_event') as $key => $value) {
                foreach ($value as $priority => $action) {
                    $this->event->register($key, new Action($action), $priority);
                }
            }
        }
        
        // Route Controller Event
        // $this->event->register('controller/' . $this->mz_document->getRoute() . '/after', new Action('extension/maza/event/asset'));
        $this->event->register('controller/' . $this->mz_document->getRoute() . '/after', new Action('extension/maza/event/controller/minify'));
	}
}
