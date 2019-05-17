<?php

namespace App\Utils\Traits;


trait Singleton
{
    protected static $instance;

    final public static function instance()
    {
        return isset(static::$instance) ? static::$instance : static::$instance = new static;
    }

    final public static function forgetInstance()
    {
        static::$instance = null;
    }

    final protected function __construct()
    {
        $this->init();
    }

    protected function init()
    {

    }

    public function __clone()
    {
        trigger_error('Cloning '.__CLASS__.' is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('UnSerializing '.__CLASS__.' is not allowed.', E_USER_ERROR);
    }
}
