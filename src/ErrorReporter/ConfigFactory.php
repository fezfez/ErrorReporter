<?php

namespace ErrorReporter;

class ConfigFactory
{
    private static $_instance = null;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if(null === self::$_instance) {
            $config = new \Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
            $errorReporterConfig = $config->ErrorReporter;
            if(empty($errorReporterConfig)) {
                throw new \Exception('you have to add a ErrorReporter section in application.ini');
            }

            self::$_instance = $errorReporterConfig;
        }

        return self::$_instance;
    }
}