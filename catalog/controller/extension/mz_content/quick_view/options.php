<?php
class ControllerExtensionMzContentQuickViewOptions extends maza\layout\Content {
        public function index($setting) {
                $data['option_label']   =   $setting['content_option_name'];
                $data['radio_style']    =   $setting['content_radio_style'];
                $data['checkbox_style'] =   $setting['content_checkbox_style'];
                $data['column']         = array();
                
                $prev_column_size = 0;
                $column_sizes = array(
                    'xs' => (int)$setting['content_column_xs'], 
                    'sm' => (int)$setting['content_column_sm'], 
                    'md' => (int)$setting['content_column_md'], 
                    'lg' => (int)$setting['content_column_lg'], 
                    'xl' => (int)$setting['content_column_xl']);
                
                foreach($column_sizes as $breakpoint => $column_size){
                    if($breakpoint == 'xs' && $column_size != 1){
                        $data['column'][] = 'col-' . 12/$column_size;
                    } elseif($breakpoint != 'xs' && $prev_column_size != $column_size){
                        $data['column'][] = 'col-' . $breakpoint . '-' . 12/$column_size;
                    }
                    $prev_column_size = $column_size;
                }
                
                $data['column'] = implode(' ', $data['column']);
                
                return $this->load->view('product/product/options', $data);
        }
}
