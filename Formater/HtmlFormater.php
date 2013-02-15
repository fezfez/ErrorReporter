<?php

namespace Corp\ErrorReporter\Formater;

use Corp\ErrorReporter\Pretty\ArrayPretty;

class HtmlFormater
{
    public static function format(array $data)
    {
        $html = '
        <style type="text/css">
        body {
        color: #465262;
        font-family: "Helvetica Neue",helvetica,sans-serif;
        font-size: 13px;
        line-height: 20px;
        padding: 25px 30px 10px;
        }
        .module {
        margin-bottom: 20px;
        }
        section.body .page-header:first-child {
        margin-top: 0;
        }
        .page-header {
        border-bottom: 0 none;
        margin: 20px 0;
        padding-bottom: 0;
        }
        .page-header {
        border-bottom: 1px solid #EEEEEE;
        margin: 20px 0 30px;
        padding-bottom: 9px;
        }
        .table-striped {
        border-top: 2px solid #F4F6F9;
        }
        .table {
        margin-bottom: 20px;
        width: 100%;
        }
        table {
        background-color: transparent;
        border-collapse: collapse;
        border-spacing: 0;
        max-width: 100%;
        }
        .table-striped tbody tr:nth-child(2n+1) td, .table-striped tbody tr:nth-child(2n+1) th {
        background-color: #F4F6F9;
        }
        .table-striped tbody tr:nth-child(2n+1) td, .table-striped tbody tr:nth-child(2n+1) th {
        background-color: #F9F9F9;
        }
        table.vars td, table.vars th {
        border: 0 none;
        vertical-align: top;
        }
        table.table th, table.vars th, table.table td, table.vars td {
        border: medium none;
        padding: 5px 8px;
        }
        table.table th, table.vars th, table.table td, table.vars td {
        border: medium none;
        padding: 5px 8px;
        }
        table.table th {
        vertical-align: top;
        }
        .table th {
        font-weight: bold;
        }
        .table th, .table td {
        border-top: 1px solid #DDDDDD;
        line-height: 20px;
        padding: 8px;
        text-align: left;
        vertical-align: top;
        }
        table.table td.values, table.vars td.values, table.table td.code, table.vars td.code {
        overflow: hidden;
        }
        table.vars td, table.vars th {
        border: 0 none;
        vertical-align: top;
        }
        table.table th, table.vars th, table.table td, table.vars td {
        border: medium none;
        padding: 5px 8px;
        }
        table.table th, table.vars th, table.table td, table.vars td {
        border: medium none;
        padding: 5px 8px;
        }
        table.table td {
        vertical-align: middle;
        }
        .table th, .table td {
        border-top: 1px solid #DDDDDD;
        line-height: 20px;
        padding: 8px;
        text-align: left;
        vertical-align: top;
        }
        table.table td.values > pre, table.vars td.values > pre, table.table td.code > pre, table.vars td.code > pre {
        background-color: inherit;
        border: 0 none;
        color: rgba(0, 0, 0, 0.75);
        margin-bottom: 0;
        padding: 1px 3px;
        }
        table.table td.values > pre, table.vars td.values > pre, table.table td.code > pre, table.vars td.code > pre {
        background-color: inherit;
        border: 0 none;
        color: rgba(0, 0, 0, 0.75);
        margin-bottom: 0;
        padding: 1px 3px;
        }
        .module-content table pre {
        margin-bottom: 0;
        }
        pre {
        border: 0 none;
        }
        pre {
        background-color: #F5F5F5;
        border: 1px solid rgba(0, 0, 0, 0.15);
        border-radius: 4px 4px 4px 4px;
        display: block;
        font-size: 12px;
        line-height: 20px;
        margin: 0 0 10px;
        padding: 9.5px;
        white-space: pre-wrap;
        word-break: break-all;
        word-wrap: break-word;
        }
        code, pre {
        border-radius: 3px 3px 3px 3px;
        color: #404F60;
        font-family: Monaco,Menlo,Consolas,"Courier New",monospace;
        font-size: 11px;
        padding: 0 3px 2px;
        }
        .page-header h2 {
        font-weight: normal;
        }
        h2 {
        font-size: 24px;
        line-height: 36px;
        }
        h1, h2, h3, h4, h5, h6 {
        margin: 0;
        }
        h2 {
        font-size: 29.25px;
        }
        h1, h2, h3 {
        line-height: 40px;
        }
        h1, h2, h3, h4, h5, h6 {
        color: inherit;
        font-family: inherit;
        font-weight: bold;
        line-height: 20px;
        margin: 10px 0;
        text-rendering: optimizelegibility;
        }
        </style>
        <div class="module">
        <div class="page-header">
        <h2>Exception</h2>
        </div>
        <div class="module-content">
        <table class="table table-striped">
        <colgroup>
        <col style="width:100px;">
        </colgroup>
        <tbody>
        <tr>
        <th>Types:</th>
        <td class="code"><pre>' . $data['sentry.interfaces.Exception']['type'] . '</pre></td>
        </tr>
        <tr>
        <th>Value:</th>
        <td class="code"><pre>' . $data['message'] . '</pre></td>
        </tr>
        <tr>
        <th>Location:</th>
        <td>' . $data['sentry.interfaces.Exception']['module'] . '</td>
        </tr>
        <tr>
        <th>Error id:</th>
        <td>' . $data['errorID'] . '</td>
        </tr>
        <tr>
        <th>Event id:</th>
        <td>' . $data['event_id'] . '</td>
        </tr>
        </tbody>
        </table>
        </div>
        </div>
        <div id="stacktrace">
        <div class="module">
        <div class="page-header">
        <h2>
        Stacktrace
        <small>
        (most recent call last)
        </small>
        </h2>
        </div>
        <div class="module-content">
        <div class="raw_stacktrace">
        <pre>' . $data['sentry.interfaces.Stacktrace']['raw'] . '</pre>
        </div>
        </div>
        </div>
        </div>
        
        <div id="http">
        <div class="module">
        <div class="page-header">
        <h2>Requête</h2>
        </div>
        <div class="module-content">
        <table class="table table-striped vars">
        <colgroup>
        <col style="width:130px;">
        </colgroup>
        <tbody>
        <tr>
        <th>URL</th>
        <td><a href="' . $data['sentry.interfaces.Http']['url'] . '">' . $data['sentry.interfaces.Http']['url'] . '</a></td>
        </tr>
        <tr>
        <th>Méthode:</th>
        <td>' . $data['sentry.interfaces.Http']['method'] . '</td>
        </tr>
        <tr>
        <th>Requête:</th>
        <td class="values">
        <pre>' . $data['sentry.interfaces.Http']['query_string'] . '</pre>
        </td>
        </tr>
        <tr>
        <th>En-têtes:</th>
        <td class="values">
        <table class="table table-striped vars">
        <colgroup>
        <col style="width:100px;">
        </colgroup>
        <tbody>
        ';
        foreach($data['sentry.interfaces.Http']['headers'] as $key => $value):
        $html .= '
        <tr>
        <th>' . $key . '</th>
        <td class="values">
        <pre>' . $value . '</pre>
        </td>
        </tr>';
        endforeach;
        $html .= '
        </tbody>
        </table>
        </td>
        </tr>
        <tr>
        <th>Environnement:</th>
        <td class="values">
        <table class="table table-striped vars">
        <colgroup>
        <col style="width:100px;">
        </colgroup>
        <tbody>
        ';
        foreach($data['sentry.interfaces.Http']['env'] as $key => $value):
        $html .= '
        <tr>
        <th>' . $key . '</th>
        <td class="values">
        <pre>' . $value . '</pre>
        </td>
        </tr>';
        endforeach;

        $html .= '
        </tbody>
        </table>
        </td>
        </tr>
        </tbody>
        </table>
        </div>
        </div>
        </div>
        <div id="user">
        <div class="module" id="request">
        <div class="page-header">
        <h2>Utilisateur</h2>
        </div>
        <div class="module-content">
        <table class="table table-striped vars">
        <colgroup>
        <col style="width:130px;">
        </colgroup>
        <tbody>
        <tr>
        <th>ID:</th>
        <td class="code">' . $data['sentry.interfaces.User']['id'] . '</td>
        </tr>
        <tr>
        <th>Data</th>
        <td class="code"><pre>' . ArrayPretty::format($data['sentry.interfaces.User']['data']) . '</pre></td>
        </tr>
        </tbody>
        </table>
        </div>
        </div>
        
        </div>
        ';

        return $html;
    }
}