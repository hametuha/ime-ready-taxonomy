<?php

namespace IRT;

/**
 * Class Singleton
 *
 * Singleton pattern implements
 *
 * @package IRT
 * @author Takahashi Fumiki
 * @since 1.0
 */
abstract class Singleton
{

    /**
     * Instance store
     *
     * @var array
     */
    private static $instances = array();

    /**
     * Constructor
     */
    abstract  protected function __construct();

    /**
     * Accessor
     *
     * @return self
     */
    public static function get_instance(){
        $class_name = get_called_class();
        if( !isset(self::$instances[$class_name]) || is_null(self::$instances[$class_name])){
            self::$instances[$class_name] = new $class_name();
        }
        return self::$instances[$class_name];
    }

    /**
     * Return get key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key){
        if( isset($_GET[$key]) ){
            return $_GET[$key];
        }else{
            return null;
        }
    }
}
