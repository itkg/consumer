<?php

namespace Itkg\Exception;

/**
 * Classe d'exception pour les objets non trouvés
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 * 
 * @package \Itkg\Exception
 */
class ValidationException extends \Exception
{
    protected $aMessages;

    public function __construct ($aMessages = array(), $message = null, $code = 0, $previous = null) 
    {
        parent::__construct($message, $code, $previous);
        $this->aMessages = $aMessages;

        return $this;
    }
    
    public function getAllMessages()
    {
        return $this->aMessages;        
    }
    
    
    public function getParameterMessage($parameter)
    {
        return $this->aMessages[$parameter];        
    }
}