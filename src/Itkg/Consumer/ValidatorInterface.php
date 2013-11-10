<?php

namespace Itkg\Consumer;

/**
 * Interface ValidatorInterface
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
interface ValidatorInterface
{
    /**
     * Validate an object with specific rules
     *
     * @param AbstractModel $object
     */
    public function validate(AbstractModel $object);
}