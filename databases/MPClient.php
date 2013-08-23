<?php

require_once 'MPDatabase.php';

class MPClient {
    
    private static $id_client;
    private static $db_info;
    
    public static function checkSubdomain($subdomain) {
        $conn = MPDatabase::db();
        $query = $conn->prepare('select subdominio from subdominios where subdominio = :subdominio');
        $query->bindValue(':subdominio', $subdomain, PDO::PARAM_STR);
        $query->execute();
        
        if ($query->rowCount() > 0) return true; //se já houver o registro, retorna TRUE
        else return false; //se não houver o registro, retorna FALSE
    }
    
    public static function register($empresa, $cnpj, $endereco, $complemento, $cidade, $uf, $telefone, $celular, $email, $usuario, $senha, $subdomain) {
        
        //Verifica se subdomínio já existe
        $subdomain_exists = self::checkSubdomain($subdomain);
        
        //Se subdomíno NÃO existe
        if (! $subdomain_exists) {
            $conn = MPDatabase::db();
            $query = $conn->prepare('insert into clientes 
                (empresa, cnpj, endereco, complemento, cidade, uf, telefone, celular, email, usuario, senha) 
                values (:empresa, :cnpj, :endereco, :complemento, :cidade, :uf, :telefone, :celular, :email, :usuario, :senha)');
            $query->bindValue(':empresa', $empresa, PDO::PARAM_STR);
            $query->bindValue(':cnpj', $cnpj, PDO::PARAM_STR);
            $query->bindValue(':endereco', $endereco, PDO::PARAM_STR);
            $query->bindValue(':complemento', $complemento, PDO::PARAM_STR);
            $query->bindValue(':cidade', $cidade, PDO::PARAM_STR);
            $query->bindValue(':uf', $uf, PDO::PARAM_STR);
            $query->bindValue(':telefone', $telefone, PDO::PARAM_STR);
            $query->bindValue(':celular', $celular, PDO::PARAM_STR);
            $query->bindValue(':email', $email, PDO::PARAM_STR);
            $query->bindValue(':usuario', $usuario, PDO::PARAM_STR);
            $query->bindValue(':senha', $senha, PDO::PARAM_STR);
            $success = $query->execute();
            self::$id_client = (int) $conn->lastInsertId();

            if ($success) {
                self::applyToApp(self::$id_client);
                
                return self::$id_client;
            }else {
                return 'Ocorreu um erro ao registrar seu cadastro.';
            }
        }else {
            return 'Este subdomínio já esta cadastrado.';
        }
    }
    
    public static function createDB($id_client, $subdomain) {
        
        // Define um nome
        $newfile_name = "$subdomain.FDB";
        
        // Copia arquivo do banco
        $newfile = copy('BANCO_DEFAULT.FDB', "$subdomain.FDB");
        
        if ($newfile) {
            //Insere dados de conexão com o novo banco na tabela subdomínios
            $db_driver = 'firebird';
            $db_username = 'SYSDBA';
            $db_password = 'masterkey';
            $db_host = 'localhost';
            $db_name = '';
            $db_url = realpath(__DIR__.'/'.$newfile_name);

            $conn = MPDatabase::db();
            $query = $conn->prepare('insert into subdominios 
                (subdominio, db_driver, db_username, db_password, db_host, db_name, db_url, db_debug) 
                values (:subdominio, :db_driver, :db_username, :db_password, :db_host, :db_name, :db_url, 1)');
            $query->bindValue(':subdominio', $subdomain, PDO::PARAM_STR);
            $query->bindValue(':db_driver', $db_driver, PDO::PARAM_STR);
            $query->bindValue(':db_username', $db_username, PDO::PARAM_STR);
            $query->bindValue(':db_password', $db_password, PDO::PARAM_STR);
            $query->bindValue(':db_host', $db_host, PDO::PARAM_STR);
            $query->bindValue(':db_name', $db_name, PDO::PARAM_STR);
            $query->bindValue(':db_url', $db_url, PDO::PARAM_STR);
            $success = $query->execute();

            self::$db_info = array(
                                'db_driver' => $db_driver, 
                                'db_username' => $db_username, 
                                'db_password' => $db_password, 
                                'db_host' => $db_host, 
                                'db_name' => $db_name, 
                                'db_url' => $db_url);

            if ($success) {
                $query2 = $conn->prepare('update cadastro set subdominio = :subdominio where id = :id_client');
                $query2->bindValue(':subdominio', $subdomain, PDO::PARAM_STR);
                $query2->bindValue(':id_client', $id_client, PDO::PARAM_INT);
                $query2->execute();
                
                return self::$db_info;
            }else return 'Ocorreu um erro ao cadastrar as informações do banco de dados recém criado.';
        }else 
            return 'Ocorreu um erro ao criar banco de dados.';   
    }
    
    function applyToApp($client_id) {
        //Por enquanto, apenas SIGMA ANDROID
        $conn = MPDatabase::db();
        $query_app = $conn->prepare("insert into clientes_aplicativos (cliente_id, aplicativo, aprovado) 
                    values (:cliente_id, 'sigmaandroid', 0");
        $query_app->bindValue(':cliente_id', $client_id, PDO::PARAM_INT);
        $success = $query_app->execute();
        
        if ($success) return true;
        else return false;
    }
}
?>
