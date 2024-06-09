<?php
class ControllerExtensionMazaEventControllerCommonHeader extends Controller {
	public function before(): void {
        // Document
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "mz_document d LEFT JOIN " . DB_PREFIX . "mz_document_description dd ON (d.document_id = dd.document_id) WHERE d.status = 1 AND d.route = '" . $this->db->escape($this->mz_document->getRoute()) . "' AND d.store_id = '" . (int)$this->config->get('config_store_id') . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
        
        if ($query->num_rows > 0) {
            $this->mz_document->addOGP('og:title', $query->row['og_title']);
            $this->mz_document->addOGP('og:description', $query->row['og_description']);
            $this->mz_document->addOGP('og:video', $query->row['og_video']);
            $this->mz_document->addOGP('og:image:alt', $query->row['og_image_alt']);

            if ($query->row['og_image']) {
                if($query->row['og_image_width'] && $query->row['og_image_height']){
                    $this->mz_document->addOGP('og:image', $this->model_tool_image->resize($query->row['og_image'], $query->row['og_image_width'], $query->row['og_image_height']));
                    $this->mz_document->addOGP('og:image:width', $query->row['og_image_width']);
                    $this->mz_document->addOGP('og:image:height', $query->row['og_image_height']);
                } else {
                    $this->mz_document->addOGP('og:image', $query->row['og_image']);
                }
            }
            
            if ($query->row['meta_title']) {
                $this->document->setTitle($query->row['meta_title']);
            }
            if ($query->row['meta_description']) {
                $this->document->setDescription($query->row['meta_description']);
            }
            if ($query->row['meta_keyword']) {
                $this->document->setKeywords($query->row['meta_keyword']);
            }
        }
	}
}
