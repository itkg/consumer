<?php

namespace Itkg\Consumer;

use Itkg\Consumer\Hydrator\ArrayHydrator;
use Itkg\Consumer\Hydrator\Simple;

/**
 * Class AbstractModel
 * @package Itkg\Consumer
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class AbstractModel
{
    /**
     * @var Val
     */
    protected $validator;
    protected $errors;
    protected $hydrator;
    protected $data;

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

    public function bind($data = array())
    {
        $this->setData($data);
        $this->hydrate($data);

        $this->validate();
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data = array())
    {
        $this->data = $data;
    }
    public function validate()
    {
        if($this->validator) {
            $this->validator->validate($this);
        }
    }

    public function hydrate($data = array(), $options = array())
    {
        if($this->hasHydrator()) {
            $this->getHydrator()->hydrate($this, $data, $options);
        }else {
            $this->data = $data;
        }
    }

    public function getHydrator()
    {
        return $this->hydrator;
    }

    public function hasHydrator()
    {
        return (null != $this->getHydrator());
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
