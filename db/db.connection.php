<?php

    function getConnection() {
        $username = 'u398111847_hw';
        $password = 'eSoIxZO39v9K';
        $host = 'mysql.hostinger.in';
        $db_name = 'u398111847_home';
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
        $mssqldriver = '{SQL Server}';
        $dsn = "odbc:Driver=$mssqldriver;Server=$host;Database=$db_name";
        try {
        	$PDO = new PDO($dsn, $username, $password);
        	return $PDO;
        } catch(PDOException $e) {
            print($e);
        	return NULL;
        }
    }

?>
