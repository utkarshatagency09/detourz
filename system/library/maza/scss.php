<?php
/**
 * @package		Maza
 * @author		Jay Padaliya
 * @copyright   Copyright (c) 2021, Templatemaza.com
 * @license		per domain
 * @link		https://www.templatemaza.com
*/
namespace maza;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\ValueConverter;
use ScssPhp\ScssPhp\OutputStyle;
use Padaliyajay\PHPAutoprefixer\Autoprefixer;


/**
* Scss class
*/
class Scss extends Library{
	private $compiler;
        
    /**
	 * Constructor
	 *
 	*/
	public function __construct() {
        $this->compiler = new Compiler();
	}
	
        
    /**
     * Get value of property
     * @param string $prop property name
     * @return mixed value
     */
    public function __get($prop) {
            return $this->compiler->$prop;
    }
    
    /**
     * Set property value to compiler
     * @param string $prop property name
     * @param mixed $value property value
     */
    public function __set($prop, $value) {
		$this->compiler->$prop = $value;
	}
        
    /**
     * Call methods of compiler
     * @param string $method method name
     * @param array $arguments method arguments
     * @return mixed return data of method
     */
    public function __call($method, $arguments) {
        return call_user_func_array(array($this->compiler, $method), $arguments);
    }
    
    /**
     * Compile SASS to css and autoprefix CSS vendor
     * @param string $sass sass code
     * @param boolean $autoprefix css autoprefix
     * @return string CSS code
     */
    public function compile(string $sass, bool $autoprefix = true, bool $compressed = false): string {
        if ($compressed) {
            $this->compiler->setOutputStyle(OutputStyle::COMPRESSED);
        }

        $css = $this->compiler->compileString($sass)->getCss();
        
        if($autoprefix){
            $autoprefixer = new Autoprefixer($css);
            $autoprefixer->setVendors(array(
                \Padaliyajay\PHPAutoprefixer\Vendor\Webkit::class,
                \Padaliyajay\PHPAutoprefixer\Vendor\Mozilla::class,
            ));            
            return $autoprefixer->compile();
        } else {
            return $css;
        }
    }

    public function setVariables(array $variables): void {
        $this->compiler->addVariables(array_map(function($variable){
            return ValueConverter::fromPhp($variable);
        }, $variables));
    }

    /**
     * Convert PHP array to Scss variables
     * @param array $data Array of variables
     * @return string Scss variables
     */
    public static function getVariableFormat(array $data): string {
        $sass_var = '';
        
        foreach($data as $name => $value){
            if ($name[0] === '$') {
                $name = substr($name, 1);
            }
            
            if(!in_array($value, [null, '', []], true)){
                $sass_var .= '$' . $name . ': ' . self::toSassType($value) . ';' . PHP_EOL;
            }
        }
        
        return $sass_var;
    }
    
    /**
     * Convert php data type to Sass type format string
     * @param mixed $data PHP type
     * @return string Sass type
     */
    protected static function toSassType($data): string {
        if(is_bool($data)) { // Boolean type
            return $data?'true':'false';
        } elseif(is_null($data)) { // Null type
            return 'null';
        } elseif(is_string($data)) { // string type
            return $data;
        } elseif(is_array($data)) { // array type
            $key_pair = array();
            foreach($data as $arr_key => $arr_val){
                $key_pair[] = $arr_key . ': ' . self::toSassType($arr_val);
            }
            return '(' . implode(', ', $key_pair) . ')';
        }
        
        return $data;
    }
        
}
