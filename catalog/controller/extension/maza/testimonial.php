<?php
class ControllerExtensionMazaTestimonial extends Controller {
        private $error = array();
        
	public function index(): void {
		$this->load->language('extension/maza/testimonial');

		$this->load->model('extension/maza/testimonial');

                // Submit testimonial
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()){

                        $this->model_extension_maza_testimonial->addTestimonial($this->request->post);

                        if($this->mz_skin_config->get('testimonial_mail_status') && !empty($this->request->post['email'])){
                            $this->model_extension_maza_testimonial->sendMail($this->request->post['email']);
                        }

                        $data['success'] = $this->language->get('text_success');
                }

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
                
                if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = (int)$this->mz_skin_config->get('testimonial_limit');
		}

                // $this->document->setTitle($this->mz_skin_config->get('testimonial_meta_title'));
                // $this->document->setDescription($this->mz_skin_config->get('testimonial_meta_description'));
                // $this->document->setKeywords($this->mz_skin_config->get('testimonial_meta_keyword'));

                $data['heading_title'] = $this->language->get('heading_title');

                $data['testimonials'] = array();

                $filter_data = array(
                    'start'              => ($page - 1) * $limit,
                    'limit'              => $limit
                );

                $testimonial_total = $this->model_extension_maza_testimonial->getTotalTestimonials($filter_data);

                $results = $this->model_extension_maza_testimonial->getTestimonials($filter_data);

                foreach ($results as $result) {
                        // Image
                        if($this->mz_skin_config->get('testimonial_list_image')){
                            if ($result['image']) {
                                    $image = $this->model_tool_image->resize($result['image'], $this->mz_skin_config->get('testimonial_thumb_width'), $this->mz_skin_config->get('testimonial_thumb_height'));
                            } else {
                                    $image = $this->model_tool_image->resize('placeholder.png', $this->mz_skin_config->get('testimonial_thumb_width'), $this->mz_skin_config->get('testimonial_thumb_height'));
                            }
                        } else {
                            $image = null;
                        }
                        
                        // Rating
                        if($this->mz_skin_config->get('testimonial_list_rating')){
                            $rating = $result['rating'];
                        } else {
                            $rating = null;
                        }
                        
                        // Extra
                        if($this->mz_skin_config->get('testimonial_list_extra')){
                            $extra = $result['extra'];
                        } else {
                            $extra = null;
                        }
                        
                        // Timestamp
                        if($this->mz_skin_config->get('testimonial_list_timestamp')){
                            $timestamp = date('d M Y', strtotime($result['date_added']));
                        } else {
                            $timestamp = null;
                        }

                        $data['testimonials'][] = array(
                                'testimonial_id'  => $result['testimonial_id'],
                                'thumb'       => $image,
                                'author'      => $result['name'],
                                'extra'       => $extra,
                                'rating'      => $rating,
                                'description' => trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))),
                                'timestamp'   => $timestamp,
                        );
                }
                
                $pagination = new Pagination();
                $pagination->total = $testimonial_total;
                $pagination->page = $page;
                $pagination->limit = $limit;
                $pagination->url = $this->url->link('extension/maza/testimonial', 'page={page}');

                $data['pagination'] = $pagination->render();

                $data['results'] = sprintf($this->language->get('text_pagination'), ($testimonial_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($testimonial_total - $limit)) ? $testimonial_total : ((($page - 1) * $limit) + $limit), $testimonial_total, ceil($testimonial_total / $limit));
                
                // http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
                if ($page == 1) {
                    $this->document->addLink($this->url->link('extension/maza/testimonial'), 'canonical');
                } else {
                    $this->document->addLink($this->url->link('extension/maza/testimonial', 'page='. $page), 'canonical');
                }

                if ($page > 1) {
                    $this->document->addLink($this->url->link('extension/maza/testimonial', (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
                }

                if ($limit && ceil($testimonial_total / $limit) > $page) {
                    $this->document->addLink($this->url->link('extension/maza/testimonial', 'page='. ($page + 1)), 'next');
                }

                $data['continue'] = $this->url->link('common/home');
                
                // Form
                $data['testimonial_form']    = $this->mz_skin_config->get('testimonial_submit_status');
                $data['testimonial_rating']  = $this->mz_skin_config->get('testimonial_form_rating');
                $data['testimonial_email']   = $this->mz_skin_config->get('testimonial_form_email');
                $data['testimonial_image']   = $this->mz_skin_config->get('testimonial_form_image');
                $data['testimonial_extra']   = $this->mz_skin_config->get('testimonial_form_extra');
                
                $data['action'] = $this->url->link('extension/maza/testimonial');
                
                if (isset($this->request->post['name'])) {
                        $data['form_name'] = $this->request->post['name'];
                } else {
                        $data['form_name'] = '';
                }

                if (isset($this->request->post['extra'])) {
                        $data['form_extra'] = $this->request->post['extra'];
                } else {
                        $data['form_extra'] = '';
                }

                if (isset($this->request->post['description'])) {
                        $data['form_description'] = $this->request->post['description'];
                } else {
                        $data['form_description'] = '';
                }

                if (isset($this->request->post['rating'])) {
                        $data['form_rating'] = $this->request->post['rating'];
                } else {
                        $data['form_rating'] = '';
                }

                if (isset($this->request->post['email'])) {
                        $data['form_email'] = $this->request->post['email'];
                } else {
                        $data['form_email'] = '';
                }
                
                // Captcha
                if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $this->mz_skin_config->get('testimonial_form_captcha')) {
                    if(isset($this->error['captcha'])){
                        $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), array('captcha' => $this->error['captcha']));
                    } else {
                        $data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
                    }  
                } else {
                        $data['captcha'] = null;
                }
                
                // Error
                if(isset($this->error['warning'])){
                    $data['warning'] = $this->error['warning'];
                }
                
                if (isset($this->error['name'])) {
                        $data['invalid_name'] = 1;
                }

                if (isset($this->error['extra'])) {
                        $data['invalid_extra'] = 1;
                }

                if (isset($this->error['description'])) {
                        $data['invalid_description'] = 1;
                }

                if (isset($this->error['rating'])) {
                        $data['invalid_rating'] = 1;
                }

                if (isset($this->error['email'])) {
                        $data['invalid_email'] = 1;
                }
                
                if (isset($this->error['image'])) {
                        $data['invalid_image'] = $this->error['image'];
                }
                
                // Layout
                $data['column_xs'] = $this->mz_skin_config->get('testimonial_column_xs');
                $data['column_sm'] = $this->mz_skin_config->get('testimonial_column_sm');
                $data['column_md'] = $this->mz_skin_config->get('testimonial_column_md');
                $data['column_lg'] = $this->mz_skin_config->get('testimonial_column_lg');
                $data['column_xl'] = $this->mz_skin_config->get('testimonial_column_xl');
                
                // Quote icon
                $data['testimonial_quote_icon_width']   = $this->mz_skin_config->get('testimonial_quote_icon_width');
                $data['testimonial_quote_icon_height']  = $this->mz_skin_config->get('testimonial_quote_icon_height');
                $data['testimonial_quote_icon_size']    = $this->mz_skin_config->get('testimonial_quote_icon_size');
                
                if(!empty($this->mz_skin_config->get('testimonial_quote_icon_font')[$this->config->get('config_language_id')])){
                    $data['testimonial_quote_icon_font'] = $this->mz_skin_config->get('testimonial_quote_icon_font')[$this->config->get('config_language_id')];
                } else {
                    $data['testimonial_quote_icon_font'] = false;
                }
                if(!empty($this->mz_skin_config->get('testimonial_quote_icon_svg')[$this->config->get('config_language_id')]) && is_file(MZ_CONFIG::$DIR_SVG_IMAGE . $this->mz_skin_config->get('testimonial_quote_icon_svg')[$this->config->get('config_language_id')])){
                    $data['testimonial_quote_icon_svg'] =  $this->mz_document->addSVG(MZ_CONFIG::$DIR_SVG_IMAGE . $this->mz_skin_config->get('testimonial_quote_icon_svg')[$this->config->get('config_language_id')]);
                } else {
                    $data['testimonial_quote_icon_svg'] = false;
                }
                if(!empty($this->mz_skin_config->get('testimonial_quote_icon_image')[$this->config->get('config_language_id')]) && is_file(DIR_IMAGE . $this->mz_skin_config->get('testimonial_quote_icon_image')[$this->config->get('config_language_id')])){
                    list($width, $height) = $this->model_extension_maza_image->getEstimatedSize($this->mz_skin_config->get('testimonial_quote_icon_image')[$this->config->get('config_language_id')], $this->mz_skin_config->get('testimonial_quote_icon_width'), $this->mz_skin_config->get('testimonial_quote_icon_height'));
                    $data['testimonial_quote_icon_image'] = $this->model_tool_image->resize($this->mz_skin_config->get('testimonial_quote_icon_image')[$this->config->get('config_language_id')], $width, $height);
                } else {
                    $data['testimonial_quote_icon_image'] = false;
                }
                

                $data['column_left'] = $this->load->controller('common/column_left');
                $data['column_right'] = $this->load->controller('common/column_right');
                $data['content_top'] = $this->load->controller('common/content_top');
                $data['content_bottom'] = $this->load->controller('common/content_bottom');
                $data['footer'] = $this->load->controller('common/footer');
                $data['header'] = $this->load->controller('common/header');

                $this->response->setOutput($this->load->view('extension/maza/testimonial', $data));
		
	}
        
        public function write(): void {
		$this->load->language('extension/maza/testimonial');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
                        $this->load->model('extension/maza/testimonial');

                        $this->model_extension_maza_testimonial->addTestimonial($this->request->post);

                        if($this->mz_skin_config->get('testimonial_mail_status') && !empty($this->request->post['email'])){
                            $this->model_extension_maza_testimonial->sendMail($this->request->post['email']);
                        }

                        $json['success'] = $this->language->get('text_success');
		}
                
                if (isset($this->error['name'])) {
                        $json['error_name'] = $this->error['name'];
                }

                if (isset($this->error['extra'])) {
                        $json['error_extra'] = $this->error['extra'];
                }

                if (isset($this->error['description'])) {
                        $json['error_description'] = $this->error['description'];
                }

                if (isset($this->error['rating'])) {
                        $json['error_rating'] = $this->error['rating'];
                }

                if (isset($this->error['email'])) {
                        $json['error_email'] = $this->error['email'];
                }
                
                if (isset($this->error['image'])) {
                        $json['error_image'] = $this->error['image'];
                }

                // Captcha
                if (isset($this->error['captcha'])) {
                        $json['error_captcha'] = $this->error['captcha'];
                }

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
        
        protected function validateForm(): bool {
		if (!$this->mz_skin_config->get('testimonial_submit_status')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
                
                if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 50)) {
                        $this->error['name'] = $this->language->get('error_name');
                }

                if (($this->mz_skin_config->get('testimonial_form_extra') > 0 && empty($this->request->post['extra']))
                        || (isset($this->request->post['extra']) && utf8_strlen($this->request->post['extra']) > 20)) {
                        $this->error['extra'] = $this->language->get('error_extra');
                }

                if ((utf8_strlen($this->request->post['description']) < 10) || (utf8_strlen($this->request->post['description']) > 1000)) {
                        $this->error['description'] = $this->language->get('error_description');
                }

                if (($this->mz_skin_config->get('testimonial_form_rating') > 0 && empty($this->request->post['rating']))
                        || (isset($this->request->post['rating']) && ($this->request->post['rating'] < 1 || $this->request->post['rating'] > 5))) {
                        $this->error['rating'] = $this->language->get('error_rating');
                }

                if(($this->mz_skin_config->get('testimonial_form_email') > 0 && empty($this->request->post['email']))
                        || (!empty($this->request->post['email']) && !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL))){
                         $this->error['email'] = $this->language->get('error_email');
                }

                // Captcha
                if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && $this->mz_skin_config->get('testimonial_form_captcha')) {
                        $captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

                        if ($captcha) {
                                $this->error['captcha'] = $captcha;
                        }
                }
                
                // Image
                if(isset($this->request->files['image']["tmp_name"]) && is_file($this->request->files['image']["tmp_name"])){
                    
                    // Check file is image
                    if(empty($this->error['image']) && !getimagesize($this->request->files['image']["tmp_name"])) {
                        $this->error['image'] = $this->language->get('error_not_image');
                    }
                    
                    // check file size
                    if(empty($this->error['image']) && $this->request->files['image']['size'] > 1048576) {
                        $this->error['image'] = $this->language->get('error_image_size');
                    }
                    
                    // Accept image
                    if(!$this->error){
                        maza\createDirPath(MZ_CONFIG::$DIR_TESTIMONIAL_IMAGE); // Create directory
                        
                        $image_extension = strtolower(pathinfo($this->request->files['image']['name'],PATHINFO_EXTENSION));
                        do{
                            $target_file = MZ_CONFIG::$DIR_TESTIMONIAL_IMAGE . mt_rand() . '.' . $image_extension;
                        } while(is_file($target_file));
                    
                        if(move_uploaded_file($this->request->files['image']["tmp_name"], $target_file)){
                            $this->request->post['image'] = substr($target_file, strlen(DIR_IMAGE));
                        }
                    }
                } elseif($this->mz_skin_config->get('testimonial_form_image') > 0){ // Require field
                    $this->error['image'] = $this->language->get('error_upload_image');
                }
                
                if(!isset($this->error['warning']) && $this->error){
                    $this->error['warning'] = $this->language->get('error_warning');
                }

		return !$this->error;
	}
}
