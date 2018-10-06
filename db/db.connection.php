<?php

    function getConnection() {
        $username = 'root';
        $password = '';
        $host = 'localhost';
        $db_name = 'homework';
        $dsn = "mysql:host=$host;dbname=$db_name";
        $options = [
        	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        	PDO::ATTR_EMULATE_PREPARES => false
        ];

        try {
        	$PDO = new PDO($dsn, $username, $password, $options);
        	return $PDO;
        } catch(PDOException $e) {
        	return null;
        }
    }

?>
