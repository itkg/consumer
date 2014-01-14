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
        $ipClient = '';
        if (getenv('HTTP_X_FORWARDED_FOR') === false) {
            if (function_exists('apache_request_headers')){
                $headers = apache_request_headers();
                $ipClient = $headers["X-Forwarded-For"];
            }
            
        } else {
            $ipClient = getenv('HTTP_X_FORWARDED_FOR');
        }
        if (!$ipClient){
            $ipClient =$_SERVER["REMOTE_ADDR"];
        }
        return $ipClient;
    }
    
    /**
     * Retourne le referer
     * 
     * @return string
     */
    public static function getReferer()
    {
        return "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    }
    
}
