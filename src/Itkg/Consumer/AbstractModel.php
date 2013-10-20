<?php

namespace Itkg\Consumer;

use Itkg\Consumer\Hydrator\ArrayHydrator;
use Itkg\Consumer\Hydrator\Simple;

class AbstractModel
{
    protected $validator;
    protected $errors;
    protected $hydrator;
    protected $datas;

    public function getValidator()
    {
        return $this->validator;
    }

    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    public function isValid()
    {
        return (empty($this->errors));
    }

    public function getErrors()
    {
        return $this->errors;
    }
    public function setErrors(array $errors = array())
    {
        $this->errors = $errors;
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    public function bind($datas = array())
    {
        $this->setDatas($datas);
        $this->hydrate($datas);

        $this->validate();
    }

    public function getDatas()
    {
        return $this->parameters;
    }

    public function setDatas($datas = array())
    {
        $this->datas = $datas;
    }
    public function validate()
    {
        if($this->validator) {
            $this->validator->validate($this);
        }
    }

    public function hydrate($datas = array(), $options = array())
    {
        if($this->hasHydrator()) {
            $this->getHydrator()->hydrate($this, $datas, $options);
        }else {
            $this->datas = $datas;
        }
    }

    public function getHydrator()
    {
        return $this->hydrator;
    }

    public function hasHydrator()
    {
        return (null != $this->hydrator);
    }

    public function toLog()
    {}

    public function toArray()
    {
        return \get_object_vars($this);
    }

    public function toJson()
    {
        return json_encode($this);
    }
}
