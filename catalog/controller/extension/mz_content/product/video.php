<?php
class ControllerExtensionMzContentProductVideo extends maza\layout\Content {
        public function index(array $setting): string {
                $data = array();

                $data['mz_suffix']        = $setting['mz_suffix'];

                $data['carousel_status']  = $setting['content_carousel_status'];
                $data['carousel_pagination']= $setting['content_carousel_pagination'];
                
                $data['column_xs']        = $setting['content_column_xs'];
                $data['column_sm']        = $setting['content_column_sm'];
                $data['column_md']        = $setting['content_column_md'];
                $data['column_lg']        = $setting['content_column_lg'];
                $data['column_xl']        = $setting['content_column_xl'];

                $data['gutter_width']     = $this->mz_skin_config->get('style_gutter_width');
                $data['breakpoint_sm']    = $this->mz_skin_config->get('style_breakpoints')['sm'];
                $data['breakpoint_md']    = $this->mz_skin_config->get('style_breakpoints')['md'];
                $data['breakpoint_lg']    = $this->mz_skin_config->get('style_breakpoints')['lg'];
                $data['breakpoint_xl']    = $this->mz_skin_config->get('style_breakpoints')['xl'];
                
                return $this->load->view('product/product/video', $data);
        }
}
