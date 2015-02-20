<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

require_once __DIR__.'/../vendor/autoload.php';

$session = new Session();

$oauth = null;
if ($session->has('itkg_consumer.oauth2')) {
    // oauth 2.0
    $oauth = $session->get('itkg_consumer.oauth2');
} else if($session->has('itkg_consumer.oauth')) {
    // oauth 1.0A
    $oauth = $session->get('itkg_consumer.oauth');
}

// Handle callback
if ($oauth) {
    $oauth->handleCallback(Request::createFromGlobals());
}
