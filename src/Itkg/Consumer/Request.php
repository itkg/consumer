<?php

namespace Itkg\Consumer;

class Request extends \Symfony\Component\HttpFoundation\Request
{
    /**
     * Get request identifier
     *
     * @return string
     */
    public function getHash()
    {
        return md5(sprintf('%s_%s', $this->getPathInfo(), $this->getContent()));
    }
}
