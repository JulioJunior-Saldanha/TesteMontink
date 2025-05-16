<?php
    $host = 'localhost';        // Endereço do servidor
    $dbname = 'montink';  // Nome do banco de dados
    $usuario = 'root';       // Nome de usuário
    $senha = '';           // Senha
   
    $mysqli =  new mysqli($host,$usuario,$senha,$dbname);
    if ($mysqli->connect_errno){
        echo "falha ao conectar:  (". $mysqli->connect_errno . ") " .$mysqli->connect_error;  
    };