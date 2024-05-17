<?php

/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2020, TemplateMaza
 * @license		https://themeforest.net/licenses
 * @link		https://pocotheme.com/
 */
class ModelExtensionMazaExtension extends model {
        
        /**
         * Check extension is installed or not
         * @param string $type extension type
         * @param string $code code of extension
         * @return boolean
         */
        public function hasInstalled(string $type, string $code): bool {
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `type` = '" . $this->db->escape($type) . "' AND `code` = '" . $this->db->escape($code) . "'");
                
                if($query->row){
                    return true;
                } else {
                    return false;
                }
        }
        
        /**
         * Get list of widgets
         * @return array
         */
        public function getWidgets(): array {
                $data = array();
                
                $widgets = glob(DIR_APPLICATION . 'controller/extension/mz_widget/*.php');
                
                foreach ($widgets as $widget) {
                    $code = basename($widget, ".php");
                    
                    if(file_exists(DIR_APPLICATION . 'controller/extension/mz_widget/' . $code . '.php') && $this->user->hasPermission('access', 'extension/mz_widget/' . $code)){
                        $this->load->language('extension/mz_widget/' . $code, 'widget');

                        if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                            $heading_title = $this->language->get('heading_title');
                        } else {
                            $heading_title = $this->language->get('widget')->get('heading_title');
                        }
                        
                        $data[] = array(
                            'code'  =>  $code,
                            'name'  =>  strip_tags($heading_title)
                        );
                    }
                }

                array_multisort(array_column($data, 'name'), $data);
                
                return $data;
        }
        
        /**
         * Get list of designs components
         * @return array
         */
        public function getDesigns(): array {
                $data = array();
                
                $designs = glob(DIR_APPLICATION . 'controller/extension/mz_design/*.php');
                
                foreach ($designs as $design) {
                    $code = basename($design, ".php");
                    
                    if(file_exists(DIR_APPLICATION . 'controller/extension/mz_design/' . $code . '.php') && $this->user->hasPermission('access', 'extension/mz_design/' . $code)){
                        $this->load->language('extension/mz_design/' . $code, 'design');

                        if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                            $heading_title = $this->language->get('heading_title');
                        } else {
                            $heading_title = $this->language->get('design')->get('heading_title');
                        }
                        
                        $data[] = array(
                            'code'  =>  $code,
                            'name'  =>  strip_tags($heading_title)
                        );
                    }
                }

                array_multisort(array_column($data, 'name'), $data);
                
                return $data;
        }
        
        
        /**
         * Get list of content types
         * @return array
         */
        public function getContentTypes(): array {
                $data = array();
                
                foreach (new DirectoryIterator(DIR_APPLICATION . 'controller/extension/mz_content/') as $file) {
                    if ($file->isDir() && !$file->isDot()) {
                        $code = $file->getFilename();
                        
                        $this->load->language('extension/mz_content/' . $code, 'content');

                        if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                            $heading_title = $this->language->get('heading_title');
                        } else {
                            $heading_title = $this->language->get('content')->get('heading_title');
                        }
                        
                        $data[] = array(
                            'name' => strip_tags($heading_title),
                            'code' => $code
                        );
                    }
                }

                array_multisort(array_column($data, 'name'), $data);
                
                return $data;
        }
        
        /**
         * Get list of contents of type
         * @param string $content_type code
         * @return array
         */
        public function getContentsOfType(string $content_type): array {
                $data = array();
                
                if(is_dir(DIR_APPLICATION . 'controller/extension/mz_content/' . $content_type)){
                    foreach (new DirectoryIterator(DIR_APPLICATION . 'controller/extension/mz_content/' . $content_type) as $file) {
                        if ($file->isFile() && !$file->isDot() && $this->user->hasPermission('access', 'extension/mz_content/' . $content_type . '/' . $file->getBasename('.php'))) {
                            $code = $file->getBasename('.php');

                            $this->load->language('extension/mz_content/' . $content_type . '/' . $code, 'content');

                            if(version_compare(VERSION, '3.0.0.0') < 0){ // For opencart 2
                                $heading_title = $this->language->get('heading_title');
                            } else {
                                $heading_title = $this->language->get('content')->get('heading_title');
                            }

                            $data[] = array(
                                'name' => strip_tags($heading_title),
                                'code' => $content_type . '.' . $code
                            );
                        }
                    }
                }

                array_multisort(array_column($data, 'name'), $data);
                
                return $data;
        }
}
