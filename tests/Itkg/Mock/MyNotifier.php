<?php

namespace Itkg\Mock;

use Itkg\Log\Notifiable;

/**
 * Classe MyNotifier
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class MyNotifier implements Notifiable
{

    public function notify()
    {
        echo 'Say hello';
    }
}
