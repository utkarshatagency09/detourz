<?php
namespace maza\Cache;

class DB extends \maza\Library {
	private $expire;

	public function __construct(int $expire = 3600) {
		$this->expire = $expire;
	}
        
    /**
     * Get cache
     * @param string $key cache key
     * @return mixed cache data
    */
	public function get(string $key) {
        $query = $this->db->query("SELECT value FROM `" . DB_PREFIX . "mz_cache` WHERE `key` = '" . $this->db->escape(preg_replace('/[^A-Z0-9\._-]/i', '', $key)) . "' AND (`timestamp` = 0 OR `timestamp` >= " . time() . ")");
        
        if($query->num_rows){
            return json_decode($query->row['value'], true);
        } else {
            return false;
        }
	}
        
    /**
     * set cache by key
     * @param string $key
     * @param mixed $value
     * @param mixed $expire
     */
	public function set(string $key, $value, $expire = true): void {
        if(!$expire){ // No expire
            $time = 0;
        } elseif(is_int($expire)){ // Set specified expire date
            $time = time() + $expire;
        } else { // Set default expire date
            $time = time() + $this->expire;
        }
        
        $this->db->query("REPLACE INTO `" . DB_PREFIX . "mz_cache` SET `key` = '" . $this->db->escape(preg_replace('/[^A-Z0-9\._-]/i', '', $key)) . "', `value` = '" . $this->db->escape(json_encode($value)) . "', `timestamp` = '" . $time . "'");
	}
        
    /**
     * Delete cache by key
     * @param string $key
     */
	public function delete(string $key): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_cache` WHERE `key` LIKE '" . $this->db->escape(preg_replace('/[^A-Z0-9\._-]/i', '', $key)) . "%'");
	}
        
    /**
     * Clear all cache
     */
    public function clear(): void {
        $this->db->query("TRUNCATE `" . DB_PREFIX . "mz_cache`");
    }
    
    /**
     * Flush the expire cache
     */
    public function flush(): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "mz_cache` WHERE `timestamp` <> 0 AND `timestamp` < '" . time() . "'");
    }
}