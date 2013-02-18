<?php

namespace ErrorReporter;

interface SenderInterface
{
    public function __construct($config);
    public function send(array $data);
}