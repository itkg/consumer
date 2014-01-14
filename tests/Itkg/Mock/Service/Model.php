<?php

namespace Itkg\Mock\Service;

use Itkg\Service\Model as BaseModel;

/**
 * Implementation de Model (Mock)
 *
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Mock
 */
class Model extends BaseModel
{
    protected $login;
    protected $password;
    
    public function validate()
    {
        return $this->validator->validate($this);
    }
}
