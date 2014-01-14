<?php

\Itkg::$config['SERVICE'] = array(
    'class' => 'Itkg\Mock\Service',
    'configuration' => 'Itkg\Mock\Service\Configuration'
);

\Itkg::$config['BATCH'] = array(
    'class' => 'Itkg\Mock\Batch\Hello',
    'configuration' => 'Itkg\Mock\Batch\Hello\Configuration'
);

\Itkg::$config['BATCH_CONFIGURATION'] = 'Itkg\Mock\Batch\Configuration';

\Itkg::$config['SERVICE']['PARAMETERS'] = array(
    'location' => 'http://webservices.canal.dev:8081',
    'signature'=> 'signature',
    'login'    => 'login',
    'password' => 'password',
    'timeout'  => 10,
    'wsdl'     => 'service.wsdl'
);
