<?php

namespace Itkg\Exception;

/**
 * Classe exception pour les erreurs fonctionnelles
 * Enregistre la trame liée à la requête
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Exception
 */
class FonctionnalException extends \Exception
{
    /**
     * La trame contenant la requête soap
     *
     * @var string
     */
    protected $trame;
    /**
     * Les données transmis dans la trame
     *
     * @var array
     */
    protected $aDatas;

    /**
     * Renvoi la trame
     *
     * @return string
     */
    public function getTrame()
    {
        return $this->trame;
    }

    /**
     * Set trame
     *
     * @param string $trame
     */
    public function setTrame($trame)
    {
        $this->trame = $trame;
    }

    /**
     * Renvoi la données
     *
     * @return array
     */
    public function getDatas()
    {
        return $this->aDatas;
    }

    /**
     * Set datas
     *
     * @param array $aDatas
     */
    public function setDatas($aDatas)
    {
        $this->aDatas = $aDatas;
    }
}
