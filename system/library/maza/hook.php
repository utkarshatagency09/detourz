<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright           Copyright (c) 2021 Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;
/**
* hook class
*/
final class hook{
    private $hooks;
    private $registry;

    /**
     * Constructor
    */
    public function __construct($registry) {
        $this->registry = $registry;
        $this->db = $this->registry->get('db');
    }

    public function addData(string $trigger, $data): void {
        $this->hooks[$trigger][] = $data;
    }
    
    /**
     * Call hook and fetch data
     * @param string $trigger
     * @param array $data
     * @return array
     */
    public function fetch(string $trigger, array $data = array()): array {
        $return = array();

        if(isset($this->hooks[$trigger])){
            foreach($this->hooks[$trigger] as $action){
                if ($action instanceof \Action) {
                    $cb_return = $action->execute($this->registry, $data);
                } else {
                    $cb_return = $action;
                }

                if(is_array($cb_return)){
                    $return = array_merge($return, $cb_return);
                } else if($cb_return){
                    $return[] = $cb_return;
                }
            }
        }

        return $return;
    }

    /**
     * Shortcode hook call for twig template
     * @param string $shortcode Ex. gallery.12
     */
    public function shortcode(string $shortcode){
        $data = explode('.', $shortcode);

        $trigger = array_shift($data);

        return implode('', $this->fetch($trigger, $data));
    }
    
    /**
     * Add hook
     * @param string $code hook code
     * @param string $trigger
     * @param string $action class method
     */
    public function addHook(string $code, string $trigger, string $action): void {
        $this->db->query("REPLACE INTO `" . DB_PREFIX . "mz_hook` SET `code` = '" . $this->db->escape($code) . "', `trigger` = '" . $this->db->escape($trigger) . "', `action` = '" . $this->db->escape($action) . "'");
    }
    
    /**
     * Delete specific hook
     * @param string $trigger
     * @param string $action
     */
    public function deleteHook(string $trigger, string $action): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_hook` WHERE `trigger` = '" . $this->db->escape($trigger) . "' AND `action` = '" . $this->db->escape($action) . "' LIMIT 1");
    }
    
    /**
     * disable specific hook
     * @param string $trigger
     * @param string $action
     */
    public function disableHook(string $trigger, string $action): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "mz_hook` SET `status` = 0 WHERE `trigger` = '" . $this->db->escape($trigger) . "' AND `action` = '" . $this->db->escape($action) . "' LIMIT 1");
    }
    
    /**
     * delete hooks by code
     * @param string $code
     */
    public function deleteHooks(string $code): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_hook` WHERE `code` = '" . $this->db->escape($code) . "'");
    } 
        
}