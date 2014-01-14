<?php

namespace Itkg\Service;

use Itkg\Exception\UnauthorizedException;
use Itkg\Exception\NotFoundException;

/**
 * Classe de création des Services
 * Génère un service et l'initialise gràce aux paramêtres définis 
 * 
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 * 
 * @package \Itkg\Service
 */
class Factory 
{
    /**
     * Renvoi un service dont la clé est passée en paramêtre
     * Charge l'ensemble de la configuration liée au service
     * 
     * @static
     * @param string $service La clé du service
     * @param array $parameters La liste des paramêtres 
     * @return \Itkg\Service
     */
    public static function getService($service, array $parameters = array(), $bypassAccess = false) 
    {
        // Instanciation du service par définition
        if(isset(\Itkg::$config[$service]['class'])) {
            $oService = new \Itkg::$config[$service]['class'];
            $sServiceClass = \Itkg::$config[$service]['class'];
        }else {
            $sPackage = \preg_replace('/SERVICE.*/', '', str_replace('_', '', $service));
            $sClass = \preg_replace('/.*_SERVICE_/', '', $service);
            $aTemp = \explode('_', $sClass);
            $sServiceClass = \ucfirst(strtolower($sPackage)).'\\Service\\';
            
            if(is_array($aTemp)) {
                foreach($aTemp as $sValue) {
                    $sServiceClass .= \ucfirst(strtolower($sValue));
                }
            }else {
                $sServiceClass .= \ucfirst(strtolower($sClass));
            }
            // Instanciation du service par nom
            if(class_exists($sServiceClass)) {
                $oService = new $sServiceClass;
            }
        }
        
        if(!isset($oService) || !is_object($oService)) {
            throw new NotFoundException('Le service '.$service.' n\'existe pas car la classe '.$sServiceClass.' n\'est pas définie');
        }
        $oService->setParameters($parameters);

        /**
         * Chargement de la configuration depuis la définition du service
         */
        if(isset(\Itkg::$config[$service]['configuration'])) {
            $oConfiguration = new \Itkg::$config[$service]['configuration'];
        }else {
            /**
             * Chargement de la configuration en essayant d'instanciant la classe 
             * NomService\Configuration
             */
            $sConfigurationClass = $sServiceClass.'\Configuration';
            if(class_exists($sConfigurationClass)) {
                $oConfiguration = new $sConfigurationClass;
            }

        }
        if(!is_object($oConfiguration)) {
            throw new \Itkg\Exception\NotFoundException('La classe de configuration du service '.$service.' n\'existe pas car la classe '.$sConfigurationClass.' n\'est pas définie');
        }

        /**
         * Chargement des paramètres de configuration du service
         * Utile pour les identifiants de WS ou d'autres parametres dépendant de l'environnement
         */
        if(isset(\Itkg::$config[$service]['PARAMETERS'])) {
            $oConfiguration->loadParameters(\Itkg::$config[$service]['PARAMETERS']);
        }else {
            // La définition des paramêtres est obligatoire et doit être initialisé
            // Cela évite les oublis ou les problèmes de nommage
            throw new \Itkg\Exception\NotFoundException('Aucun paramêtre n\'est défini pour le service '.$service.'. Veuillez définir \Itkg::$config[\''.$service.'\'][\'PARAMETERS\']');
        }
        
         // Surcharge de la configuration via la méthode override
        $oConfiguration->override($service);
        $oConfiguration->init();
            if(isset(\Itkg::$config['TYPE_ENVIRONNEMENT'])) {
                $method = 'load'.ucFirst(\Itkg::$config['TYPE_ENVIRONNEMENT']);
            call_user_func_array(array($oConfiguration, $method), array());
        }
        // Chargement de la configuration
        $oService->setConfiguration($oConfiguration);

        // Vérification de l'accès au service
        if(!$bypassAccess && !$oService->canAccess()) { // Access denied
            throw new UnauthorizedException('Vous n\'avez pas le droit d\'accéder à ce service', UnauthorizedException::NON_ABONNE);
        }
         
        // Initialisation du service
        $oService->init();
        
        return $oService;
    }	
}
