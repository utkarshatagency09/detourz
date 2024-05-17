<?php
class ModelExtensionMazaOpencart extends Model {
        private $event;
        private $module;
        private $extension;
                
        public function __construct($registry) {
                parent::__construct($registry);

                if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                        $this->load->model('extension/event');
                        $this->load->model('extension/module');
                        $this->load->model('extension/extension');
                        
                        $this->event = $this->model_extension_event;
                        $this->module = $this->model_extension_module;
                        $this->extension = $this->model_extension_extension;
                } else {
                        $this->load->model('setting/event');
                        $this->load->model('setting/module');
                        $this->load->model('setting/extension');
                        
                        $this->event = $this->model_setting_event;
                        $this->module = $this->model_setting_module;
                        $this->extension = $this->model_setting_extension;
                }
            
        }
        
        ## Event ---
        public function addEvent($code, $trigger, $action) {
                $this->event->addEvent($code, $trigger, $action);
	}
        
        public function deleteEventByCode($code) {
                if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                        $this->event->deleteEvent($code);
                } else {
                        $this->event->deleteEventByCode($code);
                }
        }
        
        ## Module ---
        public function deleteModule($module_id) {
                $this->module->deleteModule($module_id);
        }
        
        public function getModule($module_id) {
                return $this->module->getModule($module_id);
        }
        
        public function getModulesByCode($code) {
		return $this->module->getModulesByCode($code);
	}
        
        ## Extension ---
        public function getInstalled($type) {
                return $this->extension->getInstalled($type);
        }
}