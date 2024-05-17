<?php
class ControllerExtensionMazahooksGallery extends Controller {
    public function default(int $gallery_id): string {
        $setting = array(
            'widget_title' => array(),
            'widget_image_width' => 240,
            'widget_image_height' => 160,
            'widget_gallery_id' => $gallery_id,
            'widget_gutter' => 1,
        );

        return $this->load->controller('extension/mz_widget/gallery', $setting);
    }
}