<?php

    // https://auth-db13.hostinger.in/ - for checking PHPMyAdmin

    function getConnection() {
        // main db
        $username = 'u398111847_hw';
        $password = 'm8XDm09a9zdz';
        $host = 'localhost';
        $db_name = 'u398111847_home';

        // testing db
        // $username = 'u398111847_test';
        // $password = 'test@123';
        // $host = 'sql7.main-hosting.eu';
        // $db_name = 'u398111847_test';

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
            print($e);
        	return null;
        }
    }

    function getMSSQLCon() {
        $username = 'rainbowschooljp';
        $password = 'Tqg6p$30';
        $host = '182.50.133.109';
        $db_name = 'rainbowjanakpuri';
        $mssqldriver = '{SQL Server}';
        $mssqldriver2 = '{FreeTDS}';
        $dsn = "odbc:Driver=$mssqldriver;Server=$host;Database=$db_name";
        $dblib= "dblib:host=$host;dbname=$db_name";
        try {
        	$PDO = new PDO($dsn, $username, $password);
        	return $PDO;
        } catch(PDOException $e) {
            print($e);
        	return NULL;
        }
    }

?>
