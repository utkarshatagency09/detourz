<?php
namespace maza\Cache;

class File extends \maza\Library {
    private $expire;

    public function __construct($expire = 3600) {
        $this->expire = $expire;
    }

    public function get($key) {
        $files = glob(\MZ_CONFIG::$DIR_CACHE . str_replace('.', '/', preg_replace('/[^A-Z0-9\._-]/i', '', $key)) . '.*');

        foreach ($files as $file) {
            $expire = substr(strrchr($file, '.'), 1);

            if ($expire == 0 || $expire > time()) {
                $handle = fopen($file, 'r');

                flock($handle, LOCK_SH);

                $data = fread($handle, filesize($file));

                flock($handle, LOCK_UN);

                fclose($handle);

                return json_decode($data, true);
            }
        }

        return false;
    }

    public function set($key, $value, $expire = true) {
        $this->delete($key);

        if (!$expire) { // No expire
            $file = \MZ_CONFIG::$DIR_CACHE . str_replace('.', '/', preg_replace('/[^A-Z0-9\._-]/i', '', $key)) . '.0';
        } elseif (is_int($expire)) { // Set specified expire date
            $file = \MZ_CONFIG::$DIR_CACHE . str_replace('.', '/', preg_replace('/[^A-Z0-9\._-]/i', '', $key)) . '.' . (time() + $expire);
        } else { // Set default expire date
            $file = \MZ_CONFIG::$DIR_CACHE . str_replace('.', '/', preg_replace('/[^A-Z0-9\._-]/i', '', $key)) . '.' . (time() + $this->expire);
        }

        // Create directory for file
        \maza\createDirPath(substr($file, 0, strrpos($file, '/') + 1));

        $handle = fopen($file, 'w');

        flock($handle, LOCK_EX);

        fwrite($handle, json_encode($value));

        fflush($handle);

        flock($handle, LOCK_UN);

        fclose($handle);
    }

    public function delete($key) {
        $path = \MZ_CONFIG::$DIR_CACHE . str_replace('.', '/', preg_replace('/[^A-Z0-9\._-]/i', '', $key));

        if (is_dir($path)) {
            \maza\deletePath($path);
        } else {
            $files = glob($path . '.*');

            foreach ($files as $file) {
                \maza\deletePath($file);
            }
        }
    }

    public function clear() {
        \maza\deletePath(\MZ_CONFIG::$DIR_CACHE);
    }

    /**
     * Flush the expire cache
     */
    public function flush() {
        $this->deleteExpired(\MZ_CONFIG::$DIR_CACHE);
    }

    /**
     * Delete expire cache file
     * @param string $path file path
     */
    private function deleteExpired($path) {
        if (is_dir($path)) {
            $files = glob($path . '.*');

            foreach ($files as $file) {
                $this->deleteExpired($file);
            }
        } elseif (is_file($path)) {
            $time = substr(strrchr($path, '.'), 1);

            if ($time > 0 && $time < time()) {
                unlink($path);
            }
        }
    }
}