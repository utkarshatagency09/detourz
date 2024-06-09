<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright           Copyright (c) 2021 Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;

class Profiler {
    private $handle;

    public function __construct() {
        $this->handle = fopen(DIR_LOGS . 'profiler.csv', 'w');

        fputcsv($this->handle, ['identifier', 'time', 'initiator', 'tag']);
    }

    public function add(string $identifier, float $time, string $initiator, string $tag = '') {
        fputcsv($this->handle, [$identifier, $time, $initiator, $tag]);
    }

    public function __destruct() {
		fclose($this->handle);
	}
}
