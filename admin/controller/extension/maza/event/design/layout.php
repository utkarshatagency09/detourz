<?php
class ControllerExtensionMazaEventDesignLayout extends Controller {
    
        
        /**
         * Delete setting when layout deleted
         * @param string $route controller route
         * @param array $param parameter of method
         * @return void
         */
        public function deleteLayout($route, $param) {
                $layout_id = $param[0];
                
                $this->load->model('extension/maza/layout_builder');
                
                $this->model_extension_maza_layout_builder->deleteLayout('layout', $layout_id);
                $this->model_extension_maza_layout_builder->deleteLayout('layout_content_top', $layout_id);
                $this->model_extension_maza_layout_builder->deleteLayout('layout_content_bottom', $layout_id);
                $this->model_extension_maza_layout_builder->deleteLayout('layout_column_left', $layout_id);
                $this->model_extension_maza_layout_builder->deleteLayout('layout_column_right', $layout_id);
        }
        
}
