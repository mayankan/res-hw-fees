<?php

    // https://auth-db13.hostinger.in/ - for checking PHPMyAdmin

    function getConnection() {
        $username = 'u398111847_hw';
        $password = 'eSoIxZO39v9K';
        $host = 'sql7.main-hosting.eu';
        $db_name = 'u398111847_home';
        $dsn = "mysql:host=$host;dbname=$db_name";
        $options = [
            // instead of throwing error PDO throws an exception
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // instead of normal array with 0, 1, 2 as keys 
            // it fetches data in a associated array with column names as associated values
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // PDO does not emulate prepare when provided with data
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
        $sqlsrv= "sqlsrv:Server=$host;Database=$db_name";
        try {
        	$PDO = new PDO($sqlsrv, $username, $password);
        	return $PDO;
        } catch(PDOException $e) {
            print($e);
        	return NULL;
        }
    }

?>
