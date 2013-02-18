<?php

namespace Corp\ErrorReporter;

class InfosCollector
{
    public function is_http_request()
    {
        return isset($_SERVER['REQUEST_METHOD']);
    }

    public function get_http_data()
    {
        $env = $headers = array();

        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                if (in_array($key, array('HTTP_CONTENT_TYPE', 'HTTP_CONTENT_LENGTH'))) {
                    continue;
                }
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
            } elseif (in_array($key, array('CONTENT_TYPE', 'CONTENT_LENGTH'))) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))))] = $value;
            } else {
                $env[$key] = $value;
            }
        }

        return array(
            'sentry.interfaces.Http' => array(
                'method' => $this->_server_variable('REQUEST_METHOD'),
                'url' => $this->get_current_url(),
                'query_string' => $this->_server_variable('QUERY_STRING'),
                'data' => $_POST,
                'cookies' => $_COOKIE,
                'headers' => $headers,
                'env' => $env,
            )
        );
    }

    public function get_user_data()
    {
        return array(
            'sentry.interfaces.User' => array(
                'is_authenticated' => isset($_SESSION) && count($_SESSION) ? true : false,
                'id' => session_id(),
                'data' => isset($_SESSION) ? $_SESSION : null,
            )
        );
    }

    public function get_extra_data()
    {
        return array();
    }

    public function get_auth_header($signature, $timestamp, $api_key=null)
    {
        $header = array(
                        sprintf("sentry_timestamp=%F", $timestamp),
        );
        if (!empty($signature)) {
            $header[] = "sentry_signature={$signature}";
        }

        if ($api_key) {
            $header[] = "sentry_key={$api_key}";
        }

        return sprintf('Sentry %s', implode(', ', $header));
    }

    /**
     * Return the URL for the current request
     */
    public function get_current_url()
    {
        // When running from commandline the REQUEST_URI is missing.
        if ($this->_server_variable('REQUEST_URI') === '') {
            return null;
        }

        $schema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                        || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        return $schema . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * @param unknown_type $key
     * @return unknown|string
     */
    public function _server_variable($key)
    {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return '';
    }

    
    /**
     * Generate an uuid4 value
     */
    public function uuid4()
    {
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );

        return str_replace('-', '', $uuid);
    }
}