<?php
class ControllerExtensionMzDesignContentBuilder extends maza\layout\Design {
    private static $instance_count = 0;

	public function index(array $setting): string {
        return $this->layout_builder(['group' => 'content_builder', 'group_owner' => $setting['design_content_builder_id'], 'suffix' => $setting['mz_suffix']??self::$instance_count]);
	}
}
