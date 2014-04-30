<?php

/**
 * Contient les paramètres de la librairie Itkg
 * Les paramêtres par défaut de la librairie sont définis ici
 *
 * @author Pascal DENIS pascal.denis@businessdecision.com
 */
class Itkg
{
    /**
     * Conteneur de paramêres
     *
     * @static
     * @var array
     */
    public static $config = array(
        'LOG_PATH' => '/var/logs',
        'TYPE_ENVIRONNEMENT' => 'dev',
        'LOG' => array(
            'WRITERS' => array(
                'syslog' => 'Itkg\Log\Writer\SysLogWriter',
                'error_log' => 'Itkg\Log\Writer\ErrorLogWriter',
                'echo' => 'Itkg\Log\Writer\EchoWriter',
                'soap' => 'Itkg\Log\Writer\SoapWriter',
                'file' => 'Itkg\Log\Writer\FileWriter'
            ),
            'FORMATTERS' => array(
                'simple' => 'Itkg\Log\Formatter\SimpleFormatter',
                'string' => 'Itkg\Log\Formatter\StringFormatter',
                'xml' => 'Itkg\Log\Formatter\XMLFormatter'
            ),
            'DEFAULT_WRITER' => 'echo',
            'DEFAULT_FORMATTER' => 'string',
        ),
        'TEMP_ROOT' => '/tmp',
        'AUTHENTICATION_PROVIDERS' => array(
            'oauth' => 'Itkg\Authentication\Provider\OAuth',
            'oauth2' => 'Itkg\Authentication\Provider\OAuth2'
        )
    );
}
