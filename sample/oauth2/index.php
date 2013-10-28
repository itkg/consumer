<?php

ini_set('display_errors', 1);
$loader = require_once '../../vendor/autoload.php';

$loader->add('Consumer\\Sample',__DIR__.'/../');

// Itkg_cache.php contains config && debug is actived
$itkg = new Itkg\Core('../../var/cache/Itkg_cache.php', true);

// Add extensions
$itkg->registerExtension(new \Itkg\Cache\DependencyInjection\ItkgCacheExtension());
$itkg->registerExtension(new \Itkg\Log\DependencyInjection\ItkgLogExtension());
$itkg->registerExtension(new \Itkg\Consumer\DependencyInjection\ItkgConsumerExtension());
$itkg->registerExtension(new \Consumer\Sample\DependencyInjection\ConsumerSampleExtension());

// Load config
$itkg->load();

$oauth = new Itkg\Consumer\Authentication\Provider\OAuth2(
    array(
        "id" => "youtube_oauth",
        "client_id" => "?",
        "user_id" => "?",
        "client_secret" => "?",
        "token_endpoint" => "https://accounts.google.com/o/oauth2/token",
        "authorize_endpoint" => "https://accounts.google.com/o/oauth2/auth",
        "scope" => "https://gdata.youtube.com",
        "redirect_uri" => "http://localhost/itkg_lib/consume/sample/oauth2/callback.php",
        "credentials_in_request_body" => true
    )
);

$token = $oauth->getAuthToken();
print_r($token);