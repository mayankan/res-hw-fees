<?php 
    /**
     * This page is used to export logs from `log` table
    */
    require(__DIR__.'/../db/db.connection.php');
    require(__DIR__.'/../helpers.php');
    session_start();

    // logs out user if it's not a admin
    if ($_SESSION['role'] !== 'fee_clerk') {
        header('Location: ../404.html');
        exit();
    }

    $PDO = getConnection();
    if (is_null($PDO)) {
        die("Can't connect to the database");
    }

    /**
     * put `message` data in csv file and provide it as a response
     *
     * @param PDOObject $PDO
     * @param Array $array - Homework data array
     * @param String $filename - Filename for the export
     *
     * @return File
     *
     * @throws Exception // No Specefic Exception Defined
     *
    */
    function array_to_csv_download($array, $filename = "export.csv") {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        $f = fopen('php://output', 'w');
        fputcsv($f, array(
            "Date",
            "Admission Number",
            "Name",
            "Class & Section",
            "Total Fees",
            "Paid"
        ));

        foreach ($array as $fee) {
            if (is_null($fee['paid_at'])) {
                fputcsv($f, array(
                    date_format(date_create($fee['month']), 'F y'),
                    $fee['admission_no'],
                    $fee['name'],
                    $fee['class'],
                    $fee['total_fee'],
                    "Not Paid"
                ));
            } else {
                fputcsv($f, array(
                    date_format(date_create($fee['month']), 'F y'),
                    $fee['admission_no'],
                    $fee['name'],
                    $fee['class'],
                    $fee['total_fee'],
                    date_format(date_create($fee['paid_at']), 'd F y H:i:s')
                ));
            }
        }
        fclose($f);
    }   

    // array_to_csv_download($PDO, getAllLogs($PDO), 'logs.csv');
    array_to_csv_download($_SESSION['feeData'], "fee_data.csv");
    unset($_SESSION['feeData']);
    unset($PDO);

?>