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
     * Validator object
     *
     * @var ValidatorInterface
     */
    protected $validator;
    /**
     * List of errors
     *
     * @var array
     */
    protected $errors;
    /**
     * Hydrator object
     *
     * @var HydratorInterface
     */
    protected $hydrator;
    /**
     * List of keys/values
     *
     * @var array
     */
    protected $data;

    /**
     * Getter Validator
     *
     * @return ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Setter validator
     *
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * setter hydrator
     *
     * @param HydratorInterface $hydrator
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * If no errors
     *
     * @return bool
     */
    public function isValid()
    {
        return (empty($this->errors));
    }

    /**
     * Getter errors
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Setter errors
     *
     * @param array $errors
     */
    public function setErrors(array $errors = array())
    {
        $this->errors = $errors;
    }

    /**
     * Add error to the list
     *
     * @param $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Bind object with data and validate data
     *
     * @param array $data
     */
    public function bind($data = array())
    {
        $this->setData($data);
        $this->hydrate($data);

        $this->validate();
    }

    /**
     * Getter data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Setter data
     *
     * @param array $data
     */
    public function setData($data = array())
    {
        $this->data = $data;
    }

    /**
     * Validate object with validator
     */
    public function validate()
    {
        if ($this->validator) {
            $this->validator->validate($this);
        }
    }

    /**
     * Hydrate object with data
     *
     * @param array $data Data to inject
     * @param array $options Speicfic hydrator options
     */
    public function hydrate($data = array(), $options = array())
    {
        if ($this->hasHydrator()) {
            $this->getHydrator()->hydrate($this, $data, $options);
        } else {
            $this->data = $data;
        }
    }

    /**
     * Getter hydrator
     *
     * @return mixed
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }

    /**
     * Hydrator is set?
     * @return bool
     */
    public function hasHydrator()
    {
        return (null != $this->getHydrator());
    }

    /**
     * Represent object for logging
     *
     * @return string
     */
    public function toLog()
    {
        return '';
    }

    /**
     * Array representation of object
     *
     * @return array
     */
    public function toArray()
    {
        return \get_object_vars($this);
    }

    /**
     * Json representation ob object
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }
}