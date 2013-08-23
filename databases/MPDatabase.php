<?php

class MPDatabase {
    
    private static $dbgeral;
    private static $subdomain;

    public static function db($subdomain = NULL) {
        
        self::$dbgeral = new PDO(
                    'mysql:host=mysql.centralsigma.com.br;dbname=mobile_provider', 
                    'webadmin', 'webADMIN', 
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
        self::$dbgeral->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        if($subdomain === NULL) {
            // retorna banco geral
            return self::$dbgeral;
        }else {
            //retorna nome do banco do subdomínio passado por parâmetro
            $qry_subdomain = self::$dbgeral->prepare('select * from subdominios where subdominio = :subdomain');
            $qry_subdomain->execute(array(':subdomain' => $subdomain));
            
            return self::$subdomain = $qry_subdomain->fetch();
        }
    }
}

