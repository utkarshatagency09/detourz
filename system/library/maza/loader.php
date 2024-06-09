<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2021 Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;
/**
* Loader class
*/
final class Loader {
	protected $registry;

	/**
	 * Constructor
	 * @param	object	$registry
 	*/
	public function __construct($registry) {
		$this->registry = $registry;
	}

	/**
    * Render string view data
    * @param string $template
    * @param array	$data
    * @param string $route
    * @return string
 	*/
	public function view($template, $data = array(), $route = null) {
        if(!$template){
            return;
        }

        if($route){
            // Sanitize the call
            $route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);

            // Keep the original trigger
            $trigger = $route;

            // Trigger the pre events
            $result = $this->registry->get('event')->trigger('view/' . $trigger . '/before', array(&$route, &$data));
        } else {
            $result = false;
        }
		
		
		// Make sure its only the last event that returns an output if required.
		if ($result && !$result instanceof Exception) {
			$output = $result;
		} else if(version_compare(VERSION, '3.0.3.4') < 1) { // Version <= 3.0.3.4
            if(!class_exists('\Twig_Autoloader')){
                include_once(DIR_SYSTEM . 'library/template/Twig/Autoloader.php');
                \Twig_Autoloader::register();
            }
            
            $config = array('autoescape' => false);

            if (Registry::config('template_cache')) {
                    $config['cache'] = DIR_CACHE;
            }
    
            $twig = new \Twig_Environment(new \Twig_Loader_String(), $config);
            $output = $twig->render($template, $data);
        } else {
            $config = array(
                    'autoescape'  => false,
                    'debug'       => false,
                    'auto_reload' => true,
                    'cache'       => DIR_CACHE . 'template/'
            );
            
            if($route){
                $loader = new \Twig\Loader\ArrayLoader(array($route . '.twig' => $template));
                $twig = new \Twig\Environment($loader, $config);
                $output = $twig->render($route . '.twig', $data);
            } else {
                $loader = new \Twig\Loader\ArrayLoader(array());
                $twig = new \Twig\Environment($loader, $config);
                $output = $twig->createTemplate($template)->render($data);
            }
        }

        if($route){
            // Trigger the post events
            $result = $this->registry->get('event')->trigger('view/' . $trigger . '/after', array(&$route, &$data, &$output));
        }
		
		
		if ($result && !$result instanceof Exception) {
			$output = $result;
		}
		
		return $output;
	}
}