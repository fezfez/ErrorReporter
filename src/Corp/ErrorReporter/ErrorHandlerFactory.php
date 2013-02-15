<?php

namespace Corp\ErrorReporter;

class ErrorHandlerFactory
{
    private static $_instance = null;

    private function __construct()
    {

    }

    /**
     * @return ErrorHandler
     */
    public static function getInstance()
    {
        if(null === self::$_instance) {
            $client = ClientFactory::getInstance();
            $error_handler = new ErrorHandler($client);

            self::$_instance = $error_handler;
        }

        return self::$_instance;
    }
}