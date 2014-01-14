<?php

namespace Itkg\Exception;

/**
 * Classe Exception pour les access denied
 * Gère également un niveau de droits pour différencier les actions en
 * fonction du type d'utilisateur
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 *
 * @package \Itkg\Exception
 */
class UnauthorizedException extends \Exception
{
    const NON_ABONNE = 0;
    const NON_INSCRIT = 1;
    const MAUVAIS_PROFIL = 2;

}
