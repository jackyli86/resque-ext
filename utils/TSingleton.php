<?php

namespace we\utils;


trait TSingleton
{

    private static $_instance;

    private function __construct()
    {
    }

    /**
     * @return static
     */
    public final static function instance()
    {
        $className = self::getClassName();
        if (!(self::$_instance instanceof $className)) {
            self::$_instance = new $className();
            self::$_instance->initialize();
        }
        return self::$_instance;
    }

    protected static function getClassName()
    {
        return get_called_class();
    }

    /*
    public final static function newInstance()
    {
        $className = self::getClassName();

        $newInstance = new $className();
        $newInstance->initialize();

        return $newInstance;
    }
    */

    final public function reinitialize()
    {
        $this->initialize();
    }

    protected function initialize()
    {
    }

    private function __clone()
    {
    }
}