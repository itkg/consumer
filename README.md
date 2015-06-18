Webservice consumer library
===========================

[![Build Status](https://travis-ci.org/itkg/consumer.png?branch=2.0)](https://travis-ci.org/itkg/consumer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/itkg/consumer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/itkg/consumer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/itkg/consumer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/itkg/consumer/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c02f50ba-599c-4f7a-aa41-45e10a7aa839/small.png)](https://insight.sensiolabs.com/projects/c02f50ba-599c-4f7a-aa41-45e10a7aa839)
## features
* Rest & Soap consumer library
* Webservice caching
* Logging
* OAuth management

## Installation

### Installation by Composer

If you use composer, add library as a dependency to the composer.json of your application

```json
    "require": {
        "itkg/consumer": "dev-master"
    }

```

## Usage 

Simple example of Rest WS

```php

$service =  new \Itkg\Consumer\Service\Service(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'identifier' => 'my test'
    )
);

$response = $service
    ->sendRequest(\Symfony\Component\HttpFoundation\Request::create('http://URL/OF/MY/WEBSERVICE.json'))
    ->getResponse();

```
## Configuration

### Logs

* Create a new logger instance 

```php

$service =  new \Itkg\Consumer\Service\Service(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'identifier' => 'my test',
        'logger'     => new \Monolog\Logger('my_logger', array(new \Monolog\Handler\StreamHandler('/tmp/test'))),
    )
);

```

* Add logger listener to your event dispatcher

```php

$eventDispatcher->addSubscriber(new \Itkg\Consumer\Listener\LoggerListener());

```

### Serialization

* Add Deseralizer listener to your event dispatcher (Create serializer with JMS serializer builder)

```php

    $eventDispatcher->addSubscriber(
        new \Itkg\Consumer\Listener\DeserializerListener(JMS\Serializer\SerializerBuilder::create()->build())
    );

```

* Define response_type & response_format

```php

$service =  new \Itkg\Consumer\Service\Service(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'identifier' => 'my test',
        'reponse_format' => 'xml,
        'response_type'  => 'My\Class
    )
);

```

* Get deserialized content like this 

```php

$object = $service->getResponse()->getDeserializedContent();

```

### Cache

* Add Cache listener to your event dispatcher


```php

    $eventDispatcher->addSubscriber(
        new \Itkg\Consumer\Listener\CacheListener($eventDispatcher)
    );

```

* Add cache adapter to your service (see : https://github.com/itkg/core for adapters list)

```php

$service =  new \Itkg\Consumer\Service\Service(
    $eventDispatcher,
    new Itkg\Consumer\Client\RestClient(array(
        'timeout' => 10
    )),
    array(
        'identifier' => 'my test',
        'cache_adapter' => new \Itkg\Core\Cache\Adapter\Registry(),
        'cache_ttl => 10000
    )
);

```

* You can define serialize & deserialize method by defining 'cache_serializer' & 'cache_deserializer' options
