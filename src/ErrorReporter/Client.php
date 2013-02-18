<?php

/*
 * This file is part of Raven.
 *
 * (c) Sentry Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Raven PHP Client
 *
 * @package raven
 */


namespace ErrorReporter;

use ErrorReporter\SenderInterface;

class Client
{
    const DEBUG = 'debug';
    const INFO = 'info';
    const WARN = 'warning';
    const WARNING = 'warning';
    const ERROR = 'error';
    const FATAL = 'fatal';

    private $severity_map;
    private $exclude = array();
    private $_config = array(
        
    );
    private $_sender = null;

    /**
     * @param array $options
     */
    public function __construct(SenderInterface $sender, InfosCollector $infosCollector, $options = array())
    {
        $this->_sender = $sender;
        $this->_infosCollection = $infosCollector;

        $this->logger = null;
        $this->servers = null;
        $this->auto_log_stacks = false;
        $this->name = gethostname();
        $this->site = $this->_infosCollection->_server_variable('SERVER_NAME');
        $this->tags = array();
        $this->trace = true;
        $this->severity_map = NULL;

        // XXX: Signing is disabled by default as it is no longer required by modern versions of Sentrys
        $this->signing = false;

        $this->_lasterror = null;
    }

    public function getLastError()
    {
        return $this->_lasterror;
    }

    /**
     * Given an identifier, returns a Sentry searchable string.
     */
    public function getIdent($ident)
    {
        // XXX: We dont calculate checksums yet, so we only have the ident.
        return $ident;
    }

    /**
     * @param string $message
     * @param array $params
     * @param array $level_or_options
     * @param string $stack
     */
    public function captureMessage($message,
                                   $params = array(),
                                   $level_or_options = array(),
                                   $stack = false)
    {
        // Gracefully handle messages which contain formatting characters, but were not
        // intended to be used with formatting.
        if (!empty($params)) {
            $formatted_message = vsprintf($message, $params);
        } else {
            $formatted_message = $message;
        }

        if ($level_or_options === null) {
            $data = array();
        } else if (!is_array($level_or_options)) {
            $data = array(
                'level' => $level_or_options,
            );
        } else {
            $data = $level_or_options;
        }

        $data['message'] = $formatted_message;
        $data['sentry.interfaces.Message'] = array(
            'message' => $message,
            'params' => $params,
        );

        return $this->capture($data, $stack);
    }

    /**
     * @param \Exception $exception
     * @param unknown_type $logger
     * @return Ambiguous|string
     */
    public function captureException($exception, $logger = null)
    {
        if (in_array(get_class($exception), $this->exclude)) {
            return null;
        }

        $exc_message = $exception->getMessage();
        if (empty($exc_message)) {
            $exc_message = '<unknown exception>';
        }

        $data['message'] = $exc_message;
        $data['sentry.interfaces.Exception'] = array(
            'value' => $exc_message,
            'type' => get_class($exception),
            'module' => $exception->getFile() .':'. $exception->getLine(),
        );

        $data['errorID'] = md5(serialize($data['sentry.interfaces.Exception']));

        if ($logger !== null) {
            $data['logger'] = $logger;
        }

        if (empty($data['level'])) {
            if (method_exists($exception, 'getSeverity')) {
                $data['level'] = $this->translateSeverity($exception->getSeverity());
            } else {
                $data['level'] = self::ERROR;
            }
        }

        /**'sentry.interfaces.Exception'
         * Exception::getTrace doesn't store the point at where the exception
         * was thrown, so we have to stuff it in ourselves. Ugh.
         */
        $trace = $exception->getTrace();
        $traceString = $exception->getTraceAsString();
        $frame_where_exception_thrown = array(
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        );

        array_unshift($trace, $frame_where_exception_thrown);

        return $this->capture($data, $trace, $traceString);
    }

    /**
     * @param unknown_type $query
     * @param unknown_type $level
     * @param unknown_type $engine
     * @return string
     */
    public function captureQuery($query, $level = self::INFO, $engine = '')
    {
        $data = array(
            'message' => $query,
            'level' => $level,
            'sentry.interfaces.Query' => array(
                'query' => $query
            )
        );

        if ($engine !== '') {
            $data['sentry.interfaces.Query']['engine'] = $engine;
        }
        return $this->capture($data, false);
    }

    /**
     * @param array $data
     * @param array $stack
     * @param string $traceString
     * @return string
     */
    private function capture($data, $stack, $traceString = '')
    {
        $event_id = $this->_infosCollection->uuid4();

        if (!isset($data['timestamp'])) $data['timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        if (!isset($data['level'])) $data['level'] = self::ERROR;

        $data = array_merge($data, array(
            'server_name' => $this->name,
            'event_id'    => $event_id,
            'project'     => $this->project,
            'site'        => $this->site,
            'logger'      => $this->logger,
            'tags'        => $this->tags
        ));

        if ($this->_infosCollection->is_http_request()) {
            $data = array_merge($data, $this->_infosCollection->get_http_data());
            $data = array_merge($data, $this->_infosCollection->get_user_data());
        }

        if ((!$stack && $this->auto_log_stacks) || $stack === True) {
            $stack = debug_backtrace();

            // Drop last stack
            array_shift($stack);
        }

        if (!empty($stack)) {
            if (!isset($data['sentry.interfaces.Stacktrace'])) {
                $data['sentry.interfaces.Stacktrace'] = array(
                    'frames' => Pretty\StacktracePretty::get_stack_info($stack, $this->trace),
                    'raw'    => $traceString
                );
            }
        }

        if ($extra = $this->_infosCollection->get_extra_data()) {
            $data["extra"] = $extra;
        }

        $this->_sender->send($data);

        return $event_id;
    }

    public function translateSeverity($severity)
    {
        if (is_array($this->severity_map) && isset($this->severity_map[$severity])) {
            return $this->severity_map[$severity];
        }
        switch ($severity) {
            case E_ERROR:              return self::ERROR;
            case E_WARNING:            return self::WARN;
            case E_PARSE:              return self::ERROR;
            case E_NOTICE:             return self::INFO;
            case E_CORE_ERROR:         return self::ERROR;
            case E_CORE_WARNING:       return self::WARN;
            case E_COMPILE_ERROR:      return self::ERROR;
            case E_COMPILE_WARNING:    return self::WARN;
            case E_USER_ERROR:         return self::ERROR;
            case E_USER_WARNING:       return self::WARN;
            case E_USER_NOTICE:        return self::INFO;
            case E_STRICT:             return self::INFO;
            case E_RECOVERABLE_ERROR:  return self::ERROR;
        }
        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
          switch ($severity) {
            case E_DEPRECATED:         return self::WARN;
            case E_USER_DEPRECATED:    return self::WARN;
          }
        }
        return Client::ERROR;
    }
}
