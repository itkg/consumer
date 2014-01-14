<?php

namespace Itkg\Http;

/*
 * Classe de manipulation de cookies
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Http
 */
class Cookie
{
    /**
     * Encrypt data
     *
     * Cette méthode encrypte les données en utilisant une clé, un vecteur et un cipher
     * Par défaut, les données seront encryptées avec RIJNDAEL/AES 256 bit cipher
     *
     * @param   string  $data       Les données
     * @param   string  $key        La clé de cryptage
     * @param   string  $iv         Le vecteur d'initialiation
     * @param   array   $settings   couples Clé/valeur pour surchargé les méthodes de cryptage
     * @return  string
     */
    public static function encrypt($data, $key, $iv, $settings = array()) 
    {
        if ( $data === '' || !extension_loaded('mcrypt') ) {
            return $data;
        }

        //Merge settings with defaults
        $settings = array_merge(array(
            'algorithm' => MCRYPT_RIJNDAEL_256,
            'mode' => MCRYPT_MODE_CBC
        ), $settings);
        
        //Get module
        $module = mcrypt_module_open($settings['algorithm'], '', $settings['mode'], '');

        //Validate IV
        $ivSize = mcrypt_enc_get_iv_size($module);
        if ( strlen($iv) > $ivSize ) {
            $iv = substr($iv, 0, $ivSize);
        }

        //Validate key
        $keySize = mcrypt_enc_get_key_size($module);
        if ( strlen($key) > $keySize ) {
            $key = substr($key, 0, $keySize);
        }

        //Encrypt value
        mcrypt_generic_init($module, $key, $iv);
        $res = @mcrypt_generic($module, $data);
        mcrypt_generic_deinit($module);

        return $res;
    }

    /**
     * Decrypte les données
     *
     * Cette méthode décrypte les données en utilisant une clé, un vecteur et un cipher
     * Par défaut, les données seront décryptées avec RIJNDAEL/AES 256 bit cipher
     *
     * @param   string  $data       The encrypted data
     * @param   string  $key        The encryption key
     * @param   string  $iv         The encryption initialization vector
     * @param   array   $settings   Optional key-value array with custom algorithm and mode
     * @return  string
     */
    public static function decrypt($data, $key, $iv, $settings = array()) 
    {
        if ( $data === '' || !extension_loaded('mcrypt') ) {
            return $data;
        }

        //Merge settings with defaults
        $settings = array_merge(array(
            'algorithm' => MCRYPT_RIJNDAEL_256,
            'mode' => MCRYPT_MODE_CBC
        ), $settings);

        //Get module
        $module = mcrypt_module_open($settings['algorithm'], '', $settings['mode'], '');

        //Validate IV
        $ivSize = mcrypt_enc_get_iv_size($module);
        if ( strlen($iv) > $ivSize ) {
            $iv = substr($iv, 0, $ivSize);
        }

        //Validate key
        $keySize = mcrypt_enc_get_key_size($module);
        if ( strlen($key) > $keySize ) {
            $key = substr($key, 0, $keySize);
        }

        //Decrypt value
        mcrypt_generic_init($module, $key, $iv);
        $decryptedData = @mdecrypt_generic($module, $data);
        $res = str_replace("\x0", '', $decryptedData);
        mcrypt_generic_deinit($module);

        return $res;
    }

    /**
     * Encode secure cookie data
     *
     * Crée une valeur secure d'un cookie HTTP
     * La valeur est encryptée et hashée
     *
     * @param   string  $value      The unsecure HTTP cookie value
     * @param   int     $expires    The UNIX timestamp at which this cookie will expire
     * @param   string  $secret     The secret key used to hash the cookie value
     * @param   int     $algorithm  The algorithm to use for encryption
     * @param   int     $mode       The algorithm mode to use for encryption
     * @param   string
     */
    public static function encodeSecureCookie($value, $expires, $secret, $algorithm, $mode) 
    {
        $key = hash_hmac('sha1', $expires, $secret);
        $iv = self::get_iv($expires, $secret);
        $secureString = base64_encode(self::encrypt($value, $key, $iv, array(
            'algorithm' => $algorithm,
            'mode' => $mode
        )));
        $verificationString = hash_hmac('sha1', $expires . $value, $key);
        return implode('|', array($expires, $secureString, $verificationString));
    }

    /**
     * Decode secure cookie value
     *
     * Décode une valeur secure d'un cookie HTTP
     *
     * @param   string  $value      The secure HTTP cookie value
     * @param   int     $expires    The UNIX timestamp at which this cookie will expire
     * @param   string  $secret     The secret key used to hash the cookie value
     * @param   int     $algorithm  The algorithm to use for encryption
     * @param   int     $mode       The algorithm mode to use for encryption
     * @param   string
     */
    public static function decodeSecureCookie($value, $secret, $algorithm, $mode) 
    {
        if ( $value ) {
            $value = explode('|', $value);
            if ( count($value) === 3 && ( (int)$value[0] === 0 || (int)$value[0] > time() ) ) {
                $key = hash_hmac('sha1', $value[0], $secret);
                $iv = self::get_iv($value[0], $secret);
                $data = self::decrypt(base64_decode($value[1]), $key, $iv, array(
                    'algorithm' => $algorithm,
                    'mode' => $mode
                ));
                $verificationString = hash_hmac('sha1', $value[0] . $data, $key);
                if ( $verificationString === $value[2] ) {
                    return $data;
                }
            }
        }
        return false;
    }

