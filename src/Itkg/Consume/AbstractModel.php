<?php

namespace Itkg\Consume;

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
        // @TODO : Gestion des erreurs
        if($this->validator) {
            $this->validator->validate($this);
        }
    }

    public function hydrate($datas = array())
    {
        $this->getHydrator()->hydrate($this, $datas);
    }

    public function getHydrator()
    {
        if(!$this->hydrator) {
            $this->hydrator = new ArrayHydrator();
        }

        return $this->hydrator;
    }
}