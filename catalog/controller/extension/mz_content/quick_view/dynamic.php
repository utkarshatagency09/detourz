<?php
class ControllerExtensionMzContentQuickViewDynamic extends maza\layout\Content {
	public function index(array $setting): string {

		$data['price_min']		=	$setting['content_condition_price_min'];
		$data['price_max']		=	$setting['content_condition_price_max'];
		$data['quantity_min']	=	$setting['content_condition_quantity_min'];
		$data['quantity_max']	=	$setting['content_condition_quantity_max'];
		$data['rating_min']		=	$setting['content_condition_rating_min'];
		$data['rating_max']		=	$setting['content_condition_rating_max'];
		$data['stock_status']	=	$setting['content_condition_stock_status'];
		$data['special']	    =	$setting['content_condition_special'];
		$data['reward']			=	$setting['content_condition_reward'];
		$data['points']			=	$setting['content_condition_points'];
		$data['tax_class_id']	=	$setting['content_condition_tax_class_id'];
		$data['products']		=	$setting['content_condition_product']??[];
		$data['manufacturers']  =	$setting['content_condition_manufacturer']??[];

		// Content
		if($setting['content_type'] == 'content' && $setting['content_content'] && !empty($setting['content_content_data'])){
			$data['content'] = $this->entryContent($setting['content_content'], $setting['content_content_data'], $setting['entry_id'], $setting['mz_suffix']);
		}

		// Design
		if($setting['content_type'] == 'design' && $setting['content_design'] && !empty($setting['content_design_data'])){
			$data['content'] = $this->entryDesign($setting['content_design'], $setting['content_design_data'], $setting['entry_id'], $setting['mz_suffix']);
		}

		// widget
		if($setting['content_type'] == 'widget' && $setting['content_widget'] && !empty($setting['content_widget_data'])){
			$data['content'] = $this->entryWidget($setting['content_widget'], $setting['content_widget_data'], $setting['entry_id'], $setting['mz_suffix']);
		}

		// Module
		if($setting['content_type'] == 'module' && $setting['content_module']){
			$data['content'] = $this->entryModule($setting['content_module'], $setting['entry_id'], $setting['mz_suffix']);
		}
		
		// Content
		if($setting['content_type'] == 'content_builder' && $setting['content_content_builder_id']){
			$data['content']  = $this->layout_builder(['group' => 'content_builder', 'group_owner' => $setting['content_content_builder_id'], 'suffix' => $setting['mz_suffix']]);
		}

		$data['opposite']  = $setting['content_opposite'];
		$data['mz_suffix'] = $setting['mz_suffix'];
		
		return $this->load->view('product/product/dynamic', $data);
	}
}
