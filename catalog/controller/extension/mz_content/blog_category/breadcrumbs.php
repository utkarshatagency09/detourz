<?php
class ControllerExtensionMzContentBlogCategoryBreadcrumbs extends maza\layout\Content {
        public function index($setting) {
                return $this->load->view('extension/maza/blog/common/breadcrumbs');
        }
}
