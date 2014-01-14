<?php

namespace Itkg\Service;

/**
 * Classe abstraite de validation d'un Model
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @abstract
 * @package \Itkg
 */
abstract class Validator
{
    /**
     * @abstract
     * @param \Itkg\Service\Model $model Le modèle à valider
     */
    public abstract function validate(Model $model);
}
