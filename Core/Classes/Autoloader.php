<?php

class VconstAutoloader
{
    public static function register()
    {
        spl_autoload_register(function($class) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            if (file_exists(PLUGIN_PATH.$file)) {
                include(PLUGIN_PATH.$file);
                return true;
            }
            return false;
        });
    }
}
