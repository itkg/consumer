<?php

namespace Itkg\Consumer;

class Response
{
    /**
     * @var mixed
     */
    protected $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}
