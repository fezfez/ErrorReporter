<?php

namespace Corp\ErrorReporter\Sender;

use Corp\ErrorReporter\SenderInterface;
use Corp\ErrorReporter\Formater\TextFormater;
use Corp\ErrorReporter\Formater\HtmlFormater;

class MailSender implements SenderInterface
{
    private $_config = null;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function send(array $data)
    {
        $infos = array(
            'project' => '[' . $this->_config->options->projectName . ' : ' . APPLICATION_ENV . ']',
            'errorId' => '[ERROR_ID : ' . $data['errorID'] . ']',
            'eventId' => '[EVENT ID :' . $data['event_id'] . ']',
        );

        $mail = new \Zend_Mail('utf-8');
        $mail->setBodyText(TextFormater::format($data));
        $mail->setBodyHtml(HtmlFormater::format($data));
        $mail->setFrom($this->_config->options->sender->mail->options->from, $this->_config->options->sender->mail->options->fromName);
        $mail->addTo($this->_config->options->sender->mail->options->to, $this->_config->options->sender->mail->options->toName);
        $mail->setSubject(implode(' ', $infos));
        $mail->send();
    }
}