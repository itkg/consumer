<?php

namespace Itkg\Http;

/**
 * Classe Client
 *
 * Cette classe modélise un client HTTP
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class Client
{
    /**
     * Retourne l'IP du client
     *
     * @return string
     */
    public static function getIp()
    {
        if (getenv('HTTP_X_FORWARDED_FOR') === false && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            return $headers["X-Forwarded-For"];
        }

        if (getenv('HTTP_X_FORWARDED_FOR')) {
            return getenv('HTTP_X_FORWARDED_FOR');
        }

        return $_SERVER["REMOTE_ADDR"];
    }

    /**
     * Retourne le referer
     *
     * @return string
     */
    public static function getReferer()
    {
        return "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }
}