    /**
     * Set HTTP cookie header
     *
     * Méthode qui modifie la valeur HTTP `Set-Cookie` header.
     *
     * Modifie le tableau passé en paramètre
     *
     * @TODO : Utilisé la méthode setCookie
     *
     * @param   array   $header
     * @param   string  $name
     * @param   string  $value
     * @return  void
     */
    public static function setCookieHeader(&$header, $name, $value) 
    {
        //Build cookie header
        if ( is_array($value) ) {
            $domain = '';
            $path = '';
            $expires = '';
            $secure = '';
            $httponly = '';
            if ( isset($value['domain']) && $value['domain'] ) {
                $domain = '; domain=' . $value['domain'];
            }
            if ( isset($value['path']) && $value['path'] ) {
                $path = '; path=' . $value['path'];
            }
            if ( isset($value['expires']) ) {
                if ( is_string($value['expires']) ) {
                    $timestamp = strtotime($value['expires']);
                } else {
                    $timestamp = (int)$value['expires'];
                }
                if ( $timestamp !== 0 ) {
                    $expires = '; expires=' . gmdate('D, d-M-Y H:i:s e', $timestamp);
                }
            }
            if ( isset($value['secure']) && $value['secure'] ) {
                $secure = '; secure';
            }
            if ( isset($value['httponly']) && $value['httponly'] ) {
                $httponly = '; HttpOnly';
            }
            $cookie = sprintf('%s=%s%s', urlencode($name), urlencode((string)$value['value']), $domain . $path . $expires . $secure . $httponly);
        } else {
            $cookie = sprintf('%s=%s', urlencode($name), urlencode((string)$value));
        }

        //Set cookie header
        if ( !isset($header['Set-Cookie']) || $header['Set-Cookie'] === '' ) {
            $header['Set-Cookie'] = $cookie;
        } else {
            $header['Set-Cookie'] = implode("\n", array($header['Set-Cookie'], $cookie));
        }
        
    }

    /**
     * Supprime un cookie HTTP du header passé en paramètre
     *
     * @param   array   $header
     * @param   string  $name
     * @param   string  $value
     * @return  void
     */
    public static function deleteCookieHeader(&$header, $name, $value = array()) 
    {
        //Remove affected cookies from current response header
        $cookiesOld = array();
        $cookiesNew = array();
        if ( isset($header['Set-Cookie']) ) {
            $cookiesOld = explode("\n", $header['Set-Cookie']);
        }
        foreach ( $cookiesOld as $c ) {
            if ( isset($value['domain']) && $value['domain'] ) {
                $regex = sprintf('@%s=.*domain=%s@', urlencode($name), preg_quote($value['domain']));
            } else {
                $regex = sprintf('@%s=@', urlencode($name));
            }
            if ( preg_match($regex, $c) === 0 ) {
                $cookiesNew[] = $c;
            }
        }
        if ( $cookiesNew ) {
            $header['Set-Cookie'] = implode("\n", $cookiesNew);
        } else {
            unset($header['Set-Cookie']);
        }

        //Set invalidating cookie to clear client-side cookie
        self::setCookieHeader($header, $name, array_merge(array('value' => '', 'path' => null, 'domain' => null, 'expires' => time() - 100), $value));
    }

    /**
     * Récupère les cookies du header et les renvoi à travers un
     * tableau associatif
     *
     * @param   string
     * @return  array
     */
    public static function parseCookieHeader($header) 
    {
        $cookies = array();
        $header = rtrim($header, "\r\n");
        $headerPieces = preg_split('@\s*[;,]\s*@', $header);
        foreach ( $headerPieces as $c ) {
            $cParts = explode('=', $c);
            if ( count($cParts) === 2 ) {
                $key = urldecode($cParts[0]);
                $value = urldecode($cParts[1]);
                if ( !isset($cookies[$key]) ) {
                    $cookies[$key] = $value;
                }
            }
        }
        return $cookies;
    }

    /**
     * Genere un IV random
     *
     * @param   int     $expires    The UNIX timestamp at which this cookie will expire
     * @param   string  $secret     The secret key used to hash the cookie value
     * @return  binary string with length 40
     */
    private static function get_iv($expires, $secret) 
    {
        $data1 = hash_hmac('sha1', 'a'.$expires.'b', $secret);
        $data2 = hash_hmac('sha1', 'z'.$expires.'y', $secret);
        
        return pack("h*", $data1.$data2);
    }
}
