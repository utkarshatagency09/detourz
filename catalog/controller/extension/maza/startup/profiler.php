<?php
class ControllerExtensionMazaStartupProfiler extends Controller {
	public function index(): void {
        if (isset($this->request->get['mz_profiler'])) {
            $this->mz_profiler = new maza\Profiler();

            // DB wraper
            $this->db = new class($this->db, $this->registry) extends maza\Wrapper {
                private $mz_profiler;
                private $request;
    
                public function __construct($db, $registry) {
                    parent::__construct($db);
    
                    $this->mz_profiler = $registry->get('mz_profiler');
                    $this->request = $registry->get('request');
                }
    
                public function query($sql) {
                    $timer = microtime(true);
    
                    $result = $this->object->query($sql);
    
                    $interval = microtime(true) - $timer;
                    
                    $this->mz_profiler->add($sql, $interval, $this->request->get['route']??'common/home', 'sql');
    
                    return $result;
                }
            };
        }
	}
}
