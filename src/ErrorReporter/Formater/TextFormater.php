<?php

namespace ErrorReporter\Formater;

use ErrorReporter\Pretty\ArrayPretty;

class TextFormater
{
    /**
     * @param array $data
     * @return string
     */
    public static function format(array $data)
    {
        $text = '
        Exception : #' . $data['event_id'] . "\n\n" . '
        Types : ' . $data['sentry.interfaces.Exception']['type'] . "\n" . '
        Value : ' . $data['message'] . "\n" . '
        Location: : ' . $data['sentry.interfaces.Exception']['module'] . "\n\n" . '
        Stacktrace : ' . $data['sentry.interfaces.Stacktrace']['raw'] . "\n\n" . '
        Requête : ' . "\n\n" . '
        URL : ' . $data['sentry.interfaces.Http']['url'] . "\n" . "\n\n" . '
        Méthode : ' . $data['sentry.interfaces.Http']['method'] . "\n\n" . '
        Requête: ' . $data['sentry.interfaces.Http']['query_string'] . '
        En-têtes:</th>';
        foreach($data['sentry.interfaces.Http']['headers'] as $key => $value):
        $text .= '' . $key . '' . $value . '';
        endforeach;
        $text .= 'Environnement:';
        foreach($data['sentry.interfaces.Http']['env'] as $key => $value):
        $text .= '' . $key . '' . $value . '';
        endforeach;

        $text .= '
        Utilisateur
        ID : ' . $data['sentry.interfaces.User']['id'] . '
        Data : ' . ArrayPretty::format($data['sentry.interfaces.User']['data']) . '';

        return $text;
    }
}