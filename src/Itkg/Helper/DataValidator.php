<?php

namespace Itkg\Helper;

/**
 * Classe utilitaires pour la validation du format de données
 * 
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 * @author Benoit de JACOBET <benoit.dejacobet@businessdecision.com>
 * @author Clément GUINET <clement.guinet@businessdecision.com>
 * 
 * @abstract
 * @package \Itkg\Helper
 */
class DataValidator 
{
    
    /**
     * Valide que le donnée est une date au format aaaa-mm-dd ou aaaa-mm-dd hh:mm:ss
     *
     * @param string $sDate
     * @return boolean
     */
    public static function isDateAAAAMMDD($sDate) 
    {
        if ($sDate == '') {
            return true;
        }
        
        $aDate = explode(" ", $sDate);
        if (is_array($aDate)) {
            $aYearMonthDay = explode("-", $aDate[0]);
            if(strlen($aYearMonthDay[0]) == 4 && is_numeric($aYearMonthDay[0])
                    && strlen($aYearMonthDay[1]) == 2 && is_numeric($aYearMonthDay[1]) 
                    && strlen($aYearMonthDay[2]) == 2 && is_numeric($aYearMonthDay[2])
                    ) {
               return true;
            }
        }
        return false;
    }
}
