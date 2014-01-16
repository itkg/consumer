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
    /**
     * Messages validation
     *
     * @var array
     */
    protected $aMessages;

    /**
     * Constructor
     * @param array $aMessages Messages validation
     * @param null $message Main message
     * @param int $code Exception code
     * @param null $previous
     */
    public function __construct($aMessages = array(), $message = null, $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->aMessages = $aMessages;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public function getAllMessages()
    {
        return $this->aMessages;
    }

    /**
     * Get specific message
     *
     * @param string $key key
     * @return mixed
     */
    public function getParameterMessage($key)
    {
        return $this->aMessages[$key];
    }
}
