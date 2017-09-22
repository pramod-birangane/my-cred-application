<?php
namespace AdminBundle\Service;

class CommonFunctions
{
    public static function showDetailsInside($mixedVar, $die = false){
        echo "<pre>";
        print_r($mixedVar);
        if($die===true){
            die("Done");
        }
    }
    
    public static function vardumpDetailsInside($mixedVar, $die = false){
        echo "<pre>";
        var_dump($mixedVar);
        if($die===true){
            die("Done");
        }
    }
}
