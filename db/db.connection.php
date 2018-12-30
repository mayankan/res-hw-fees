<?php

    function getConnection() {
        $username = 'u281853335_hw';
        $password = 'ViKHubGJ7L1O';
        $host = 'sql7.main-hosting.eu';
        $db_name = 'u281853335_home';
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

    function getMSSQLCon() {
        $username = 'rainbowschooljp';
        $password = 'Ovho#210';
        $host = '182.50.133.109';
        $db_name = 'rainbowjanakpuri';
    }

?>
