<?php

namespace Itkg\Ldap;

use Zend\Ldap\Ldap;

/**
 * Classe Client pour les appel Ldap
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Ldap
 */
class Client extends Ldap
{
    /**
     * Retrouve un compte dans l'annuaire
     *
     * @param string $method La méthode a appeler
     * @param string $request Le nom du compte à retrouver
     * @param array $attributes Les attributs à récupérer
     * @param
     * @return mixed
     */
    public function call($method, $request, $attributes = array())
    {
        // a terminer
        $this->connect();
        $account = $this->$method($request, $attributes);

        return $account;
    }
}
    