<?php
class ModelExtensionMazaOpencart extends Model {
        private $module;
        private $extension;
                
        public function __construct($registry) {
                parent::__construct($registry);

                if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                        $this->load->model('extension/module');
                        $this->load->model('extension/extension');
                        
                        $this->module = $this->model_extension_module;
                        $this->extension = $this->model_extension_extension;
                } else {
                        $this->load->model('setting/module');
                        $this->load->model('setting/extension');
                        
                        $this->module = $this->model_setting_module;
                        $this->extension = $this->model_setting_extension;
                }
                
        }

        public function getModule(int $module_id): array {
                return $this->module->getModule($module_id);
        }

        public function getExtensions(string $type): array {
                return $this->extension->getExtensions($type);
        }
}