Webservice consumer library
===========================


## features
* Rest & Soap consumer library
* Authentication providers : OAuth 1.0.a (PECL extension) & OAuth 2
* Webservice caching
* Logging & debugging

## Installation

### Installation by Composer

If you use composer, add ExtraFormBundle bundle as a dependency to the composer.json of your application

```php
    "require": {
        ...
        "itkg/consumer": "dev-master"
        ...
    },

```

If you use itkg/core DIC, you can do :

```php
// app/AppKernel.php
<?php
    // ...
    $core = new Itkg\Core('../../var/cache/itkg_cache.php', true);

    // Add extension
    $core->registerExtension(new \Itkg\Consumer\DependencyInjection\ItkgConsumerExtension());
    $core->load();

```

## Usage

* Service

* Request & Response

* Caching

* Authentication providers

* Logging