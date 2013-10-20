<?php

ini_set('display_errors', 1);
session_start();
$loader = require_once '../vendor/autoload.php';

$loader->add('Consumer\\Sample',__DIR__);

use Itkg;

// Itkg_cache.php contains config && debug is actived
$itkg = new Itkg('../var/cache/Itkg_cache.php', true);

// Add extensions
$itkg->registerExtension(new \Itkg\Cache\DependencyInjection\ItkgCacheExtension());
$itkg->registerExtension(new \Itkg\Log\DependencyInjection\ItkgLogExtension());
$itkg->registerExtension(new \Itkg\Consumer\DependencyInjection\ItkgConsumerExtension());
$itkg->registerExtension(new \Consumer\Sample\DependencyInjection\ConsumerSampleExtension());

// Load config
$itkg->load();

try {
    // Call google translate WS
    $shortener = \Itkg::get('google.shortener')->call(array(
        'url' => 'www.canalplus.fr'
    ));
}catch(\Exception $e) {
    print_r($e);
}

try {
    // Call google translate WS
    $shortener = \Itkg::get('google.shortener')->call(array(
            'url' => 'www.canalplay.com'
        ));
}catch(\Exception $e) {
    print_r($e);
}

print_r($shortener);

echo '<br />CACHE stockage : <br />';
print_r(
   $_SESSION
);