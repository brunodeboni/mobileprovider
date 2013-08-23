<?php

require_once 'MPClient.php';

$id_client = MPClient::register('cocacola', 'cnpj', 'endereco', 'complemento', 'cidade', 'uf', 'telefone', 'celular', 'email', 'usuario', 'senha', 'cocacola');

if (is_int($id_client)) {
    
    echo 'Id do cliente cadastrado: '.$id_client.'<br>';
    $db_info = MPClient::createDB($id_client, 'cocacola');
    
    if (is_array($db_info)) {
        echo 'Infos para banco de dados criado:<br>'; var_dump($db_info);
    }else {
        echo $db_info; //variável traz o erro.
    }
}else {
    echo $id_client; //variável traz o erro.
}

?>
