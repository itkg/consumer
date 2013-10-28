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

session_start();
$clientConfig = $_SESSION['itkg_consumer_oauth2']['config'];
$id = $_SESSION['itkg_consumer_oauth2']['id'];
echo '<pre>';
print_r($clientConfig);

try {
    $tokenStorage = new \fkooman\OAuth\Client\SessionStorage();
    $httpClient = new \Guzzle\Http\Client();

    $cb = new \fkooman\OAuth\Client\Callback($id, $clientConfig, $tokenStorage, $httpClient);
    $cb->handleCallback($_GET);

    header("HTTP/1.1 302 Found");
    header("Location: ".$_SESSION['itkg_consumer_oauth2']['redirect']);
    exit;
} catch (\fkooman\OAuth\Client\AuthorizeException $e) {
    // this exception is thrown by Callback when the OAuth server returns a
    // specific error message for the client, e.g.: the user did not authorize
    // the request
    die(sprintf("ERROR: %s, DESCRIPTION: %s", $e->getMessage(), $e->getDescription()));
} catch (\Exception $e) {

    // other error, these should never occur in the normal flow
    die(sprintf("ERROR: %s", $e->getMessage()));
}