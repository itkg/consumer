<?php

namespace Itkg\Mock\Service;

use Itkg\Service\Validator as BaseValidator;

/**
 * Implementation de Validator (Mock)
 *
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Mock
 */
class Validator extends BaseValidator
{
    public function validate(\Itkg\Service\Model $model)
    {
        return true;
    }
}
