<?php

namespace Itkg\Mock;

use Itkg\Service as BaseService;

/**
 * Implementation de Service (Mock)
 *
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Mock
 */
class Service extends BaseService 
{
    protected $client = null;
    
    public function init() {
    }
    
    public function monitor() {
        
    }
}
