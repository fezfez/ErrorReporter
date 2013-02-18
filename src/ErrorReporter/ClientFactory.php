<?php

namespace ErrorReporter;

class ClientFactory
{
    private static $_instance = null;

    /**
     * @return ErrorHandler
     */
    private function __construct()
    {

    }

    /**
     * @return Client
     */
    public static function getInstance()
    {
        if(null === self::$_instance) {
            $config      = ConfigFactory::getInstance();
            $sender      = $config->options->sender->type;
            $senderClass = 'ErrorReporter\Sender\\' . $sender . 'Sender';

            if(empty($sender)) {
                throw new \Exception('You have to define a sender type');
            } elseif(!class_exists($senderClass)) {
                throw new \Exception('this sender type "' . $sender . '" does not exist');
            } else {
                $sender = new $senderClass($config);
            }

            $infosCollector = new InfosCollector();
            $client         = new Client($sender, $infosCollector, $config);

            self::$_instance = $client;
        }

        return self::$_instance;
    }
}